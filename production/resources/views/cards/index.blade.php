@extends('layouts.app')

@section('title', 'Cetak Kartu Pelajar Digital ISO CR-80 - SDN Kebonagung')

@section('content')
<div class="space-y-6" x-data="{
    activeTab: 'front',
    selectedIds: [],
    selectAll: false,
    toggleSelectAll() {
        if (this.selectAll) {
            this.selectedIds = Array.from(document.querySelectorAll('.student-checkbox')).map(cb => cb.value);
        } else {
            this.selectedIds = [];
        }
    },
    updateSelectAll() {
        const total = document.querySelectorAll('.student-checkbox').length;
        this.selectAll = (this.selectedIds.length === total && total > 0);
    },
    printSelected() {
        if (this.selectedIds.length === 0) {
            alert('Silakan pilih minimal 1 siswa terlebih dahulu.');
            return;
        }
        window.open('{{ route('cards.print.bulk') }}?ids=' + this.selectedIds.join(','), '_blank');
    }
}">
    <!-- Header Section -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold mb-2">
                <i class="bi bi-person-badge-fill"></i> Standar Percetakan ISO CR-80 (85.60 x 53.98 mm)
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Cetak Kartu Siswa Digital</h1>
            <p class="text-xs text-slate-500 mt-1">
                Penerbitan kartu identitas resmi siswa SDN Kebonagung untuk pemindai barcode gerbang, kartu perpustakaan, dan verifikasi kesiswaan.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button type="button" @click="printSelected()" class="btn-secondary px-3 py-2 text-xs flex items-center gap-1.5 shadow-xs border border-rose-200 text-rose-700 hover:bg-rose-50">
                <i class="bi bi-printer-fill text-rose-600"></i>
                <span>Cetak Siswa Terpilih (<span x-text="selectedIds.length">0</span>)</span>
            </button>

            @if($selectedClassroomId && $selectedClassroomId !== 'all')
                <a href="{{ route('cards.print.bulk', ['classroom_id' => $selectedClassroomId]) }}" target="_blank" class="btn-primary px-4 py-2 text-xs flex items-center gap-2 shadow-xs">
                    <i class="bi bi-collection-fill"></i>
                    <span>Cetak 1 Rombel Ini</span>
                </a>
            @else
                <a href="{{ route('cards.print.bulk') }}" target="_blank" class="btn-primary px-4 py-2 text-xs flex items-center gap-2 shadow-xs">
                    <i class="bi bi-collection-fill"></i>
                    <span>Cetak Lembar A4 (Massal)</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Total Siswa Aktif</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900">{{ number_format($stats['total_students']) }}</div>
            <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">Siap Terbit Kartu</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Rombongan Belajar</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-indigo-600">{{ $stats['total_classes'] }} Kelas</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Tingkat 1 s/d 6</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Siswa Laki-Laki</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-blue-600">{{ number_format($stats['total_male']) }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Peserta Didik Putra</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Siswa Perempuan</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-pink-600">{{ number_format($stats['total_female']) }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Peserta Didik Putri</p>
        </div>
    </div>

    <!-- Main Grid: Left Table & Right Live Card Preview -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Column: Student Directory & Filter (7 Cols) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="card overflow-hidden">
                <div class="card-header flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Daftar Siswa untuk Dicetak</h2>
                            <p class="text-xs text-slate-500">Pilih siswa perorangan atau rombel untuk pratinjau dan cetak</p>
                        </div>
                    </div>

                    <!-- Filter Controls -->
                    <form action="{{ route('cards.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <div>
                            <select name="classroom_id" class="form-input text-xs w-full" onchange="this.form.submit()">
                                <option value="all">Semua Rombel ({{ $stats['total_classes'] }} Kelas)</option>
                                @foreach($classrooms as $c)
                                    <option value="{{ $c->id }}" {{ $selectedClassroomId == $c->id ? 'selected' : '' }}>
                                        Kelas {{ $c->code }} ({{ $c->total_students }} Siswa)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <select name="gender" class="form-input text-xs w-full" onchange="this.form.submit()">
                                <option value="">Semua Gender</option>
                                <option value="L" {{ $gender === 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                                <option value="P" {{ $gender === 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / NISN..." class="form-input text-xs w-full">
                            <button type="submit" class="btn-secondary px-3 py-2 text-xs flex-shrink-0" title="Cari">
                                <i class="bi bi-search"></i>
                            </button>
                            @if($search || $gender || ($selectedClassroomId && $selectedClassroomId !== 'all'))
                                <a href="{{ route('cards.index') }}" class="p-2 text-slate-400 hover:text-slate-600 text-xs flex-shrink-0" title="Reset Filter">
                                    <i class="bi bi-x-circle-fill text-base"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="w-10 text-center">
                                    <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500 cursor-pointer">
                                </th>
                                <th>Siswa & NISN</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center">L/P</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $s)
                                <tr class="{{ $previewStudent && $previewStudent->id === $s->id ? 'bg-rose-50/60 font-semibold' : '' }}">
                                    <td class="text-center">
                                        <input type="checkbox" value="{{ $s->id }}" x-model="selectedIds" @change="updateSelectAll()" class="student-checkbox rounded border-slate-300 text-rose-600 focus:ring-rose-500 cursor-pointer">
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2.5">
                                            @if($s->photo)
                                                <img src="{{ asset('storage/' . $s->photo) }}" alt="Foto {{ $s->name }}" class="w-8 h-8 rounded-full object-cover border border-slate-200">
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-600 font-bold text-xs flex items-center justify-center">
                                                    {{ strtoupper(substr($s->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('cards.index', array_merge(request()->query(), ['preview' => $s->id])) }}" class="font-bold text-slate-900 hover:text-rose-600 transition block text-xs">
                                                    {{ $s->name }}
                                                </a>
                                                <span class="font-mono text-[11px] text-slate-500">NISN: {{ $s->nisn }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="inline-block px-2 py-0.5 rounded text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            Kelas {{ $s->classroom->code ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $s->gender == 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                            {{ $s->gender }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="inline-flex items-center gap-1">
                                            <a href="{{ route('cards.index', array_merge(request()->query(), ['preview' => $s->id])) }}" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition" title="Lihat Kartu">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            <a href="{{ route('cards.print.single', $s) }}" target="_blank" class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition font-bold text-xs flex items-center gap-1" title="Cetak Satuan PVC">
                                                <i class="bi bi-printer-fill"></i> Cetak
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-slate-400 text-xs">
                                        Tidak ada siswa ditemukan sesuai kriteria filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($students->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Interactive CR-80 Live Card Preview (5 Cols) -->
        <div class="lg:col-span-5 sticky top-20 space-y-4">
            @if($previewStudent)
                <div class="card p-5 shadow-sm border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-rose-600 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                Live Preview ISO CR-80
                            </span>
                            <h3 class="text-sm font-bold text-slate-900 mt-1">Pratinjau Fisik Kartu Pelajar</h3>
                        </div>

                        <!-- Front/Back Switcher -->
                        <div class="inline-flex rounded-lg border border-slate-200 bg-slate-100 p-0.5 text-xs font-semibold">
                            <button type="button" @click="activeTab = 'front'" :class="activeTab === 'front' ? 'bg-white shadow-xs text-rose-600 font-bold' : 'text-slate-600'" class="px-2.5 py-1 rounded-md transition cursor-pointer">
                                Sisi Depan
                            </button>
                            <button type="button" @click="activeTab = 'back'" :class="activeTab === 'back' ? 'bg-white shadow-xs text-rose-600 font-bold' : 'text-slate-600'" class="px-2.5 py-1 rounded-md transition cursor-pointer">
                                Sisi Belakang
                            </button>
                        </div>
                    </div>

                    <!-- CARD CONTAINER (CR-80 Aspect Ratio ~ 1.586) -->
                    <div class="w-full max-w-[380px] mx-auto">
                        
                        <!-- SISI DEPAN KARTU -->
                        <div x-show="activeTab === 'front'" class="transition duration-300">
                            <div class="w-full aspect-[85.6/53.98] rounded-2xl bg-gradient-to-br from-rose-700 via-rose-800 to-slate-900 text-white p-4 shadow-xl relative overflow-hidden flex flex-col justify-between border border-rose-400/30">
                                <!-- Background Watermark Ornaments -->
                                <div class="absolute -right-8 -bottom-8 w-40 h-40 rounded-full bg-rose-500/10 pointer-events-none blur-xl"></div>
                                <div class="absolute -left-8 -top-8 w-32 h-32 rounded-full bg-amber-500/10 pointer-events-none blur-lg"></div>

                                <!-- Card Header -->
                                <div class="flex items-center gap-2.5 border-b border-white/20 pb-2 relative z-10">
                                    <img src="{{ asset('images/logo_sdnkebonagung.jpeg') }}" alt="Logo" class="w-9 h-9 object-contain bg-white rounded-lg p-0.5 shadow-xs shrink-0">
                                    <div class="leading-tight">
                                        <div class="text-[9px] font-extrabold uppercase tracking-wider text-rose-200">UPTD SDN KEBONAGUNG KOTA PASURUAN</div>
                                        <div class="text-xs font-black tracking-tight text-white uppercase">KARTU TANDA SISWA</div>
                                        <div class="text-[8px] text-rose-200/90 font-mono">NPSN: 20535412 &bull; Jl. Panglima Sudirman 120</div>
                                    </div>
                                </div>

                                <!-- Card Body: Photo & Identity -->
                                <div class="flex items-center gap-3 my-1 relative z-10">
                                    <!-- Photo 3x4 -->
                                    <div class="w-20 aspect-[3/4] bg-white/95 rounded-lg border-2 border-white/90 shadow-md overflow-hidden shrink-0 flex items-center justify-center p-0.5">
                                        @if($previewStudent->photo)
                                            <img src="{{ asset('storage/' . $previewStudent->photo) }}" alt="{{ $previewStudent->name }}" class="w-full h-full object-cover rounded">
                                        @else
                                            <div class="w-full h-full bg-slate-100 flex flex-col items-center justify-center text-slate-400">
                                                <i class="bi bi-person-fill text-3xl"></i>
                                                <span class="text-[8px] font-bold mt-0.5">3x4</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Biodata -->
                                    <div class="space-y-0.5 text-[10px] leading-snug flex-1">
                                        <div>
                                            <div class="text-[8px] font-bold uppercase text-rose-200">Nama Lengkap Siswa</div>
                                            <div class="text-xs font-extrabold text-white truncate">{{ $previewStudent->name }}</div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-1 pt-0.5">
                                            <div>
                                                <div class="text-[8px] font-bold uppercase text-rose-200">NISN Siswa</div>
                                                <div class="font-mono font-bold text-[10px] text-white">{{ $previewStudent->nisn }}</div>
                                            </div>
                                            <div>
                                                <div class="text-[8px] font-bold uppercase text-rose-200">Rombel</div>
                                                <div class="font-bold text-[10px] text-amber-300">Kelas {{ $previewStudent->classroom->code ?? '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="pt-0.5">
                                            <div class="text-[8px] font-bold uppercase text-rose-200">Jenis Kelamin</div>
                                            <div class="text-[10px] text-slate-100 font-semibold">{{ $previewStudent->gender == 'L' ? 'Laki-Laki' : 'Perempuan' }}</div>
                                        </div>
                                    </div>

                                    <!-- QR Code Otentikasi Siswa -->
                                    <div class="w-14 aspect-square bg-white rounded-lg p-1 shrink-0 flex items-center justify-center shadow-xs">
                                        {!! $qrCodeSvg !!}
                                    </div>
                                </div>

                                <!-- Card Footer: 1D Barcode (Code 128) -->
                                <div class="bg-white rounded-lg px-2 py-1 shadow-sm relative z-10 flex flex-col items-center">
                                    <div class="w-full overflow-hidden flex justify-center max-h-7">
                                        {!! $barcodeSvg !!}
                                    </div>
                                    <div class="text-[8px] font-mono font-extrabold text-slate-800 tracking-widest -mt-0.5">
                                        * {{ $previewStudent->nisn }} *
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SISI BELAKANG KARTU -->
                        <div x-show="activeTab === 'back'" class="transition duration-300" style="display: none;">
                            <div class="w-full aspect-[85.6/53.98] rounded-2xl bg-slate-900 text-white p-4 shadow-xl relative overflow-hidden flex flex-col justify-between border border-slate-700">
                                <!-- Watermark Background -->
                                <div class="absolute right-0 top-0 bottom-0 w-32 bg-rose-600/5 pointer-events-none"></div>

                                <!-- Header Ketentuan -->
                                <div class="border-b border-slate-700 pb-1.5 flex items-center justify-between">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-rose-400">KETENTUAN KARTU TANDA SISWA</span>
                                    <span class="text-[8px] text-slate-400 font-mono">EDISI 2024 - 2026</span>
                                </div>

                                <!-- Isi Ketentuan Tata Tertib -->
                                <div class="text-[8px] leading-relaxed text-slate-300 space-y-1 my-1">
                                    <div class="flex items-start gap-1.5">
                                        <span class="text-rose-400 font-bold">1.</span>
                                        <span>Kartu ini merupakan tanda pengenal sah siswa UPTD SDN Kebonagung.</span>
                                    </div>
                                    <div class="flex items-start gap-1.5">
                                        <span class="text-rose-400 font-bold">2.</span>
                                        <span>Wajib dibawa setiap hari efektif untuk presensi gerbang tap masuk & pulang.</span>
                                    </div>
                                    <div class="flex items-start gap-1.5">
                                        <span class="text-rose-400 font-bold">3.</span>
                                        <span>Jika kartu hilang atau rusak, segera laporkan ke Ruang Tata Usaha.</span>
                                    </div>
                                    <div class="flex items-start gap-1.5">
                                        <span class="text-rose-400 font-bold">4.</span>
                                        <span>Penemu kartu ini dimohon mengembalikan ke UPTD SDN Kebonagung, Jl. Panglima Sudirman 120 Kota Pasuruan.</span>
                                    </div>
                                </div>

                                <!-- Signature & Kepala Sekolah Info -->
                                <div class="border-t border-slate-700/80 pt-1.5 flex items-center justify-between text-[8px]">
                                    <div class="space-y-0.5">
                                        <div class="text-slate-400">Kota Pasuruan, 2024</div>
                                        <div class="text-slate-300 font-semibold">Kepala UPTD SDN Kebonagung,</div>
                                        <div class="font-extrabold text-white text-[9px] underline decoration-rose-500 pt-3">
                                            {{ $kepsek?->name ?? 'WAHYU AGUNG SETIYAWAN S.Pd' }}
                                        </div>
                                        <div class="font-mono text-slate-400 text-[7.5px]">NIP. {{ $kepsek?->nip_nuptk ?? '198708222019031004' }}</div>
                                    </div>

                                    <!-- QR Code TTE Kepala Sekolah -->
                                    <div class="flex flex-col items-center">
                                        <div class="w-11 aspect-square bg-white rounded p-0.5 shadow-xs">
                                            {!! $kepsekQrSvg !!}
                                        </div>
                                        <span class="text-[6.5px] font-mono text-emerald-400 mt-0.5 font-bold">TTE TERVALIDASI</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons for Selected Student -->
                    <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        <div class="text-xs text-slate-500 truncate">
                            Siswa: <span class="font-bold text-slate-800">{{ $previewStudent->name }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('cards.print.single', $previewStudent) }}" target="_blank" class="btn-primary px-3 py-1.5 text-xs flex items-center gap-1.5 shadow-xs">
                                <i class="bi bi-printer-fill"></i> Cetak Kartu Ini (PVC)
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="card p-8 text-center text-slate-400">
                    <i class="bi bi-person-badge text-4xl mb-2 text-slate-300"></i>
                    <p class="text-xs">Pilih siswa di tabel sebelah kiri untuk memuat pratinjau kartu.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
