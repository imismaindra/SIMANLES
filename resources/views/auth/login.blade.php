<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk Sistem — LKP Sang Siroju Lillah</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    @fonts

    <!-- Typography: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }
        body, input, button, select, textarea {
            font-family: var(--font-sans);
        }
        html, body {
            overflow-x: hidden;
            width: 100%;
        }
        * {
            -webkit-tap-highlight-color: transparent;
        }
        /* Smooth subtle transitions */
        *, *::before, *::after {
            transition-property: background-color, border-color, color, fill, stroke;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }
    </style>

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();

        function toggleDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateThemeIcons();
        }

        function updateThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            const sunIcons = document.querySelectorAll('.theme-icon-sun');
            const moonIcons = document.querySelectorAll('.theme-icon-moon');
            sunIcons.forEach(el => el.classList.toggle('hidden', !isDark));
            moonIcons.forEach(el => el.classList.toggle('hidden', isDark));
        }

        document.addEventListener('DOMContentLoaded', updateThemeIcons);
    </script>
</head>

<body class="min-h-full min-h-dvh m-0 p-0 antialiased bg-[#f6f8fa] dark:bg-[#090d16] text-slate-900 dark:text-slate-100 selection:bg-slate-900 selection:text-white dark:selection:bg-white dark:selection:text-slate-950 flex flex-col justify-between relative">

    <!-- Ambient Subtle Glow for Depth (Quiet & Atmospheric) -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden select-none">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[42rem] h-[24rem] bg-gradient-to-b from-blue-500/10 dark:from-blue-600/10 via-indigo-500/5 to-transparent blur-3xl rounded-full"></div>
    </div>

    <!-- Top Navigation Utility (Theme Switcher) -->
    <header class="w-full max-w-6xl mx-auto px-4 sm:px-6 pt-4 sm:pt-6 flex items-center justify-between relative z-10">
        <!-- Academic Pill Indicator -->
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-[11px] font-medium text-slate-600 dark:text-slate-400 shadow-2xs">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>T.A. 2026/2027 &bull; Ponokawan, Sidoarjo</span>
        </div>

        <!-- Sleek Theme Toggle Pill -->
        <button type="button" onclick="toggleDarkMode()"
            class="min-h-[38px] px-3 py-1.5 rounded-full border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 active:scale-95 transition-all shadow-2xs flex items-center gap-2"
            aria-label="Ganti tema tampilan">
            <svg class="theme-icon-sun w-3.5 h-3.5 hidden text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg class="theme-icon-moon w-3.5 h-3.5 text-slate-700 dark:text-slate-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <span class="text-xs font-semibold">Tema</span>
        </button>
    </header>

    <!-- Main Workspace: Precision Centered Luxury Auth Card -->
    <main class="w-full max-w-[430px] mx-auto px-4 py-6 sm:py-8 my-auto relative z-10">
        
        <!-- Luxury Authentication Card -->
        <div class="rounded-3xl bg-white dark:bg-[#101625] border border-slate-200/80 dark:border-slate-800/90 p-6 sm:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04),0_1px_3px_rgb(0,0,0,0.02)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.45)]">
            
            <!-- Brand Crest & Header -->
            <div class="text-center mb-6">
                <!-- Official Logo Emblem Container -->
                <div class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200/90 dark:border-slate-700/80 p-2.5 mx-auto mb-3.5 flex items-center justify-center shadow-xs">
                    <img src="{{ asset('logoo.png') }}" alt="Logo LKP Sang Siroju Lillah" class="w-full h-full object-contain filter drop-shadow-xs">
                </div>
                
                <span class="block text-[11px] font-bold tracking-widest uppercase text-slate-500 dark:text-slate-400">
                    LKP Sang Siroju Lillah
                </span>
                
                <h1 class="text-2xl sm:text-[26px] font-extrabold tracking-tight text-slate-900 dark:text-white mt-1">
                    Masuk ke Portal
                </h1>
                
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    SIMANLES &bull; Presensi, Jadwal & Administrasi
                </p>
            </div>

            <!-- Sleek Native Segmented Control for Evaluator Roles -->
            <div class="mb-5">
                <div class="p-1 rounded-xl bg-slate-100 dark:bg-slate-900/90 border border-slate-200/60 dark:border-slate-800/60 grid grid-cols-3 gap-1">
                    <button type="button" onclick="setRole('pusat')" id="btn-role-pusat"
                        class="role-btn min-h-[36px] py-1.5 px-1 rounded-lg font-bold text-center text-xs transition-all bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs flex items-center justify-center">
                        <span>Admin Pusat</span>
                    </button>
                    <button type="button" onclick="setRole('cabang')" id="btn-role-cabang"
                        class="role-btn min-h-[36px] py-1.5 px-1 rounded-lg font-medium text-center text-xs transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center justify-center">
                        <span>Pimp. Cabang</span>
                    </button>
                    <button type="button" onclick="setRole('guru')" id="btn-role-guru"
                        class="role-btn min-h-[36px] py-1.5 px-1 rounded-lg font-medium text-center text-xs transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center justify-center">
                        <span>Guru Pengajar</span>
                    </button>
                </div>
                
                <!-- Role Scope Dynamic Indicator (Quiet & Informative) -->
                <p id="role-desc" class="text-[11px] text-slate-500 dark:text-slate-400 text-center mt-2 leading-relaxed font-medium">
                    Admin Pusat &bull; Akses multi-cabang, tarif master, jadwal & audit.
                </p>
            </div>

            <!-- Session Feedback Messages -->
            @if(session('success'))
                <div class="mb-4 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/80 text-emerald-800 dark:text-emerald-300 text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="mb-4 p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200/80 dark:border-rose-800/80 text-rose-800 dark:text-rose-300 text-xs">
                    <div class="flex items-center gap-1.5 font-bold mb-1">
                        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Gagal Masuk</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 pl-1 text-[11px]">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Authentic Login Form -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Identifier Field (Email / NIP) -->
                <div>
                    <label for="identifier" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Email Dinas atau NIP
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="text" name="identifier" id="identifier" value="{{ old('identifier', 'admin@lespintar.id') }}" required autofocus
                            autocomplete="username"
                            autocapitalize="none"
                            autocorrect="off"
                            spellcheck="false"
                            placeholder="nama@lespintar.id atau NIP"
                            class="w-full h-11 pl-10 pr-3.5 py-2 rounded-xl border border-slate-300/80 dark:border-slate-700/80 bg-white dark:bg-[#0c101c] text-slate-900 dark:text-slate-100 text-base sm:text-sm placeholder:text-slate-400 dark:placeholder:text-slate-600 focus:outline-none focus:border-slate-900 dark:focus:border-white focus:ring-3 focus:ring-slate-900/5 dark:focus:ring-white/5 transition-all shadow-2xs">
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                            Kata Sandi
                        </label>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">
                            Guru: YYYY-MM-DD
                        </span>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" name="password" id="password" value="admin123" required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full h-11 pl-10 pr-11 py-2 rounded-xl border border-slate-300/80 dark:border-slate-700/80 bg-white dark:bg-[#0c101c] text-slate-900 dark:text-slate-100 text-base sm:text-sm placeholder:text-slate-400 dark:placeholder:text-slate-600 focus:outline-none focus:border-slate-900 dark:focus:border-white focus:ring-3 focus:ring-slate-900/5 dark:focus:ring-white/5 transition-all shadow-2xs">
                        
                        <!-- Toggle Password Button (Ergonomic Touch Target) -->
                        <button type="button" onclick="togglePasswordVisibility()"
                            class="absolute inset-y-0 right-0 w-11 h-11 flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 active:scale-95 transition-all"
                            aria-label="Tampilkan atau sembunyikan kata sandi">
                            <svg id="eye-icon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Option -->
                <div class="pt-0.5">
                    <label class="inline-flex items-center gap-2.5 cursor-pointer select-none text-xs text-slate-600 dark:text-slate-400">
                        <input type="checkbox" name="remember" value="1" class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-slate-900 focus:ring-slate-900 dark:bg-slate-900">
                        <span>Ingat sesi masuk peramban ini</span>
                    </label>
                </div>

                <!-- Submit Button (High-End & Confident) -->
                <button type="submit"
                    class="group relative w-full h-11 mt-1 rounded-xl bg-slate-900 hover:bg-slate-800 active:bg-black text-white dark:bg-white dark:hover:bg-slate-100 dark:active:bg-slate-200 dark:text-slate-950 font-semibold text-sm transition-all duration-150 shadow-sm active:scale-[0.99] flex items-center justify-center gap-2">
                    <span>Masuk ke Portal Layanan</span>
                    <svg class="w-4 h-4 transition-transform duration-150 group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>

            <!-- Quiet Institutional Integrity Seal -->
            <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800/80 text-center">
                <p class="text-[11px] text-slate-400 dark:text-slate-500 flex items-center justify-center gap-1.5 font-medium">
                    <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>Sesi Terenkripsi &bull; Server Pusat Ponokawan</span>
                </p>
            </div>
        </div>
    </main>

    <!-- Bottom Institutional Footer (Mobile Safe) -->
    <footer class="text-center text-xs text-slate-400 dark:text-slate-500 py-4 pb-[max(1rem,env(safe-area-inset-bottom))] relative z-10">
        <p class="font-medium">&copy; {{ date('Y') }} LKP Sang Siroju Lillah. Hak cipta dilindungi.</p>
        <p class="mt-0.5 text-[11px] text-slate-400/80">SIMANLES &bull; Ponokawan, Sidoarjo</p>
    </footer>

    <!-- Interactive Script for Role Switching & Password Visibility -->
    <script>
        const ROLES = {
            pusat: {
                desc: 'Admin Pusat &bull; Akses multi-cabang, tarif master, jadwal & audit.',
                identifier: 'admin@lespintar.id',
                password: 'admin123'
            },
            cabang: {
                desc: 'Pimpinan Cabang &bull; Kelola siswa, jadwal cabang & mutasi kas.',
                identifier: 'cabang.ponokawan@lespintar.id',
                password: 'cabang123'
            },
            guru: {
                desc: 'Guru Pengajar &bull; Jadwal mengajar, daftar siswa & presensi QR.',
                identifier: 'nip3101@lespintar.id',
                password: '1995-05-14'
            }
        };

        function setRole(roleKey) {
            const r = ROLES[roleKey];
            if (!r) return;

            // Fill form inputs
            const idInput = document.getElementById('identifier');
            const pwdInput = document.getElementById('password');
            if (idInput) idInput.value = r.identifier;
            if (pwdInput) pwdInput.value = r.password;

            // Update scope description
            const descEl = document.getElementById('role-desc');
            if (descEl) descEl.innerHTML = r.desc;

            // Update button styles (Apple segmented style)
            const roleButtons = {
                pusat: document.getElementById('btn-role-pusat'),
                cabang: document.getElementById('btn-role-cabang'),
                guru: document.getElementById('btn-role-guru')
            };

            for (const [k, btn] of Object.entries(roleButtons)) {
                if (!btn) continue;
                if (k === roleKey) {
                    btn.className = 'role-btn min-h-[36px] py-1.5 px-1 rounded-lg font-bold text-center text-xs transition-all bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs flex items-center justify-center';
                } else {
                    btn.className = 'role-btn min-h-[36px] py-1.5 px-1 rounded-lg font-medium text-center text-xs transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center justify-center';
                }
            }

            if (idInput) idInput.focus();
        }

        function togglePasswordVisibility() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />';
            } else {
                pwd.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            }
        }
    </script>
</body>

</html>
