<x-member-layout>
    <div x-data="memberProfileApp(@js($user), @js($history))" class="pb-36 min-h-screen bg-slate-50/60">
        <!-- Profile Header -->
        <header class="bg-white border-b border-gray-100 px-6 py-6 flex flex-col items-center text-center shadow-xs" style="background-color: #ffffff !important; backdrop-filter: none !important; -webkit-backdrop-filter: none !important; opacity: 1 !important;">
            <div class="relative mb-4">
                <div class="w-24 h-24 rounded-full border-4 border-emerald-50 overflow-hidden shadow-md bg-white">
                    <img src="{{ $user->avatar_url }}" alt="User Avatar" class="w-full h-full object-cover">
                </div>
                <div class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center border-2 border-white shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
            </div>
            <h1 class="text-xl font-black text-gray-900 leading-tight tracking-tight">{{ $user->name }}</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1.5">{{ $user->member_type === 'teacher' ? 'Guru / Pengajar' : 'Siswa / Murid' }}</p>
            <a href="{{ route('profile.view', $user) }}" class="mt-3 px-4 py-1.5 bg-slate-100 hover:bg-slate-200 text-gray-600 rounded-full text-[10px] font-black uppercase tracking-wider transition-all inline-flex items-center gap-1 shadow-2xs">
                Lihat Profil Publik Saya ↗
            </a>
        </header>

        <!-- Navigation Tabs -->
        <div class="max-w-4xl mx-auto px-4 mt-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
                <div class="flex border-b border-gray-100 bg-slate-50/40">
                    <button 
                        @click="activeTab = 'info'" 
                        :class="activeTab === 'info' ? 'bg-white text-emerald-600 font-black border-b-2 border-emerald-600 shadow-xs' : 'text-gray-400 font-bold hover:text-gray-600'" 
                        class="flex-1 py-4 text-xs uppercase tracking-wider transition-all"
                    >
                        Profil Saya
                    </button>
                    <button 
                        @click="activeTab = 'history'" 
                        :class="activeTab === 'history' ? 'bg-white text-emerald-600 font-black border-b-2 border-emerald-600 shadow-xs' : 'text-gray-400 font-bold hover:text-gray-600'" 
                        class="flex-1 py-4 text-xs uppercase tracking-wider transition-all"
                    >
                        Riwayat Pinjam
                    </button>
                </div>

                <!-- Tab Panels -->
                <div class="p-6">
                    <!-- Tab 1: Profile Info -->
                    <div x-show="activeTab === 'info'" class="space-y-6 animate-fade-in">
                        <!-- Profile Card Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 bg-slate-50 rounded-2xl">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Nama Lengkap</span>
                                <span class="text-sm font-black text-gray-900 block mt-1" x-text="user.name"></span>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-2xl">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Kelas / Tingkat</span>
                                <span class="text-sm font-black text-gray-900 block mt-1" x-text="user.member_type === 'teacher' ? 'Staf Guru' : (user.kelas + ' (' + user.jurusan + ')')"></span>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-2xl">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Alamat Email</span>
                                <span class="text-sm font-black text-gray-900 block mt-1" x-text="user.email"></span>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-2xl flex justify-between items-center">
                                <div>
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Kata Sandi</span>
                                    <span class="text-sm font-black text-gray-900 block mt-1">••••••••</span>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Profile Details Form -->
                        <div class="border-t border-gray-100 pt-6">
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-4">Lengkapi Profil & Media Sosial</h3>
                            
                            @if(session('status') === 'profile-details-updated')
                                <div class="mb-4 p-4 bg-emerald-50 border border-emerald-100 text-emerald-900 rounded-2xl text-xs font-bold animate-fade-in">
                                    ✓ Profil Anda berhasil diperbarui!
                                </div>
                            @endif

                            <form action="{{ route('profile.update-details') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                                @csrf

                                <!-- Avatar Upload Block -->
                                <div class="p-5 bg-slate-50 rounded-3xl border border-slate-100 space-y-4">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Foto Profil</span>
                                    
                                    <div class="flex flex-col sm:flex-row items-center gap-5">
                                        <!-- Current Avatar -->
                                        <div class="relative">
                                            <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-emerald-500 shadow-md shrink-0 bg-white">
                                                <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover">
                                            </div>
                                            <span class="absolute bottom-0 right-0 px-2 py-0.5 bg-emerald-600 text-white text-[8px] font-black rounded-full uppercase tracking-wider border border-white">Aktif</span>
                                        </div>

                                        <!-- Pending Avatar -->
                                        @if($user->avatar_pending)
                                            <div class="flex items-center gap-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                                <div class="relative">
                                                    <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-amber-500 shadow-md shrink-0 bg-white opacity-70">
                                                        <img src="{{ $user->pending_avatar_url }}" class="w-full h-full object-cover">
                                                    </div>
                                                    <span class="absolute bottom-0 right-0 px-2 py-0.5 bg-amber-500 text-white text-[8px] font-black rounded-full uppercase tracking-wider border border-white">Pending</span>
                                                </div>
                                                <p class="text-[10px] text-amber-600 font-bold leading-normal max-w-[150px]">Foto baru sedang menunggu persetujuan pustakawan.</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Warning Alert -->
                                    <div class="p-4 bg-red-50 border border-red-100 text-red-900 rounded-2xl text-[11px] leading-relaxed space-y-1">
                                        <p class="font-black text-red-700">⚠️ PERINGATAN KETENTUAN FOTO:</p>
                                        <p>1. Foto profil **wajib** memperlihatkan wajah Anda secara jelas.</p>
                                        <p>2. **Dilarang keras** mengunggah foto yang mengandung unsur SARA, pornografi, kekerasan, politik, atau gambar tidak pantas lainnya.</p>
                                        <p>3. Setiap perubahan foto profil akan **ditinjau terlebih dahulu** oleh Pustakawan sebelum dipublikasikan ke publik.</p>
                                    </div>

                                    <div>
                                        <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Unggah Foto Baru</label>
                                        <input type="file" name="avatar" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-wider file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-all cursor-pointer">
                                    </div>
                                </div>

                                <!-- Bio Block -->
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Bio (Quotes / Motivasi / Motto Hidup)</label>
                                    <textarea name="bio" rows="3" placeholder="Tuliskan motivasi, quotes, atau motto hidup Anda di sini..." class="w-full bg-slate-50 border border-gray-200 rounded-2xl text-xs p-4 focus:ring-2 focus:ring-emerald-500/20 focus:bg-white transition-all outline-none resize-none">{{ old('bio', $user->bio) }}</textarea>
                                    <span class="text-[9px] text-gray-400 font-medium block mt-1">Catatan: Bio akan ditampilkan secara publik agar dapat dibaca oleh pengguna lain.</span>
                                </div>

                                <!-- Dynamic Social Media Slots (Max 3) -->
                                <div x-data="socialLinksManager(@js($user->social_links ?? []))" class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Tautan Media Sosial (Maks. 3 Akun)</label>
                                        <template x-if="slots.length < 3">
                                            <button type="button" @click="addSlot()" class="text-xs font-black text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1 transition-colors">
                                                + Tambah Akun
                                            </button>
                                        </template>
                                    </div>

                                    <template x-if="slots.length === 0">
                                        <div class="p-4 bg-slate-50 border border-dashed border-gray-200 rounded-2xl text-center">
                                            <p class="text-xs text-gray-400 font-semibold">Belum ada akun media sosial yang ditambahkan.</p>
                                            <button type="button" @click="addSlot()" class="mt-1 text-xs font-black text-emerald-600 hover:underline">Tambah Sekarang</button>
                                        </div>
                                    </template>

                                    <div class="space-y-3">
                                        <template x-for="(slot, index) in slots" :key="index">
                                            <div class="flex flex-col sm:flex-row items-center gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-100 animate-fade-in">
                                                <!-- Platform Selector -->
                                                <div class="w-full sm:w-44 shrink-0">
                                                    <select :name="`social_links[${index}][platform]`" x-model="slot.platform" class="w-full bg-white border border-gray-200 rounded-xl text-xs p-3 font-black text-gray-700 outline-none focus:ring-2 focus:ring-emerald-500/20 shadow-2xs" required>
                                                        <option value="" disabled>Pilih Platform</option>
                                                        <template x-for="p in availablePlatforms" :key="p.key">
                                                            <option :value="p.key" :disabled="isPlatformSelected(p.key, index)" x-text="p.label"></option>
                                                        </template>
                                                    </select>
                                                </div>

                                                <!-- Username or URL Input -->
                                                <div class="flex-1 w-full">
                                                    <input type="text" :name="`social_links[${index}][value]`" x-model="slot.value" placeholder="Username / link akun sosmed" class="w-full bg-white border border-gray-200 rounded-xl text-xs p-3 font-semibold text-gray-800 outline-none focus:ring-2 focus:ring-emerald-500/20 shadow-2xs" required>
                                                </div>

                                                <!-- Remove Slot Button -->
                                                <button type="button" @click="removeSlot(index)" class="p-2.5 text-red-500 hover:bg-red-50 rounded-xl transition-all shrink-0 active:scale-90" title="Hapus Akun Ini">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <button type="submit" class="px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md transition-all active:scale-95">
                                        Simpan Perubahan Profil
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Change Password Form Section -->
                        <div class="border-t border-gray-100 pt-6">
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-4">Pengaturan Keamanan (Ubah Sandi)</h3>
                            
                            <!-- Step 1: Send OTP -->
                            <div x-show="otpStep === 1" class="space-y-4">
                                <p class="text-xs text-gray-500 leading-relaxed">Untuk mengubah kata sandi, sistem akan mengirimkan kode verifikasi OTP ke alamat email terdaftar Anda terlebih dahulu.</p>
                                <button 
                                    @click="sendOtp()" 
                                    :disabled="isOtpLoading"
                                    class="px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md transition-all active:scale-95 disabled:opacity-50 flex items-center gap-2"
                                >
                                    <template x-if="isOtpLoading">
                                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </template>
                                    <span>Kirim Kode OTP Ke Email</span>
                                </button>
                            </div>

                            <!-- Step 2: Verify OTP & Input New Password -->
                            <div x-show="otpStep === 2" class="space-y-5 max-w-md animate-fade-in" style="display: none;">
                                <div class="p-4 bg-amber-50 border border-amber-100 text-amber-900 rounded-2xl text-xs flex justify-between items-center">
                                    <span>Kode OTP terkirim! Sisa waktu verifikasi: <strong x-text="formatTime(countdown)"></strong></span>
                                    <button @click="sendOtp()" :disabled="countdown > 0 || isOtpLoading" class="font-black underline text-amber-700 disabled:opacity-50">Resend</button>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Kode OTP</label>
                                        <input type="text" x-model="otpCode" placeholder="Masukkan 6 Digit OTP" class="w-full bg-slate-50 border border-gray-200 rounded-xl text-xs p-3.5 focus:ring-2 focus:ring-emerald-500/20 focus:bg-white transition-all outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Kata Sandi Baru</label>
                                        <input type="password" x-model="newPassword" placeholder="Min. 8 karakter" class="w-full bg-slate-50 border border-gray-200 rounded-xl text-xs p-3.5 focus:ring-2 focus:ring-emerald-500/20 focus:bg-white transition-all outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Konfirmasi Kata Sandi Baru</label>
                                        <input type="password" x-model="newPasswordConfirmation" placeholder="Masukkan ulang sandi baru" class="w-full bg-slate-50 border border-gray-200 rounded-xl text-xs p-3.5 focus:ring-2 focus:ring-emerald-500/20 focus:bg-white transition-all outline-none">
                                    </div>
                                    
                                    <div class="flex gap-3 pt-2">
                                        <button 
                                            @click="otpStep = 1" 
                                            class="px-5 py-3.5 bg-slate-100 hover:bg-slate-200 text-gray-600 rounded-xl text-xs font-black uppercase tracking-widest transition-all active:scale-95"
                                        >
                                            Batal
                                        </button>
                                        <button 
                                            @click="changePassword()" 
                                            :disabled="isOtpLoading || !otpCode || !newPassword || !newPasswordConfirmation"
                                            class="flex-1 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md transition-all active:scale-95 disabled:opacity-50"
                                        >
                                            Simpan Sandi Baru
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Borrow History -->
                    <div x-show="activeTab === 'history'" class="space-y-4 animate-fade-in" style="display: none;">
                        <template x-if="history.length === 0">
                            <div class="py-12 text-center bg-slate-50/50 rounded-2xl border border-dashed border-gray-200">
                                <p class="text-gray-400 text-xs font-bold">Belum ada riwayat peminjaman buku.</p>
                            </div>
                        </template>

                        <div class="divide-y divide-slate-100">
                            <template x-for="item in history" :key="item.id">
                                <div class="py-4 flex gap-4 items-center">
                                    <div class="w-12 h-16 rounded-lg overflow-hidden bg-slate-100 border border-slate-100 shrink-0">
                                        <img :src="item.book.cover_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(item.book.title)}&background=f0fdf4&color=15803d&size=256`" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-xs font-black text-gray-900 truncate" x-text="item.book.title"></h4>
                                        <p class="text-[10px] text-gray-400 font-bold truncate mt-0.5" x-text="item.book.author"></p>
                                        <p class="text-[9px] text-gray-400 mt-1">Pinjam: <span class="font-bold text-gray-700" x-text="formatDate(item.borrow_date)"></span></p>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <span :class="getHistoryStatusClass(item.status)" class="px-2.5 py-1 rounded-full text-[8px] font-black uppercase tracking-wider" x-text="getHistoryStatusText(item.status)"></span>
                                        <template x-if="item.return_date">
                                            <p class="text-[8px] text-gray-400 mt-1.5">Kembali: <span class="font-bold text-gray-500" x-text="formatDate(item.return_date)"></span></p>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function memberProfileApp(initialUser, initialHistory) {
            return {
                user: initialUser || {},
                history: initialHistory || [],
                activeTab: 'info',

                // OTP & Password State
                otpStep: 1, // 1: Send OTP request, 2: Input OTP & Password change
                isOtpLoading: false,
                otpSent: false,
                countdown: 0,
                countdownTimer: null,

                // Form Data
                otpCode: '',
                newPassword: '',
                newPasswordConfirmation: '',

                startCountdown() {
                    this.countdown = 300; // 5 minutes
                    if (this.countdownTimer) clearInterval(this.countdownTimer);
                    this.countdownTimer = setInterval(() => {
                        if (this.countdown > 0) {
                            this.countdown--;
                        } else {
                            clearInterval(this.countdownTimer);
                        }
                    }, 1000);
                },

                formatTime(sec) {
                    const m = Math.floor(sec / 60);
                    const s = sec % 60;
                    return `${m}:${s.toString().padStart(2, '0')}`;
                },

                formatDate(dateStr) {
                    if (!dateStr) return '-';
                    try {
                        const date = new Date(dateStr);
                        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                    } catch(e) {
                        return dateStr;
                    }
                },

                getHistoryStatusClass(status) {
                    const map = {
                        'active': 'bg-amber-100 text-amber-800 border border-amber-200',
                        'returned': 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                        'late': 'bg-red-100 text-red-800 border border-red-200',
                        'lost': 'bg-slate-100 text-slate-800 border border-slate-200'
                    };
                    return map[status] || 'bg-gray-100 text-gray-700';
                },

                getHistoryStatusText(status) {
                    const map = {
                        'active': 'DIPINJAM',
                        'returned': 'DIKEMBALIKAN',
                        'late': 'TERLAMBAT',
                        'lost': 'HILANG'
                    };
                    return map[status] || (status || 'UNKNOWN').toUpperCase();
                },

                async sendOtp() {
                    this.isOtpLoading = true;
                    try {
                        const response = await fetch("{{ route('profile.send-otp') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (response.ok) {
                            this.otpSent = true;
                            this.otpStep = 2;
                            this.startCountdown();
                            alert(data.message);
                        } else {
                            alert(data.message || "Gagal mengirim OTP.");
                        }
                    } catch(e) {
                        console.error("OTP send error:", e);
                        alert("Terjadi kesalahan jaringan.");
                    } finally {
                        this.isOtpLoading = false;
                    }
                },

                async changePassword() {
                    if (this.newPassword !== this.newPasswordConfirmation) {
                        return alert("Konfirmasi kata sandi tidak cocok!");
                    }

                    this.isOtpLoading = true;
                    try {
                        const response = await fetch("{{ route('profile.change-password') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                otp: this.otpCode,
                                password: this.newPassword,
                                password_confirmation: this.newPasswordConfirmation
                            })
                        });
                        const data = await response.json();
                        if (response.ok) {
                            alert(data.message);
                            // Reset form
                            this.otpCode = '';
                            this.newPassword = '';
                            this.newPasswordConfirmation = '';
                            this.otpStep = 1;
                            this.otpSent = false;
                        } else {
                            alert(data.message || "Gagal mengubah kata sandi.");
                        }
                    } catch(e) {
                        console.error("Change password error:", e);
                        alert("Terjadi kesalahan jaringan.");
                    } finally {
                        this.isOtpLoading = false;
                    }
                }
            }
        }

        function socialLinksManager(initialLinks) {
            return {
                availablePlatforms: [
                    { key: 'instagram', label: 'Instagram' },
                    { key: 'tiktok', label: 'TikTok' },
                    { key: 'facebook', label: 'Facebook' },
                    { key: 'threads', label: 'Threads' },
                    { key: 'linkedin', label: 'LinkedIn' },
                    { key: 'x', label: 'X (Twitter)' },
                ],
                slots: Array.isArray(initialLinks) && initialLinks.length > 0 ? initialLinks.slice(0, 3) : [],
                
                addSlot() {
                    if (this.slots.length < 3) {
                        const unused = this.availablePlatforms.find(p => !this.isPlatformSelected(p.key));
                        this.slots.push({
                            platform: unused ? unused.key : 'instagram',
                            value: ''
                        });
                    }
                },
                removeSlot(index) {
                    this.slots.splice(index, 1);
                },
                isPlatformSelected(key, currentSlotIndex = -1) {
                    return this.slots.some((slot, idx) => slot.platform === key && idx !== currentSlotIndex);
                }
            }
        }
    </script>

    <style>
        .animate-fade-in { animation: fadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</x-member-layout>
