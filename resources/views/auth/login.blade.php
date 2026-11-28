<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Selamat Datang — Masuk ke Portal SIMANLES LKP Sang Siroju Lillah</title>
    <link rel="icon" type="image/png" href="{{ asset('logoo.png') }}">

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
        #btn-login-submit {
            width: 100%;
            height: 3rem;
            border-radius: 0.75rem;
            transition: width 0.38s cubic-bezier(0.4, 0, 0.2, 1), border-radius 0.38s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.25s ease, transform 0.15s ease;
        }
        @media (min-width: 640px) {
            #btn-login-submit {
                height: 3.25rem;
            }
        }
        #btn-login-submit.is-circle {
            width: 3.25rem;
            height: 3.25rem;
            border-radius: 9999px;
            padding: 0;
        }
        #btn-login-submit.is-success {
            background-image: none !important;
            background-color: #059669 !important;
        }
    </style>

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        document.documentElement.classList.remove('dark');
        try { localStorage.removeItem('theme'); } catch(e) {}
    </script>
</head>

<body class="min-h-[100dvh] relative flex items-center justify-center p-3 sm:p-6 lg:p-8 antialiased selection:bg-[#0a52be] selection:text-white transition-colors duration-200 bg-gradient-to-br from-[#ffffff] via-[#edf4fd] to-[#dbeafe] text-slate-800">

    <!-- Subtle Ambient Gradient Lighting -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden select-none z-0">
        <div class="absolute -top-32 left-1/4 w-[38rem] h-[38rem] bg-gradient-to-b from-blue-200/50 via-sky-100/30 to-transparent rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 right-1/4 w-[38rem] h-[38rem] bg-gradient-to-t from-blue-300/40 via-blue-100/20 to-transparent rounded-full blur-3xl"></div>
    </div>

    <!-- Dynamic Animated Blue Wave Lines Canvas -->
    <canvas id="topo-canvas" class="fixed inset-0 w-full h-full pointer-events-none z-0"></canvas>

    <!-- Center Card Container -->
    <div id="login-card" class="w-full max-w-md lg:max-w-5xl bg-white/95 dark:bg-[#101726]/95 backdrop-blur-md lg:backdrop-blur-none lg:bg-white lg:dark:bg-[#101726] rounded-[28px] sm:rounded-[32px] overflow-hidden shadow-2xl shadow-blue-900/10 dark:shadow-black/70 border border-slate-200/80 dark:border-slate-800/80 grid grid-cols-1 lg:grid-cols-2 relative z-10 my-auto">

        <!-- LEFT COLUMN: Form Login -->
        <div class="relative p-6 sm:p-10 lg:p-12 flex flex-col justify-between z-10">
            
            <!-- Top Action Row -->
            <div class="flex items-center justify-between w-full mb-1">
                <span class="sr-only">SIMANLES LKP Sang Siroju Lillah</span>
                <div class="text-[10px] font-semibold text-slate-400 hidden sm:block tracking-wide">
                    SIMANLES &bull; Portal Layanan
                </div>
            </div>

            <!-- Form Content -->
            <div class="my-auto py-1">
                <!-- Top-Right Toast Notifications for Login -->
                <div id="login-toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2.5 max-w-sm w-[calc(100%-2rem)] pointer-events-none">
                    @if(session('success'))
                        <div class="toast-item pointer-events-auto bg-white border border-emerald-300 rounded-2xl p-3.5 shadow-xl flex items-center justify-between gap-3 transition-all duration-300">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-900">Berhasil</p>
                                    <p class="text-xs text-slate-600 font-medium truncate sm:whitespace-normal">{{ session('success') }}</p>
                                </div>
                            </div>
                            <button type="button" onclick="this.closest('.toast-item').remove()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    @endif

                    @if(isset($errors) && $errors->any())
                        <div class="toast-item pointer-events-auto bg-white border border-rose-300 rounded-2xl p-3.5 shadow-xl flex items-start justify-between gap-3 transition-all duration-300">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                                <div class="min-w-0 text-xs">
                                    <p class="font-bold text-rose-900">Gagal Masuk</p>
                                    <ul class="list-disc list-inside mt-1 space-y-0.5 text-[11px] text-rose-700 font-medium">
                                        @foreach($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" onclick="this.closest('.toast-item').remove()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    @endif
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const toasts = document.querySelectorAll('#login-toast-container .toast-item');
                        toasts.forEach(toast => {
                            setTimeout(() => {
                                toast.style.opacity = '0';
                                toast.style.transform = 'translateY(-10px)';
                                setTimeout(() => toast.remove(), 250);
                            }, 4000);
                        });
                    });
                </script>

                <!-- Centered Logo: logoo.png -->
                <div class="flex justify-center mb-3">
                    <img src="{{ asset('logoo.png') }}" alt="Logo LKP Sang Siroju Lillah" loading="lazy" decoding="async"
                        class="h-12 sm:h-14 w-auto object-contain filter drop-shadow-sm img-skeleton" onload="this.classList.add('img-loaded')">
                </div>

                <!-- Centered Heading & Subtitle -->
                <div class="text-center mb-5">
                    <h1 class="text-xl sm:text-[22px] font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Selamat Datang
                    </h1>
                    <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto leading-relaxed">
                        Masuk untuk mengakses sistem LKP Sang Siroju Lillah
                    </p>
                </div>

                <!-- Form Login (Mirrors reference input style) -->
                <form id="login-form" action="{{ route('login.post') }}" method="POST" class="space-y-3.5">
                    @csrf

                    <!-- Field 1: USERNAME -->
                    <div>
                        <label for="identifier" class="block text-[10px] font-bold tracking-wider uppercase text-slate-500 dark:text-slate-400 mb-1">
                            USERNAME
                        </label>
                        <div class="relative flex items-center bg-[#f0f4f9] dark:bg-slate-800/80 rounded-xl px-3.5 h-10 sm:h-11 border border-slate-200/50 dark:border-slate-700/60 focus-within:border-[#0a52be] focus-within:ring-2 focus-within:ring-[#0a52be]/20 focus-within:bg-white dark:focus-within:bg-slate-800 transition-all">
                            <!-- User outline icon -->
                            <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 shrink-0 mr-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <input type="text" name="identifier" id="identifier" value="{{ old('identifier', 'admin@lespintar.id') }}" required autofocus
                                autocomplete="username" autocapitalize="none" autocorrect="off" spellcheck="false"
                                placeholder="lppm_user"
                                class="w-full bg-transparent border-none text-slate-900 dark:text-slate-100 text-xs sm:text-[13px] font-medium focus:outline-none placeholder:text-slate-400">
                        </div>
                    </div>

                    <!-- Field 2: PASSWORD -->
                    <div>
                        <label for="password" class="block text-[10px] font-bold tracking-wider uppercase text-slate-500 dark:text-slate-400 mb-1">
                            PASSWORD
                        </label>
                        <div class="relative flex items-center bg-[#f0f4f9] dark:bg-slate-800/80 rounded-xl px-3.5 h-10 sm:h-11 border border-slate-200/50 dark:border-slate-700/60 focus-within:border-[#0a52be] focus-within:ring-2 focus-within:ring-[#0a52be]/20 focus-within:bg-white dark:focus-within:bg-slate-800 transition-all">
                            <!-- Lock outline icon -->
                            <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 shrink-0 mr-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <input type="password" name="password" id="password" value="admin123" required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full bg-transparent border-none text-slate-900 dark:text-slate-100 text-xs sm:text-[13px] font-medium focus:outline-none placeholder:text-slate-400 pr-2">
                            <!-- Eye Toggle Button -->
                            <button type="button" onclick="togglePasswordVisibility()"
                                class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 shrink-0 transition-colors"
                                aria-label="Tampilkan atau sembunyikan kata sandi">
                                <svg id="eye-icon" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Hidden Remember Field -->
                    <input type="hidden" name="remember" value="1">

                    <!-- Submit Button: "Masuk Sekarang →" -->
                    <div class="pt-2 flex justify-center">
                        <button type="submit" id="btn-login-submit"
                            class="bg-gradient-to-r from-[#0a52be] via-[#2563eb] to-[#0ea5e9] hover:from-[#084299] hover:via-[#1d4ed8] hover:to-[#0284c7] active:from-[#07367c] active:to-[#0369a1] text-white font-semibold text-sm shadow-lg shadow-blue-600/25 flex items-center justify-center gap-2 group cursor-pointer overflow-hidden">
                            <span id="btn-login-label" class="flex items-center justify-center gap-2 whitespace-nowrap">
                                <span>Masuk Sekarang</span>
                                <span class="text-base font-bold transition-transform group-hover:translate-x-1">&rarr;</span>
                            </span>
                            <svg id="btn-login-spinner" class="hidden w-5 h-5 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3.5"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v3.5a4.5 4.5 0 00-4.5 4.5H4z"></path>
                            </svg>
                            <svg id="btn-login-check" class="hidden w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Quick Role Presets Helper -->
                <div class="mt-5 pt-3.5 border-t border-slate-100 dark:border-slate-800/80">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-medium text-slate-400 dark:text-slate-500">Pilih Akun Cepat:</span>
                        <span id="role-desc" class="text-[9.5px] text-slate-400 dark:text-slate-500">Admin Pusat</span>
                    </div>
                    <div class="grid grid-cols-3 gap-1.5">
                        <button type="button" onclick="setRole('pusat')" id="btn-role-pusat"
                            class="py-1 px-1.5 rounded-lg text-[11px] font-semibold bg-blue-50 dark:bg-blue-950/50 text-[#0a52be] dark:text-blue-300 border border-blue-200/60 dark:border-blue-900/60 hover:bg-blue-100 transition-all text-center">
                            Admin Pusat
                        </button>
                        <button type="button" onclick="setRole('cabang')" id="btn-role-cabang"
                            class="py-1 px-1.5 rounded-lg text-[11px] font-medium bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 border border-slate-200/60 dark:border-slate-700/60 hover:bg-slate-100 transition-all text-center">
                            Pimp. Cabang
                        </button>
                        <button type="button" onclick="setRole('guru')" id="btn-role-guru"
                            class="py-1 px-1.5 rounded-lg text-[11px] font-medium bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 border border-slate-200/60 dark:border-slate-700/60 hover:bg-slate-100 transition-all text-center">
                            Guru Pengajar
                        </button>
                    </div>
                    <div class="sr-only">
                        Masuk ke Portal
                    </div>
                </div>
            </div>

            <!-- Footer note -->
            <div class="text-center pt-2.5 text-[10px] text-slate-400 dark:text-slate-500">
                &copy; {{ date('Y') }} LKP Sang Siroju Lillah
            </div>
        </div>

        <!-- RIGHT COLUMN: Visual Hero Banner (Hidden on Mobile, Displayed on Desktop lg+) -->
        <div class="hidden lg:flex relative bg-cover bg-center min-h-full flex-col justify-end p-8 sm:p-10 lg:p-12 text-white overflow-hidden"
            style="background-image: url('{{ asset('belajar.webp') }}');">
            
            <!-- Dark Navy Gradient Overlay (matching reference image) -->
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950/30 via-slate-950/70 to-[#070e1b] z-0"></div>

            <!-- Content Container -->
            <div class="relative z-10 w-full">
                <!-- Title & Blue line accent -->
                <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">
                    SIMANLES LKP
                </h2>
                <div class="w-7 h-1 bg-[#0a52be] rounded-full my-2.5"></div>

                <!-- Description Paragraph -->
                <p class="text-[11px] sm:text-xs text-slate-200/90 leading-relaxed max-w-sm mb-5">
                    Kelola operasional bimbingan belajar, presensi QR cerdas, jadwal guru, dan administrasi keuangan secara real-time dengan linimasa pelacakan alur yang sistematis dan transparan.
                </p>

                <!-- 3 Feature Badges in a Row (exactly like reference image) -->
                <div class="grid grid-cols-3 gap-2.5 sm:gap-3 pt-1 text-center">
                    <!-- Item 1: Rekap & Pelacakan Terintegrasi -->
                    <div class="flex flex-col items-center justify-start text-center group">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-[#1c2438]/85 dark:bg-slate-800/85 backdrop-blur-md border border-white/10 flex items-center justify-center shadow-lg shadow-black/30 mb-2 transition-transform duration-200 group-hover:scale-105">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-[10px] sm:text-[11px] font-semibold text-slate-100 leading-tight">
                            Rekap & Pelacakan<br>Terintegrasi
                        </span>
                    </div>

                    <!-- Item 2: Aman & Terpercaya -->
                    <div class="flex flex-col items-center justify-start text-center group">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-[#1c2438]/85 dark:bg-slate-800/85 backdrop-blur-md border border-white/10 flex items-center justify-center shadow-lg shadow-black/30 mb-2 transition-transform duration-200 group-hover:scale-105">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                        </div>
                        <span class="text-[10px] sm:text-[11px] font-semibold text-slate-100 leading-tight">
                            Aman &<br>Terpercaya
                        </span>
                    </div>

                    <!-- Item 3: Transparan & Akuntabel -->
                    <div class="flex flex-col items-center justify-start text-center group">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-[#1c2438]/85 dark:bg-slate-800/85 backdrop-blur-md border border-white/10 flex items-center justify-center shadow-lg shadow-black/30 mb-2 transition-transform duration-200 group-hover:scale-105">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M18 20V10m-6 10V4m-6 10v6" />
                            </svg>
                        </div>
                        <span class="text-[10px] sm:text-[11px] font-semibold text-slate-100 leading-tight">
                            Transparan &<br>Akuntabel
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Script for Role Switching & Password Visibility -->
    <script>
        const ROLES = {
            pusat: {
                title: 'Admin Pusat',
                desc: 'Admin Pusat &bull; Akses penuh multi-cabang',
                identifier: 'admin@lespintar.id',
                password: 'admin123'
            },
            cabang: {
                title: 'Pimp. Cabang',
                desc: 'Pimpinan Cabang &bull; Kelola cabang & keuangan',
                identifier: 'cabang.ponokawan@lespintar.id',
                password: 'cabang123'
            },
            guru: {
                title: 'Guru Pengajar',
                desc: 'Guru Pengajar &bull; Jadwal & presensi QR',
                identifier: 'nip3101@lespintar.id',
                password: '1995-05-14'
            }
        };

        function setRole(roleKey) {
            const r = ROLES[roleKey];
            if (!r) return;

            const idInput = document.getElementById('identifier');
            const pwdInput = document.getElementById('password');
            if (idInput) idInput.value = r.identifier;
            if (pwdInput) pwdInput.value = r.password;

            const descEl = document.getElementById('role-desc');
            if (descEl) descEl.textContent = r.title;

            const roleButtons = {
                pusat: document.getElementById('btn-role-pusat'),
                cabang: document.getElementById('btn-role-cabang'),
                guru: document.getElementById('btn-role-guru')
            };

            for (const [k, btn] of Object.entries(roleButtons)) {
                if (!btn) continue;
                if (k === roleKey) {
                    btn.className = 'py-1 px-1.5 rounded-lg text-[11px] font-semibold bg-blue-50 dark:bg-blue-950/50 text-[#0a52be] dark:text-blue-300 border border-blue-200/60 dark:border-blue-900/60 hover:bg-blue-100 transition-all text-center';
                } else {
                    btn.className = 'py-1 px-1.5 rounded-lg text-[11px] font-medium bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 border border-slate-200/60 dark:border-slate-700/60 hover:bg-slate-100 transition-all text-center';
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

        (function initLoginButtonMorph() {
            const form = document.getElementById('login-form');
            const btn = document.getElementById('btn-login-submit');
            const label = document.getElementById('btn-login-label');
            const spinner = document.getElementById('btn-login-spinner');
            const check = document.getElementById('btn-login-check');
            if (!form || !btn) return;

            let isSubmitting = false;

            const resetButton = () => {
                isSubmitting = false;
                btn.disabled = false;
                btn.classList.remove('is-circle', 'is-success');
                spinner?.classList.add('hidden');
                check?.classList.add('hidden');
                label?.classList.remove('hidden');
            };

            form.addEventListener('submit', async (e) => {
                if (isSubmitting) {
                    e.preventDefault();
                    return;
                }
                e.preventDefault();
                isSubmitting = true;
                btn.disabled = true;

                label?.classList.add('hidden');
                btn.classList.add('is-circle');
                spinner?.classList.remove('hidden');

                const startedAt = Date.now();
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        credentials: 'same-origin',
                    });

                    const elapsed = Date.now() - startedAt;
                    const minSpinnerMs = 550;
                    if (elapsed < minSpinnerMs) {
                        await new Promise((r) => setTimeout(r, minSpinnerMs - elapsed));
                    }

                    if (response.ok && response.redirected && !response.url.includes('/login')) {
                        spinner?.classList.add('hidden');
                        btn.classList.add('is-success');
                        check?.classList.remove('hidden');
                        setTimeout(() => {
                            window.location.href = response.url;
                        }, 550);
                        return;
                    }
                } catch (err) {
                    console.warn('Login fetch fallback:', err);
                }

                // Fallback to standard submit so Laravel validation errors render normally
                resetButton();
                form.submit();
            });
        })();

        (function initMobileKeyboardScroll() {
            const inputs = [document.getElementById('identifier'), document.getElementById('password')].filter(Boolean);
            const card = document.getElementById('login-card');
            if (!inputs.length || !card) return;

            let blurTimer = null;
            let readyForFocus = false;
            setTimeout(() => { readyForFocus = true; }, 350);

            const moveCardTop = () => {
                if (window.innerWidth >= 1024) return;
                clearTimeout(blurTimer);
                document.body.classList.remove('items-center');
                document.body.classList.add('items-start', 'pt-4');
                card.classList.remove('my-auto');
                setTimeout(() => {
                    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 120);
            };

            const restoreCardCenter = () => {
                if (window.innerWidth >= 1024) return;
                clearTimeout(blurTimer);
                blurTimer = setTimeout(() => {
                    if (!inputs.includes(document.activeElement)) {
                        document.body.classList.add('items-center');
                        document.body.classList.remove('items-start', 'pt-4');
                        card.classList.add('my-auto');
                    }
                }, 150);
            };

            inputs.forEach((input) => {
                input.addEventListener('pointerdown', moveCardTop);
                input.addEventListener('focus', () => {
                    if (readyForFocus) moveCardTop();
                });
                input.addEventListener('blur', restoreCardCenter);
            });

            if (window.visualViewport) {
                window.visualViewport.addEventListener('resize', () => {
                    if (inputs.includes(document.activeElement)) {
                        moveCardTop();
                    }
                });
            }
        })();

        // Dynamic Animated Blue Wave Lines (Garis gelombang bergerak berwarna biru bermacam-macam)
        (function initBlueWaveLinesBackground() {
            const canvas = document.getElementById('topo-canvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let width, height;

            function resize() {
                const dpr = Math.min(window.devicePixelRatio || 1, 2);
                width = window.innerWidth;
                height = window.innerHeight;
                canvas.width = width * dpr;
                canvas.height = height * dpr;
                ctx.setTransform(1, 0, 0, 1, 0, 0);
                ctx.scale(dpr, dpr);
            }
            window.addEventListener('resize', resize);
            resize();

            const bluePalette = [
                [10, 82, 190],   // #0a52be (primary deep blue)
                [37, 99, 235],   // #2563eb (royal blue)
                [14, 165, 233],  // #0ea5e9 (sky blue)
                [59, 130, 246],  // #3b82f6 (bright blue)
                [56, 189, 248],  // #38bdf8 (cyan blue)
                [30, 64, 175],   // #1e40af (navy blue)
                [96, 165, 250],  // #60a5fa (soft blue)
            ];

            const waves = Array.from({ length: 14 }, (_, i) => ({
                yRatio: 0.08 + (i / 13) * 0.84,
                amplitude: 16 + (i % 4) * 11,
                wavelength: 220 + (i % 5) * 75,
                speed: (0.45 + (i % 4) * 0.22) * (i % 2 === 0 ? 1 : -1),
                phase: i * 0.75,
                lineWidth: 1.2 + (i % 3) * 0.65,
                alpha: 0.18 + (i % 4) * 0.07,
                colorA: bluePalette[i % bluePalette.length],
                colorB: bluePalette[(i + 2) % bluePalette.length],
            }));

            let lastTime = performance.now();

            function render(now) {
                if (document.hidden) {
                    requestAnimationFrame(render);
                    return;
                }

                const dt = Math.min((now - lastTime) / 1000, 0.1);
                lastTime = now;
                ctx.clearRect(0, 0, width, height);
                ctx.setLineDash([]);

                waves.forEach((w) => {
                    w.phase += w.speed * dt;
                    const baseY = height * w.yRatio;

                    const grad = ctx.createLinearGradient(0, baseY, width, baseY);
                    grad.addColorStop(0, `rgba(${w.colorA[0]}, ${w.colorA[1]}, ${w.colorA[2]}, 0)`);
                    grad.addColorStop(0.2, `rgba(${w.colorA[0]}, ${w.colorA[1]}, ${w.colorA[2]}, ${w.alpha})`);
                    grad.addColorStop(0.5, `rgba(${w.colorB[0]}, ${w.colorB[1]}, ${w.colorB[2]}, ${w.alpha + 0.08})`);
                    grad.addColorStop(0.8, `rgba(${w.colorA[0]}, ${w.colorA[1]}, ${w.colorA[2]}, ${w.alpha})`);
                    grad.addColorStop(1, `rgba(${w.colorB[0]}, ${w.colorB[1]}, ${w.colorB[2]}, 0)`);

                    ctx.beginPath();
                    const step = 12;
                    for (let x = 0; x <= width + step; x += step) {
                        const y = baseY
                            + Math.sin((x / w.wavelength) * Math.PI * 2 + w.phase) * w.amplitude
                            + Math.cos((x / (w.wavelength * 0.6)) * Math.PI * 2 - w.phase * 0.6) * (w.amplitude * 0.3);
                        if (x === 0) {
                            ctx.moveTo(x, y);
                        } else {
                            ctx.lineTo(x, y);
                        }
                    }

                    ctx.strokeStyle = grad;
                    ctx.lineWidth = w.lineWidth;
                    ctx.stroke();
                });

                requestAnimationFrame(render);
            }

            requestAnimationFrame(render);
        })();
    </script>
</body>

</html>
