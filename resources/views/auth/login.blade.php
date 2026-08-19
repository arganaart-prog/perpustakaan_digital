<x-guest-layout>
    <!-- Logo & Header Section -->
    <div class="flex flex-col items-center mb-10">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-indigo-700 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-open"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-indigo-900">Katalog Buku</h1>
        </div>
        
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight text-center">Masuk ke Perpustakaan</h2>
        <p class="text-gray-500 text-sm mt-3 text-center max-w-[280px] leading-relaxed">
            Silakan masuk untuk mengakses dan meminjam koleksi buku digital kami.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Main Card -->
    <div class="bg-white rounded-[3rem] p-10 shadow-2xl shadow-gray-200/50 border border-white">
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Alamat Email</label>
                <div class="relative group">
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        :value="old('email')" 
                        required 
                        autofocus 
                        placeholder="nama@email.com"
                        class="w-full bg-gray-100/80 border-none rounded-2xl py-4 pl-5 pr-12 text-sm focus:ring-2 focus:ring-indigo-600 transition-all placeholder:text-gray-400"
                    >
                    <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-at-sign"><circle cx="12" cy="12" r="4"/><path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-4 8"/></svg>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div x-data="{ show: false }">
                <div class="flex justify-between items-end mb-2 px-1">
                    <label for="password" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest">Kata Sandi</label>
                    <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 transition-colors">Lupa kata sandi?</a>
                </div>
                <div class="relative group">
                    <input 
                        id="password" 
                        :type="show ? 'text' : 'password'" 
                        name="password" 
                        required 
                        placeholder="••••••••"
                        class="w-full bg-gray-100/80 border-none rounded-2xl py-4 pl-5 pr-12 text-sm focus:ring-2 focus:ring-indigo-600 transition-all placeholder:text-gray-400"
                    >
                    <button 
                        type="button" 
                        @click="show = !show"
                        class="absolute inset-y-0 right-0 pr-5 flex items-center text-gray-400 hover:text-indigo-600 transition-colors"
                    >
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg x-show="show" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-off" style="display: none;"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="px-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                    <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-indigo-700 shadow-sm focus:ring-indigo-600 transition-all">
                    <span class="ms-3 text-[13px] text-gray-500 group-hover:text-gray-700 transition-colors">Ingat saya</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-indigo-700 hover:bg-indigo-800 text-white py-4 rounded-2xl font-bold text-sm tracking-widest uppercase shadow-xl shadow-indigo-100 transition-all active:scale-95">
                Masuk
            </button>
        </form>

        <div class="relative my-8">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
            <div class="relative flex justify-center text-[10px] uppercase font-bold tracking-[0.2em]"><span class="bg-white px-4 text-gray-300">Atau</span></div>
        </div>

        <!-- Google Login -->
        <a 
            href="{{ route('google.login') }}" 
            class="w-full flex items-center justify-center gap-3 py-4 border border-gray-100 rounded-2xl hover:bg-gray-50 transition-all active:scale-[0.98] shadow-sm"
        >
            <img class="h-5 w-5" src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google logo">
            <span class="text-xs font-bold text-gray-700">Lanjutkan dengan Google</span>
        </a>
    </div>

    <!-- Footer Links -->
    <div class="mt-10 text-center">
        <p class="text-sm text-gray-500 font-medium">
            Belum menjadi anggota? <a href="{{ route('register') }}" class="text-indigo-700 font-bold hover:underline">Daftar sekarang</a>
        </p>
    </div>
</x-guest-layout>