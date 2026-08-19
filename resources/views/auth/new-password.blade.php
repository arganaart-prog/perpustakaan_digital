<x-guest-layout>
    <!-- Logo & Header Section -->
    <div class="flex flex-col items-center mb-10">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-indigo-700 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 1 1 10 0v4"/></svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-indigo-900">Skarifta Perpus</h1>
        </div>
        
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight text-center">Buat Kata Sandi Baru</h2>
        <p class="text-gray-500 text-sm mt-3 text-center max-w-[280px] leading-relaxed">
            Berhasil! Silakan masukkan kata sandi baru Anda untuk mengamankan akun.
        </p>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-[3rem] p-10 shadow-2xl shadow-gray-200/50 border border-white">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.update.otp') }}" class="space-y-6">
            @csrf

            <!-- New Password -->
            <div>
                <label for="password" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Kata Sandi Baru</label>
                <div class="relative group">
                    <input 
                        id="password" 
                        type="password" 
                        name="password" 
                        required 
                        placeholder="Minimal 8 karakter"
                        class="w-full bg-gray-100/80 border-none rounded-2xl py-4 pl-5 pr-12 text-sm focus:ring-2 focus:ring-indigo-600 transition-all placeholder:text-gray-400"
                    >
                    <div class="absolute inset-y-0 right-0 pr-5 flex items-center text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-lock"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.5 3.8 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg></div>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Ulangi Kata Sandi Baru</label>
                <div class="relative group">
                    <input 
                        id="password_confirmation" 
                        type="password" 
                        name="password_confirmation" 
                        required 
                        placeholder="Ketik ulang sandi"
                        class="w-full bg-gray-100/80 border-none rounded-2xl py-4 pl-5 pr-12 text-sm focus:ring-2 focus:ring-indigo-600 transition-all placeholder:text-gray-400"
                    >
                    <div class="absolute inset-y-0 right-0 pr-5 flex items-center text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-check"><path d="M11 18H3"/><path d="m15 18 2 2 4-4"/><path d="M16 12H3"/><path d="M16 6H3"/></svg></div>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit" class="w-full bg-indigo-700 hover:bg-indigo-800 text-white py-4 rounded-2xl font-bold text-sm tracking-widest uppercase shadow-xl shadow-indigo-100 transition-all active:scale-95">
                Simpan Kata Sandi
            </button>
        </form>
    </div>

    <!-- Footer Links -->
    <div class="mt-10 text-center">
        <p class="text-sm text-gray-500 font-medium">
            Ingat kembali? <a href="{{ route('login') }}" class="text-indigo-700 font-bold hover:underline">Masuk di sini</a>
        </p>
    </div>
</x-guest-layout>