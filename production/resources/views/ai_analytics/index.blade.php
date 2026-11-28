@extends('layouts.app')

@section('title', 'Pusat Analisis Data AI & Asisten Pedagogik - SDN Kebonagung')

@section('content')
<div class="space-y-6" x-data="aiAnalyticsApp()">
    <!-- Header Banner -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-violet-50 border border-violet-200 text-violet-700 text-xs font-bold mb-2">
                <i class="bi bi-stars"></i> Stasiun Intelijen Kesiswaan AI
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Analisis Data AI & Rekomendasi Pedagogik</h1>
            <p class="text-xs text-slate-500 mt-1">
                Pemantauan kedisiplinan 29 rombel, deteksi dini siswa berisiko, dan asisten konsultasi kesiswaan bertenaga Google Gemini & Rule Engine.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if(Auth::user()->isAdmin() || Auth::user()->isOperator())
                <button type="button" @click="showConfig = !showConfig" 
                    class="px-3.5 py-2 rounded-xl text-xs font-bold border transition flex items-center gap-1.5 cursor-pointer shadow-xs"
                    :class="showConfig ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 hover:bg-slate-50 border-slate-200'">
                    <i class="bi bi-gear-wide-connected text-rose-500"></i>
                    <span>Pengaturan API Gemini</span>
                    <span x-show="hasKey" class="w-2 h-2 rounded-full bg-emerald-500 inline-block ml-1"></span>
                </button>
            @endif

            <button type="button" @click="resetChat()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 cursor-pointer">
                <i class="bi bi-arrow-clockwise"></i> Reset Konsultasi
            </button>
        </div>
    </div>

    @if(Auth::user()->isAdmin() || Auth::user()->isOperator())
    <!-- Panel Konfigurasi API Key Google Gemini (Khusus Admin / Operator) -->
    <div x-show="showConfig" x-transition.duration.200ms class="card p-6 border-2 border-violet-200 bg-gradient-to-r from-violet-50/40 via-white to-rose-50/30">
        <div class="flex items-start justify-between gap-4 border-b border-slate-200/80 pb-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-violet-600 text-white flex items-center justify-center shadow-md">
                    <i class="bi bi-key-fill text-xl"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Konfigurasi Google Gemini Generative AI</h3>
                    <p class="text-xs text-slate-500">
                        Atur API Key resmi untuk mengaktifkan penalaran mendalam dan generator rekomendasi pedagogik.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold text-slate-500">Status API:</span>
                <template x-if="testSuccess === true">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span> Terhubung Aktif (<span x-text="testLatency + 'ms'"></span>)
                    </span>
                </template>
                <template x-if="testSuccess === false">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-300">
                        Gagal Terhubung
                    </span>
                </template>
                <template x-if="testSuccess === null">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-700 border border-slate-300">
                        {{ $aiConfig['has_key'] ? 'Tersimpan (Siaga)' : 'Belum Dikonfigurasi' }}
                    </span>
                </template>
            </div>
        </div>

        <form @submit.prevent="saveApiKey()">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <!-- Input API Key -->
                <div class="md:col-span-6 space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Google Gemini API Key:</label>
                    <div class="relative">
                        <input :type="showKeyText ? 'text' : 'password'" x-model="apiKeyInput" placeholder="Masukkan API Key (AIzaSy...)"
                            class="w-full pl-3.5 pr-10 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-mono font-medium text-slate-900 focus:outline-none focus:border-violet-600 focus:ring-4 focus:ring-violet-500/10 transition">
                        <button type="button" @click="showKeyText = !showKeyText" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600 text-sm">
                            <i :class="showKeyText ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill'"></i>
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-500">
                        Dapatkan API Key gratis di <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-violet-600 font-bold underline hover:text-violet-800">Google AI Studio</a>.
                    </p>
                </div>

                <!-- Pilihan Model AI -->
                <div class="md:col-span-3 space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Model AI Terpilih:</label>
                    <select x-model="modelInput" class="w-full px-3 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-violet-600">
                        <option value="gemini-1.5-flash">Gemini 1.5 Flash (Cepat & Hemat)</option>
                        <option value="gemini-1.5-pro">Gemini 1.5 Pro (Penalaran Kompleks)</option>
                    </select>
                </div>

                <!-- Tombol Action -->
                <div class="md:col-span-3 flex items-center gap-2">
                    <button type="button" @click="testConnection()" :disabled="testingApi"
                        class="w-1/2 py-2.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 font-bold text-xs rounded-xl transition flex items-center justify-center gap-1 cursor-pointer">
                        <span x-show="!testingApi"><i class="bi bi-activity"></i> Uji Coba</span>
                        <span x-show="testingApi"><i class="bi bi-arrow-repeat animate-spin"></i> Cek...</span>
                    </button>

                    <button type="submit" :disabled="savingKey"
                        class="w-1/2 py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-1 cursor-pointer">
                        <span x-show="!savingKey"><i class="bi bi-check-lg"></i> Simpan</span>
                        <span x-show="savingKey"><i class="bi bi-arrow-repeat animate-spin"></i>...</span>
                    </button>
                </div>
            </div>

            <!-- Pesan Uji Koneksi -->
            <div x-show="testMessage" class="mt-3 p-3 rounded-xl text-xs flex items-start gap-2"
                :class="testSuccess ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200'">
                <i :class="testSuccess ? 'bi bi-check-circle-fill text-emerald-600' : 'bi bi-exclamation-triangle-fill text-rose-600'" class="text-sm mt-0.5"></i>
                <div x-text="testMessage"></div>
            </div>
        </form>
    </div>
    @endif

    <!-- 4 KPI Cards Analisis AI Hari Ini -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-4.5 bg-white border border-slate-200">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Siswa Binaan</span>
            <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($context['total_students']) }}</div>
            <p class="text-[11px] text-slate-500 mt-0.5">29 Rombel Kelas Aktif</p>
        </div>

        <div class="card p-4.5 bg-white border border-slate-200">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Indeks Kehadiran Sekolah</span>
            <div class="mt-2 text-2xl font-extrabold text-emerald-600">{{ $context['attendance_rate'] }}%</div>
            <p class="text-[11px] font-bold text-emerald-700 mt-0.5">
                {{ $context['attendance_rate'] >= 95 ? 'Kategori: Sangat Baik' : 'Kategori: Perlu Pendampingan' }}
            </p>
        </div>

        <div class="card p-4.5 bg-white border border-slate-200">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Keterlambatan Gerbang</span>
            <div class="mt-2 text-2xl font-extrabold text-amber-500">{{ $context['late_count'] }} <span class="text-xs font-bold text-slate-400">Siswa</span></div>
            <p class="text-[11px] text-amber-700 font-semibold mt-0.5">T-1 / T-2 / T-3</p>
        </div>

        <div class="card p-4.5 bg-white border border-slate-200">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Early Warning System</span>
            <div class="mt-2 text-2xl font-extrabold text-rose-600">{{ count($riskStudents) }} <span class="text-xs font-bold text-slate-400">Siswa</span></div>
            <p class="text-[11px] text-rose-600 font-semibold mt-0.5">Perlu Dialog Pedagogik</p>
        </div>
    </div>

    <!-- Main Workspace Layout: Kiri Chat AI / Kanan Panel Matriks & Rekap Rombel -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- KIRI: STASIUN KONSULTASI AI INTERAKTIF (7 Kolom) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="card border border-slate-200 overflow-hidden shadow-xs">
                <!-- Header Chat Stasiun -->
                <div class="bg-gradient-to-r from-violet-600 to-indigo-600 p-4 text-white flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur-xs flex items-center justify-center font-bold text-base">
                            <i class="bi bi-robot"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-extrabold tracking-wide uppercase">Asisten Pedagogik & Analisis Data</h3>
                            <p class="text-[10px] text-violet-100">
                                Model Aktif: <span class="font-bold underline" x-text="currentAiModel"></span>
                            </p>
                        </div>
                    </div>

                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-400 text-emerald-950 flex items-center gap-1 shadow-xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-900 animate-ping"></span> Live Konsultan
                    </span>
                </div>

                <!-- 5 Tombol Pintasan Cepat Analisis -->
                <div class="p-3 bg-slate-50 border-b border-slate-200 flex flex-wrap gap-1.5">
                    <button type="button" @click="sendQuickPrompt('summary', 'Ringkas analisis kehadiran dan kedisiplinan gerbang hari ini')"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white hover:bg-violet-50 text-slate-700 hover:text-violet-700 border border-slate-200 transition flex items-center gap-1 cursor-pointer">
                        <i class="bi bi-file-earmark-text text-violet-600"></i> Ringkas Presensi
                    </button>

                    <button type="button" @click="sendQuickPrompt('risk', 'Deteksi siswa berisiko keterlambatan atau absensi kritis hari ini')"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white hover:bg-amber-50 text-slate-700 hover:text-amber-700 border border-slate-200 transition flex items-center gap-1 cursor-pointer">
                        <i class="bi bi-exclamation-triangle-fill text-amber-500"></i> Deteksi Siswa Berisiko
                    </button>

                    <button type="button" @click="sendQuickPrompt('draft_sp', 'Draf Surat Pemberitahuan Keterlambatan resmi untuk orang tua')"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white hover:bg-rose-50 text-slate-700 hover:text-rose-700 border border-slate-200 transition flex items-center gap-1 cursor-pointer">
                        <i class="bi bi-envelope-paper-fill text-rose-600"></i> Draf SP Kedisiplinan
                    </button>

                    <button type="button" @click="sendQuickPrompt('draft_wa', 'Draf pesan WhatsApp santun dari wali kelas ke wali murid')"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border border-slate-200 transition flex items-center gap-1 cursor-pointer">
                        <i class="bi bi-whatsapp text-emerald-600"></i> Draf Pesan WA Wali Murid
                    </button>

                    <button type="button" @click="sendQuickPrompt('pedagogic', 'Rekomendasi pendekatan disiplin positif ramah anak untuk sekolah')"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white hover:bg-blue-50 text-slate-700 hover:text-blue-700 border border-slate-200 transition flex items-center gap-1 cursor-pointer">
                        <i class="bi bi-lightbulb-fill text-blue-600"></i> Tips Disiplin Positif
                    </button>
                </div>

                <!-- Riwayat Percakapan Chat Stream -->
                <div class="p-4 space-y-4 max-h-[460px] overflow-y-auto bg-white" x-ref="chatBox">
                    <template x-for="(msg, idx) in chatMessages" :key="idx">
                        <div>
                            <!-- User Message -->
                            <template x-if="msg.sender === 'user'">
                                <div class="flex items-start justify-end gap-2.5">
                                    <div class="max-w-[85%] bg-violet-600 text-white rounded-2xl rounded-tr-xs px-4 py-2.5 shadow-xs text-xs">
                                        <p x-text="msg.text" class="leading-relaxed"></p>
                                        <span class="text-[9px] text-violet-200 font-mono mt-1 block text-right" x-text="msg.time"></span>
                                    </div>
                                    <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 font-extrabold text-[10px] flex items-center justify-center shrink-0">
                                        {{ substr(Auth::user()->name, 0, 2) }}
                                    </div>
                                </div>
                            </template>

                            <!-- AI Bot Message -->
                            <template x-if="msg.sender === 'ai'">
                                <div class="flex items-start gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-violet-600 to-indigo-600 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-xs">
                                        <i class="bi bi-stars"></i>
                                    </div>
                                    <div class="max-w-[88%] bg-slate-50 border border-slate-200/90 rounded-2xl rounded-tl-xs px-4 py-3 shadow-xs text-xs text-slate-800">
                                        <div class="flex items-center justify-between gap-2 mb-1.5 border-b border-slate-200/60 pb-1">
                                            <span class="font-extrabold text-[11px] text-violet-700 flex items-center gap-1">
                                                <i class="bi bi-cpu-fill"></i> Analisis AI SDN Kebonagung
                                            </span>
                                            <span class="text-[9px] text-slate-400 font-mono" x-text="msg.model"></span>
                                        </div>
                                        <div class="prose prose-xs text-slate-700 leading-relaxed space-y-1.5 whitespace-pre-line" x-text="msg.text"></div>
                                        <span class="text-[9px] text-slate-400 font-mono mt-2 block" x-text="msg.time"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Loading Indicator saat Menunggu Respon AI -->
                    <div x-show="chatLoading" class="flex items-start gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-violet-600 text-white font-bold text-xs flex items-center justify-center shrink-0 animate-pulse">
                            <i class="bi bi-stars"></i>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 rounded-2xl rounded-tl-xs px-4 py-3 text-xs text-slate-500 flex items-center gap-2">
                            <i class="bi bi-arrow-repeat animate-spin text-violet-600"></i>
                            <span>AI sedang menganalisis data kesiswaan...</span>
                        </div>
                    </div>
                </div>

                <!-- Input Chat Bar -->
                <div class="p-3 bg-slate-50 border-t border-slate-200">
                    <form @submit.prevent="sendCustomPrompt()" class="flex items-center gap-2">
                        <input type="text" x-model="userInputPrompt" :disabled="chatLoading"
                            placeholder="Ketik pertanyaan atau konsultasi analisis data kesiswaan..."
                            class="flex-1 px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:border-violet-600 focus:ring-4 focus:ring-violet-500/10 transition">
                        
                        <button type="submit" :disabled="chatLoading || !userInputPrompt.trim()"
                            class="px-4 py-2.5 bg-violet-600 hover:bg-violet-700 disabled:opacity-50 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                            <span>Kirim</span>
                            <i class="bi bi-send-fill text-[11px]"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- KANAN: EARLY WARNING MATRIX & REKAP KEHADIRAN 29 ROMBEL (5 Kolom) -->
        <div class="lg:col-span-5 space-y-5">
            
            <!-- 1. Matriks Deteksi Dini Siswa Berisiko (Early Warning Matrix) -->
            <div class="card p-4.5 border border-slate-200">
                <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-2.5">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                        <i class="bi bi-shield-exclamation text-rose-600"></i> Deteksi Siswa Berisiko Hari Ini
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200">
                        {{ count($riskStudents) }} Siswa
                    </span>
                </div>

                <div class="space-y-2 max-h-56 overflow-y-auto">
                    @forelse($riskStudents as $rs)
                        <div class="p-2.5 rounded-xl border border-slate-200/80 hover:bg-rose-50/40 transition flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 font-extrabold text-xs flex items-center justify-center">
                                    {{ substr($rs->student?->name ?? 'S', 0, 2) }}
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900">{{ $rs->student?->name }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">
                                        Kelas {{ $rs->student?->classroom?->code ?? '-' }} &bull; +{{ $rs->late_minutes }} mnt ({{ $rs->late_level }})
                                    </div>
                                </div>
                            </div>

                            <button type="button" @click="consultStudent('{{ addslashes($rs->student?->name) }}', '{{ $rs->student?->classroom?->code }}', '{{ $rs->late_level }}', {{ $rs->late_minutes }})"
                                class="px-2 py-1 rounded bg-violet-50 hover:bg-violet-100 text-violet-700 border border-violet-200 text-[10px] font-bold transition cursor-pointer">
                                Konsul AI
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            <i class="bi bi-check-circle-fill text-emerald-500 text-xl block mb-1"></i>
                            Tidak ada siswa yang terdeteksi terlambat kritis hari ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 2. Rekap Rombel Terdisiplin vs Perlu Perhatian -->
            <div class="card p-4.5 border border-slate-200">
                <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-2.5">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                        <i class="bi bi-bar-chart-fill text-indigo-600"></i> Rekap Kedisiplinan 29 Rombel
                    </span>
                    <span class="text-[10px] text-slate-400 font-semibold">Realtime Gerbang</span>
                </div>

                <div class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
                    @foreach($rombelStats as $rombel)
                        <div class="p-2 rounded-xl bg-slate-50 border border-slate-200/70 text-xs">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-slate-900">Kelas {{ $rombel['code'] }}</span>
                                <span class="font-mono font-bold text-[11px]"
                                    class="{{ $rombel['percent'] >= 90 ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $rombel['percent'] }}% Hadir
                                </span>
                            </div>
                            <!-- Mini Progress Bar -->
                            <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                <div class="h-1.5 rounded-full transition-all duration-300 {{ $rombel['percent'] >= 90 ? 'bg-emerald-500' : 'bg-amber-500' }}"
                                    style="width: {{ $rombel['percent'] }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-500 mt-1">
                                <span>{{ $rombel['present'] }} Tepat Waktu</span>
                                <span>{{ $rombel['late'] }} Terlambat</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Script Stasiun AI Analytics -->
<script>
function aiAnalyticsApp() {
    return {
        showConfig: false,
        apiKeyInput: '{{ $aiConfig['raw_api_key'] }}',
        showKeyText: false,
        modelInput: '{{ $aiConfig['model'] }}',
        hasKey: {{ $aiConfig['has_key'] ? 'true' : 'false' }},
        currentAiModel: '{{ $aiConfig['has_key'] ? "Google " . $aiConfig['model'] : "STAR-KIDS Rule Engine" }}',

        // State Uji Coba API
        testingApi: false,
        testSuccess: null,
        testMessage: '',
        testLatency: 0,
        savingKey: false,

        // State Chat
        userInputPrompt: '',
        chatLoading: false,
        chatMessages: [
            {
                sender: 'ai',
                text: 'Selamat datang di Pusat Analisis Data AI & Asisten Pedagogik SDN Kebonagung. Saya siap membantu Anda menganalisis tren kedisiplinan 29 rombel, menyusun draf surat peringatan/pesan santun, atau merekomendasikan solusi ramah anak.',
                model: '{{ $aiConfig['has_key'] ? "Google " . $aiConfig['model'] : "STAR-KIDS Rule Engine" }}',
                time: '{{ Carbon\Carbon::now("Asia/Jakarta")->format("H:i:s") }}'
            }
        ],

        // 1. Kirim Prompt Pintasan Cepat
        sendQuickPrompt(type, label) {
            this.chatMessages.push({
                sender: 'user',
                text: label,
                time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
            });

            this.executeChatCall(label, type);
        },

        // 2. Kirim Custom Prompt Pengguna
        sendCustomPrompt() {
            if (!this.userInputPrompt.trim()) return;

            const text = this.userInputPrompt.trim();
            this.userInputPrompt = '';

            this.chatMessages.push({
                sender: 'user',
                text: text,
                time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
            });

            this.executeChatCall(text, 'custom');
        },

        // 3. Konsultasi Khusus Siswa Terlambat dari Matriks
        consultStudent(nama, kelas, level, menit) {
            const prompt = `Berikan rekomendasi tindakan pedagogik ramah anak untuk siswa ${nama} (Kelas ${kelas}) yang tercatat terlambat ${level} (+${menit} menit) di gerbang hari ini.`;
            this.chatMessages.push({
                sender: 'user',
                text: prompt,
                time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
            });

            this.executeChatCall(prompt, 'pedagogic');
        },

        // 4. Eksekusi AJAX ke Backend Controller
        executeChatCall(prompt, type) {
            this.chatLoading = true;
            this.scrollToBottom();

            fetch('{{ route("ai.analytics.chat") }}', {
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
                this.chatLoading = false;
                if (data.reply) {
                    this.chatMessages.push({
                        sender: 'ai',
                        text: data.reply,
                        model: data.model,
                        time: data.timestamp
                    });
                }
                this.scrollToBottom();
            })
            .catch(err => {
                this.chatLoading = false;
                this.chatMessages.push({
                    sender: 'ai',
                    text: 'Maaf, terjadi gangguan saat menghubungkan ke asisten AI. Silakan periksa koneksi atau ulangi kembali.',
                    model: 'System Error',
                    time: new Date().toLocaleTimeString('id-ID')
                });
                this.scrollToBottom();
            });
        },

        // 5. Uji Coba Koneksi API Key Gemini
        testConnection() {
            this.testingApi = true;
            this.testMessage = '';

            fetch('{{ route("ai.analytics.test") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    api_key: this.apiKeyInput,
                    model: this.modelInput
                })
            })
            .then(res => res.json())
            .then(data => {
                this.testingApi = false;
                this.testSuccess = data.success;
                this.testMessage = data.message + (data.reply ? ' Respon AI: "' + data.reply + '"' : '');
                this.testLatency = data.latency || 0;
            })
            .catch(err => {
                this.testingApi = false;
                this.testSuccess = false;
                this.testMessage = 'Gagal menghubungi server pengujian: ' + err;
            });
        },

        // 6. Simpan Konfigurasi API Key
        saveApiKey() {
            this.savingKey = true;

            fetch('{{ route("ai.analytics.settings") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    gemini_api_key: this.apiKeyInput,
                    gemini_model: this.modelInput
                })
            })
            .then(res => res.json())
            .then(data => {
                this.savingKey = false;
                alert(data.message || 'Konfigurasi berhasil disimpan!');
                this.hasKey = !!this.apiKeyInput.trim();
                this.currentAiModel = this.hasKey ? 'Google ' + this.modelInput : 'STAR-KIDS Rule Engine';
            })
            .catch(err => {
                this.savingKey = false;
                alert('Gagal menyimpan konfigurasi: ' + err);
            });
        },

        resetChat() {
            this.chatMessages = [
                {
                    sender: 'ai',
                    text: 'Riwayat percakapan telah direset. Silakan pilih tombol pintasan analisis atau ketik pertanyaan baru.',
                    model: this.currentAiModel,
                    time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
                }
            ];
        },

        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.chatBox) {
                    this.$refs.chatBox.scrollTop = this.$refs.chatBox.scrollHeight;
                }
            });
        }
    };
}
</script>
@endsection
