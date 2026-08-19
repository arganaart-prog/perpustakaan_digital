<x-guest-layout>
    <!-- Logo & Header Section -->
    <div class="flex flex-col items-center mb-10">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-indigo-700 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-indigo-900">Verifikasi OTP</h1>
        </div>
        
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight text-center">Masukkan Kode</h2>
        <p class="text-gray-500 text-sm mt-3 text-center max-w-[280px] leading-relaxed">
            Kode 6 digit telah dikirim ke email Anda. Segera masukkan untuk melanjutkan.
        </p>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-[3rem] p-10 shadow-2xl shadow-gray-200/50 border border-white">
        <!-- Status Messages -->
        @if (session('status'))
            <div class="mb-4 bg-green-50 text-green-700 p-4 rounded-2xl text-xs font-bold border border-green-100 italic">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.otp.verify') }}" class="space-y-8">
            @csrf

            <!-- OTP Input -->
            <div class="space-y-4">
                <label class="block text-[11px] font-bold text-center text-gray-400 uppercase tracking-[0.3em]">Kode Keamanan</label>
                <input 
                    type="text" 
                    name="otp" 
                    maxlength="6" 
                    required 
                    autofocus
                    placeholder="000000"
                    class="w-full bg-gray-100/80 border-none rounded-2xl py-6 text-center text-3xl font-black tracking-[0.5em] text-indigo-900 focus:ring-2 focus:ring-indigo-600 transition-all placeholder:text-gray-200"
                >
                <x-input-error :messages="$errors->get('otp')" class="mt-2 text-center" />
            </div>

            <div class="text-center">
                @if ($expired_at)
                    <p class="text-xs text-gray-500 font-medium">Berlaku hingga: <span id="timer" class="text-indigo-600 font-black">--:--</span></p>
                @else
                    <p class="text-xs text-amber-600 font-bold uppercase tracking-wider italic">Gagal mendapatkan waktu kadaluarsa</p>
                @endif
            </div>

            <button type="submit" class="w-full bg-indigo-700 hover:bg-indigo-800 text-white py-4 rounded-2xl font-bold text-sm tracking-widest uppercase shadow-xl shadow-indigo-100 transition-all active:scale-95">
                Verifikasi
            </button>
        </form>

        <div class="relative my-8">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
            <div class="relative flex justify-center text-[10px] uppercase font-bold tracking-[0.2em]"><span class="bg-white px-4 text-gray-300">Resend</span></div>
        </div>

        <!-- Resend OTP Section -->
        @php
            $canResend = !$next_allowed_at || now()->gte($next_allowed_at);
        @endphp

        <form method="POST" action="{{ route('password.otp.resend') }}" class="text-center">
            @csrf
            @if ($canResend)
                <button type="submit" class="text-[13px] font-bold text-indigo-700 hover:text-indigo-800 hover:underline">
                    Kirim ulang kode baru
                </button>
            @else
                <p id="resend-timer-text" class="text-xs text-gray-400 font-medium italic">
                    Kirim ulang tersedia kembali dalam <span class="font-bold text-gray-600">...</span>
                </p>
                <button type="submit" disabled class="hidden"></button>
            @endif
        </form>
    </div>

    <!-- Scripts for Timer -->
    @if ($expired_at)
    <script>
        (function() {
            let secondsLeft = {{ max(0, \Carbon\Carbon::parse($expired_at)->getTimestamp() - now()->getTimestamp()) }};
            const timerEl = document.getElementById("timer");
            
            let x = setInterval(function () {
                secondsLeft--;
                if (secondsLeft <= 0) {
                    clearInterval(x);
                    if(timerEl) timerEl.innerHTML = "EXPIRED";
                    return;
                }
                let min = Math.floor(secondsLeft / 60);
                let sec = secondsLeft % 60;
                if(timerEl) timerEl.innerHTML = String(min).padStart(2, '0') + ":" + String(sec).padStart(2, '0');
            }, 1000);
        })();
    </script>
    @endif

    <script>
        (function() {
            let resendSecs = {{ $next_allowed_at ? max(0, \Carbon\Carbon::parse($next_allowed_at)->getTimestamp() - now()->getTimestamp()) : 0 }};
            const resendTextEl = document.getElementById("resend-timer-text");
            
            if (resendSecs > 0 && resendTextEl) {
                let y = setInterval(function () {
                    resendSecs--;
                    if (resendSecs <= 0) {
                        clearInterval(y);
                        window.location.reload(); 
                    } else {
                        let m = Math.floor(resendSecs / 60);
                        let s = resendSecs % 60;
                        let text = (m > 0 ? m + "m " : "") + s + "s";
                        resendTextEl.querySelector('span').innerHTML = text;
                    }
                }, 1000);
            }
        })();
    </script>
</x-guest-layout>