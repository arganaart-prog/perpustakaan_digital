<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\SendOtpMail;

class PasswordOtpController extends Controller
{
    /**
     * hitungDelay: Menentukan waktu tunggu (menit) sebelum user boleh minta OTP lagi.
     * Semakin sering mencoba, semakin lama delay-nya (antispam).
     */
    private function hitungDelay(int $attempt): int
    {
        if ($attempt <= 2)  return 5;
        if ($attempt <= 5)  return 20;
        if ($attempt <= 10) return 60;
        return 180;
    }

    // =========================================================
    // LUPA SANDI — OTP via tabel password_otps
    // =========================================================

    public function sendOtp($user)
    {
        $lastOtp = PasswordOtp::where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$user->otp_unlocked) {
            if ($lastOtp && $lastOtp->next_allowed_at && now()->lt($lastOtp->next_allowed_at)) {
                return back()->withErrors([
                    'otp' => 'Terlalu sering. Tunggu sampai ' . $lastOtp->next_allowed_at->format('H:i:s')
                ]);
            }
        }

        if ($user->otp_unlocked) {
            $user->update(['otp_unlocked' => false]);
            PasswordOtp::where('user_id', $user->id)->delete();
            $lastOtp = null;
        }

        $attempt = $lastOtp ? $lastOtp->attempt + 1 : 1;
        $delay   = $this->hitungDelay($attempt);

        $otp = rand(100000, 999999);

        PasswordOtp::create([
            'user_id'         => $user->id,
            'otp'             => $otp,
            'expired_at'      => now()->addMinutes(5),
            'attempt'         => $attempt,
            'next_allowed_at' => now()->addMinutes($delay),
        ]);

        Mail::to($user->email)->send(new SendOtpMail($otp));
    }

    public function showForm()
    {
        $userId = session('otp_pending_user_id');

        $latestOtp = PasswordOtp::where('user_id', $userId)
            ->latest()
            ->first();

        $activeOtp = ($latestOtp && $latestOtp->expired_at > now()) ? $latestOtp : null;

        return view('auth.verify-otp', [
            'expired_at'      => $activeOtp ? $activeOtp->expired_at : null,
            'next_allowed_at' => $latestOtp ? $latestOtp->next_allowed_at : null,
        ]);
    }

    public function resendOtp()
    {
        $userId = session('otp_pending_user_id');
        $user   = User::find($userId);

        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Sesi tidak valid, silakan ulangi dari awal.']);
        }

        $result = $this->sendOtp($user);

        if ($result instanceof \Illuminate\Http\RedirectResponse) {
            return $result;
        }

        return redirect()->route('password.otp.form')
            ->with('status', 'OTP baru telah dikirim ke email kamu.');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required']);

        $otp = PasswordOtp::where('otp', $request->otp)
            ->where('expired_at', '>', now())
            ->latest()
            ->first();

        if (!$otp) {
            return back()->withErrors(['otp' => 'OTP salah / expired']);
        }

        session(['otp_user_id' => $otp->user_id]);

        return redirect()->route('password.otp.reset');
    }

    public function showResetForm()
    {
        return view('auth.new-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::find(session('otp_user_id'));

        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'Sesi tidak valid.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        PasswordOtp::where('user_id', $user->id)->delete();
        session()->forget('otp_user_id');

        return redirect()->route('login')->with('status', 'Password berhasil diubah');
    }

    // =========================================================
    // REGISTER — OTP via kolom users.otp
    // =========================================================

    public function showRegisterOtp()
    {
        $user = User::find(session('user_id'));

        return view('auth.verify-register-otp', [
            'otp_expired_at'      => $user ? $user->otp_expired_at : null,
            'otp_next_allowed_at' => $user ? $user->otp_next_allowed_at : null,
        ]);
    }

    public function resendRegisterOtp()
    {
        $user = User::find(session('user_id'));

        if (!$user) {
            return redirect()->route('register')
                ->withErrors(['email' => 'Sesi tidak valid, silakan daftar ulang.']);
        }

        if (!$user->otp_unlocked) {
            if ($user->otp_next_allowed_at && now()->lt($user->otp_next_allowed_at)) {
                return back()->withErrors([
                    'otp' => 'Terlalu sering. Tunggu sampai ' . $user->otp_next_allowed_at->format('H:i:s')
                ]);
            }
        }

        $attempt = $user->otp_unlocked
            ? 1
            : (($user->otp_attempt ?? 0) + 1);

        $delay = $this->hitungDelay($attempt);
        $otp   = rand(100000, 999999);

        $user->update([
            'otp'                 => $otp,
            'otp_expired_at'      => now()->addMinutes(5),
            'otp_attempt'         => $attempt,
            'otp_next_allowed_at' => now()->addMinutes($delay),
            'otp_unlocked'        => false,
        ]);

        Mail::to($user->email)->send(new SendOtpMail($otp, 'register'));

        return redirect()->route('register.otp.form')
            ->with('status', 'OTP baru telah dikirim ke email kamu.');
    }

    /**
     * Verifikasi OTP register — arahkan ke antrian/pending sesuai register_as
     */
    public function verifyRegisterOtp(Request $request)
    {
        $request->validate(['otp' => 'required']);

        $user = User::find(session('user_id'));

        if (!$user || $user->otp != $request->otp || $user->otp_expired_at < now()) {
            return back()->withErrors(['otp' => 'OTP salah / expired']);
        }

        // Semua member diset ke pending dan menunggu interaksi petugas (CRM)
        $user->update([
            'is_verified'         => true,
            'status'              => 'pending',
            'otp'                 => null,
            'otp_attempt'         => 0,
            'otp_next_allowed_at' => null,
            'pending_expired_at'  => now()->addHours(24),
        ]);

        session(['verify_user_id' => $user->id]);
        session()->forget('user_id');

        return redirect()->route('pending.approval')
            ->with('status', 'Email terverifikasi. Akun Anda sedang menunggu persetujuan petugas.');
    }

    // =========================================================
    // HALAMAN PENDING APPROVAL
    // =========================================================

    public function showPendingApproval()
    {
        $userId = session('verify_user_id');
        $user   = $userId ? User::find($userId) : null;

        $allPetugas = User::role('petugas')->where('is_visible', true)->get();
        
        // Map hari Inggris ke Indonesia
        $mapHari = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu'
        ];
        $hariIni = $mapHari[now()->format('l')] ?? '';
        
        $petugasHariIni = collect();
        $petugasLainnya = collect();
        
        foreach ($allPetugas as $petugas) {
            $days = array_map('trim', explode(',', $petugas->work_days ?? ''));
            if (in_array($hariIni, $days)) {
                $petugasHariIni->push($petugas);
            } else {
                $petugasLainnya->push($petugas);
            }
        }

        return view('auth.pending-approval', compact('user', 'petugasHariIni', 'petugasLainnya', 'hariIni'));
    }

    // =========================================================
    // INPUT KODE AKTIVASI (setelah petugas generate)
    // =========================================================



    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $userId = session('verify_user_id');
        $user   = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi tidak valid.']);
        }

        // Proteksi percobaan: Jika salah memasukkan kode lebih dari 5 kali, akun ditangguhkan.
        if ($user->code_attempt >= 5) {
            $user->update(['status' => 'suspended']);
            return redirect()->route('login')->withErrors(['code' => 'Terlalu banyak percobaan salah. Akun ditangguhkan otomatis, silakan hubungi admin.']);
        }

        // Cari kode aktivasi yang cocok dengan input user dan milik user tersebut.
        $code = \App\Models\ActivationCode::where('code', strtoupper($request->code))
            ->where('user_id', $user->id)
            ->first();

        // Validasi: Jika kode tidak ditemukan atau tidak cocok.
        if (!$code) {
            $user->increment('code_attempt'); // Tambah hitungan percobaan salah.
            $sisa = 5 - $user->code_attempt;
            if ($sisa <= 0) {
                $user->update(['status' => 'suspended']);
                return redirect()->route('login')->withErrors(['code' => 'Terlalu banyak percobaan salah. Akun ditangguhkan.']);
            }
            return back()->withErrors(['code' => "Kode aktivasi salah. Sisa percobaan: {$sisa}"]);
        }

        // Cek status penggunaan kode: Pastikan belum pernah dipakai sebelumnya.
        if ($code->is_used) {
            return back()->withErrors(['code' => 'Kode ini sudah pernah digunakan sebelumnya.']);
        }

        // Cek masa berlaku: Jika sudah lewat waktu expired, kode akan dihapus.
        if ($code->expired_at && now()->gt($code->expired_at)) {
            $code->delete();
            $user->update(['status' => 'pending']); // Kembalikan ke antrian persetujuan.
            return back()->withErrors(['code' => 'Kode ini sudah kedaluwarsa. Silakan minta kode baru ke petugas.']);
        }

        // Proses AKTIVASI: Kode benar, akun diaktifkan.
        $user->update([
            'status'      => 'active',
            'approved_by' => $code->created_by,
            'approved_at' => now(),
            'code_attempt'=> 0, // Reset percobaan jika berhasil.
        ]);

        $code->update([
            'is_used' => true,
            'used_at' => now(),
        ]);

        // Mapping role
        if ($code->role === 'student') {
            $user->assignRole('member');
            $user->update(['member_type' => 'student']);
        } elseif ($code->role === 'teacher') {
            $user->assignRole('member');
            $user->update(['member_type' => 'teacher']);
        } elseif ($code->role === 'petugas') {
            $user->assignRole('petugas');
        }

        session()->forget('verify_user_id');

        return redirect()->route('login')
            ->with('status', 'Akun berhasil diaktifkan berdasarkan kode. Silakan login!');
    }
}