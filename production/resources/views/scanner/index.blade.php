@extends('layouts.app')

@section('title', 'Terminal Pemindai Gerbang Presensi 3-Mode - SDN Kebonagung')

@section('content')
<div class="space-y-6" x-data="scannerApp()">
    <!-- Header Banner & Realtime Gate Status -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold mb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <i class="bi bi-upc-scan"></i> Gerbang Presensi Siswa Aktif
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Meja Scanner Gerbang 3-Mode</h1>
            <p class="text-xs text-slate-500 mt-1">
                Perekaman presensi otomatis: USB Barcode Gun, Pemindai Kamera HP/WebRTC, dan input manual NISN jika siswa tidak membawa kartu.
            </p>
        </div>

        <!-- Mode Switcher Tabs -->
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <div class="bg-slate-100 p-1 rounded-xl border border-slate-200 flex items-center text-xs font-bold">
                <button type="button" @click="setMode('masuk')" :class="mode === 'masuk' ? 'bg-white shadow-xs text-rose-600' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1.5 rounded-lg transition cursor-pointer flex items-center gap-1.5">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Masuk Pagi</span>
                </button>
                <button type="button" @click="setMode('gatepass')" :class="mode === 'gatepass' ? 'bg-white shadow-xs text-blue-600' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1.5 rounded-lg transition cursor-pointer flex items-center gap-1.5">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span>Gate Pass (Izin)</span>
                </button>
                <button type="button" @click="setMode('pulang')" :class="mode === 'pulang' ? 'bg-white shadow-xs text-purple-600' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1.5 rounded-lg transition cursor-pointer flex items-center gap-1.5">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Pulang Sore</span>
                </button>
            </div>

            <!-- Digital Clock Display -->
            <div class="px-3.5 py-1.5 bg-slate-900 text-white rounded-xl font-mono text-xs font-extrabold flex items-center gap-2 shadow-xs">
                <i class="bi bi-clock-fill text-rose-400"></i>
                <span x-text="clockTime">00:00:00 WIB</span>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards Hari Ini -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Siswa Terpindai Hari Ini</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900" x-text="statsScanned">{{ $stats['today_scanned'] }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Dari {{ number_format($stats['total_students']) }} Siswa Aktif</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Hadir Tepat Waktu</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-emerald-600" x-text="statsOntime">{{ $stats['today_ontime'] }}</div>
            <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">Sebelum 07:00 WIB</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Terlambat Masuk</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-amber-500" x-text="statsLate">{{ $stats['today_late'] }}</div>
            <p class="text-[11px] text-amber-600 font-semibold mt-0.5">T-1 / T-2 / T-3</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Digital Gate Pass</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-blue-600" x-text="statsGatepass">{{ $stats['today_gatepass'] }}</div>
            <p class="text-[11px] text-blue-600 font-semibold mt-0.5">Izin Keluar Kampus</p>
        </div>
    </div>

    <!-- Main Scanner Layout: Left Terminal / Right Monitor -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Input Methods (Terminal, Kamera WebRTC, Isi Manual) (6 Cols) -->
        <div class="lg:col-span-6 space-y-4">
            <div class="card p-5">
                <!-- Method Selector Tabs -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <span class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="bi bi-terminal-fill text-rose-600"></i> Pilih Perangkat Pemindai
                    </span>

                    <div class="inline-flex rounded-lg border border-slate-200 bg-slate-100 p-0.5 text-xs font-semibold">
                        <button type="button" @click="activeMethod = 'usb'" :class="activeMethod === 'usb' ? 'bg-white shadow-xs text-rose-600 font-bold' : 'text-slate-600'" class="px-2.5 py-1 rounded-md transition cursor-pointer flex items-center gap-1">
                            <i class="bi bi-upc"></i> Barcode USB
                        </button>
                        <button type="button" @click="activeMethod = 'camera'" :class="activeMethod === 'camera' ? 'bg-white shadow-xs text-rose-600 font-bold' : 'text-slate-600'" class="px-2.5 py-1 rounded-md transition cursor-pointer flex items-center gap-1">
                            <i class="bi bi-camera-fill"></i> Kamera HP
                        </button>
                        <button type="button" @click="activeMethod = 'manual'" :class="activeMethod === 'manual' ? 'bg-white shadow-xs text-indigo-600 font-bold' : 'text-slate-600'" class="px-2.5 py-1 rounded-md transition cursor-pointer flex items-center gap-1">
                            <i class="bi bi-pencil-square"></i> Input Manual
                        </button>
                    </div>
                </div>

                <!-- 1. METODE USB BARCODE GUN / KEYBOARD-WEDGE -->
                <div x-show="activeMethod === 'usb'" class="space-y-3">
                    <div class="p-3 bg-rose-50/60 border border-rose-200 rounded-xl text-xs text-rose-800 flex items-start gap-2">
                        <i class="bi bi-info-circle-fill text-rose-600 text-sm mt-0.5"></i>
                        <div>
                            <strong>Mode Barcode Gun USB Siap:</strong> Arahkan alat pemindai ke Barcode 1D / QR Code pada Kartu Pelajar siswa. Kotak input di bawah otomatis mendeteksi tanpa perlu klik mouse.
                        </div>
                    </div>

                    <form @submit.prevent="submitScan('barcode_usb')">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="bi bi-upc-scan text-xl"></i>
                            </span>
                            <input type="text" x-ref="usbInput" x-model="barcodeInput" placeholder="Menunggu scan barcode atau ketik NISN lalu tekan Enter..."
                                class="w-full pl-11 pr-24 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm font-mono font-bold text-slate-900 focus:bg-white focus:outline-none focus:border-rose-600 focus:ring-4 focus:ring-rose-500/10 transition">
                            <button type="submit" :disabled="loading" class="absolute right-2 top-2 bottom-2 px-3 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-lg transition flex items-center gap-1 cursor-pointer">
                                <span x-show="!loading">Submit</span>
                                <span x-show="loading"><i class="bi bi-arrow-repeat animate-spin"></i></span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 2. METODE KAMERA WEBRTC (HP / LAPTOP CAMERA SCANNER) -->
                <div x-show="activeMethod === 'camera'" class="space-y-3" style="display: none;">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-700">Pemindai Kamera Video (html5-qrcode)</span>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="toggleCamera()" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 cursor-pointer shadow-xs transition"
                                :class="cameraRunning ? 'bg-rose-500 hover:bg-rose-600 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white'">
                                <i :class="cameraRunning ? 'bi bi-camera-video-off' : 'bi bi-camera-video'"></i>
                                <span x-text="cameraRunning ? 'Matikan Kamera' : 'Buka Kamera'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Viewfinder Kamera Frame -->
                    <div class="relative bg-black rounded-xl overflow-hidden aspect-video border border-slate-700 flex items-center justify-center">
                        <div id="cameraReader" class="w-full h-full"></div>
                        <div x-show="!cameraRunning" class="text-slate-400 text-xs flex flex-col items-center gap-2">
                            <i class="bi bi-camera text-4xl text-slate-500"></i>
                            <span>Klik "Buka Kamera" untuk mengaktifkan pemindai WebRTC</span>
                        </div>
                    </div>
                </div>

                <!-- 3. METODE INPUT MANUAL NISN / NAMA SISWA (JIKA SISWA TIDAK BAWA KARTU) -->
                <div x-show="activeMethod === 'manual'" class="space-y-3" style="display: none;">
                    <div class="p-3 bg-indigo-50 border border-indigo-200 rounded-xl text-xs text-indigo-900 flex items-start gap-2">
                        <i class="bi bi-person-exclamation text-indigo-600 text-sm mt-0.5"></i>
                        <div>
                            <strong>Siswa Lupa / Tidak Membawa Kartu:</strong> Cari siswa berdasarkan nama atau NISN di bawah ini, lalu klik untuk mencatat kehadirannya secara langsung.
                        </div>
                    </div>

                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-search text-base"></i>
                        </span>
                        <input type="text" x-model="manualQuery" @input.debounce.300ms="searchManualStudents()" placeholder="Ketik minimal 2 huruf nama atau nomor NISN siswa..."
                            class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:border-indigo-600 transition">
                    </div>

                    <!-- Hasil Pencarian Cepat Siswa -->
                    <div x-show="manualResults.length > 0" class="max-h-56 overflow-y-auto space-y-1.5 border border-slate-200 rounded-xl p-2 bg-slate-50">
                        <template x-for="st in manualResults" :key="st.id">
                            <div @click="selectManualStudent(st)" class="p-2 bg-white hover:bg-indigo-50/70 border border-slate-200/80 rounded-lg flex items-center justify-between cursor-pointer transition">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center">
                                        <span x-text="st.name.substring(0, 2).toUpperCase()"></span>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-900" x-text="st.name"></div>
                                        <div class="text-[10px] text-slate-500 font-mono">
                                            NISN: <span x-text="st.nisn"></span> &bull; <span x-text="st.classroom ? 'Kelas ' + st.classroom.code : '-'"></span>
                                        </div>
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-[10px] font-bold rounded bg-indigo-600 text-white">
                                    Catat Presensi
                                </span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Gate Pass Optional Reason Input (Hanya di mode Gatepass) -->
                <div x-show="mode === 'gatepass'" class="mt-3 pt-3 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Izin Keluar (Digital Gate Pass):</label>
                    <input type="text" x-model="gatePassReason" placeholder="Misal: Dijemput orang tua sakit / Lomba sekolah..." class="form-input text-xs w-full">
                </div>
            </div>

            <!-- Audio Synthesizer Status Pill -->
            <div class="bg-white border border-slate-200 rounded-xl p-3 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <i class="bi bi-volume-up-fill text-slate-600 text-base"></i>
                    <span class="font-bold text-slate-700">Audio Chime & Haptic:</span>
                    <span class="text-emerald-600 font-semibold">Web Audio API Aktif</span>
                </div>
                <button type="button" @click="testAudio()" class="text-slate-500 hover:text-slate-800 text-[11px] font-bold underline cursor-pointer">
                    Uji Bunyi Chime
                </button>
            </div>
        </div>

        <!-- Right: Realtime Monitor Card & Recent Log (6 Cols) -->
        <div class="lg:col-span-6 space-y-4">
            
            <!-- REALTIME MONITOR SISWA TERAKHIR DI-SCAN -->
            <div class="card p-5 border-2 transition duration-200"
                :class="{
                    'border-emerald-500 bg-emerald-50/20': lastResult && lastResult.status_color === 'emerald',
                    'border-amber-500 bg-amber-50/20': lastResult && lastResult.status_color === 'amber',
                    'border-blue-500 bg-blue-50/20': lastResult && lastResult.status_color === 'blue',
                    'border-purple-500 bg-purple-50/20': lastResult && lastResult.status_color === 'purple',
                    'border-rose-500 bg-rose-50/20': lastResult && !lastResult.success,
                    'border-slate-200': !lastResult
                }">
                
                <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-2">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                        <i class="bi bi-display-fill"></i> Monitor Siswa Terakhir
                    </span>

                    <template x-if="lastResult && lastResult.success">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black uppercase"
                            :class="{
                                'bg-emerald-100 text-emerald-800 border border-emerald-300': lastResult.status_color === 'emerald',
                                'bg-amber-100 text-amber-800 border border-amber-300': lastResult.status_color === 'amber',
                                'bg-blue-100 text-blue-800 border border-blue-300': lastResult.status_color === 'blue',
                                'bg-purple-100 text-purple-800 border border-purple-300': lastResult.status_color === 'purple'
                            }"
                            x-text="lastResult.status_label">
                        </span>
                    </template>
                </div>

                <!-- Konten Siswa -->
                <template x-if="lastResult && lastResult.student">
                    <div class="flex items-center gap-4">
                        <div class="w-18 h-22 rounded-xl bg-white border-2 border-slate-200 overflow-hidden shadow-xs shrink-0 flex items-center justify-center">
                            <template x-if="lastResult.student.photo">
                                <img :src="'/storage/' + lastResult.student.photo" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!lastResult.student.photo">
                                <div class="text-center text-slate-400">
                                    <i class="bi bi-person-fill text-3xl"></i>
                                    <div class="text-[8px] font-bold">3x4</div>
                                </div>
                            </template>
                        </div>

                        <div class="space-y-1 flex-1">
                            <h2 class="text-base font-extrabold text-slate-900 leading-tight" x-text="lastResult.student.name"></h2>
                            <div class="text-xs font-mono font-bold text-slate-600">
                                NISN: <span class="text-rose-600" x-text="lastResult.student.nisn"></span>
                            </div>
                            <div class="text-xs text-slate-600 font-semibold">
                                Rombel: <span class="font-bold text-indigo-700" x-text="lastResult.student.classroom ? 'Kelas ' + lastResult.student.classroom.code : '-'"></span> &bull; 
                                Gender: <span x-text="lastResult.student.gender === 'L' ? 'Laki-Laki' : 'Perempuan'"></span>
                            </div>
                            <div class="text-xs font-mono text-slate-500 pt-0.5" x-text="'Waktu Scan: ' + lastResult.time"></div>
                        </div>
                    </div>
                </template>

                <!-- Pesan / Error -->
                <template x-if="lastResult && lastResult.message">
                    <div class="mt-3 p-2.5 rounded-xl text-xs font-bold"
                        :class="lastResult.success ? 'bg-white border border-slate-200 text-slate-800' : 'bg-rose-100 border border-rose-300 text-rose-800'"
                        x-text="lastResult.message">
                    </div>
                </template>

                <!-- Standby Placeholder -->
                <template x-if="!lastResult">
                    <div class="py-10 text-center text-slate-400">
                        <i class="bi bi-upc text-4xl mb-2 text-slate-300 block"></i>
                        <span class="text-xs">Menunggu pemindaian barcode kartu atau input NISN...</span>
                    </div>
                </template>
            </div>

            <!-- TABEL LOG RIWAYAT SCAN HARI INI -->
            <div class="card overflow-hidden">
                <div class="card-header flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Riwayat Presensi Gerbang Hari Ini</h3>
                        <p class="text-[11px] text-slate-500">Urutan kronologis pemindaian kedatangan siswa</p>
                    </div>
                    <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full">
                        Live Feed
                    </span>
                </div>

                <div class="overflow-x-auto max-h-72 overflow-y-auto">
                    <table class="table table-hover text-xs">
                        <thead>
                            <tr>
                                <th>Jam</th>
                                <th>Siswa & NISN</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center">Status</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="item in recentLogs" :key="item.id">
                                <tr>
                                    <td class="font-mono text-slate-600 font-bold" x-text="item.checkin_time || item.gate_pass_time || item.checkout_time"></td>
                                    <td>
                                        <div class="font-bold text-slate-900" x-text="item.student ? item.student.name : '-'"></div>
                                        <div class="text-[10px] font-mono text-slate-400" x-text="item.nisn"></div>
                                    </td>
                                    <td class="text-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700" x-text="item.student && item.student.classroom ? item.student.classroom.code : '-'"></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                            :class="{
                                                'bg-emerald-50 text-emerald-700 border border-emerald-200': item.status === 'Hadir',
                                                'bg-amber-50 text-amber-700 border border-amber-200': item.status === 'Terlambat',
                                                'bg-blue-50 text-blue-700 border border-blue-200': item.gate_pass_active || item.status === 'Izin',
                                                'bg-purple-50 text-purple-700 border border-purple-200': item.status === 'Pulang'
                                            }"
                                            x-text="item.status + (item.late_level ? ' (' + item.late_level + ')' : '')">
                                        </span>
                                    </td>
                                    <td class="text-slate-500 text-[11px]" x-text="item.operator ? item.operator.name.split(' ')[0] : 'Sistem'"></td>
                                </tr>
                            </template>
                            @forelse($todayAttendances as $att)
                                <tr x-show="recentLogs.length === 0">
                                    <td class="font-mono text-slate-600 font-bold">{{ $att->checkin_time ?? $att->gate_pass_time ?? $att->checkout_time }}</td>
                                    <td>
                                        <div class="font-bold text-slate-900">{{ $att->student->name ?? '-' }}</div>
                                        <div class="text-[10px] font-mono text-slate-400">{{ $att->nisn }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700">{{ $att->student->classroom->code ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($att->status === 'Hadir')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Hadir</span>
                                        @elseif($att->status === 'Terlambat')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Terlambat ({{ $att->late_level }})</span>
                                        @elseif($att->gate_pass_active || $att->status === 'Izin')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Gate Pass</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">{{ $att->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-slate-500 text-[11px]">{{ explode(' ', $att->operator->name ?? 'Sistem')[0] }}</td>
                                </tr>
                            @empty
                                <tr x-show="recentLogs.length === 0">
                                    <td colspan="5" class="text-center py-6 text-slate-400 text-xs">
                                        Belum ada data presensi siswa yang tercatat hari ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Web Audio API Synthesizer & Scanner Script -->
<script>
function scannerApp() {
    return {
        mode: '{{ $activeMode }}',
        activeMethod: 'usb',
        barcodeInput: '',
        loading: false,
        gatePassReason: '',
        lastResult: null,
        recentLogs: [],
        clockTime: '{{ $currentTimeWib }}',
        cameraRunning: false,
        html5QrCode: null,
        manualQuery: '',
        manualResults: [],
        audioCtx: null,

        statsScanned: {{ $stats['today_scanned'] }},
        statsOntime: {{ $stats['today_ontime'] }},
        statsLate: {{ $stats['today_late'] }},
        statsGatepass: {{ $stats['today_gatepass'] }},

        init() {
            // Jalankan Jam Digital Realtime
            setInterval(() => {
                const now = new Date();
                const h = String(now.getHours()).padStart(2, '0');
                const m = String(now.getMinutes()).padStart(2, '0');
                const s = String(now.getSeconds()).padStart(2, '0');
                this.clockTime = `${h}:${m}:${s} WIB`;
            }, 1000);

            // Inisialisasi Web Audio Context saat ada interaksi pengguna
            document.addEventListener('click', () => {
                this.initAudioContext();
            }, { once: true });

            // Auto focus ke input USB Barcode Gun
            this.$nextTick(() => {
                if (this.$refs.usbInput) {
                    this.$refs.usbInput.focus();
                }
            });
        },

        initAudioContext() {
            if (!this.audioCtx) {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (AudioContext) {
                    this.audioCtx = new AudioContext();
                }
            }
        },

        setMode(newMode) {
            this.mode = newMode;
            if (this.$refs.usbInput) {
                this.$refs.usbInput.focus();
            }
        },

        // Web Audio API Sound Chime Synthesis
        playSound(type) {
            this.initAudioContext();
            if (!this.audioCtx) return;

            const ctx = this.audioCtx;
            if (ctx.state === 'suspended') {
                ctx.resume();
            }

            // Haptic feedback vibration
            if (navigator.vibrate) {
                if (type === 'beep_warning' || type === 'buzz_error') {
                    navigator.vibrate([100, 50, 100]);
                } else {
                    navigator.vibrate([60, 30, 60]);
                }
            }

            const now = ctx.currentTime;

            if (type === 'chime_success') {
                // Chord C-E-G harmonis cerah
                [523.25, 659.25, 783.99].forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + idx * 0.08);
                    gain.gain.setValueAtTime(0.15, now + idx * 0.08);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.08 + 0.35);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + idx * 0.08);
                    osc.stop(now + idx * 0.08 + 0.36);
                });
            } else if (type === 'beep_warning') {
                // Nada ganda peringatan (880Hz -> 700Hz)
                [880, 700].forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(freq, now + idx * 0.12);
                    gain.gain.setValueAtTime(0.2, now + idx * 0.12);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.12 + 0.2);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + idx * 0.12);
                    osc.stop(now + idx * 0.12 + 0.22);
                });
            } else if (type === 'buzz_error') {
                // Nada rendah buzzer 180Hz
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(180, now);
                gain.gain.setValueAtTime(0.25, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.4);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now);
                osc.stop(now + 0.42);
            } else {
                // Default melodic chime
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(600, now);
                gain.gain.setValueAtTime(0.15, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now);
                osc.stop(now + 0.32);
            }
        },

        testAudio() {
            this.playSound('chime_success');
        },

        // Submit Scan via AJAX
        submitScan(method, customCode = null) {
            const codeToSubmit = customCode || this.barcodeInput;
            if (!codeToSubmit || codeToSubmit.trim() === '') return;

            this.loading = true;

            fetch('{{ route("scanner.scan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    code: codeToSubmit,
                    method: method,
                    mode: this.mode,
                    gate_pass_reason: this.gatePassReason
                })
            })
            .then(res => res.json())
            .then(data => {
                this.loading = false;
                this.lastResult = data;
                this.barcodeInput = '';

                if (data.sound) {
                    this.playSound(data.sound);
                }

                if (data.success && data.student) {
                    // Update recent logs live
                    this.recentLogs.unshift({
                        id: Date.now(),
                        checkin_time: data.time,
                        gate_pass_time: data.time,
                        checkout_time: data.time,
                        nisn: data.student.nisn,
                        student: data.student,
                        status: data.attendance ? data.attendance.status : 'Hadir',
                        late_level: data.attendance ? data.attendance.late_level : null,
                        operator: { name: '{{ Auth::user()->name }}' }
                    });

                    // Update stats
                    this.statsScanned++;
                    if (data.status_color === 'emerald') this.statsOntime++;
                    if (data.status_color === 'amber') this.statsLate++;
                    if (data.status_color === 'blue') this.statsGatepass++;
                }

                // Kembalikan fokus ke input barcode USB
                this.$nextTick(() => {
                    if (this.$refs.usbInput) this.$refs.usbInput.focus();
                });
            })
            .catch(err => {
                this.loading = false;
                this.playSound('buzz_error');
                this.lastResult = {
                    success: false,
                    message: 'Terjadi kesalahan sistem saat memproses presensi.'
                };
            });
        },

        // Toggle Kamera WebRTC menggunakan html5-qrcode
        toggleCamera() {
            if (this.cameraRunning) {
                if (this.html5QrCode) {
                    this.html5QrCode.stop().then(() => {
                        this.cameraRunning = false;
                    }).catch(err => {
                        console.error('Stop camera error:', err);
                        this.cameraRunning = false;
                    });
                }
            } else {
                if (typeof Html5Qrcode === 'undefined') {
                    alert('Library scanner kamera sedang dimuat, silakan coba sesaat lagi.');
                    return;
                }

                this.html5QrCode = new Html5Qrcode('cameraReader');
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };

                this.html5QrCode.start(
                    { facingMode: 'environment' },
                    config,
                    (decodedText) => {
                        // Anti-duplikasi debouncing
                        this.submitScan('camera_qr', decodedText);
                    },
                    (error) => {
                        // scanning frame
                    }
                ).then(() => {
                    this.cameraRunning = true;
                }).catch(err => {
                    alert('Gagal mengakses kamera: ' + err);
                    this.cameraRunning = false;
                });
            }
        },

        // Pencarian Manual Siswa
        searchManualStudents() {
            if (this.manualQuery.length < 2) {
                this.manualResults = [];
                return;
            }

            fetch('{{ route("scanner.search") }}?q=' + encodeURIComponent(this.manualQuery), {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                this.manualResults = data;
            });
        },

        selectManualStudent(student) {
            this.manualQuery = '';
            this.manualResults = [];
            this.submitScan('manual_nisn', student.nisn);
        }
    }
}
</script>
@endsection
