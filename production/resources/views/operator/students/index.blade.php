@extends('layouts.app')

@section('title', 'Master Data Siswa & Mutasi Rombel')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold mb-2">
                <i class="bi bi-people-fill"></i> Manajemen Kesiswaan Dapodik
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Master Direktori Siswa</h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola data pokok 791 siswa, mutasi 28 rombongan belajar, dan verifikasi kontak orang tua SDN Kebonagung.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('cards.index') }}" class="px-3 py-2 rounded-xl border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center gap-1.5 shadow-2xs transition" title="Buka Modul Cetak Kartu Siswa CR-80">
                <i class="bi bi-person-badge-fill text-indigo-600 text-sm"></i>
                <span>Cetak Kartu (CR-80)</span>
            </a>
            <a href="{{ route('operator.students.export.excel', request()->query()) }}" class="px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold flex items-center gap-1.5 shadow-2xs transition" title="Unduh Berkas Excel (.xlsx)">
                <i class="bi bi-file-earmark-excel-fill text-emerald-600 text-sm"></i>
                <span>Unduh Excel</span>
            </a>
            <a href="{{ route('operator.students.export.pdf', request()->query()) }}" class="px-3 py-2 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold flex items-center gap-1.5 shadow-2xs transition" title="Unduh Laporan PDF Resmi">
                <i class="bi bi-file-earmark-pdf-fill text-rose-600 text-sm"></i>
                <span>Unduh PDF</span>
            </a>
            <a href="{{ route('operator.students.create') }}" class="btn-primary px-4 py-2 text-xs flex items-center gap-2 cursor-pointer shadow-xs">
                <i class="bi bi-person-plus-fill"></i>
                <span>Tambah Siswa Baru</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Siswa Terdaftar Aktif</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900">{{ number_format($stats['total_active']) }}</div>
            <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">Sinkron Basis Data</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Siswa Laki-Laki</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-blue-600">{{ number_format($stats['total_male']) }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Peserta Didik</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Siswa Perempuan</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-pink-600">{{ number_format($stats['total_female']) }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Peserta Didik</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Total Rombel</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-indigo-600">{{ $stats['total_classes'] }} Kelas</div>
            <p class="text-[11px] text-indigo-600 font-semibold mt-0.5">1A-1D & 2A-6E (Resmi)</p>
        </div>
    </div>

    <!-- Filter & Search Toolbar Card -->
    <div class="card p-5">
        <form action="{{ route('operator.students.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Text -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Cari Nama / NISN / Wali</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ $filters['search'] }}" 
                        placeholder="Ketik nama siswa atau NISN..." 
                        class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                </div>
            </div>

            <!-- Filter Rombel -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Filter Rombel (28 Kelas)</label>
                <select name="classroom_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    <option value="all">Semua Rombel</option>
                    @foreach($classrooms as $c)
                        <option value="{{ $c->id }}" {{ $filters['classroom_id'] == $c->id ? 'selected' : '' }}>
                            {{ $c->code }} - {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Status Kesiswaan</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    <option value="all" {{ $filters['status'] == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="active" {{ $filters['status'] == 'active' ? 'selected' : '' }}>Aktif Belajar</option>
                    <option value="moved" {{ $filters['status'] == 'moved' ? 'selected' : '' }}>Mutasi / Pindah</option>
                    <option value="graduated" {{ $filters['status'] == 'graduated' ? 'selected' : '' }}>Lulus / Alumni</option>
                    <option value="inactive" {{ $filters['status'] == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary w-full py-2 px-3 text-xs flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="bi bi-funnel-fill"></i> Terapkan
                </button>
                @if($filters['search'] || $filters['classroom_id'] !== 'all' || $filters['status'] !== 'active')
                    <a href="{{ route('operator.students.index') }}" title="Reset Filter" class="p-2 border border-slate-200 rounded-xl text-slate-500 hover:text-rose-600 hover:bg-slate-50 transition">
                        <i class="bi bi-x-circle text-base"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Interactive Students Table -->
    <div class="card overflow-hidden">
        <div class="card-header flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Daftar Siswa SDN Kebonagung</h2>
                <p class="text-xs text-slate-500">Menampilkan {{ $students->firstItem() ?? 0 }} - {{ $students->lastItem() ?? 0 }} dari total {{ $students->total() }} siswa</p>
            </div>
            <span class="text-xs font-semibold text-slate-500">
                Paginasi Halaman {{ $students->currentPage() }} dari {{ $students->lastPage() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>NISN & NIS</th>
                        <th>Nama Siswa</th>
                        <th class="text-center">Gender</th>
                        <th>Rombel Kelas</th>
                        <th>Wali Murid & Kontak WA</th>
                        <th class="text-center">Poin Tatib</th>
                        <th class="text-center">Status</th>
                        <th class="text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $idx => $s)
                        <tr>
                            <td class="text-center font-bold text-slate-400 text-xs">
                                {{ $students->firstItem() + $idx }}
                            </td>
                            <td>
                                <div class="font-mono text-xs font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded inline-block">
                                    {{ $s->nisn }}
                                </div>
                                @if($s->nis)
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">NIS: {{ $s->nis }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    @if($s->photo)
                                        <img src="{{ asset('storage/' . $s->photo) }}" alt="{{ $s->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 shadow-2xs flex-shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 text-slate-600 font-bold text-xs flex items-center justify-center flex-shrink-0">
                                            {{ strtoupper(substr($s->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $s->name }}</div>
                                        <div class="text-[11px] text-slate-400">
                                            {{ $s->birth_place ? $s->birth_place . ', ' : '' }}{{ $s->birth_date ? $s->birth_date->translatedFormat('d M Y') : '-' }} &bull; <span class="font-semibold text-slate-600">{{ $s->religion ?? 'Islam' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $s->gender == 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                    {{ $s->gender }}
                                </span>
                            </td>
                            <td>
                                <span class="px-2.5 py-1 bg-indigo-50 border border-indigo-200 text-indigo-700 font-extrabold text-xs rounded-lg">
                                    Kelas {{ $s->classroom->code ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="font-semibold text-slate-800">{{ $s->guardian_name ?? 'Belum terdata' }}</div>
                                @if($s->guardian_phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $s->guardian_phone) }}" target="_blank" class="text-[11px] text-emerald-600 hover:text-emerald-700 font-medium flex items-center gap-1 mt-0.5">
                                        <i class="bi bi-whatsapp"></i> {{ $s->guardian_phone }}
                                    </a>
                                @else
                                    <span class="text-[10px] text-slate-400">No WA belum diisi</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="font-mono text-xs font-bold px-2 py-0.5 rounded-full {{ $s->point_balance >= 80 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $s->point_balance }} Pt
                                </span>
                            </td>
                            <td class="text-center">
                                @if($s->status === 'active')
                                    <span class="status-badge status-active">
                                        <i class="bi bi-check-circle-fill"></i> Aktif
                                    </span>
                                @elseif($s->status === 'moved')
                                    <span class="status-badge status-pending">
                                        <i class="bi bi-arrow-left-right"></i> Mutasi
                                    </span>
                                @else
                                    <span class="status-badge status-cancelled">
                                        <i class="bi bi-x-circle-fill"></i> {{ ucfirst($s->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Detail Modal Trigger -->
                                    <button type="button" 
                                        onclick="showStudentDetail({{ json_encode([
                                            'id' => $s->id,
                                            'name' => $s->name,
                                            'nisn' => $s->nisn,
                                            'nis' => $s->nis ?? '-',
                                            'gender' => $s->gender == 'L' ? 'Laki-Laki' : 'Perempuan',
                                            'religion' => $s->religion ?? 'Islam',
                                            'classroom' => $s->classroom->code ?? '-',
                                            'homeroom' => $s->classroom->homeroom_teacher ?? '-',
                                            'birth_place' => $s->birth_place ?? '-',
                                            'birth_date' => $s->birth_date ? $s->birth_date->translatedFormat('d F Y') : '-',
                                            'address' => $s->address ?? 'Alamat belum diisi',
                                            'guardian_name' => $s->guardian_name ?? '-',
                                            'guardian_relation' => $s->guardian_relation ?? 'Orang Tua / Wali',
                                            'guardian_phone' => $s->guardian_phone ?? '-',
                                            'point_balance' => $s->point_balance,
                                            'status' => ucfirst($s->status),
                                            'photo' => $s->photo ? asset('storage/' . $s->photo) : null,
                                            'edit_url' => route('operator.students.edit', $s->id),
                                            'card_url' => route('cards.print.single', $s->id),
                                        ]) }})"
                                        title="Lihat Detail Siswa"
                                        class="p-1.5 bg-slate-100 hover:bg-slate-200 hover:text-slate-900 rounded-lg transition text-slate-600">
                                        <i class="bi bi-eye text-sm"></i>
                                    </button>

                                    <!-- Edit & Mutasi Button -->
                                    <a href="{{ route('operator.students.edit', $s->id) }}" title="Edit Data & Mutasi Rombel" 
                                        class="p-1.5 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition text-slate-600">
                                        <i class="bi bi-pencil-square text-sm"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <form action="{{ route('operator.students.destroy', $s->id) }}" method="POST" class="inline" 
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa {{ $s->name }}? Data kartu & riwayat akan dihapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Siswa" class="p-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 rounded-lg transition text-slate-600">
                                            <i class="bi bi-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-10 text-slate-400">
                                <i class="bi bi-people text-3xl block mb-2 text-slate-300"></i>
                                Tidak ada data siswa yang cocok dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($students->hasPages())
            <div class="card-footer p-4 border-t border-slate-100 flex items-center justify-between">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Detail Biodata Siswa -->
<div id="studentDetailModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i class="bi bi-person-vcard text-lg"></i>
                </span>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Biodata Lengkap Peserta Didik</h3>
                    <p class="text-[11px] text-slate-500">Buku Induk & Basis Data Siswa SDN Kebonagung</p>
                </div>
            </div>
            <button type="button" onclick="closeStudentModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
            <!-- Profile Overview Card -->
            <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                <div id="mPhotoContainer" class="w-16 h-16 rounded-full overflow-hidden border-2 border-white shadow-xs flex-shrink-0 bg-rose-100 text-rose-700 font-extrabold text-xl flex items-center justify-center">
                    <span id="mAvatarInitial">S</span>
                    <img id="mAvatarImg" src="" alt="Foto Siswa" class="w-full h-full object-cover hidden">
                </div>
                <div class="min-w-0 flex-1">
                    <h4 id="mName" class="text-base font-extrabold text-slate-900 truncate">Nama Siswa</h4>
                    <div class="flex items-center gap-2 mt-1">
                        <span id="mClassroom" class="px-2 py-0.5 rounded-md bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold">Kelas 1A</span>
                        <span id="mStatusBadge" class="status-badge status-active text-[10px]">Aktif</span>
                    </div>
                    <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                        <i class="bi bi-person-badge text-slate-400"></i>
                        <span id="mHomeroom" class="truncate">Wali Kelas: -</span>
                    </div>
                </div>
            </div>

            <!-- Detail Grid -->
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="p-3 rounded-xl bg-white border border-slate-200/70">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">NISN Resmi</span>
                    <span id="mNisn" class="font-mono font-extrabold text-slate-900 text-sm mt-0.5 block">0012345678</span>
                </div>
                <div class="p-3 rounded-xl bg-white border border-slate-200/70">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nomor Induk (NIS)</span>
                    <span id="mNis" class="font-mono font-bold text-slate-700 mt-0.5 block">-</span>
                </div>
                <div class="p-3 rounded-xl bg-white border border-slate-200/70">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jenis Kelamin</span>
                    <span id="mGender" class="font-semibold text-slate-800 mt-0.5 block">Laki-Laki</span>
                </div>
                <div class="p-3 rounded-xl bg-white border border-slate-200/70">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Agama</span>
                    <span id="mReligion" class="font-semibold text-slate-800 mt-0.5 block">Islam</span>
                </div>
                <div class="col-span-2 p-3 rounded-xl bg-white border border-slate-200/70">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tempat & Tanggal Lahir</span>
                    <span id="mTtl" class="font-semibold text-slate-800 mt-0.5 block">Pasuruan, 12 Januari 2017</span>
                </div>
                <div class="col-span-2 p-3 rounded-xl bg-white border border-slate-200/70">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Alamat Domisili Siswa</span>
                    <span id="mAddress" class="font-medium text-slate-700 mt-0.5 block">Jl. Kebonagung No. 1</span>
                </div>
            </div>

            <!-- Guardian & Parent Portal Info -->
            <div class="p-4 rounded-xl bg-emerald-50/50 border border-emerald-200/70 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-900 flex items-center gap-1.5">
                        <i class="bi bi-people-fill text-emerald-600"></i> Data Wali Murid & Akun Portal
                    </span>
                    <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">Login: NISN</span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                    <div>
                        <span class="text-[10px] text-slate-500 block">Nama Wali:</span>
                        <span id="mGuardianName" class="font-bold text-slate-800 block">-</span>
                        <span id="mGuardianRelation" class="text-[10px] text-slate-500">Orang Tua</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-500 block">Kontak WhatsApp:</span>
                        <div id="mGuardianPhoneWrapper" class="mt-0.5">
                            <span id="mGuardianPhone" class="font-bold text-slate-800">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-wrap items-center justify-between gap-2">
            <button type="button" onclick="closeStudentModal()" class="btn-outline text-xs px-4 py-2">
                Tutup
            </button>
            <div class="flex items-center gap-2">
                <a id="mCardBtn" href="#" target="_blank" class="btn-primary text-xs px-3.5 py-2 flex items-center gap-1.5 shadow-xs">
                    <i class="bi bi-person-badge-fill"></i>
                    <span>Cetak Kartu Siswa</span>
                </a>
                <a id="mEditBtn" href="#" class="btn-outline-primary text-xs px-3.5 py-2 flex items-center gap-1.5">
                    <i class="bi bi-pencil-square"></i>
                    <span>Edit / Mutasi</span>
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showStudentDetail(data) {
    document.getElementById('mName').textContent = data.name;
    document.getElementById('mNisn').textContent = data.nisn;
    document.getElementById('mNis').textContent = data.nis;
    document.getElementById('mGender').textContent = data.gender;
    document.getElementById('mReligion').textContent = data.religion;
    document.getElementById('mClassroom').textContent = 'Kelas ' + data.classroom;
    document.getElementById('mHomeroom').textContent = 'Wali Kelas: ' + data.homeroom;
    document.getElementById('mTtl').textContent = data.birth_place + ', ' + data.birth_date;
    document.getElementById('mAddress').textContent = data.address;
    document.getElementById('mGuardianName').textContent = data.guardian_name;
    document.getElementById('mGuardianRelation').textContent = data.guardian_relation;
    document.getElementById('mStatusBadge').textContent = data.status;

    // Contact link
    const phoneWrapper = document.getElementById('mGuardianPhoneWrapper');
    if (data.guardian_phone && data.guardian_phone !== '-') {
        const cleanPhone = data.guardian_phone.replace(/[^0-9]/g, '');
        phoneWrapper.innerHTML = `<a href="https://wa.me/${cleanPhone}" target="_blank" class="text-xs text-emerald-600 hover:text-emerald-700 font-bold flex items-center gap-1"><i class="bi bi-whatsapp"></i> ${data.guardian_phone}</a>`;
    } else {
        phoneWrapper.innerHTML = `<span class="text-xs text-slate-400">Belum terdata</span>`;
    }

    // Photo
    const avatarImg = document.getElementById('mAvatarImg');
    const avatarInitial = document.getElementById('mAvatarInitial');
    if (data.photo) {
        avatarImg.src = data.photo;
        avatarImg.classList.remove('hidden');
        avatarInitial.classList.add('hidden');
    } else {
        avatarImg.classList.add('hidden');
        avatarInitial.classList.remove('hidden');
        avatarInitial.textContent = data.name.charAt(0).toUpperCase();
    }

    // Action links
    document.getElementById('mEditBtn').href = data.edit_url;
    document.getElementById('mCardBtn').href = data.card_url;

    // Show modal
    document.getElementById('studentDetailModal').classList.remove('hidden');
}

function closeStudentModal() {
    document.getElementById('studentDetailModal').classList.add('hidden');
}

// Close when clicking outside modal box
document.getElementById('studentDetailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStudentModal();
    }
});
</script>
@endpush
@endsection
