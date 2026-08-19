<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // 🔐 Cek status akun sebelum boleh masuk
        if ($user->status !== 'active') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $pesan = match ($user->status) {
                'register'      => 'Silakan verifikasi email kamu terlebih dahulu.',
                'pending'       => 'Akun kamu sedang menunggu persetujuan petugas perpustakaan.',
                'pending_admin' => 'Akun kamu sedang menunggu persetujuan admin.',
                'approved'      => 'Akun kamu sudah disetujui. Silakan masukkan kode aktivasi.',
                'rejected'      => 'Akun kamu ditolak. Hubungi admin untuk informasi lebih lanjut.',
                default         => 'Akun kamu belum aktif.',
            };

            return back()->withErrors(['email' => $pesan]);
        }

        // 🔐 Session lebih panjang untuk admin
        if ($user->hasRole('admin')) {
            config(['session.lifetime' => 60 * 24 * 365 * 2]);
        }

        // 🎯 Redirect berdasarkan role & Bersihkan Cache Permission
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('petugas')) {
            return redirect()->route('petugas.dashboard');
        }

        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
