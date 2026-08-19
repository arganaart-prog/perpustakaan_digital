<x-guest-layout>
    <!-- Logo & Header Section -->
    <div class="flex flex-col items-center mb-10">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-indigo-700 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-indigo-900">Skarifta Perpus</h1>
        </div>
        
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight text-center">Pendaftaran Anggota</h2>
        <p class="text-gray-500 text-sm mt-3 text-center max-w-[280px] leading-relaxed">
            Bergabunglah dan jelajahi ribuan koleksi buku digital kami.
        </p>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-[3rem] p-10 shadow-2xl shadow-gray-200/50 border border-white">
        
        @if(isset($google_email))
            <form method="POST" action="{{ url('auth/google/register') }}" id="register-google-form" autocomplete="off" class="space-y-6">
                @csrf
                <input type="hidden" name="email" value="{{ $google_email }}">
                <input type="hidden" name="google_id" value="{{ $google_id }}">

                <div>
                    <label for="name" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Nama Lengkap</label>
                    <div class="relative group">
                        <input id="name" type="text" name="name" :value="old('name')" required autofocus placeholder="Nama sesuai identitas" class="w-full bg-gray-100/80 border-none rounded-2xl py-4 pl-5 pr-12 text-sm focus:ring-2 focus:ring-indigo-600 transition-all">
                        <div class="absolute inset-y-0 right-0 pr-5 flex items-center text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
                    </div>
                </div>

                <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-2xl flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-600 mt-0.5 shrink-0"><path d="M22 17a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9.5C2 7 4 5 6.5 5H18c2.2 0 4 1.8 4 4v8Z"/><polyline points="15,9 18,9 18,11"/></svg>
                    <p class="text-[11px] text-indigo-700 font-medium">Link akun Google:<br><span class="font-bold">{{ $google_email }}</span></p>
                </div>

                <button type="submit" class="w-full bg-indigo-700 hover:bg-indigo-800 text-white py-4 rounded-2xl font-bold text-sm tracking-widest uppercase shadow-xl shadow-indigo-100 transition-all active:scale-95">
                    Lanjutkan
                </button>
            </form>
        @else
            <form method="POST" action="{{ route('register') }}" id="register-form" autocomplete="off" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Nama Lengkap</label>
                    <div class="relative group">
                        <input id="name" type="text" name="name" :value="old('name')" required autofocus placeholder="Nama lengkap" class="w-full bg-gray-100/80 border-none rounded-2xl py-4 pl-5 pr-12 text-sm focus:ring-2 focus:ring-indigo-600 transition-all">
                        <div class="absolute inset-y-0 right-0 pr-5 flex items-center text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Email</label>
                    <div class="relative group">
                        <input id="email" type="email" name="email" :value="old('email')" required placeholder="nama@email.com" class="w-full bg-gray-100/80 border-none rounded-2xl py-4 pl-5 pr-12 text-sm focus:ring-2 focus:ring-indigo-600 transition-all">
                        <div class="absolute inset-y-0 right-0 pr-5 flex items-center text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-at-sign"><circle cx="12" cy="12" r="4"/><path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-4 8"/></svg></div>
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Sandi</label>
                        <input id="password" type="password" name="password" required placeholder="Min 8 kar." class="w-full bg-gray-100/80 border-none rounded-2xl py-4 px-5 text-sm focus:ring-2 focus:ring-indigo-600 transition-all">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Konfirmasi</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Ulangi" class="w-full bg-gray-100/80 border-none rounded-2xl py-4 px-5 text-sm focus:ring-2 focus:ring-indigo-600 transition-all">
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />

                <button type="submit" class="w-full bg-indigo-700 hover:bg-indigo-800 text-white py-4 rounded-2xl font-bold text-sm tracking-widest uppercase shadow-xl shadow-indigo-100 transition-all active:scale-95">
                    Daftar
                </button>
            </form>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
                <div class="relative flex justify-center text-[10px] uppercase font-bold tracking-[0.2em]"><span class="bg-white px-4 text-gray-300">Atau</span></div>
            </div>

            <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center gap-3 py-4 border border-gray-100 rounded-2xl hover:bg-gray-50 transition-all active:scale-[0.98]">
                <img class="h-5 w-5" src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google logo">
                <span class="text-xs font-bold text-gray-700">Daftar dengan Google</span>
            </a>
        @endif
    </div>

    <!-- Footer Links -->
    <div class="mt-10 text-center">
        <p class="text-sm text-gray-500 font-medium">
            Sudah terdaftar? <a href="{{ route('login') }}" class="text-indigo-700 font-bold hover:underline">Masuk sekarang</a>
        </p>
    </div>
</x-guest-layout>