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
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { isDark } }));
        }
    </script>

    <style>
        #admin-sidebar {
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1),
                        margin 0.25s cubic-bezier(0.4, 0, 0.2, 1),
                        opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: width, margin, opacity;
        }
        @media (min-width: 1024px) {
            #admin-sidebar.desktop-collapsed {
                width: 0 !important;
                min-width: 0 !important;
                max-width: 0 !important;
                margin: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                border-width: 0 !important;
                border-color: transparent !important;
                opacity: 0 !important;
                overflow: hidden !important;
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
            class="fixed inset-y-0 left-0 z-50 lg:z-30 w-64 h-screen lg:h-[calc(100vh-1.25rem)] lg:my-2.5 lg:ml-2.5 rounded-none lg:rounded-2xl bg-[#031738] border-r lg:border border-slate-800/90 flex flex-col justify-between -translate-x-full lg:translate-x-0 lg:sticky lg:top-2.5 shadow-2xl shrink-0 overflow-hidden text-slate-300">
            <div class="flex flex-col h-full overflow-y-auto px-4 py-4 select-none scrollbar-none">
                <!-- Brand Header -->
                <div class="flex items-center justify-between pb-3.5 border-b border-white/10">
                    <a href="{{ $isGuru ? route('jadwal.index') : route('admin.dashboard') }}" class="flex items-center gap-3 group min-w-0">
                        <!-- Custom Brand Logo Box -->
                        <div class="w-10 h-10 rounded-xl bg-white p-1.5 shadow-md flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                            <img src="{{ asset('logoo.png') }}" alt="Logo LKP Sang Siroju Lillah" loading="lazy" decoding="async"
                                class="w-full h-full object-contain img-skeleton" onload="this.classList.add('img-loaded')">
                        </div>
                        <div class="min-w-0">
                            <h2 class="font-bold text-sm text-white tracking-tight leading-tight truncate">
                                SIMANLES LKP
                            </h2>
                            <p class="text-[10px] font-bold tracking-wider uppercase text-blue-300/70 mt-0.5 truncate">
                                {{ $isPusat ? 'ADMIN PUSAT' : ($isCabang ? 'PIMPINAN CABANG' : 'GURU PENGAJAR') }}
                            </p>
                        </div>
                    </a>

                    <!-- Sidebar Mobile Close Action -->
                    <div class="flex items-center gap-0.5 shrink-0 lg:hidden">
                        <!-- Mobile Close button -->
                        <button type="button" onclick="toggleMobileMenu(false)"
                            class="p-1.5 text-slate-400 hover:text-white hover:bg-white/10 rounded-lg"
                            aria-label="Tutup menu">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="mt-4 space-y-1.5 flex-1 text-xs">
                    @php
                        $catOperasionalActive = request()->routeIs('siswa.*', 'guru.*');
                        $catAkademikActive = request()->routeIs('jadwal.*', 'presensi.*');
                        $catKeuanganActive = request()->routeIs('spp.*', 'kas.*');
                        $catMasterActive = request()->routeIs('pengaturan.*', 'audit.*');
                    @endphp

                    @if(! $isGuru)
                    @if($isPusat)
                    <!-- Standalone Top Item: Dashboard Pusat (Semua Cabang) -->
                    <a href="{{ route('admin.dashboard.pusat') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard.pusat') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-white/5 font-medium' }} transition-all mb-1.5">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard.pusat') ? 'text-white' : 'text-amber-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="text-xs font-semibold truncate">Dashboard Seluruh Cabang</span>
                    </a>
                    @endif

                    <!-- Standalone Top Item: Dashboard Cabang -->
                    <a href="{{ route('admin.dashboard', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard', 'dashboard', 'admin.home') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-white/5 font-medium' }} transition-all mb-2">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard', 'dashboard', 'admin.home') ? 'text-white' : 'text-blue-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        <span class="text-xs font-semibold">Dashboard Cabang</span>
                    </a>

                    <!-- Dropdown: Operasional Cabang / Data Pengguna -->
                    <details class="group select-none" {{ $catOperasionalActive ? 'open' : '' }}>
                        <summary class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-white/5 font-medium cursor-pointer list-none [&::-webkit-details-marker]:hidden transition-all text-xs">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="font-medium text-xs">Data Pengguna & Cabang</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-open:rotate-180 transition-transform duration-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <ul class="space-y-1 mt-1 pl-4 ml-3.5 border-l border-white/10 py-1">
                            <li>
                                <a href="{{ route('siswa.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg {{ request()->routeIs('siswa.*') ? 'bg-blue-600/30 text-blue-200 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }} transition-colors text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('siswa.*') ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                                    <span>Data & Pendaftaran Siswa</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('guru.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg {{ request()->routeIs('guru.*') ? 'bg-blue-600/30 text-blue-200 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }} transition-colors text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('guru.*') ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                                    <span>Distribusi & Guru</span>
                                </a>
                            </li>
                        </ul>
                    </details>
                    @endif

                    <!-- Dropdown: Akademik -->
                    <details class="group select-none" {{ $catAkademikActive ? 'open' : '' }}>
                        <summary class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-white/5 font-medium cursor-pointer list-none [&::-webkit-details-marker]:hidden transition-all text-xs">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                                <span class="font-medium text-xs">Akademik</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-open:rotate-180 transition-transform duration-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <ul class="space-y-1 mt-1 pl-4 ml-3.5 border-l border-white/10 py-1">
                            @if($isGuru)
                            <li>
                                <a href="{{ route('jadwal.index') }}"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg {{ request()->routeIs('jadwal.*') ? 'bg-blue-600/30 text-blue-200 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }} transition-colors text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('jadwal.*') ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                                    <span>Jadwal Mengajar</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('presensi.riwayat') }}"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg {{ request()->routeIs('presensi.riwayat') ? 'bg-blue-600/30 text-blue-200 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }} transition-colors text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('presensi.riwayat') ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                                    <span>Riwayat Kehadiran</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('presensi.scanner') }}"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg {{ request()->routeIs('presensi.scanner') ? 'bg-blue-600/30 text-blue-200 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }} transition-colors text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('presensi.scanner') ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                                    <span>Scan Presensi QR</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('guru.profil') }}"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg {{ request()->routeIs('guru.profil*') ? 'bg-blue-600/30 text-blue-200 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }} transition-colors text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('guru.profil*') ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                                    <span>Edit Profil Guru</span>
                                </a>
                            </li>
                            @else
                            <li>
                                <a href="{{ route('jadwal.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg {{ request()->routeIs('jadwal.*') ? 'bg-blue-600/30 text-blue-200 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }} transition-colors text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('jadwal.*') ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                                    <span>Jadwal Mengajar</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('presensi.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg {{ request()->routeIs('presensi.index', 'presensi') ? 'bg-blue-600/30 text-blue-200 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }} transition-colors text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('presensi.index', 'presensi') ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                                    <span>Layar QR & Presensi</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('presensi.riwayat', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg {{ request()->routeIs('presensi.riwayat') ? 'bg-blue-600/30 text-blue-200 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }} transition-colors text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('presensi.riwayat') ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                                    <span>Riwayat Kehadiran Guru</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </details>

                    @if(! $isGuru)
                    <!-- Dropdown: Keuangan & Kas -->
                    <details class="group select-none" {{ $catKeuanganActive ? 'open' : '' }}>
                        <summary class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-white/5 font-medium cursor-pointer list-none [&::-webkit-details-marker]:hidden transition-all text-xs">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                <span class="font-medium text-xs">Keuangan & Kas</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-open:rotate-180 transition-transform duration-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <ul class="space-y-1 mt-1 pl-4 ml-3.5 border-l border-white/10 py-1">
                            <li>
                                <a href="{{ route('spp.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg {{ request()->routeIs('spp.*') ? 'bg-blue-600/30 text-blue-200 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }} transition-colors text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('spp.*') ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                                    <span>Tagihan & SPP Siswa</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('kas.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg {{ request()->routeIs('kas.*') ? 'bg-blue-600/30 text-blue-200 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }} transition-colors text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('kas.*') ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                                    <span>Buku Kas & Kwitansi</span>
                                </a>
                            </li>
                        </ul>
                    </details>
                    @endif

                    @if($isPusat)
                    <!-- Dropdown: Informasi & Keamanan -->
                    <details class="group select-none" {{ $catMasterActive ? 'open' : '' }}>
                        <summary class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-white/5 font-medium cursor-pointer list-none [&::-webkit-details-marker]:hidden transition-all text-xs">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-4 h-4 text-sky-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium text-xs">Informasi & Keamanan</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-open:rotate-180 transition-transform duration-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </summary>
                        <ul class="space-y-1 mt-1 pl-4 ml-3.5 border-l border-white/10 py-1">
                            <li>
                                <a href="{{ route('pengaturan.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg {{ request()->routeIs('pengaturan.*') ? 'bg-blue-600/30 text-blue-200 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }} transition-colors text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('pengaturan.*') ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                                    <span>Master Data & Cabang</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('audit.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg {{ request()->routeIs('audit.*') ? 'bg-blue-600/30 text-blue-200 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' }} transition-colors text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('audit.*') ? 'bg-blue-400' : 'bg-slate-500' }}"></span>
                                    <span>Audit Log & Keamanan</span>
                                </a>
                            </li>
                        </ul>
                    </details>
                    @endif
                </nav>

                <!-- Sidebar Footer: Logged in User & Logout (matching image) -->
                <div class="pt-3.5 border-t border-white/10 mt-auto">
                    <div class="flex items-center justify-between gap-2">
                        @if($isGuru)
                        <a href="{{ route('guru.profil') }}" class="flex items-center gap-2.5 min-w-0 group hover:opacity-90 transition-opacity" title="Klik untuk Edit Profil Pengajar">
                            @if($authUser?->foto_url)
                                <div class="relative overflow-hidden w-9 h-9 rounded-full shrink-0 shadow-md shadow-blue-600/30 group-hover:scale-105 transition-transform border border-white/20 bg-slate-700">
                                    <div class="skeleton-shimmer absolute inset-0"></div>
                                    <img src="{{ $authUser->foto_url }}" alt="{{ $authUser->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover relative z-10 opacity-0 transition-opacity duration-300" onload="this.classList.remove('opacity-0'); this.previousElementSibling?.remove();" onerror="this.parentElement.style.display='none'; this.parentElement.nextElementSibling?.classList.remove('hidden');">
                                </div>
                                <div class="hidden w-9 h-9 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-md shadow-blue-600/30 group-hover:scale-105 transition-transform">
                                    {{ strtoupper(substr($authUser?->name ?? 'G', 0, 1)) }}
                                </div>
                            @else
                                <div class="w-9 h-9 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-md shadow-blue-600/30 group-hover:scale-105 transition-transform">
                                    {{ strtoupper(substr($authUser?->name ?? 'G', 0, 1)) }}
                                </div>
                            @endif
                            <div class="truncate">
                                <p class="text-xs font-bold text-white truncate leading-tight group-hover:text-blue-200 transition-colors">
                                    {{ $authUser?->name ?? 'Guru Pengajar' }}
                                </p>
                                <p class="text-[10px] text-blue-300/80 truncate mt-0.5 flex items-center gap-1">
                                    <span>Edit Profil</span>
                                    <span>&rarr;</span>
                                </p>
                            </div>
                        </a>
                        @else
                        <div class="flex items-center gap-2.5 min-w-0">
                            @if($authUser?->foto_url)
                                <img src="{{ $authUser->foto_url }}" alt="{{ $authUser->name }}" loading="lazy" decoding="async" class="w-9 h-9 rounded-full object-cover shrink-0 shadow-md shadow-blue-600/30 border border-white/20">
                            @else
                                <div class="w-9 h-9 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-md shadow-blue-600/30">
                                    {{ strtoupper(substr($authUser?->name ?? $currentCabang['pimpinan'] ?? 'A', 0, 1)) }}
                                </div>
                            @endif
                            <div class="truncate">
                                <p class="text-xs font-bold text-white truncate leading-tight">
                                    {{ $authUser?->name ?? ($isPusat ? 'Administrator' : ($isCabang ? 'Pimpinan Cabang' : 'Guru Pengajar')) }}
                                </p>
                                <p class="text-[10px] text-slate-400 truncate mt-0.5">
                                    {{ $authUser?->email ?? ($isPusat ? 'admin@pusat.com' : ($isCabang ? 'cabang@simanles.com' : 'guru@simanles.com')) }}
                                </p>
                            </div>
                        </div>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                            @csrf
                            <button type="submit" title="Keluar dari sistem"
                                class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors">
                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 w-full">
            <!-- Top Bar Header (Compact & Mobile-Optimized, Lebih Tinggi) -->
            <header
                class="sticky top-0 z-30 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xs border-b border-slate-200/80 dark:border-slate-800 px-3 sm:px-5 lg:px-6 py-3.5 sm:py-4 min-h-[60px] sm:min-h-[68px] flex items-center justify-between gap-2 sm:gap-3 shadow-2xs">
                <div class="flex items-center gap-2 sm:gap-2.5 min-w-0">
                    @if(! $isGuru)
                    <!-- Sidebar Toggle (Mobile Drawer & Desktop Collapse, disembunyikan di mobile cabang & pusat) -->
                    <button type="button" onclick="toggleSidebar()"
                        class="{{ ($isCabang || $isPusat) ? 'hidden lg:flex' : 'flex' }} p-2 -ml-1 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg items-center justify-center shrink-0 transition-colors"
                        aria-label="Buka/Tutup menu navigasi" title="Buka/Tutup Sidebar">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    @endif

                    <!-- Mobile Brand Logo -->
                    <a href="{{ $isGuru ? route('jadwal.index') : route('admin.dashboard') }}" class="flex items-center gap-1.5 lg:hidden shrink-0"
                        title="LKP Sang Siroju Lillah">
                        <img src="{{ asset('logoo.png') }}" alt="Logo LKP Sang Siroju Lillah" loading="lazy" decoding="async"
                            class="w-8 h-8 object-contain rounded-md border border-slate-200 dark:border-slate-700 bg-white p-0.5 img-skeleton" onload="this.classList.add('img-loaded')">
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
                    @if($isGuru)
                        <!-- User Profile Button with Dropdown (Khusus Guru) -->
                        <div class="relative">
                            <button type="button" id="user-profile-menu-button" onclick="toggleProfileDropdown()"
                                class="flex items-center gap-2 p-1.5 sm:px-2.5 sm:py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800 dark:hover:bg-slate-700/80 border border-slate-200/80 dark:border-slate-700 transition-colors cursor-pointer select-none"
                                title="Menu Profil & Tema" aria-expanded="false" aria-haspopup="true">
                                @if($authUser?->foto_url)
                                    <div class="relative overflow-hidden w-7 h-7 sm:w-7.5 sm:h-7.5 rounded-lg shrink-0 shadow-xs bg-slate-200 dark:bg-slate-800">
                                        <div class="skeleton-shimmer absolute inset-0"></div>
                                        <img src="{{ $authUser->foto_url }}" alt="{{ $authUser->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover relative z-10 opacity-0 transition-opacity duration-300" onload="this.classList.remove('opacity-0'); this.previousElementSibling?.remove();" onerror="this.parentElement.style.display='none'; this.parentElement.nextElementSibling?.classList.remove('hidden');">
                                    </div>
                                    <div class="hidden w-7 h-7 sm:w-7.5 sm:h-7.5 rounded-lg bg-blue-900 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-xs">
                                        {{ strtoupper(substr($authUser?->name ?? 'U', 0, 1)) }}
                                    </div>
                                @else
                                    <div class="w-7 h-7 sm:w-7.5 sm:h-7.5 rounded-lg bg-blue-900 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-xs">
                                        {{ strtoupper(substr($authUser?->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <div class="hidden sm:block text-left min-w-0 max-w-[130px]">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate leading-tight">
                                        {{ $authUser?->name ?? 'Pengguna' }}
                                    </p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium truncate">
                                        Guru Pengajar
                                    </p>
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 shrink-0 transition-transform duration-200" id="user-profile-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu (Khusus Guru) -->
                            <div id="user-profile-dropdown"
                                class="hidden absolute right-0 mt-2 w-60 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-xl z-50 py-1.5 text-xs animate-in fade-in duration-150">
                                <!-- Info Pengguna -->
                                <div class="px-3.5 py-2.5 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3">
                                    @if($authUser?->foto_url)
                                        <div class="relative overflow-hidden w-9 h-9 rounded-xl shrink-0 border border-slate-200 dark:border-slate-700 shadow-xs bg-slate-200 dark:bg-slate-800">
                                            <div class="skeleton-shimmer absolute inset-0"></div>
                                            <img src="{{ $authUser->foto_url }}" alt="{{ $authUser->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover relative z-10 opacity-0 transition-opacity duration-300" onload="this.classList.remove('opacity-0'); this.previousElementSibling?.remove();" onerror="this.parentElement.style.display='none'; this.parentElement.nextElementSibling?.classList.remove('hidden');">
                                        </div>
                                        <div class="hidden w-9 h-9 rounded-xl bg-blue-900 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-xs">
                                            {{ strtoupper(substr($authUser?->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @else
                                        <div class="w-9 h-9 rounded-xl bg-blue-900 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-xs">
                                            {{ strtoupper(substr($authUser?->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-slate-900 dark:text-white truncate leading-tight">{{ $authUser?->name }}</p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ $authUser?->email }}</p>
                                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-900">
                                            Guru Pengajar
                                        </span>
                                    </div>
                                </div>

                                <!-- Menu Edit Profil (Guru) -->
                                <a href="{{ route('guru.profil') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80 hover:text-blue-900 dark:hover:text-blue-300 font-medium transition-colors">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>Edit Profil Guru</span>
                                </a>

                                <!-- Slide Toggle: Mode Gelap / Terang -->
                                <div onclick="toggleDarkMode()"
                                    class="px-3.5 py-2 flex items-center justify-between text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80 rounded-lg transition-colors cursor-pointer select-none"
                                    title="Klik untuk beralih mode gelap / terang">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-amber-400 hidden dark:block shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                        </svg>
                                        <svg class="w-4 h-4 text-slate-500 block dark:hidden shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <span class="font-medium text-xs">Mode Gelap</span>
                                    </div>
                                    <!-- Sliding Switch Control -->
                                    <div class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out bg-slate-300 dark:bg-blue-600 pointer-events-none">
                                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out translate-x-0 dark:translate-x-4"></span>
                                    </div>
                                </div>

                                <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>

                                <!-- Logout Button -->
                                <button type="button" onclick="closeProfileDropdown(); openModal('modal-confirm-logout')"
                                    class="w-full flex items-center gap-2.5 px-3.5 py-2 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 font-medium transition-colors text-left cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Keluar dari Akun</span>
                                </button>
                            </div>
                        </div>
                    @else
                        <!-- Dark Mode Toggle Button (Admin Cabang & Admin Pusat) -->
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

                        <!-- Tombol Logout Mobile (Admin Cabang & Admin Pusat) -->
                        <button type="button" onclick="openModal('modal-confirm-logout')"
                            class="sm:hidden inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 text-xs font-semibold rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 dark:hover:bg-rose-900/50 border border-rose-200 dark:border-rose-900 transition-colors shadow-2xs"
                            title="Keluar dari akun">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Keluar</span>
                        </button>

                        <!-- Live QR Presensi Shortcut (Disembunyikan di mobile cabang & pusat) -->
                        <a href="{{ route('presensi.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                            class="{{ ($isCabang || $isPusat) ? 'hidden sm:inline-flex' : 'inline-flex' }} items-center gap-1 px-2.5 sm:px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-900 dark:text-blue-300 border border-blue-200/70 dark:border-blue-900 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors"
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

                        <!-- Primary Action: + Pendaftaran Siswa Baru (Disembunyikan di mobile cabang & pusat) -->
                        <button type="button" onclick="openModal('modal-pendaftaran')"
                            class="{{ ($isCabang || $isPusat) ? 'hidden sm:inline-flex' : 'inline-flex' }} items-center gap-1.5 px-3 sm:px-3.5 py-1.5 rounded-lg bg-blue-900 hover:bg-blue-800 text-white text-xs sm:text-sm font-semibold active:bg-blue-950 transition-all shadow-xs">
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

            <!-- Main Canvas Body (Fluid with bottom padding for mobile navbar clearance) -->
            <main class="flex-1 p-3 sm:p-4 lg:p-6 pb-24 sm:pb-28 lg:pb-6 w-full max-w-[1600px] mx-auto">
                {{-- Global Flash Messages: Floating Top-Right Toast for Welcome/Success --}}
                @if(session('success'))
                    <div id="toast-welcome"
                        class="fixed top-4 right-4 z-50 max-w-sm sm:max-w-md w-[calc(100%-2rem)] bg-white dark:bg-slate-900 border border-emerald-300 dark:border-emerald-700/80 rounded-xl p-3.5 shadow-xl flex items-center justify-between gap-3 transition-all duration-300">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-900 dark:text-white">Pemberitahuan</p>
                                <p class="text-xs text-slate-600 dark:text-slate-300 font-medium truncate sm:whitespace-normal">{{ session('success') }}</p>
                            </div>
                        </div>
                        <button type="button" onclick="closeToastWelcome()"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shrink-0"
                            aria-label="Tutup notifikasi">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <script>
                        function closeToastWelcome() {
                            const toast = document.getElementById('toast-welcome');
                            if (toast) {
                                toast.style.opacity = '0';
                                toast.style.transform = 'translateY(-10px)';
                                setTimeout(() => toast.remove(), 300);
                            }
                        }
                        setTimeout(closeToastWelcome, 5000);
                    </script>
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
            @if($isGuru)
            <!-- 1. Jadwal Mengajar (Kiri) -->
            <a href="{{ route('jadwal.index') }}"
                class="flex flex-col items-center justify-center py-1.5 px-3 {{ request()->routeIs('jadwal.*') ? 'text-blue-900 dark:text-blue-400 font-bold' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white font-medium' }} min-w-[70px] text-[10px] transition-colors">
                <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" x2="21" y1="10" y2="10" />
                </svg>
                <span>Jadwal</span>
            </a>

            <!-- 2. Presensi (Menonjol Bulat di Tengah, Sama Persis Seperti Role Cabang) -->
            <a href="{{ route('presensi.scanner') }}"
                class="flex flex-col items-center justify-center -mt-4 bg-blue-900 text-white rounded-full w-12 h-12 shadow-lg hover:bg-blue-800 active:scale-95 transition-transform shrink-0"
                title="Presensi Mandiri Guru">
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

            <!-- 3. Riwayat Kehadiran (Kanan) -->
            <a href="{{ route('presensi.riwayat') }}"
                class="flex flex-col items-center justify-center py-1.5 px-3 {{ request()->routeIs('presensi.riwayat') ? 'text-blue-900 dark:text-blue-400 font-bold' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white font-medium' }} min-w-[70px] text-[10px] transition-colors">
                <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Riwayat</span>
            </a>
            @else
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

            <!-- 4. Keuangan & SPP -->
            <a href="{{ route('spp.index') }}"
                class="flex flex-col items-center justify-center p-2 {{ request()->routeIs('spp.*') ? 'text-blue-900 dark:text-blue-400 font-bold' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }} min-w-[56px] text-[10px]">
                <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect width="20" height="14" x="2" y="5" rx="2" />
                    <line x1="2" x2="22" y1="10" y2="10" />
                </svg>
                <span>SPP</span>
            </a>

            <!-- 5. Menu Drawer -->
            <button type="button" onclick="toggleMobileMenu()"
                class="flex flex-col items-center justify-center p-2 text-slate-500 hover:text-slate-900 dark:hover:text-white min-w-[56px] text-[10px]">
                <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span>Menu</span>
            </button>
            @endif
        </div>
    </nav>

    <!-- Modal Konfirmasi Logout -->
    <div id="modal-confirm-logout" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeModal('modal-confirm-logout')"></div>
        <div class="fixed inset-0 z-10 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-sm w-full p-5 sm:p-6 shadow-2xl border border-slate-200 dark:border-slate-800 text-center space-y-4 pointer-events-auto max-h-[90vh] overflow-y-auto">
                <div class="w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center shadow-xs">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Konfirmasi Keluar</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                        Apakah Anda yakin ingin keluar dari {{ $isGuru ? 'Portal Pengajar' : ($isPusat ? 'Admin Pusat' : 'Pimpinan Cabang') }} SIMANLES?
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="button" onclick="closeModal('modal-confirm-logout')"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold text-xs transition-colors cursor-pointer">
                        Batal
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="w-full m-0 p-0 block">
                        @csrf
                        <button type="submit"
                            style="background-color: #dc2626 !important; color: #ffffff !important;"
                            class="w-full px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition-colors shadow-xs block text-center cursor-pointer">
                            Ya, Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

        function toggleProfileDropdown() {
            const dd = document.getElementById('user-profile-dropdown');
            const chevron = document.getElementById('user-profile-chevron');
            if (dd) {
                const isHidden = dd.classList.toggle('hidden');
                if (chevron) {
                    chevron.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(180deg)';
                }
            }
        }

        function closeProfileDropdown() {
            const dd = document.getElementById('user-profile-dropdown');
            const chevron = document.getElementById('user-profile-chevron');
            if (dd) {
                dd.classList.add('hidden');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        }

        document.addEventListener('click', function(e) {
            const btn = document.getElementById('user-profile-menu-button');
            const dd = document.getElementById('user-profile-dropdown');
            if (btn && dd && !btn.contains(e.target) && !dd.contains(e.target)) {
                closeProfileDropdown();
            }
        });
    </script>

    @stack('scripts')
</body>

</html>