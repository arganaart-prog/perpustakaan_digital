<x-member-layout>
    <div x-data="chatRoom({{ $conversation->id }}, {{ $canSend ? 'true' : 'false' }})" x-init="init()" class="pb-36 min-h-screen bg-slate-50/60 p-4 sm:p-6 flex flex-col">
        <div class="max-w-4xl mx-auto w-full flex-1 flex flex-col space-y-4 animate-fade-in">
            <!-- Chat Header Navigation -->
            <div class="bg-white rounded-3xl p-4 border border-gray-100 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('member.chats.index') }}" class="p-2 bg-slate-50 hover:bg-slate-100 text-gray-600 rounded-xl transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </a>
                    
                    <a href="{{ route('profile.view', $otherUser) }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-emerald-100 bg-white">
                            <img src="{{ $otherUser->avatar_url }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h3 class="text-xs font-black text-gray-900 group-hover:text-emerald-600 transition-colors">{{ $otherUser->name }}</h3>
                            <p class="text-[9px] text-gray-400 font-bold uppercase">{{ $otherUser->kelas ?? '-' }} ({{ $otherUser->jurusan ?? 'Murid' }})</p>
                        </div>
                    </a>
                </div>

                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[9px] font-black uppercase tracking-wider border border-emerald-100">
                    Siswa Skarifta
                </span>
            </div>

            <!-- Chat Messages Container -->
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm flex-1 flex flex-col p-6 min-h-[480px] max-h-[620px]">
                <div id="messages-container" class="flex-1 overflow-y-auto space-y-3 pr-2 scroll-smooth">
                    <template x-for="msg in messages" :key="msg.id">
                        <div class="flex flex-col" :class="msg.is_mine ? 'items-end' : 'items-start'">
                            <div 
                                class="max-w-[78%] sm:max-w-[65%] p-4 rounded-3xl text-xs leading-relaxed font-medium shadow-2xs space-y-1"
                                :class="msg.is_mine 
                                    ? 'bg-emerald-600 text-white rounded-br-xs' 
                                    : 'bg-slate-100 text-gray-800 rounded-bl-xs'"
                            >
                                <p x-text="msg.message" class="whitespace-pre-wrap break-words"></p>
                                <div class="flex items-center justify-end gap-1.5 pt-0.5 text-[8px]" :class="msg.is_mine ? 'text-emerald-100' : 'text-gray-400'">
                                    <span x-text="msg.time"></span>
                                    <template x-if="msg.is_mine">
                                        <span x-text="msg.is_read ? '✓✓' : '✓'"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Anti-Spam Notice / Lock Box -->
                <div x-show="!canSend" class="mt-4 p-4 bg-amber-50 rounded-2xl border border-amber-200 text-xs text-amber-900 text-center space-y-1 animate-fade-in" style="display: none;">
                    <p class="font-black text-[11px] uppercase tracking-wider">🔒 Menunggu Balasan Siswa</p>
                    <p class="text-[11px] text-amber-800">Pesan perkenalan Anda telah terkirim. Untuk mencegah spam, Anda dapat mengirim pesan selanjutnya setelah <strong>{{ $otherUser->name }}</strong> membalas obrolan ini.</p>
                </div>

                <!-- Message Input Box -->
                <div x-show="canSend" class="mt-4 pt-3 border-t border-gray-100">
                    <form @submit.prevent="sendMessage()" class="flex gap-2 items-center">
                        <input 
                            type="text" 
                            x-model="newMessage" 
                            placeholder="Ketik pesan..." 
                            class="flex-1 px-4 py-3 bg-slate-50 rounded-2xl border-none text-xs font-medium text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-emerald-500"
                            autocomplete="off"
                        >
                        <button 
                            type="submit" 
                            :disabled="!newMessage.trim() || isSending"
                            class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white rounded-2xl text-xs font-black uppercase tracking-wider transition-all shadow-md shadow-emerald-200 active:scale-95 flex items-center justify-center gap-1.5 shrink-0"
                        >
                            <span>Kirim</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function chatRoom(conversationId, initialCanSend) {
            return {
                conversationId: conversationId,
                canSend: initialCanSend,
                newMessage: '',
                isSending: false,
                messages: [],
                pollInterval: null,

                init() {
                    this.fetchMessages();
                    this.pollInterval = setInterval(() => {
                        this.fetchMessages();
                    }, 3000);
                },

                async fetchMessages() {
                    try {
                        const res = await fetch(`{{ url('/member/chats') }}/${this.conversationId}/fetch`);
                        if (!res.ok) return;
                        const data = await res.json();
                        const wasAtBottom = this.isScrolledToBottom();
                        const isFirstLoad = this.messages.length === 0;
                        
                        this.messages = data.messages || [];
                        this.canSend = data.can_send;

                        if (isFirstLoad || wasAtBottom) {
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    } catch (e) {
                        console.error("Chat fetch error:", e);
                    }
                },

                async sendMessage() {
                    if (!this.newMessage.trim() || this.isSending) return;
                    this.isSending = true;

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const res = await fetch(`{{ url('/member/chats') }}/${this.conversationId}/send`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ message: this.newMessage.trim() })
                        });

                        if (res.ok) {
                            this.newMessage = '';
                            await this.fetchMessages();
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    } catch (e) {
                        console.error("Send error:", e);
                    } finally {
                        this.isSending = false;
                    }
                },

                scrollToBottom() {
                    const el = document.getElementById('messages-container');
                    if (el) el.scrollTop = el.scrollHeight;
                },

                isScrolledToBottom() {
                    const el = document.getElementById('messages-container');
                    if (!el) return true;
                    return el.scrollHeight - el.scrollTop - el.clientHeight < 80;
                }
            };
        }
    </script>
</x-member-layout>
