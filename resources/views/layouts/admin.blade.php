<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard Admin') — LKP Sang Siroju Lillah</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    @fonts

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
        }
    </script>

    <style>
        #admin-sidebar {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                        transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                        opacity 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: margin-left, transform;
        }
        @media (min-width: 1024px) {
            #admin-sidebar.desktop-collapsed {
                margin-left: -15rem !important;
                opacity: 0 !important;
                pointer-events: none !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="h-full font-sans text-slate-800 antialiased bg-slate-50/80 dark:bg-slate-950 dark:text-slate-100 selection:bg-blue-100 selection:text-blue-900 transition-colors duration-200">
    @php
        $authUser = Auth::user();
        $userRole = $authUser?->role ?? ($viewRole ?? 'cabang');
        $isPusat = $userRole === 'pusat';
        $isCabang = $userRole === 'cabang';
        $isGuru = $userRole === 'guru';
    @endphp
    <div class="min-h-screen flex flex-row w-full bg-slate-50/80 dark:bg-slate-950">

        <!-- Mobile Sidebar Backdrop (Smooth Fade Transition) -->
        <div id="mobile-backdrop"
            class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs lg:hidden opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out"
            onclick="toggleSidebar(false)" aria-hidden="true"></div>

        <!-- Sidebar Navigation (Drawer on Mobile, Fitted Sticky on Desktop, Smooth Collapse) -->
        <aside id="admin-sidebar"
            class="fixed inset-y-0 left-0 z-50 lg:z-30 w-60 h-screen bg-slate-50 dark:bg-slate-900 border-r border-slate-200/90 dark:border-slate-800 flex flex-col justify-between -translate-x-full lg:translate-x-0 lg:sticky lg:top-0 shadow-2xl lg:shadow-none shrink-0 overflow-hidden">
            <div class="flex flex-col h-full overflow-y-auto px-3.5 py-4">
                <!-- Brand Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-200/70 dark:border-slate-800">
                    <a href="{{ $isGuru ? route('jadwal.index') : route('admin.dashboard') }}" class="flex items-center gap-2.5 group min-w-0">
                        <!-- Custom Academic Brand Logo -->
                        <div
                            class="w-8 h-8 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xs flex items-center justify-center p-1 group-hover:border-blue-400 group-hover:shadow-xs transition-all shrink-0">
                            <img src="{{ asset('logoo.png') }}" alt="Logo LKP Sang Siroju Lillah"
                                class="w-full h-full object-contain">
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1">
                                <span class="font-bold text-xs sm:text-[13px] tracking-tight text-slate-900 dark:text-white truncate">Sang
                                    Siroju Lillah</span>
                                <span
                                    class="text-[8px] uppercase font-bold tracking-wider px-1 py-0.2 rounded {{ $isPusat ? 'bg-indigo-100 dark:bg-indigo-950 text-indigo-900 dark:text-indigo-300' : ($isCabang ? 'bg-blue-100 dark:bg-blue-950 text-blue-900 dark:text-blue-300' : 'bg-amber-100 dark:bg-amber-950 text-amber-900 dark:text-amber-300') }} shrink-0">
                                    {{ $isPusat ? 'Pusat' : ($isCabang ? 'Cabang' : 'Guru') }}
                                </span>
                            </div>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium truncate">LKP Tempat Les & Bimbel</p>
                        </div>
                    </a>

                    <!-- Sidebar Toggle Actions (Desktop Collapse & Mobile Close) -->
                    <div class="flex items-center gap-0.5 shrink-0">
                        <!-- Desktop Collapse Button -->
                        <button type="button" onclick="toggleSidebar()"
                            class="hidden lg:flex p-1 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800 rounded-md transition-colors"
                            title="Tutup Sidebar">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                            </svg>
                        </button>
                        <!-- Mobile Close button -->
                        <button type="button" onclick="toggleMobileMenu(false)"
                            class="p-1.5 -mr-1 text-slate-500 hover:text-slate-800 hover:bg-slate-200/60 dark:hover:bg-slate-800 rounded-lg lg:hidden"
                            aria-label="Tutup menu">
                            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Multi-Cabang Context Switcher -->
                <div class="mt-3 p-2 rounded-xl bg-white dark:bg-slate-800/80 border border-slate-200/90 dark:border-slate-700/80 shadow-2xs">
                    <label class="block text-[9px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">
                        {{ $isPusat ? 'Lingkup: Pusat (Semua Cabang)' : 'Cabang Bertugas' }}
                    </label>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <span
                                class="w-1.5 h-1.5 rounded-full {{ $isPusat ? 'bg-indigo-600' : 'bg-emerald-500' }} shrink-0"></span>
                            <div class="truncate">
                                <p class="text-[11px] font-semibold text-slate-900 dark:text-white truncate">
                                    {{ $currentCabang['nama'] ?? 'Cabang Ponokawan' }}
                                </p>
                                <p class="text-[9.5px] text-slate-400 font-mono truncate">
                                    Kode: {{ $currentCabang['kode'] ?? 'CAB-PNK' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="mt-3 space-y-1.5 flex-1 text-xs">
                    @php
                        $catOperasionalActive = request()->routeIs('siswa.*', 'guru.*');
                        $catAkademikActive = request()->routeIs('jadwal.*', 'presensi.*');
                        $catKeuanganActive = request()->routeIs('spp.*', 'kas.*');
                        $catMasterActive = request()->routeIs('pengaturan.*', 'audit.*');
                    @endphp

                    @if(! $isGuru)
                    <!-- Standalone Top Item: Dashboard -->
                    <a href="{{ route('admin.dashboard', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                        class="flex items-center gap-2 px-2.5 py-2 rounded-lg {{ request()->routeIs('admin.dashboard', 'dashboard', 'admin.home') ? 'bg-blue-900 text-white font-semibold shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-800/80 font-medium' }} transition-colors mb-1">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard', 'dashboard', 'admin.home') ? 'text-blue-200' : 'text-slate-500 dark:text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect width="7" height="9" x="3" y="3" rx="1" />
                            <rect width="7" height="5" x="14" y="3" rx="1" />
                            <rect width="7" height="9" x="14" y="12" rx="1" />
                            <rect width="7" height="5" x="3" y="16" rx="1" />
                        </svg>
                        <span class="font-semibold">Dashboard Cabang</span>
                    </a>

                    <!-- Dropdown: Operasional Cabang -->
                    <details class="group select-none" {{ $catOperasionalActive ? 'open' : '' }}>
                        <summary class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-800/70 font-semibold cursor-pointer list-none [&::-webkit-details-marker]:hidden transition-colors">
                            <div class="flex items-center gap-2 min-w-0">
                                <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span class="text-[11px] uppercase tracking-wider font-bold">Operasional Cabang</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-open:rotate-180 transition-transform duration-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <ul class="space-y-0.5 mt-1 pl-2 border-l border-slate-200/80 dark:border-slate-800 ml-3.5 py-0.5">
                            <li>
                                <a href="{{ route('siswa.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg {{ request()->routeIs('siswa.*') ? 'bg-blue-900 text-white font-medium shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-800 font-medium' }} transition-colors">
                                    <svg class="w-3.5 h-3.5 {{ request()->routeIs('siswa.*') ? 'text-blue-200' : 'text-emerald-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                        <line x1="19" y1="8" x2="19" y2="14" />
                                        <line x1="22" y1="11" x2="16" y2="11" />
                                    </svg>
                                    <span>Data & Pendaftaran Siswa</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('guru.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg {{ request()->routeIs('guru.*') ? 'bg-blue-900 text-white font-medium shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-800 font-medium' }} transition-colors">
                                    <svg class="w-3.5 h-3.5 {{ request()->routeIs('guru.*') ? 'text-blue-200' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                    </svg>
                                    <span>Distribusi & Guru</span>
                                </a>
                            </li>
                        </ul>
                    </details>
                    @endif

                    <!-- Dropdown: Akademik & Jadwal -->
                    <details class="group select-none" {{ $catAkademikActive ? 'open' : '' }}>
                        <summary class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-800/70 font-semibold cursor-pointer list-none [&::-webkit-details-marker]:hidden transition-colors">
                            <div class="flex items-center gap-2 min-w-0">
                                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                    <line x1="16" x2="16" y1="2" y2="6" />
                                    <line x1="8" x2="8" y1="2" y2="6" />
                                    <line x1="3" x2="21" y1="10" y2="10" />
                                </svg>
                                <span class="text-[11px] uppercase tracking-wider font-bold">Akademik & Jadwal</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-open:rotate-180 transition-transform duration-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <ul class="space-y-0.5 mt-1 pl-2 border-l border-slate-200/80 dark:border-slate-800 ml-3.5 py-0.5">
                            @if($isGuru)
                            <li>
                                <a href="{{ route('jadwal.index') }}"
                                    class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg {{ request()->routeIs('jadwal.*') ? 'bg-blue-900 text-white font-medium shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-800 font-medium' }} transition-colors">
                                    <svg class="w-3.5 h-3.5 shrink-0 {{ request()->routeIs('jadwal.*') ? 'text-blue-200' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                        <line x1="16" x2="16" y1="2" y2="6" />
                                        <line x1="8" x2="8" y1="2" y2="6" />
                                        <line x1="3" x2="21" y1="10" y2="10" />
                                    </svg>
                                    <span class="truncate">Jadwal & Riwayat Hadir</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('presensi.scanner') }}"
                                    class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg {{ request()->routeIs('presensi.*') ? 'bg-blue-900 text-white font-medium shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-800 font-medium' }} transition-colors">
                                    <svg class="w-3.5 h-3.5 shrink-0 {{ request()->routeIs('presensi.*') ? 'text-blue-200' : 'text-emerald-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="truncate">Scan Presensi QR</span>
                                </a>
                            </li>
                            @else
                            <li>
                                <a href="{{ route('jadwal.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg {{ request()->routeIs('jadwal.*') ? 'bg-blue-900 text-white font-medium shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-800 font-medium' }} transition-colors">
                                    <svg class="w-3.5 h-3.5 shrink-0 {{ request()->routeIs('jadwal.*') ? 'text-blue-200' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                        <line x1="16" x2="16" y1="2" y2="6" />
                                        <line x1="8" x2="8" y1="2" y2="6" />
                                        <line x1="3" x2="21" y1="10" y2="10" />
                                    </svg>
                                    <span class="truncate">Jadwal Mengajar</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('presensi.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg {{ request()->routeIs('presensi.index', 'presensi') ? 'bg-blue-900 text-white font-medium shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-800 font-medium' }} transition-colors">
                                    <svg class="w-3.5 h-3.5 shrink-0 {{ request()->routeIs('presensi.index', 'presensi') ? 'text-blue-200' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 7V5a2 2 0 0 1 2-2h2" />
                                        <path d="M17 3h2a2 2 0 0 1 2 2v2" />
                                        <path d="M21 17v2a2 2 0 0 1-2 2h-2" />
                                        <path d="M7 21H5a2 2 0 0 1-2-2v-2" />
                                        <rect x="7" y="7" width="3" height="3" rx="0.5" />
                                        <rect x="14" y="7" width="3" height="3" rx="0.5" />
                                        <rect x="7" y="14" width="3" height="3" rx="0.5" />
                                        <path d="M14 14h.01" />
                                        <path d="M17 14v3" />
                                        <path d="M14 17h3" />
                                    </svg>
                                    <span class="truncate">Layar QR & Presensi</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </details>

                    @if(! $isGuru)
                    <!-- Dropdown: Keuangan & Kas -->
                    <details class="group select-none" {{ $catKeuanganActive ? 'open' : '' }}>
                        <summary class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-800/70 font-semibold cursor-pointer list-none [&::-webkit-details-marker]:hidden transition-colors">
                            <div class="flex items-center gap-2 min-w-0">
                                <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <line x1="12" x2="12" y1="2" y2="22" />
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                </svg>
                                <span class="text-[11px] uppercase tracking-wider font-bold">Keuangan & Kas</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-open:rotate-180 transition-transform duration-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <ul class="space-y-0.5 mt-1 pl-2 border-l border-slate-200/80 dark:border-slate-800 ml-3.5 py-0.5">
                            <li>
                                <a href="{{ route('spp.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg {{ request()->routeIs('spp.*') ? 'bg-blue-900 text-white font-medium shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-800 font-medium' }} transition-colors">
                                    <svg class="w-3.5 h-3.5 {{ request()->routeIs('spp.*') ? 'text-blue-200' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <rect width="20" height="14" x="2" y="5" rx="2" />
                                        <line x1="2" x2="22" y1="10" y2="10" />
                                    </svg>
                                    <span>Tagihan & SPP Siswa</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('kas.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg {{ request()->routeIs('kas.*') ? 'bg-blue-900 text-white font-medium shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-800 font-medium' }} transition-colors">
                                    <svg class="w-3.5 h-3.5 {{ request()->routeIs('kas.*') ? 'text-blue-200' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <line x1="12" x2="12" y1="2" y2="22" />
                                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                    </svg>
                                    <span>Buku Kas & Kwitansi</span>
                                </a>
                            </li>
                        </ul>
                    </details>
                    @endif

                    @if($isPusat)
                    <!-- Dropdown: Master & Keamanan -->
                    <details class="group select-none" {{ $catMasterActive ? 'open' : '' }}>
                        <summary class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-800/70 font-semibold cursor-pointer list-none [&::-webkit-details-marker]:hidden transition-colors">
                            <div class="flex items-center gap-2 min-w-0">
                                <svg class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="3" />
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                                </svg>
                                <span class="text-[11px] uppercase tracking-wider font-bold">Master & Keamanan</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-open:rotate-180 transition-transform duration-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <ul class="space-y-0.5 mt-1 pl-2 border-l border-slate-200/80 dark:border-slate-800 ml-3.5 py-0.5">
                            <li>
                                <a href="{{ route('pengaturan.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg {{ request()->routeIs('pengaturan.*') ? 'bg-blue-900 text-white font-medium shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-800 font-medium' }} transition-colors">
                                    <svg class="w-3.5 h-3.5 {{ request()->routeIs('pengaturan.*') ? 'text-blue-200' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="3" />
                                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                                    </svg>
                                    <span>Master Data & Cabang</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('audit.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg {{ request()->routeIs('audit.*') ? 'bg-blue-900 text-white font-medium shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-slate-800 font-medium' }} transition-colors">
                                    <svg class="w-3.5 h-3.5 {{ request()->routeIs('audit.*') ? 'text-blue-200' : 'text-sky-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>Audit Log & Keamanan</span>
                                </a>
                            </li>
                        </ul>
                    </details>
                    @endif
                </nav>

                <!-- Sidebar Footer: Logged in User & Logout -->
                <div class="pt-3 border-t border-slate-200/80 dark:border-slate-800 mt-auto">
                    <div class="p-2 rounded-xl bg-white dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs">
                        <div class="flex items-center justify-between gap-1.5 min-w-0">
                            <div class="flex items-center gap-2 min-w-0">
                                <div
                                    class="w-7 h-7 rounded-full bg-blue-900 text-white font-semibold flex items-center justify-center text-xs shrink-0">
                                    {{ strtoupper(substr($authUser?->name ?? $currentCabang['pimpinan'] ?? 'U', 0, 2)) }}
                                </div>
                                <div class="truncate">
                                    <p class="text-xs font-semibold text-slate-900 dark:text-white truncate">
                                        {{ $authUser?->name ?? $currentCabang['pimpinan'] ?? 'Pimpinan Cabang' }}
                                    </p>
                                    <p class="text-[9.5px] text-slate-500 dark:text-slate-400 truncate">
                                        {{ $isPusat ? 'Pimpinan Pusat' : ($isCabang ? 'Pimpinan Cabang' : 'Guru Pengajar') }}
                                    </p>
                                </div>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                                @csrf
                                <button type="submit" title="Keluar dari sistem"
                                    class="p-1 rounded-lg text-rose-600 hover:text-rose-800 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 w-full">
            <!-- Top Bar Header (Compact & Mobile-Optimized) -->
            <header
                class="sticky top-0 z-30 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xs border-b border-slate-200/80 dark:border-slate-800 px-3 sm:px-5 lg:px-6 py-2 sm:py-2.5 flex items-center justify-between gap-2 sm:gap-3">
                <div class="flex items-center gap-2 sm:gap-2.5 min-w-0">
                    <!-- Sidebar Toggle (Mobile Drawer & Desktop Collapse) -->
                    <button type="button" onclick="toggleSidebar()"
                        class="p-2 -ml-1 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg flex items-center justify-center shrink-0 transition-colors"
                        aria-label="Buka/Tutup menu navigasi" title="Buka/Tutup Sidebar">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Mobile Brand Logo -->
                    <a href="{{ $isGuru ? route('jadwal.index') : route('admin.dashboard') }}" class="flex items-center gap-1.5 lg:hidden shrink-0"
                        title="LKP Sang Siroju Lillah">
                        <img src="{{ asset('logoo.png') }}" alt="Logo LKP Sang Siroju Lillah"
                            class="w-7 h-7 object-contain rounded-md border border-slate-200 dark:border-slate-700 bg-white p-0.5">
                    </a>

                    @if(! $isGuru)
                    <!-- Branch Selector Dropdown (Fluid on small mobile) -->
                    <div class="flex items-center gap-1.5 min-w-0">
                        <label for="topbar-cabang-select"
                            class="text-slate-500 text-xs font-medium hidden md:inline">Cabang:</label>
                        <select id="topbar-cabang-select" onchange="window.location.href = '?cabang_id=' + this.value;"
                            class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200/70 border border-slate-200 dark:border-slate-700 rounded-lg py-1.5 px-2 sm:px-2.5 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-600 cursor-pointer max-w-[170px] sm:max-w-xs truncate">
                            @foreach($daftarCabang as $cb)
                                <option value="{{ $cb['id'] }}" {{ ($currentCabang['id'] ?? '') === $cb['id'] ? 'selected' : '' }}>
                                    {{ $cb['nama'] }} ({{ $cb['kode'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Portal Pengajar ({{ $currentCabang['nama'] ?? 'Cabang Ponokawan' }})</span>
                    </div>
                    @endif
                </div>

                <!-- Right Action Cluster -->
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    <!-- Dark Mode Toggle Button -->
                    <button type="button" onclick="toggleDarkMode()"
                        class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors"
                        title="Ganti Tema (Gelap/Terang)">
                        <svg class="w-4 h-4 hidden dark:block text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg class="w-4 h-4 block dark:hidden text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    @if($isGuru)
                    <!-- Shortcut Scan Presensi Guru -->
                    <a href="{{ route('presensi.scanner') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-xs transition-colors"
                        title="Buka scanner presensi kamera">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Scan Presensi</span>
                    </a>
                    @else
                    <!-- Live QR Presensi Shortcut (Khusus Admin Cabang / Pusat untuk ditunjukkan ke Guru) -->
                    <a href="{{ route('presensi.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                        class="inline-flex items-center gap-1 px-2.5 sm:px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-900 dark:text-blue-300 border border-blue-200/70 dark:border-blue-900 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors"
                        title="Buka Layar QR Code Presensi Cabang">
                        <svg class="w-3.5 h-3.5 text-blue-900 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 7V5a2 2 0 0 1 2-2h2" />
                            <path d="M17 3h2a2 2 0 0 1 2 2v2" />
                            <path d="M21 17v2a2 2 0 0 1-2 2h-2" />
                            <path d="M7 21H5a2 2 0 0 1-2-2v-2" />
                            <rect x="7" y="7" width="3" height="3" rx="0.5" />
                            <rect x="14" y="7" width="3" height="3" rx="0.5" />
                            <rect x="7" y="14" width="3" height="3" rx="0.5" />
                            <path d="M14 14h.01" />
                            <path d="M17 14v3" />
                            <path d="M14 17h3" />
                        </svg>
                        <span class="hidden sm:inline">Layar QR</span>
                    </a>
                    @endif

                    @if(! $isGuru)
                    <!-- Primary Action: + Pendaftaran Siswa Baru -->
                    <button type="button" onclick="openModal('modal-pendaftaran')"
                        class="inline-flex items-center gap-1.5 px-3 sm:px-3.5 py-1.5 rounded-lg bg-blue-900 hover:bg-blue-800 text-white text-xs sm:text-sm font-semibold active:bg-blue-950 transition-all shadow-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        <span class="hidden xs:inline sm:inline">Daftar Siswa</span>
                        <span class="xs:hidden sm:hidden">+ Siswa</span>
                    </button>
                    @endif
                </div>
            </header>

            <!-- Main Canvas Body (Fluid with compact padding) -->
            <main class="flex-1 p-3 sm:p-4 lg:p-6 max-w-7xl w-full mx-auto">
                {{-- Global Flash Messages --}}
                @if(session('success'))
                    <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-center justify-between gap-3 shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold p-1">&times;</button>
                    </div>
                @endif

                @if(session('error') || $errors->any())
                    <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-sm flex items-start justify-between gap-3 shadow-2xs">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                @if(session('error'))
                                    <span class="font-semibold">{{ session('error') }}</span>
                                @else
                                    <span class="font-semibold">Periksa kembali data yang dimasukkan:</span>
                                    <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs text-rose-700">
                                        @foreach($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold p-1">&times;</button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Desktop Footer Meta -->
            <footer
                class="hidden lg:flex border-t border-slate-200/80 px-8 py-4 bg-white/60 text-xs text-slate-500 items-center justify-between gap-2 mt-auto">
                <p>© 2026 LKP Sang Siroju Lillah — Sistem Manajemen Tempat Les Multi-Cabang. Sesuai Dokumen Arsitektur
                    Bisnis.</p>
                <div class="flex items-center gap-4 text-slate-400">
                    <span>Cron SPP: Tgl 1 00:05</span>
                    <span>·</span>
                    <span>Batas Rasio: Max 6 Siswa / Guru</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Mobile Bottom Navigation Bar (Thumb Zone Optimization) -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200/90 dark:border-slate-800 z-40 px-2 py-1.5 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]"
        aria-label="Navigasi Bawah">
        <div class="flex items-center justify-around max-w-md mx-auto">
            @if(! $isGuru)
            <!-- 1. Home / Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
                class="flex flex-col items-center justify-center p-2 {{ request()->routeIs('admin.dashboard') ? 'text-blue-900 dark:text-blue-400 font-bold' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }} min-w-[56px] text-[10px]">
                <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect width="7" height="9" x="3" y="3" rx="1" />
                    <rect width="7" height="5" x="14" y="3" rx="1" />
                    <rect width="7" height="9" x="14" y="12" rx="1" />
                    <rect width="7" height="5" x="3" y="16" rx="1" />
                </svg>
                <span>Beranda</span>
            </a>

            <!-- 2. Siswa -->
            <a href="{{ route('siswa.index') }}"
                class="flex flex-col items-center justify-center p-2 {{ request()->routeIs('siswa.*') ? 'text-blue-900 dark:text-blue-400 font-bold' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }} min-w-[56px] text-[10px]">
                <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                </svg>
                <span>Siswa</span>
            </a>
            @else
            <!-- Guru Home / Jadwal -->
            <a href="{{ route('jadwal.index') }}"
                class="flex flex-col items-center justify-center p-2 {{ request()->routeIs('jadwal.*') ? 'text-blue-900 dark:text-blue-400 font-bold' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }} min-w-[56px] text-[10px]">
                <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" x2="21" y1="10" y2="10" />
                </svg>
                <span>Jadwal</span>
            </a>
            @endif

            <!-- 3. Primary Center: QR Absen (Floating Target) -->
            <a href="{{ route('presensi.index') }}"
                class="flex flex-col items-center justify-center -mt-4 bg-blue-900 text-white rounded-full w-12 h-12 shadow-lg hover:bg-blue-800 active:scale-95 transition-transform"
                title="Presensi & Layar QR">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 7V5a2 2 0 0 1 2-2h2" />
                    <path d="M17 3h2a2 2 0 0 1 2 2v2" />
                    <path d="M21 17v2a2 2 0 0 1-2 2h-2" />
                    <path d="M7 21H5a2 2 0 0 1-2-2v-2" />
                    <rect x="7" y="7" width="3" height="3" rx="0.5" />
                    <rect x="14" y="7" width="3" height="3" rx="0.5" />
                    <rect x="7" y="14" width="3" height="3" rx="0.5" />
                    <path d="M14 14h.01" />
                    <path d="M17 14v3" />
                    <path d="M14 17h3" />
                </svg>
            </a>

            @if(! $isGuru)
            <!-- 4. Keuangan & SPP -->
            <a href="{{ route('spp.index') }}"
                class="flex flex-col items-center justify-center p-2 {{ request()->routeIs('spp.*') ? 'text-blue-900 dark:text-blue-400 font-bold' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }} min-w-[56px] text-[10px]">
                <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect width="20" height="14" x="2" y="5" rx="2" />
                    <line x1="2" x2="22" y1="10" y2="10" />
                </svg>
                <span>SPP</span>
            </a>
            @else
            <!-- Scanner Presensi untuk Guru -->
            <a href="{{ route('presensi.scanner') }}"
                class="flex flex-col items-center justify-center p-2 {{ request()->routeIs('presensi.scanner') ? 'text-blue-900 dark:text-blue-400 font-bold' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }} min-w-[56px] text-[10px]">
                <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <circle cx="12" cy="13" r="3" />
                </svg>
                <span>Scan</span>
            </a>
            @endif

            <!-- 5. Menu Drawer -->
            <button type="button" onclick="toggleMobileMenu()"
                class="flex flex-col items-center justify-center p-2 text-slate-500 hover:text-slate-900 dark:hover:text-white min-w-[56px] text-[10px]">
                <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span>Menu</span>
            </button>
        </div>
    </nav>

    <!-- Global Helpers & Scripts -->
    <script>
        function toggleSidebar(forceOpen) {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('mobile-backdrop');
            if (!sidebar) return;

            if (window.innerWidth >= 1024) {
                const isCurrentlyCollapsed = sidebar.classList.contains('desktop-collapsed');
                const shouldCollapse = typeof forceOpen === 'boolean' ? !forceOpen : !isCurrentlyCollapsed;
                if (shouldCollapse) {
                    sidebar.classList.add('desktop-collapsed');
                    try { localStorage.setItem('admin_sidebar_collapsed', 'true'); } catch (e) {}
                } else {
                    sidebar.classList.remove('desktop-collapsed');
                    try { localStorage.setItem('admin_sidebar_collapsed', 'false'); } catch (e) {}
                }
            } else {
                const isCurrentlyClosed = sidebar.classList.contains('-translate-x-full');
                const shouldOpen = typeof forceOpen === 'boolean' ? forceOpen : isCurrentlyClosed;
                if (shouldOpen) {
                    sidebar.classList.remove('-translate-x-full');
                    if (backdrop) {
                        backdrop.classList.remove('opacity-0', 'pointer-events-none');
                        backdrop.classList.add('opacity-100');
                    }
                    document.body.classList.add('overflow-hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    if (backdrop) {
                        backdrop.classList.remove('opacity-100');
                        backdrop.classList.add('opacity-0', 'pointer-events-none');
                    }
                    document.body.classList.remove('overflow-hidden');
                }
            }
        }

        function toggleMobileMenu(forceState) {
            toggleSidebar(forceState);
        }

        // Restore desktop collapsed state
        (function() {
            try {
                if (window.innerWidth >= 1024 && localStorage.getItem('admin_sidebar_collapsed') === 'true') {
                    const sidebar = document.getElementById('admin-sidebar');
                    if (sidebar) sidebar.classList.add('desktop-collapsed');
                }
            } catch (e) {}
        })();

        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }
    </script>

    @stack('scripts')
</body>

</html>