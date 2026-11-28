<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - STAR-KIDS SDN Kebonagung</title>

    <!-- Google Font Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind & App Assets via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans">
    <div class="admin-app" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">
        <!-- Backdrop Mobile -->
        <div class="sidebar-backdrop" :class="{ 'show': sidebarOpen }" @click="sidebarOpen = false"></div>

        <!-- Sticky Header Glassmorphism -->
        <header class="admin-header flex items-center justify-between px-4 sm:px-6 shadow-xs border-b border-slate-200 bg-white/95 backdrop-blur-md">
            <div class="flex items-center gap-3">
                <!-- Mobile Menu Button -->
                <button type="button" @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition">
                    <i class="bi bi-list text-xl"></i>
                </button>

                <!-- Brand Logo & School Title -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo_sdnkebonagung.jpeg') }}" alt="Logo SDN Kebonagung" class="w-9 h-9 object-contain rounded-lg border border-slate-200 shadow-xs group-hover:scale-105 transition">
                    <div class="hidden sm:block">
                        <span class="block font-black text-sm tracking-tight text-slate-900 group-hover:text-rose-600 transition">STAR-KIDS</span>
                        <span class="block text-[11px] font-semibold text-slate-500">Sistem Tata Tertib & Apresiasi Karakter Digital SDN Kebonagung</span>
                    </div>
                </a>
            </div>

            <!-- Header Right: User Info & Actions -->
            <div class="flex items-center gap-3">
                <!-- Status Realtime Clock -->
                <div class="hidden md:flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-full text-xs font-semibold text-slate-600 border border-slate-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>WIB (UTC+7) Live</span>
                </div>

                <!-- User Profile Dropdown Pill -->
                <div class="flex items-center gap-2 pl-2">
                    <a href="{{ route('profile.show') }}" title="Lihat Profil Saya" class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-slate-100 transition group">
                        <div class="text-right hidden sm:block">
                            <div class="text-xs font-bold text-slate-800 leading-tight group-hover:text-rose-600 transition">{{ Auth::user()->name }}</div>
                            <div class="text-[11px] font-medium text-slate-500">
                                @if(Auth::user()->isAdmin())
                                    <span class="text-rose-600 font-bold"><i class="bi bi-shield-check"></i> Kepala Sekolah</span>
                                @elseif(Auth::user()->isOperator())
                                    <span class="text-indigo-600 font-bold"><i class="bi bi-gear-fill"></i> Operator Sekolah</span>
                                @elseif(Auth::user()->isGuru())
                                    @php
                                        $headerGuruClass = Auth::user()->homeroomClassroom?->code ?? (Auth::user()->additional_info['classroom'] ?? null);
                                    @endphp
                                    <span class="text-emerald-600 font-bold"><i class="bi bi-person-video3"></i> Guru / {{ $headerGuruClass ? 'Wali ' . $headerGuruClass : 'Pendidik' }}</span>
                                @elseif(Auth::user()->isOrangTua())
                                    <span class="text-amber-600 font-bold"><i class="bi bi-people-fill"></i> Wali Murid</span>
                                @endif
                            </div>
                        </div>

                        <div class="w-9 h-9 rounded-full bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 font-bold text-sm shadow-xs group-hover:scale-105 transition">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                    </a>

                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Keluar / Logout" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')">
                            <i class="bi bi-box-arrow-right text-lg"></i>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar" :class="{ 'show': sidebarOpen }">
            <div class="p-4 border-b border-slate-100 bg-slate-50/70">
                <div class="text-[10px] font-bold tracking-wider uppercase text-slate-400 mb-1">Akun Masuk</div>
                <div class="flex items-center gap-2">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span class="text-xs font-bold text-slate-700 truncate">{{ Auth::user()->role_label }}</span>
                </div>
                <div class="text-[11px] text-slate-500 font-mono mt-0.5">ID: {{ Auth::user()->identifier }}</div>
            </div>

            <nav class="sidebar-nav py-4 space-y-1">
                @if(Auth::user()->isAdmin())
                    <!-- Menu Kepala Sekolah -->
                    <div class="px-3 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Menu Eksekutif</div>
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Dasbor Eksekutif</span>
                    </a>

                    <!-- Tata Kelola Sistem (Eksklusif Kepala Sekolah) -->
                    <div class="px-3 pt-3 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tata Kelola & Keamanan</div>
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock-fill text-base mr-3 text-rose-500"></i>
                        <span class="sidebar-text">Manajemen User</span>
                        <span class="ml-auto text-[10px] bg-rose-50 text-rose-600 px-2 py-0.5 rounded-full font-bold">Admin</span>
                    </a>
                    <a href="{{ route('admin.logs.index') }}" class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text text-base mr-3 text-indigo-500"></i>
                        <span class="sidebar-text">Log Aktifitas Sistem</span>
                        <span class="ml-auto text-[10px] bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full font-bold">Audit</span>
                    </a>

                    <!-- Menu Operasional & Kesiswaan (Supervisi Kepala Sekolah) -->
                    <div class="px-3 pt-3 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Supervisi Operasional</div>
                    <a href="{{ route('gtk.index') }}" class="nav-link {{ request()->routeIs('gtk.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Master Data GTK</span>
                        <span class="ml-auto text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full font-bold">{{ $sidebarGtkCount ?? \App\Models\User::whereIn('role', ['guru/walikelas', 'operator', 'admin'])->count() }}</span>
                    </a>
                    <a href="{{ route('operator.students.index') }}" class="nav-link {{ request()->routeIs('operator.students.*') ? 'active' : '' }}">
                        <i class="bi bi-mortarboard-fill text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Master Data Siswa</span>
                        <span class="ml-auto text-[10px] bg-rose-50 text-rose-600 px-2 py-0.5 rounded-full font-bold">{{ $sidebarStudentCount ?? \App\Models\Student::count() }}</span>
                    </a>
                    <a href="{{ route('classrooms.index') }}" class="nav-link {{ request()->routeIs('classrooms.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3 text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Master Rombel</span>
                        <span class="ml-auto text-[10px] bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full font-bold">{{ $sidebarClassroomCount ?? \App\Models\Classroom::count() }}</span>
                    </a>
                    <a href="#" class="nav-link text-slate-400 cursor-not-allowed" title="Tahap Berikutnya">
                        <i class="bi bi-person-badge text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Cetak Kartu Siswa (CR-80)</span>
                        <span class="ml-auto text-[10px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded font-bold">Soon</span>
                    </a>
                    <a href="#" class="nav-link text-slate-400 cursor-not-allowed" title="Tahap Berikutnya">
                        <i class="bi bi-upc-scan text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Scanner Gerbang</span>
                        <span class="ml-auto text-[10px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded font-bold">Soon</span>
                    </a>

                    <!-- Menu Laporan & Audit -->
                    <div class="px-3 pt-3 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Laporan & Evaluasi</div>
                    <a href="#" class="nav-link text-slate-400 cursor-not-allowed" title="Tahap Berikutnya">
                        <i class="bi bi-pie-chart text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Analitik Kehadiran</span>
                        <span class="ml-auto text-[10px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded font-bold">Soon</span>
                    </a>
                    <a href="#" class="nav-link text-slate-400 cursor-not-allowed" title="Tahap Berikutnya">
                        <i class="bi bi-journal-check text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Laporan Bulanan & ISO</span>
                        <span class="ml-auto text-[10px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded font-bold">Soon</span>
                    </a>

                @elseif(Auth::user()->isOperator())
                    <!-- Menu Operator -->
                    <div class="px-3 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Menu Operator</div>
                    <a href="{{ route('operator.dashboard') }}" class="nav-link {{ request()->routeIs('operator.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2 text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Dasbor Operator</span>
                    </a>
                    <a href="{{ route('gtk.index') }}" class="nav-link {{ request()->routeIs('gtk.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Master Data GTK</span>
                        <span class="ml-auto text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full font-bold">{{ $sidebarGtkCount ?? \App\Models\User::whereIn('role', ['guru/walikelas', 'operator', 'admin'])->count() }}</span>
                    </a>
                    <a href="{{ route('operator.students.index') }}" class="nav-link {{ request()->routeIs('operator.students.*') ? 'active' : '' }}">
                        <i class="bi bi-mortarboard-fill text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Master Data Siswa</span>
                        <span class="ml-auto text-[10px] bg-rose-50 text-rose-600 px-2 py-0.5 rounded-full font-bold">{{ $sidebarStudentCount ?? \App\Models\Student::count() }}</span>
                    </a>
                    <a href="{{ route('classrooms.index') }}" class="nav-link {{ request()->routeIs('classrooms.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3 text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Master Rombel</span>
                        <span class="ml-auto text-[10px] bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full font-bold">{{ $sidebarClassroomCount ?? \App\Models\Classroom::count() }}</span>
                    </a>
                    <a href="#" class="nav-link text-slate-400 cursor-not-allowed" title="Tahap Berikutnya">
                        <i class="bi bi-person-badge text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Cetak Kartu Siswa (CR-80)</span>
                        <span class="ml-auto text-[10px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded font-bold">Soon</span>
                    </a>
                    <a href="#" class="nav-link text-slate-400 cursor-not-allowed" title="Tahap Berikutnya">
                        <i class="bi bi-upc-scan text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Scanner Gerbang</span>
                        <span class="ml-auto text-[10px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded font-bold">Soon</span>
                    </a>

                @elseif(Auth::user()->isGuru())
                    <!-- Menu Guru / Wali Kelas -->
                    @php
                        $sidebarGuruClass = Auth::user()->homeroomClassroom?->code ?? (Auth::user()->additional_info['classroom'] ?? null);
                    @endphp
                    <div class="px-3 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Menu Guru</div>
                    <a href="{{ route('guru.dashboard') }}" class="nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-mortarboard text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Dasbor {{ $sidebarGuruClass ? 'Kelas ' . $sidebarGuruClass : 'Guru' }}</span>
                    </a>
                    <a href="#" class="nav-link text-slate-400 cursor-not-allowed" title="Tahap Berikutnya">
                        <i class="bi bi-camera text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Scan Barcode Siswa</span>
                        <span class="ml-auto text-[10px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded font-bold">Soon</span>
                    </a>
                    <a href="#" class="nav-link text-slate-400 cursor-not-allowed" title="Tahap Berikutnya">
                        <i class="bi bi-chat-square-text text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Catatan Karakter & Tatib</span>
                        <span class="ml-auto text-[10px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded font-bold">Soon</span>
                    </a>

                @elseif(Auth::user()->isOrangTua())
                    <!-- Menu Orang Tua -->
                    <div class="px-3 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Portal Wali Murid</div>
                    <a href="{{ route('ortu.dashboard') }}" class="nav-link {{ request()->routeIs('ortu.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-door text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Dasbor Presensi Anak</span>
                    </a>
                    <a href="#" class="nav-link text-slate-400 cursor-not-allowed" title="Tahap Berikutnya">
                        <i class="bi bi-file-earmark-medical text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Pengajuan Izin / Sakit</span>
                        <span class="ml-auto text-[10px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded font-bold">Soon</span>
                    </a>
                    <a href="#" class="nav-link text-slate-400 cursor-not-allowed" title="Tahap Berikutnya">
                        <i class="bi bi-shield-shaded text-base mr-3 text-slate-400"></i>
                        <span class="sidebar-text">Kanal Pengaduan Aman</span>
                        <span class="ml-auto text-[10px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded font-bold">Soon</span>
                    </a>
                @endif

                <!-- Menu Profil Pengguna (Semua Role) -->
                <div class="px-3 pt-3 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Akun Saya</div>
                <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="bi bi-person-circle text-base mr-3 text-slate-400"></i>
                    <span class="sidebar-text">Lihat Profil Saya</span>
                </a>

                <div class="pt-6 px-3">
                    <div class="p-3 bg-rose-50/70 border border-rose-100 rounded-xl text-center">
                        <div class="text-[11px] font-bold text-rose-800">SDN Kebonagung</div>
                        <div class="text-[10px] text-rose-600 mt-0.5">Kota Pasuruan, Jawa Timur</div>
                        <div class="text-[9px] text-slate-500 mt-2 font-mono">v1.0-prod (Laravel 13)</div>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="admin-main p-4 sm:p-6 lg:p-8">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="mb-5 flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium shadow-xs">
                    <i class="bi bi-check-circle-fill text-emerald-600 text-lg flex-shrink-0"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 flex items-center gap-3 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-medium shadow-xs">
                    <i class="bi bi-exclamation-triangle-fill text-rose-600 text-lg flex-shrink-0"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-5 flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-sm font-medium shadow-xs">
                    <i class="bi bi-exclamation-circle-fill text-amber-600 text-lg flex-shrink-0"></i>
                    <div>{{ session('warning') }}</div>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-5 flex items-center gap-3 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl text-sm font-medium shadow-xs">
                    <i class="bi bi-info-circle-fill text-blue-600 text-lg flex-shrink-0"></i>
                    <div>{{ session('info') }}</div>
                </div>
            @endif

            <!-- View Yield -->
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
