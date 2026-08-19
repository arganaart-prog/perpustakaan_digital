<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Major;
use App\Models\BookQueue;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemberApprovalController extends Controller
{
    /**
     * Dashboard Petugas - Menampilkan statistik dan antrean booking aktif.
     */
    public function dashboard()
    {
        $pendingCount = User::where('status', 'pending')->count();
        $totalMembers = User::role('member')->count();
        
        // Ambil antrean yang siap diambil (READY) atau sedang dipanggil (CALLED)
        $activeQueues = BookQueue::with(['user', 'book'])
            ->whereIn('status', [BookQueue::STATUS_READY, BookQueue::STATUS_CALLED])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('petugas.dashboard', compact('pendingCount', 'totalMembers', 'activeQueues'));
    }

    /**
     * Tampilkan antrian member yang menunggu persetujuan.
     */
    public function index()
    {
        $pendingMembers = User::where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        $classes = SchoolClass::orderBy('name')->get();
        $majors  = Major::orderBy('name')->get();

        return view('petugas.member-approval', compact('pendingMembers', 'classes', 'majors'));
    }

    /**
     * Generate kode aktivasi untuk satu user.
     * Untuk role student → wajib isi nama, kelas, jurusan.
     */
    public function generateCode(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:student,teacher']);

        if (!auth()->user()->hasRole('petugas')) {
            abort(403);
        }

        if ($user->status !== 'pending') {
            abort(403, 'User sudah diproses');
        }

        if (ActivationCode::where('user_id', $user->id)->exists()) {
            return back()->withErrors(['error' => 'User sudah memiliki kode aktivasi yang aktif.']);
        }

        // Jika role adalah student → wajib ada kelas & jurusan
        if ($request->role === 'student') {
            $request->validate([
                'name'    => 'required|string|max:255',
                'kelas'   => 'required|string|max:50',
                'jurusan' => 'required|string|max:100',
            ], [
                'name.required'    => 'Nama lengkap harus diisi.',
                'kelas.required'   => 'Kelas harus dipilih.',
                'jurusan.required' => 'Jurusan harus dipilih.',
            ]);

            // Perbarui data user
            $user->update([
                'name'    => trim($request->name),
                'kelas'   => $request->kelas,
                'jurusan' => $request->jurusan,
            ]);
        }

        // Generate kode unik
        $code = strtoupper(Str::random(4)) . rand(100, 999);
        while (ActivationCode::where('code', $code)->exists()) {
            $code = strtoupper(Str::random(4)) . rand(100, 999);
        }

        ActivationCode::create([
            'code'       => $code,
            'user_id'    => $user->id,
            'role'       => $request->role,
            'created_by' => auth()->id(),
            'expired_at' => now()->addHours(24),
        ]);

        $user->update(['status' => 'approved']);

        return back()->with('generated', [
            'user_id' => $user->id,
            'nama'    => $user->name,
            'email'   => $user->email,
            'kode'    => $code,
            'role'    => $request->role,
        ]);
    }

    /**
     * Tampilkan semua user (member, suspended) — untuk monitoring petugas.
     */
    public function allUsers()
    {
        $users = User::role('member')
            ->whereIn('status', ['approved', 'active', 'suspended'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('petugas.all-users', compact('users'));
    }

    /**
     * Tampilkan riwayat peminjaman dan aktivitas satu user.
     */
    public function userHistory(User $user)
    {
        if (!auth()->user()->hasRole(['petugas', 'admin'])) {
            abort(403);
        }

        $borrows = \App\Models\Borrow::with('book')
            ->where('user_id', $user->id)
            ->latest('borrow_date')
            ->paginate(15);

        $queues = \App\Models\BookQueue::with('book')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('petugas.user-history', compact('user', 'borrows', 'queues'));
    }

    /**
     * Unlock Suspended User (hanya untuk student/teacher jika petugas)
     */
    public function unlock(User $user)
    {
        if (!auth()->user()->hasRole('petugas')) {
            abort(403);
        }

        if ($user->hasRole(['admin', 'petugas'])) {
            return back()->withErrors(['error' => 'Anda tidak memiliki hak untuk membuka blokir role ini.']);
        }

        if ($user->status !== 'suspended') {
            return back()->withErrors(['error' => 'User ini tidak sedang disuspend.']);
        }

        $user->update([
            'status'       => 'approved',
            'code_attempt' => 0,
        ]);

        return back()->with('status', "Akun " . $user->name . " berhasil di-unlock dan dapat mencoba input kode kembali.");
    }

    /**
     * Hapus member secara permanen (hanya untuk student/teacher)
     */
    public function destroyUser(User $user)
    {
        if (!auth()->user()->hasRole('petugas')) {
            abort(403);
        }

        if ($user->hasRole(['admin', 'petugas'])) {
            return back()->withErrors(['error' => 'Petugas hanya boleh menghapus akun member (murid/guru).']);
        }

        $nama = $user->name;
        $user->delete();

        return back()->with('status', "Akun member {$nama} berhasil dihapus permanen.");
    }

    /**
     * Tampilkan halaman moderasi foto profil yang pending.
     */
    public function avatarModeration()
    {
        if (!auth()->user()->hasRole('petugas')) {
            abort(403);
        }

        $pendingUsers = User::whereNotNull('avatar_pending')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('petugas.avatar-moderation', compact('pendingUsers'));
    }

    /**
     * Setujui foto profil baru.
     */
    public function approveAvatar(User $user)
    {
        if (!auth()->user()->hasRole('petugas')) {
            abort(403);
        }

        if (!$user->avatar_pending) {
            return back()->withErrors(['error' => 'Tidak ada foto profil yang pending untuk disetujui.']);
        }

        // Hapus foto lama jika ada
        if ($user->avatar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
        }

        // Jadikan pending sebagai avatar utama
        $user->avatar = $user->avatar_pending;
        $user->avatar_pending = null;
        $user->save();

        return back()->with('status', "Foto profil baru {$user->name} berhasil disetujui.");
    }

    /**
     * Tolak foto profil baru.
     */
    public function rejectAvatar(User $user)
    {
        if (!auth()->user()->hasRole('petugas')) {
            abort(403);
        }

        if (!$user->avatar_pending) {
            return back()->withErrors(['error' => 'Tidak ada foto profil yang pending untuk ditolak.']);
        }

        // Hapus file pending avatar
        \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar_pending);

        $user->avatar_pending = null;
        $user->save();

        return back()->with('status', "Foto profil baru {$user->name} ditolak dan dihapus.");
    }
}
