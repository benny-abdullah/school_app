@extends('layouts.app')

@section('title', 'Biodata & Profil Anak - ' . $student->name)

@section('content')
<div class="space-y-6" x-data="{ activeCardTab: 'front', previewPhotoUrl: null }">
    <!-- Header Banner -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 text-2xl font-bold flex-shrink-0">
                <i class="bi bi-person-vcard-fill"></i>
            </div>
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold mb-1">
                    <i class="bi bi-mortarboard-fill"></i> Profil Resmi Peserta Didik
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">{{ $student->name }}</h1>
                <p class="text-xs text-slate-500">
                    NISN: <span class="font-mono font-bold text-slate-800">{{ $student->nisn }}</span> &bull; 
                    Rombel: <span class="font-bold text-slate-800">Kelas {{ $classroom->code ?? '-' }}</span> &bull; 
                    Wali Kelas: <span class="font-semibold text-slate-700">{{ $classroom->homeroom_teacher ?? '-' }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('ortu.dashboard') }}" class="btn-outline text-xs px-4 py-2.5 flex items-center gap-2">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Dasbor Presensi</span>
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Kolom Kiri: Form Pas Foto & Biodata Siswa (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Card 1: Unggah & Perbarui Pas Foto Resmi Siswa -->
            <div class="card p-6 border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
                            <i class="bi bi-camera-fill"></i>
                        </span>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900">Pas Foto Resmi Peserta Didik</h3>
                            <p class="text-[11px] text-slate-500">Unggah pas foto ananda untuk Kartu Pelajar Digital dan Pemindai Presensi</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200">
                        Rasio 3x4
                    </span>
                </div>

                <form action="{{ route('ortu.child.update_photo') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="flex flex-col sm:flex-row items-center gap-5 p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                        <!-- Current Photo or Live Preview -->
                        <div class="relative w-24 aspect-[3/4] rounded-xl bg-white border-2 border-slate-200 shadow-xs overflow-hidden flex-shrink-0 flex items-center justify-center">
                            <template x-if="previewPhotoUrl">
                                <img :src="previewPhotoUrl" alt="Pratinjau Foto" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!previewPhotoUrl">
                                @if($student->photo)
                                    <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="flex flex-col items-center justify-center text-slate-400 p-2 text-center">
                                        <i class="bi bi-person-bounding-box text-3xl text-slate-300"></i>
                                        <span class="text-[9px] font-bold mt-1 text-slate-400">Belum Ada Foto</span>
                                    </div>
                                @endif
                            </template>
                        </div>

                        <!-- Upload Controls -->
                        <div class="flex-1 space-y-2 w-full text-center sm:text-left">
                            <label class="block text-xs font-bold text-slate-700">Pilih Berkas Foto Baru</label>
                            <input type="file" name="photo" id="student_photo_input" accept="image/jpeg,image/png,image/webp" 
                                @change="const file = $event.target.files[0]; if (file) { previewPhotoUrl = URL.createObjectURL(file); }"
                                class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 cursor-pointer border border-slate-200 rounded-xl bg-white">
                            
                            <p class="text-[11px] text-slate-400">
                                Format didukung: <strong>JPG, JPEG, PNG, WebP</strong> (Maksimal 2 MB). Disarankan berlatar belakang polos dan berpakaian seragam sekolah.
                            </p>

                            <div class="pt-2">
                                <button type="submit" class="btn-primary text-xs px-4 py-2 flex items-center gap-1.5 shadow-xs w-full sm:w-auto justify-center cursor-pointer">
                                    <i class="bi bi-cloud-arrow-up-fill"></i>
                                    <span>Simpan & Perbarui Foto</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Card 2: Biodata Lengkap Peserta Didik (Data Pokok Read-Only) -->
            <div class="card p-6 border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-sm">
                            <i class="bi bi-file-earmark-person-fill"></i>
                        </span>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900">Data Pokok Pendidikan (Dapodik)</h3>
                            <p class="text-[11px] text-slate-500">Buku Induk Kesiswaan resmi SDN Kebonagung Kota Pasuruan</p>
                        </div>
                    </div>
                    <span class="status-badge status-active text-[10px]">
                        <i class="bi bi-shield-check"></i> Terverifikasi Sekolah
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <!-- NISN -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">NISN Resmi (Kemdikbud)</span>
                        <div class="font-mono font-extrabold text-slate-900 text-sm mt-0.5 flex items-center gap-2">
                            <span>{{ $student->nisn }}</span>
                            <span class="text-[10px] bg-slate-200 text-slate-600 px-1.5 py-0.5 rounded font-mono font-normal">10 Digit</span>
                        </div>
                    </div>

                    <!-- NIS -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nomor Induk Siswa (NIS)</span>
                        <div class="font-mono font-bold text-slate-800 text-sm mt-0.5">
                            {{ $student->nis ?? '-' }}
                        </div>
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Lengkap Peserta Didik</span>
                        <div class="font-bold text-slate-900 text-sm mt-0.5">
                            {{ $student->name }}
                        </div>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jenis Kelamin</span>
                        <div class="font-bold text-slate-800 mt-0.5 flex items-center gap-1.5">
                            <i class="bi {{ $student->gender == 'L' ? 'bi-gender-male text-blue-600' : 'bi-gender-female text-pink-600' }}"></i>
                            <span>{{ $student->gender == 'L' ? 'Laki-Laki' : 'Perempuan' }}</span>
                        </div>
                    </div>

                    <!-- Agama -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Agama</span>
                        <div class="font-bold text-slate-800 mt-0.5">
                            {{ $student->religion ?? 'Islam' }}
                        </div>
                    </div>

                    <!-- Tempat, Tgl Lahir -->
                    <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tempat & Tanggal Lahir</span>
                        <div class="font-bold text-slate-800 mt-0.5">
                            {{ $student->birth_place ? $student->birth_place . ', ' : '' }}{{ $student->birth_date ? $student->birth_date->translatedFormat('d F Y') : '-' }}
                            @if($student->birth_date)
                                <span class="text-slate-500 font-normal text-xs">({{ \Carbon\Carbon::parse($student->birth_date)->age }} tahun)</span>
                            @endif
                        </div>
                    </div>

                    <!-- Rombel Kelas -->
                    <div class="p-3.5 rounded-xl bg-indigo-50/50 border border-indigo-100">
                        <span class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider block">Rombongan Belajar (Rombel)</span>
                        <div class="font-extrabold text-indigo-950 text-sm mt-0.5">
                            Kelas {{ $classroom->code ?? '-' }}
                        </div>
                        <span class="text-[11px] text-indigo-600 font-medium">Tingkat {{ $classroom->grade_level ?? '-' }} &bull; Semester Ganjil</span>
                    </div>

                    <!-- Wali Kelas -->
                    <div class="p-3.5 rounded-xl bg-indigo-50/50 border border-indigo-100">
                        <span class="text-[10px] font-bold text-indigo-700 uppercase tracking-wider block">Wali Kelas Pengampu</span>
                        <div class="font-bold text-indigo-950 mt-0.5 truncate" title="{{ $classroom->homeroom_teacher }}">
                            {{ $classroom->homeroom_teacher ?? '-' }}
                        </div>
                        <span class="text-[10px] text-slate-500 font-mono">NIP: {{ $classroom->homeroom_nip ?? '-' }}</span>
                    </div>

                    <!-- Alamat Rumah -->
                    <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Alamat Domisili Siswa</span>
                        <div class="font-medium text-slate-700 mt-0.5">
                            {{ $student->address ?? 'Alamat domisili belum terisi di sistem Dapodik.' }}
                        </div>
                    </div>

                    <!-- Saldo Poin Tatib -->
                    <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-100">
                        <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block">Saldo Poin Karakter Tatib</span>
                        <div class="font-mono font-black text-emerald-800 text-lg mt-0.5">
                            {{ $student->point_balance }} <span class="text-xs font-bold text-emerald-600">Poin</span>
                        </div>
                        <span class="text-[10px] text-emerald-600 font-semibold">Predikat: Teladan & Tertib</span>
                    </div>

                    <!-- Status Kesiswaan -->
                    <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-100">
                        <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block">Status Kesiswaan</span>
                        <div class="font-bold text-emerald-800 text-sm mt-1 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ ucfirst($student->status) }} Aktif Belajar</span>
                        </div>
                        <span class="text-[10px] text-slate-500">Tahun Ajaran 2025/2026</span>
                    </div>
                </div>

                <!-- Informasi Keamanan Data Pokok -->
                <div class="mt-4 p-3.5 rounded-xl bg-amber-50/70 border border-amber-200 flex items-start gap-3">
                    <i class="bi bi-lock-fill text-amber-600 text-base mt-0.5 shrink-0"></i>
                    <div class="text-[11px] text-amber-800 leading-relaxed">
                        <strong>Perhatian:</strong> Data pokok kesiswaan (Nama, NISN, Rombel, Tempat/Tanggal Lahir) berstatus <strong>Terkunci (Read-Only)</strong> untuk menjaga validitas data Dapodik resmi. Jika terdapat kesalahan data, silakan berkoordinasi dengan pihak <strong>Tata Usaha / Operator Sekolah</strong>.
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Pratinjau Kartu Siswa Digital ISO CR-80 & Kontak Wali (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Card 3: Pratinjau Fisik Kartu Siswa Digital (CR-80) -->
            <div class="card p-5 border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Kartu Pelajar Digital ISO CR-80</h3>
                        <p class="text-[11px] text-slate-500">Standar fisik 85.60 x 53.98 mm untuk Presensi Gerbang</p>
                    </div>

                    <!-- Switcher Sisi Kartu -->
                    <div class="flex items-center bg-slate-100 p-1 rounded-lg text-xs">
                        <button type="button" @click="activeCardTab = 'front'" 
                            :class="activeCardTab === 'front' ? 'bg-white shadow-2xs text-rose-600 font-bold' : 'text-slate-600'" 
                            class="px-2.5 py-1 rounded-md transition cursor-pointer">
                            Depan
                        </button>
                        <button type="button" @click="activeCardTab = 'back'" 
                            :class="activeCardTab === 'back' ? 'bg-white shadow-2xs text-rose-600 font-bold' : 'text-slate-600'" 
                            class="px-2.5 py-1 rounded-md transition cursor-pointer">
                            Belakang
                        </button>
                    </div>
                </div>

                <!-- CARD CANVAS CONTAINER -->
                <div class="w-full max-w-[360px] mx-auto my-2">
                    
                    <!-- SISI DEPAN KARTU -->
                    <div x-show="activeCardTab === 'front'" class="transition duration-300">
                        <div class="w-full aspect-[85.6/53.98] rounded-2xl bg-gradient-to-br from-rose-700 via-rose-800 to-slate-900 text-white p-3.5 shadow-xl relative overflow-hidden flex flex-col justify-between border border-rose-400/30">
                            <!-- Watermark Ornaments -->
                            <div class="absolute -right-8 -bottom-8 w-40 h-40 rounded-full bg-rose-500/10 pointer-events-none blur-xl"></div>
                            <div class="absolute -left-8 -top-8 w-32 h-32 rounded-full bg-amber-500/10 pointer-events-none blur-lg"></div>

                            <!-- Header Kartu -->
                            <div class="flex items-center gap-2 border-b border-white/20 pb-1.5 relative z-10">
                                <img src="{{ asset('images/logo_sdnkebonagung.jpeg') }}" alt="Logo" class="w-8 h-8 object-contain bg-white rounded-lg p-0.5 shadow-xs shrink-0">
                                <div class="leading-tight">
                                    <div class="text-[8px] font-extrabold uppercase tracking-wider text-rose-200">UPTD SDN KEBONAGUNG PASURUAN</div>
                                    <div class="text-[11px] font-black tracking-tight text-white uppercase">KARTU TANDA SISWA</div>
                                    <div class="text-[7.5px] text-rose-200/90 font-mono">NPSN: 20535412 &bull; Jl. Panglima Sudirman 120</div>
                                </div>
                            </div>

                            <!-- Body Kartu: Pas Foto & Biodata -->
                            <div class="flex items-center gap-2.5 my-1 relative z-10">
                                <!-- Foto 3x4 -->
                                <div class="w-[72px] aspect-[3/4] bg-white/95 rounded-lg border-2 border-white/90 shadow-md overflow-hidden shrink-0 flex items-center justify-center p-0.5">
                                    <template x-if="previewPhotoUrl">
                                        <img :src="previewPhotoUrl" alt="Foto Siswa" class="w-full h-full object-cover rounded">
                                    </template>
                                    <template x-if="!previewPhotoUrl">
                                        @if($student->photo)
                                            <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" class="w-full h-full object-cover rounded">
                                        @else
                                            <div class="w-full h-full bg-slate-100 flex flex-col items-center justify-center text-slate-400">
                                                <i class="bi bi-person-fill text-2xl"></i>
                                                <span class="text-[8px] font-bold mt-0.5">3x4</span>
                                            </div>
                                        @endif
                                    </template>
                                </div>

                                <!-- Ringkasan Biodata di Kartu -->
                                <div class="space-y-0.5 text-[9px] leading-snug flex-1 min-w-0">
                                    <div>
                                        <div class="text-[7.5px] font-bold uppercase text-rose-200">Nama Lengkap Siswa</div>
                                        <div class="text-xs font-extrabold text-white truncate">{{ $student->name }}</div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-1 pt-0.5">
                                        <div>
                                            <div class="text-[7.5px] font-bold uppercase text-rose-200">NISN Siswa</div>
                                            <div class="font-mono font-bold text-[9px] text-white">{{ $student->nisn }}</div>
                                        </div>
                                        <div>
                                            <div class="text-[7.5px] font-bold uppercase text-rose-200">Rombel</div>
                                            <div class="font-bold text-[9px] text-amber-300">Kelas {{ $classroom->code ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="pt-0.5">
                                        <div class="text-[7.5px] font-bold uppercase text-rose-200">Jenis Kelamin</div>
                                        <div class="text-[9px] text-slate-100 font-semibold">{{ $student->gender == 'L' ? 'Laki-Laki' : 'Perempuan' }}</div>
                                    </div>
                                </div>

                                <!-- QR Code Otentikasi -->
                                <div class="w-12 aspect-square bg-white rounded-lg p-1 shrink-0 flex items-center justify-center shadow-xs">
                                    {!! $qrCodeSvg !!}
                                </div>
                            </div>

                            <!-- Barcode 1D Code 128 (NISN) -->
                            <div class="bg-white rounded-lg px-2 py-0.5 shadow-sm relative z-10 flex flex-col items-center">
                                <div class="w-full overflow-hidden flex justify-center max-h-6">
                                    {!! $barcodeSvg !!}
                                </div>
                                <div class="text-[7.5px] font-mono font-extrabold text-slate-800 tracking-widest -mt-0.5">
                                    * {{ $student->nisn }} *
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SISI BELAKANG KARTU -->
                    <div x-show="activeCardTab === 'back'" class="transition duration-300" style="display: none;">
                        <div class="w-full aspect-[85.6/53.98] rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-rose-950 text-white p-3.5 shadow-xl relative overflow-hidden flex flex-col justify-between border border-slate-700">
                            <!-- Background Accent -->
                            <div class="absolute -right-10 -top-10 w-36 h-36 rounded-full bg-rose-600/10 pointer-events-none blur-xl"></div>

                            <!-- Ketentuan Kartu -->
                            <div class="relative z-10">
                                <div class="text-[9px] font-extrabold text-rose-300 uppercase tracking-wider border-b border-white/10 pb-1">
                                    KETENTUAN PENGGUNAAN KARTU
                                </div>
                                <ol class="text-[7.5px] text-slate-300 space-y-0.5 mt-1 list-decimal list-inside leading-snug">
                                    <li>Kartu ini adalah identitas resmi siswa SDN Kebonagung.</li>
                                    <li>Wajib dibawa setiap hari untuk presensi gerbang otomatis.</li>
                                    <li>Dilarang mencoret, merusak, atau meminjamkan kartu.</li>
                                    <li>Bila kartu hilang, segera lapor ke pihak Tata Usaha.</li>
                                </ol>
                            </div>

                            <!-- Tanda Tangan Digital Kepala Sekolah -->
                            <div class="pt-1.5 border-t border-white/10 flex items-center justify-between text-[7.5px] relative z-10">
                                <div>
                                    <div class="text-[7px] text-slate-400">Diterbitkan di Kota Pasuruan</div>
                                    <div class="font-semibold text-slate-200">Berlaku Selama Menjadi Siswa Aktif</div>
                                </div>

                                <div class="text-right flex items-center gap-2">
                                    <div class="w-8 aspect-square bg-white rounded p-0.5 shrink-0">
                                        {!! $kepsekQrSvg !!}
                                    </div>
                                    <div class="text-right">
                                        <div class="text-[7px] text-rose-300 font-bold uppercase">Kepala Sekolah</div>
                                        <div class="font-extrabold text-white text-[8px]">{{ $kepsek['name'] ?? 'WAHYU AGUNG SETIYAWAN, S.Pd.' }}</div>
                                        <div class="text-[6.5px] font-mono text-slate-400">NIP. {{ $kepsek['nip'] ?? '198708222019031004' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-[11px] text-slate-500 text-center mt-3">
                    Kartu digital ini dapat disimpan atau difoto pada ponsel orang tua dan dapat langsung di-scan di scanner gerbang jika kartu fisik tertinggal.
                </p>
            </div>

            <!-- Card 4: Wali Murid & Akun Portal Orang Tua -->
            <div class="card p-5 border-slate-200 space-y-3">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5">
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                        <i class="bi bi-people-fill"></i>
                    </span>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">Data Wali Murid & Akun Portal</h4>
                        <p class="text-[10px] text-slate-500">Informasi wali murid yang terhubung dengan siswa ini</p>
                    </div>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-500 text-[11px]">Nama Wali Terdaftar:</span>
                        <span class="font-bold text-slate-900">{{ $user->name }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-500 text-[11px]">Hubungan Keluarga:</span>
                        <span class="font-semibold text-slate-800">{{ $student->guardian_relation ?? 'Orang Tua / Wali' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-500 text-[11px]">Kontak WhatsApp:</span>
                        <span class="font-mono font-bold text-emerald-700">{{ $student->guardian_phone ?? ($user->phone ?? 'Belum diisi') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span class="text-slate-500 text-[11px]">Username Login Portal:</span>
                        <span class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded text-[11px]">{{ $user->username }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
