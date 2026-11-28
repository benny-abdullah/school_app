<!-- Floating AI Copilot & Slide-Out Assistant Drawer -->
<div x-data="aiCopilot()" @open-ai-copilot.window="openDrawer()" x-cloak>
    <!-- 1. Floating Action Button (Pojok Kanan Bawah) -->
    <div class="fixed bottom-6 right-6 z-40">
        <button type="button" @click="openDrawer()" 
            class="group flex items-center gap-2.5 px-4 py-3 bg-gradient-to-tr from-indigo-600 via-purple-600 to-rose-500 hover:from-indigo-700 hover:to-rose-600 text-white rounded-full shadow-lg hover:shadow-purple-500/25 hover:scale-105 transition-all duration-200 cursor-pointer">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-300 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-400"></span>
            </span>
            <i class="bi bi-stars text-lg group-hover:rotate-12 transition-transform"></i>
            <span class="text-xs font-black tracking-wide pr-1">AI Copilot</span>
        </button>
    </div>

    <!-- 2. Backdrop Overlay -->
    <div x-show="isOpen" 
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="isOpen = false"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50">
    </div>

    <!-- 3. Slide-Out Drawer Panel (Kanan Layar) -->
    <div x-show="isOpen"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 w-full sm:w-[440px] bg-white shadow-2xl z-50 flex flex-col border-l border-slate-200">
        
        <!-- Header Drawer -->
        <div class="p-4 bg-gradient-to-r from-indigo-900 via-purple-900 to-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-400 to-rose-400 p-0.5 flex items-center justify-center text-slate-950 font-black text-lg shadow-sm">
                    <i class="bi bi-stars"></i>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h2 class="text-sm font-black tracking-tight text-white">STAR-KIDS AI Copilot</h2>
                        <span class="text-[9px] font-extrabold bg-purple-500/30 border border-purple-400/40 text-purple-200 px-1.5 py-0.2 rounded font-mono">
                            Gemini 1.5
                        </span>
                    </div>
                    <p class="text-[10px] text-slate-300">Asisten Pedagogik & Analisis Presensi SDN Kebonagung</p>
                </div>
            </div>

            <button type="button" @click="isOpen = false" class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-white/10 transition">
                <i class="bi bi-x-lg text-base"></i>
            </button>
        </div>

        <!-- Quick Action Prompt Pills -->
        <div class="p-3 bg-slate-50 border-b border-slate-200 overflow-x-auto flex items-center gap-1.5 text-[11px] whitespace-nowrap scrollbar-none">
            <button type="button" @click="sendQuickPrompt('Ringkas tingkat kehadiran siswa hari ini', 'summary')" 
                class="px-2.5 py-1 rounded-full bg-white border border-slate-200 hover:border-indigo-400 hover:text-indigo-600 text-slate-700 font-bold transition shadow-2xs cursor-pointer flex items-center gap-1">
                <span>📊 Ringkas Hari Ini</span>
            </button>
            <button type="button" @click="sendQuickPrompt('Deteksi siswa yang berisiko terlambat berulang', 'risk')" 
                class="px-2.5 py-1 rounded-full bg-white border border-slate-200 hover:border-amber-400 hover:text-amber-600 text-slate-700 font-bold transition shadow-2xs cursor-pointer flex items-center gap-1">
                <span>⚠️ Siswa Terlambat</span>
            </button>
            <button type="button" @click="sendQuickPrompt('Buatkan draf surat panggilan SP untuk orang tua', 'draft_sp')" 
                class="px-2.5 py-1 rounded-full bg-white border border-slate-200 hover:border-rose-400 hover:text-rose-600 text-slate-700 font-bold transition shadow-2xs cursor-pointer flex items-center gap-1">
                <span>📝 Draf SP</span>
            </button>
            <button type="button" @click="sendQuickPrompt('Buatkan draf pesan santun WhatsApp ke wali murid', 'draft_wa')" 
                class="px-2.5 py-1 rounded-full bg-white border border-slate-200 hover:border-emerald-400 hover:text-emerald-600 text-slate-700 font-bold transition shadow-2xs cursor-pointer flex items-center gap-1">
                <span>💬 Pesan WA</span>
            </button>
            <button type="button" @click="sendQuickPrompt('Saran strategi disiplin positif ramah anak', 'pedagogic')" 
                class="px-2.5 py-1 rounded-full bg-white border border-slate-200 hover:border-purple-400 hover:text-purple-600 text-slate-700 font-bold transition shadow-2xs cursor-pointer flex items-center gap-1">
                <span>💡 Disiplin Positif</span>
            </button>
        </div>

        <!-- Chat Conversation Area -->
        <div class="flex-1 p-4 overflow-y-auto space-y-3.5" x-ref="chatBox">
            <!-- Pesan Pembuka AI -->
            <div class="flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs shrink-0 mt-0.5">
                    <i class="bi bi-stars"></i>
                </div>
                <div class="bg-indigo-50/70 border border-indigo-100 rounded-2xl rounded-tl-none p-3 text-xs text-slate-800 leading-relaxed max-w-[85%]">
                    Halo Bapak/Ibu! Saya asisten AI STAR-KIDS. Saya siap membantu menganalisis kehadiran harian, menyusun draf pesan ke wali murid, atau memberikan saran pendekatan disiplin positif ramah anak di SDN Kebonagung. Silakan pilih tombol cepat di atas atau ajukan pertanyaan Anda!
                </div>
            </div>

            <!-- Loop Pesan Obrolan -->
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex items-start gap-2.5'">
                    <template x-if="msg.role === 'ai'">
                        <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs shrink-0 mt-0.5">
                            <i class="bi bi-stars"></i>
                        </div>
                    </template>
                    <div :class="msg.role === 'user' 
                        ? 'bg-rose-600 text-white rounded-2xl rounded-tr-none p-3 text-xs leading-relaxed max-w-[85%] shadow-xs' 
                        : 'bg-white border border-slate-200 rounded-2xl rounded-tl-none p-3 text-xs text-slate-800 leading-relaxed max-w-[85%] shadow-xs space-y-1.5'">
                        <div x-html="renderMarkdown(msg.text)"></div>
                        <div class="text-[9px] font-mono opacity-60 text-right mt-1" x-text="msg.time"></div>
                    </div>
                </div>
            </template>

            <!-- Loading Indicator -->
            <div x-show="isLoading" class="flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs shrink-0 mt-0.5 animate-pulse">
                    <i class="bi bi-stars"></i>
                </div>
                <div class="bg-slate-100 border border-slate-200 rounded-2xl rounded-tl-none p-3 text-xs text-slate-500 flex items-center gap-2">
                    <span class="inline-flex gap-1">
                        <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"></span>
                        <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.15s"></span>
                        <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
                    </span>
                    <span>AI sedang menganalisis data kesiswaan...</span>
                </div>
            </div>
        </div>

        <!-- Input Box Area (Bawah) -->
        <div class="p-3 bg-white border-t border-slate-200">
            <form @submit.prevent="submitUserMessage()" class="flex items-center gap-2">
                <input type="text" x-model="userInput" placeholder="Tanya AI tentang kehadiran atau ketik permintaan..." 
                    class="flex-1 px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 transition"
                    :disabled="isLoading">
                <button type="submit" :disabled="isLoading || !userInput.trim()"
                    class="p-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-xl transition cursor-pointer flex items-center justify-center shrink-0">
                    <i class="bi bi-send-fill text-xs"></i>
                </button>
            </form>
            <div class="text-[10px] text-slate-400 text-center mt-1.5">
                Mendukung Google Gemini 1.5 Flash & STAR-KIDS Pedagogical Engine
            </div>
        </div>
    </div>
</div>

<script>
function aiCopilot() {
    return {
        isOpen: false,
        isLoading: false,
        userInput: '',
        messages: [],

        openDrawer() {
            this.isOpen = true;
            this.scrollToBottom();
        },

        sendQuickPrompt(promptText, type) {
            this.messages.push({
                role: 'user',
                text: promptText,
                time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'
            });

            this.executeAiCall(promptText, type);
        },

        submitUserMessage() {
            const text = this.userInput.trim();
            if (!text || this.isLoading) return;

            this.messages.push({
                role: 'user',
                text: text,
                time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'
            });

            this.userInput = '';
            this.executeAiCall(text, 'custom');
        },

        executeAiCall(prompt, type) {
            this.isLoading = true;
            this.scrollToBottom();

            fetch('{{ route("ai.copilot.chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    prompt: prompt,
                    type: type
                })
            })
            .then(res => res.json())
            .then(data => {
                this.isLoading = false;
                this.messages.push({
                    role: 'ai',
                    text: data.reply || 'Maaf, terjadi kendala saat memproses jawaban.',
                    time: data.time || new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'
                });
                this.scrollToBottom();
            })
            .catch(err => {
                this.isLoading = false;
                this.messages.push({
                    role: 'ai',
                    text: 'Terjadi gangguan jaringan atau timeout saat menghubungi asisten AI.',
                    time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'
                });
                this.scrollToBottom();
            });
        },

        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.chatBox) {
                    this.$refs.chatBox.scrollTop = this.$refs.chatBox.scrollHeight;
                }
            });
        },

        renderMarkdown(text) {
            if (!text) return '';
            // Konversi markdown sederhana: **bold**, *italic*, \n break, bullet list
            let html = text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/\n/g, '<br>');
            return html;
        }
    }
}
</script>
