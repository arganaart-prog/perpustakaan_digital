<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        $user = $request->user();

        if ($user && $user->hasRole('member')) {
            // Get Borrowing History
            $history = \App\Models\Borrow::where('user_id', $user->id)
                ->with('book')
                ->orderByDesc('borrow_date')
                ->get();

            return view('member.profile', compact('user', 'history'));
        }

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Send OTP for password change request.
     */
    public function sendChangePasswordOtp(Request $request)
    {
        $user = Auth::user();
        
        \App\Models\PasswordOtp::where('user_id', $user->id)->delete();
        
        $otp = rand(100000, 999999);
        
        \App\Models\PasswordOtp::create([
            'user_id'         => $user->id,
            'otp'             => $otp,
            'expired_at'      => now()->addMinutes(5),
            'attempt'         => 1,
            'next_allowed_at' => now()->addMinutes(5),
        ]);
        
        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\SendOtpMail($otp, 'reset'));
        
        return response()->json([
            'ok' => true,
            'message' => 'Kode OTP berhasil dikirim ke email Anda.'
        ]);
    }

    /**
     * Change password using OTP code.
     */
    public function changePasswordWithOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        
        $otpRecord = \App\Models\PasswordOtp::where('user_id', $user->id)
            ->where('otp', $request->otp)
            ->where('expired_at', '>', now())
            ->first();
            
        if (!$otpRecord) {
            return response()->json([
                'ok' => false,
                'message' => 'Kode OTP salah atau telah kedaluwarsa.'
            ], 422);
        }
        
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password)
        ]);
        
        \App\Models\PasswordOtp::where('user_id', $user->id)->delete();
        
        return response()->json([
            'ok' => true,
            'message' => 'Kata sandi Anda berhasil diperbarui!'
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update user bio, social media and pending profile photo.
     */
    public function updateDetails(Request $request)
    {
        $request->validate([
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048', // 2MB
            'social_links' => 'nullable|array|max:3',
            'social_links.*.platform' => 'nullable|string|in:tiktok,instagram,facebook,threads,linkedin,x',
            'social_links.*.value' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        
        $user->bio = $request->bio;

        // Process social links (Max 3, no duplicate platforms)
        $socialLinks = [];
        if ($request->has('social_links') && is_array($request->social_links)) {
            $usedPlatforms = [];
            foreach ($request->social_links as $item) {
                $platform = trim($item['platform'] ?? '');
                $value = trim($item['value'] ?? '');
                if (!empty($platform) && !empty($value) && !in_array($platform, $usedPlatforms)) {
                    $socialLinks[] = [
                        'platform' => $platform,
                        'value' => $value,
                    ];
                    $usedPlatforms[] = $platform;
                }
                if (count($socialLinks) >= 3) break;
            }
        }
        $user->social_links = $socialLinks;

        if ($request->hasFile('avatar')) {
            // Upload pending photo
            $path = $request->file('avatar')->store('avatars/pending', 'public');
            
            // Delete existing pending photo from storage if any
            if ($user->avatar_pending) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar_pending);
            }
            
            $user->avatar_pending = $path;
        }

        $user->save();

        return back()->with('status', 'profile-details-updated');
    }

    /**
     * View public profile page of any user.
     */
    public function viewPublicProfile(\App\Models\User $user)
    {
        // 1. User Reviews (Approved reviews only)
        $reviews = \App\Models\BookReview::where('user_id', $user->id)
            ->where('is_approved', true)
            ->with('book')
            ->latest()
            ->get();

        // 2. Borrowing History
        $borrows = \App\Models\Borrow::where('user_id', $user->id)
            ->with('book')
            ->orderByDesc('borrow_date')
            ->get();

        return view('member.public-profile', compact('user', 'reviews', 'borrows'));
    }

    /**
     * Stream user avatar directly from storage disk to bypass any OS symlink issues.
     */
    public function avatar(\App\Models\User $user)
    {
        if (request()->has('pending') && $user->avatar_pending && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar_pending)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->response($user->avatar_pending);
        }

        if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->response($user->avatar);
        }

        return redirect('https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=047857&background=ecfdf5&size=256');
    }
}
