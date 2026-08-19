<x-guest-layout>
    <!-- Logo & Header Section -->
    <div class="flex flex-col items-center mb-10">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-indigo-700 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-key-round"><path d="M2 18v3c0 .6.4 1 1 1h4v-3h3v-3h2l1.4-1.4a6.5 6.5 0 1 0-4-4Z"/><circle cx="16.5" cy="7.5" r=".5"/></svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-indigo-900">Skarifta Perpus</h1>
        </div>
        
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight text-center">Lupa Kata Sandi?</h2>
        <p class="text-gray-500 text-sm mt-3 text-center max-w-[280px] leading-relaxed">
            Tenang, masukkan email Anda untuk mendapatkan kode verifikasi OTP.
        </p>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-[3rem] p-10 shadow-2xl shadow-gray-200/50 border border-white">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Alamat Email</label>
                <div class="relative group">
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@email.com" class="w-full bg-gray-100/80 border-none rounded-2xl py-4 pl-5 pr-12 text-sm focus:ring-2 focus:ring-indigo-600 transition-all">
                    <div class="absolute inset-y-0 right-0 pr-5 flex items-center text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg></div>
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <button type="submit" class="w-full bg-indigo-700 hover:bg-indigo-800 text-white py-4 rounded-2xl font-bold text-sm tracking-widest uppercase shadow-xl shadow-indigo-100 transition-all active:scale-95">
                Kirim Kode
            </button>
        </form>
    </div>

    <!-- Footer Links -->
    <div class="mt-10 text-center">
        <p class="text-sm text-gray-500 font-medium">
            Ingat kata sandi? <a href="{{ route('login') }}" class="text-indigo-700 font-bold hover:underline">Masuk di sini</a>
        </p>
    </div>
</x-guest-layout>