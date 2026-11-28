<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk Portal - STAR-KIDS SDN Kebonagung</title>

    <!-- Google Font Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind CSS v4 & Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-50 via-rose-50/25 to-slate-100 min-h-screen flex items-center justify-center p-4 font-sans text-slate-800">

    <div class="w-full max-w-md my-8">
        <!-- Logo & Header Brand Resmi -->
        <div class="text-center mb-6">
            <div class="inline-flex p-2 bg-white rounded-2xl shadow-sm border border-slate-200/90 mb-3 group hover:scale-105 transition-transform duration-200">
                <img src="{{ asset('images/logo_sdnkebonagung.jpeg') }}" alt="Logo SDN Kebonagung" class="w-16 h-16 object-contain rounded-xl">
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">STAR-KIDS</h1>
            <p class="text-xs font-bold text-rose-600 mt-1.5 max-w-sm mx-auto leading-relaxed">
                Sistem Tata Tertib & Apresiasi Karakter Terintegrasi Digital SDN Kebonagung
            </p>
            <p class="text-[11px] text-slate-500 mt-1 font-medium">Kota Pasuruan - Jawa Timur</p>
        </div>

        <!-- Main Card Form Login -->
        <div class="bg-white border border-slate-200/90 rounded-2xl shadow-sm p-6 sm:p-8">
            <div class="mb-5 border-b border-slate-100 pb-4">
                <h2 class="text-lg font-extrabold text-slate-900">Masuk ke Portal</h2>
                <p class="text-xs text-slate-500 mt-0.5">Silakan masukkan identitas resmi Anda</p>
            </div>

            <!-- Flash & Error Alerts -->
            @if(session('info'))
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl text-xs flex items-center gap-2">
                    <i class="bi bi-info-circle-fill text-blue-600 text-sm"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-4 p-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-xs flex items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-amber-600 text-sm"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs">
                    <div class="font-bold flex items-center gap-1.5 mb-1.5">
                        <i class="bi bi-exclamation-circle-fill text-rose-600 text-sm"></i> Gagal Masuk:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-4" id="loginForm">
                @csrf

                <!-- Input Identifier -->
                <div>
                    <label for="identifier" class="block text-xs font-bold text-slate-700 mb-1.5">
                        NIP / NUPTK / NISN Siswa <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-person-badge text-base"></i>
                        </span>
                        <input type="text" id="identifier" name="identifier" value="{{ old('identifier') }}" required autofocus
                            placeholder="Masukkan NIP, NUPTK, atau NISN Siswa"
                            class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Gunakan NIP/NUPTK (Guru/Staff) atau NISN Siswa (Orang Tua).</p>
                </div>

                <!-- Input Password -->
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-shield-lock text-base"></i>
                        </span>
                        <input type="password" id="password" name="password" required
                            placeholder="Masukkan kata sandi akun"
                            class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition" title="Lihat kata sandi">
                            <i class="bi bi-eye text-base" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Kata sandi awal sama dengan nomor NIP/NUPTK atau NISN.</p>
                </div>

                <!-- Captcha Penjumlahan Sederhana -->
                <div class="p-3.5 bg-slate-50/90 border border-slate-200 rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                        <label for="captcha" class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <i class="bi bi-calculator-fill text-rose-600"></i> Verifikasi Penjumlahan <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" onclick="refreshCaptcha()" title="Ganti soal penjumlahan" class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 flex items-center gap-1 cursor-pointer">
                            <i class="bi bi-arrow-clockwise"></i> Acak Soal
                        </button>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Math Box Display -->
                        <div class="px-4 py-2 bg-white border border-rose-200 text-rose-700 font-black text-base rounded-lg tracking-wider shadow-2xs select-none" id="captchaBox">
                            {{ $num1 }} + {{ $num2 }} = ?
                        </div>

                        <!-- Answer Input Box -->
                        <div class="flex-1">
                            <input type="number" id="captcha" name="captcha" required
                                placeholder="Tulis jawaban..."
                                class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-center text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1.5">Hitung hasil penjumlahan di atas untuk verifikasi keamanan.</p>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600 select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 border-slate-300">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                    class="w-full py-3 px-4 bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg transition-all duration-150 flex items-center justify-center gap-2 cursor-pointer">
                    <span>Masuk ke Sistem</span>
                    <i class="bi bi-arrow-right font-bold text-base"></i>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-xs text-slate-400">
            &copy; 2026 SDN Kebonagung Kota Pasuruan. All rights reserved.
        </div>
    </div>

    <script>
        // Toggle password show/hide
        function togglePasswordVisibility() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        // Refresh Captcha via AJAX
        function refreshCaptcha() {
            fetch('{{ route("captcha.refresh") }}')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('captchaBox').innerText = `${data.num1} + ${data.num2} = ?`;
                    document.getElementById('captcha').value = '';
                    document.getElementById('captcha').focus();
                })
                .catch(err => {
                    console.error('Gagal merefresh captcha:', err);
                });
        }
    </script>
</body>
</html>
