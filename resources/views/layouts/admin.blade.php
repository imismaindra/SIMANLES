<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard Admin') — LKP Sang Siroju Lillah</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    @fonts

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="h-full font-sans text-slate-800 antialiased bg-slate-50/80 selection:bg-blue-100 selection:text-blue-900">
    <div class="min-h-full flex flex-col lg:flex-row">

        <!-- Mobile Sidebar Backdrop -->
        <div id="mobile-backdrop"
            class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs lg:hidden hidden transition-opacity duration-200"
            onclick="toggleMobileMenu()" aria-hidden="true"></div>

        <!-- Sidebar Navigation (Drawer on Mobile, Sticky on Desktop) -->
        <aside id="admin-sidebar"
            class="fixed inset-y-0 left-0 z-50 w-72 sm:w-80 lg:w-64 bg-slate-50 border-r border-slate-200/90 flex flex-col justify-between transition-transform duration-200 ease-in-out -translate-x-full lg:translate-x-0 lg:static lg:h-screen lg:sticky lg:top-0 shadow-2xl lg:shadow-none">
            <div class="flex flex-col h-full overflow-y-auto px-4 py-5">
                <!-- Brand Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-200/70">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                        <!-- Custom Academic Brand Logo -->
                        <div
                            class="w-10 h-10 rounded-xl bg-white border border-slate-200 shadow-2xs flex items-center justify-center p-1 group-hover:border-blue-400 group-hover:shadow-xs transition-all shrink-0">
                            <img src="{{ asset('logoo.png') }}" alt="Logo LKP Sang Siroju Lillah"
                                class="w-full h-full object-contain">
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-sm sm:text-base tracking-tight text-slate-900 truncate">Sang
                                    Siroju Lillah</span>
                                <span
                                    class="text-[9px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-blue-100 text-blue-900 shrink-0">
                                    {{ ($viewRole ?? 'cabang') === 'pusat' ? 'Pusat' : 'Cabang' }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 font-medium truncate">LKP Tempat Les & Bimbel</p>
                        </div>
                    </a>

                    <!-- Close button on mobile -->
                    <button type="button" onclick="toggleMobileMenu()"
                        class="p-2 -mr-1 text-slate-500 hover:text-slate-800 hover:bg-slate-200/60 rounded-lg lg:hidden"
                        aria-label="Tutup menu">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Multi-Cabang Context Switcher -->
                <div class="mt-4 p-3 rounded-xl bg-white border border-slate-200/90 shadow-2xs">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                        {{ ($viewRole ?? 'cabang') === 'pusat' ? 'Lingkup: Pusat (Semua Cabang)' : 'Cabang Aktif' }}
                    </label>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 min-w-0">
                            <span
                                class="w-2 h-2 rounded-full {{ ($viewRole ?? 'cabang') === 'pusat' ? 'bg-indigo-600' : 'bg-emerald-500' }} shrink-0"></span>
                            <div class="truncate">
                                <p class="text-xs font-semibold text-slate-900 truncate">
                                    {{ $currentCabang['nama'] ?? 'Cabang Ponokawan' }}
                                </p>
                                <p class="text-[10px] text-slate-400 font-mono truncate">
                                    Kode: {{ $currentCabang['kode'] ?? 'CAB-PNK' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="mt-5 space-y-5 flex-1 text-sm">
                    <!-- Kelompok: Operasional Cabang -->
                    <div>
                        <p class="px-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Operasional Cabang</p>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('admin.dashboard', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard', 'dashboard', 'admin.home') ? 'bg-blue-900 text-white font-medium shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard', 'dashboard', 'admin.home') ? 'text-blue-200' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <rect width="7" height="9" x="3" y="3" rx="1" />
                                            <rect width="7" height="5" x="14" y="3" rx="1" />
                                            <rect width="7" height="9" x="14" y="12" rx="1" />
                                            <rect width="7" height="5" x="3" y="16" rx="1" />
                                        </svg>
                                        <span>Dashboard Cabang</span>
                                    </div>
                                    @if(request()->routeIs('admin.dashboard', 'dashboard', 'admin.home'))
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('siswa.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-lg {{ request()->routeIs('siswa.*') ? 'bg-blue-900 text-white font-medium shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 {{ request()->routeIs('siswa.*') ? 'text-blue-200' : 'text-emerald-600' }}" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                            <circle cx="9" cy="7" r="4" />
                                            <line x1="19" y1="8" x2="19" y2="14" />
                                            <line x1="22" y1="11" x2="16" y2="11" />
                                        </svg>
                                        <span>Data & Pendaftaran Siswa</span>
                                    </div>
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ request()->routeIs('siswa.*') ? 'bg-blue-800 text-blue-100' : 'bg-emerald-50 text-emerald-800 border border-emerald-200/60' }}">
                                        {{ $currentCabang['siswa_aktif'] ?? 80 }}
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('guru.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-lg {{ request()->routeIs('guru.*') ? 'bg-blue-900 text-white font-medium shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 {{ request()->routeIs('guru.*') ? 'text-blue-200' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                            <circle cx="9" cy="7" r="4" />
                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                        </svg>
                                        <span>Distribusi & Guru</span>
                                    </div>
                                    <span class="text-[11px] font-semibold px-1.5 py-0.5 rounded {{ request()->routeIs('guru.*') ? 'bg-blue-800 text-blue-100' : 'bg-amber-50 text-amber-900 border border-amber-200/60' }} num-tabular">
                                        Max 6/guru
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('jadwal.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-lg {{ request()->routeIs('jadwal.*') ? 'bg-blue-900 text-white font-medium shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 {{ request()->routeIs('jadwal.*') ? 'text-blue-200' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                            <line x1="16" x2="16" y1="2" y2="6" />
                                            <line x1="8" x2="8" y1="2" y2="6" />
                                            <line x1="3" x2="21" y1="10" y2="10" />
                                        </svg>
                                        <span>Jadwal Mengajar</span>
                                    </div>
                                    <span class="text-[11px] {{ request()->routeIs('jadwal.*') ? 'text-blue-200' : 'text-slate-400' }} num-tabular">Anti-bentrok</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Kelompok: Presensi QR Dinamis -->
                    <div>
                        <p class="px-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Presensi Guru</p>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('presensi.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-lg {{ request()->routeIs('presensi.*') ? 'bg-blue-900 text-white font-medium shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 {{ request()->routeIs('presensi.*') ? 'text-blue-200' : 'text-blue-800' }}" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <rect width="5" height="5" x="3" y="3" rx="1" />
                                            <rect width="5" height="5" x="16" y="3" rx="1" />
                                            <rect width="5" height="5" x="3" y="16" rx="1" />
                                            <path d="M21 16h-3a2 2 0 0 0-2 2v3" />
                                            <path d="M21 21v.01" />
                                            <path d="M12 7v3a2 2 0 0 1-2 2H7" />
                                        </svg>
                                        <span>Presensi & Layar QR</span>
                                    </div>
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Kelompok: Keuangan & Buku Kas Cabang -->
                    <div>
                        <p class="px-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Keuangan & Kas</p>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('spp.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-lg {{ request()->routeIs('spp.*') ? 'bg-blue-900 text-white font-medium shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 {{ request()->routeIs('spp.*') ? 'text-blue-200' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <rect width="20" height="14" x="2" y="5" rx="2" />
                                            <line x1="2" x2="22" y1="10" y2="10" />
                                        </svg>
                                        <span>Tagihan & SPP Siswa</span>
                                    </div>
                                    <span class="text-[11px] font-semibold {{ request()->routeIs('spp.*') ? 'text-blue-100' : 'text-amber-800' }} num-tabular">Bulanan</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('kas.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-lg {{ request()->routeIs('kas.*') ? 'bg-blue-900 text-white font-medium shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 {{ request()->routeIs('kas.*') ? 'text-blue-200' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <line x1="12" x2="12" y1="2" y2="22" />
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                        </svg>
                                        <span>Buku Kas & Kwitansi</span>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Kelompok: Master Data & Cabang -->
                    <div>
                        <p class="px-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Master & Pengaturan</p>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('pengaturan.index', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-lg {{ request()->routeIs('pengaturan.*') ? 'bg-blue-900 text-white font-medium shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 {{ request()->routeIs('pengaturan.*') ? 'text-blue-200' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="3" />
                                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                                        </svg>
                                        <span>Master Data & Cabang</span>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Sidebar Footer: Logged in User & Role Switch -->
                <div class="pt-4 border-t border-slate-200/80 mt-auto">
                    <div class="p-2.5 rounded-xl bg-white border border-slate-200/80">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-900 text-white font-semibold flex items-center justify-center text-xs shrink-0">
                                {{ strtoupper(substr($currentCabang['pimpinan'] ?? 'PC', 0, 2)) }}
                            </div>
                            <div class="truncate">
                                <p class="text-xs font-semibold text-slate-900 truncate">{{ $currentCabang['pimpinan'] ?? 'Pimpinan Cabang' }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ ($viewRole ?? 'cabang') === 'pusat' ? 'Pimpinan Pusat' : 'Pimpinan Cabang' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 pb-20 lg:pb-0">
            <!-- Top Bar Header (Compact & Mobile-Optimized) -->
            <header
                class="sticky top-0 z-30 bg-white/95 backdrop-blur-xs border-b border-slate-200/80 px-3.5 sm:px-6 lg:px-8 py-2.5 sm:py-3 flex items-center justify-between gap-2 sm:gap-4">
                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                    <!-- Mobile Hamburger (44px target) -->
                    <button type="button" onclick="toggleMobileMenu()"
                        class="p-2.5 -ml-1 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg lg:hidden flex items-center justify-center shrink-0"
                        aria-label="Buka menu navigasi">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Mobile Brand Logo -->
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1.5 lg:hidden shrink-0"
                        title="LKP Sang Siroju Lillah">
                        <img src="{{ asset('logoo.png') }}" alt="Logo LKP Sang Siroju Lillah"
                            class="w-7 h-7 object-contain rounded-md border border-slate-200 bg-white p-0.5">
                    </a>

                    <!-- Branch Selector Dropdown (Fluid on small mobile) -->
                    <div class="flex items-center gap-1.5 min-w-0">
                        <label for="topbar-cabang-select"
                            class="text-slate-500 text-xs font-medium hidden md:inline">Cabang:</label>
                        <select id="topbar-cabang-select" onchange="window.location.href = '?cabang_id=' + this.value;"
                            class="bg-slate-100 hover:bg-slate-200/70 border border-slate-200 rounded-lg py-1.5 px-2 sm:px-2.5 text-xs font-semibold text-slate-900 focus:outline-none focus:ring-1 focus:ring-blue-600 cursor-pointer max-w-[170px] sm:max-w-xs truncate">
                            @foreach($daftarCabang as $cb)
                                <option value="{{ $cb['id'] }}" {{ ($currentCabang['id'] ?? '') === $cb['id'] ? 'selected' : '' }}>
                                    {{ $cb['nama'] }} ({{ $cb['kode'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Right Action Cluster -->
                <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                    <!-- Live QR Presensi Shortcut (Always reachable) -->
                    <button type="button" onclick="openModal('modal-qr-display')"
                        class="inline-flex items-center gap-1 px-2.5 sm:px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-50 text-blue-900 border border-blue-200/70 hover:bg-blue-100 transition-colors"
                        title="Tampilkan QR code absensi guru">
                        <svg class="w-3.5 h-3.5 text-blue-900" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <rect width="5" height="5" x="3" y="3" rx="1" />
                            <rect width="5" height="5" x="16" y="3" rx="1" />
                            <rect width="5" height="5" x="3" y="16" rx="1" />
                        </svg>
                        <span class="hidden sm:inline">QR Absen</span>
                    </button>

                    <!-- Primary Action: + Pendaftaran Siswa Baru -->
                    <button type="button" onclick="openModal('modal-pendaftaran')"
                        class="inline-flex items-center gap-1.5 px-3 sm:px-3.5 py-1.5 rounded-lg bg-blue-900 text-white text-xs sm:text-sm font-semibold hover:bg-blue-800 active:bg-blue-950 transition-all shadow-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        <span class="hidden xs:inline sm:inline">Daftar Siswa</span>
                        <span class="xs:hidden sm:hidden">+ Siswa</span>
                    </button>
                </div>
            </header>

            <!-- Main Canvas Body (Fluid with padding scaled for mobile) -->
            <main class="flex-1 p-3.5 sm:p-5 lg:p-8 max-w-7xl w-full mx-auto">
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
    <nav class="lg:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-slate-200/90 z-40 px-2 py-1.5 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]"
        aria-label="Navigasi Bawah">
        <div class="flex items-center justify-around max-w-md mx-auto">
            <!-- 1. Home / Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
                class="flex flex-col items-center justify-center p-2 {{ request()->routeIs('admin.dashboard') ? 'text-blue-900 font-bold' : 'text-slate-500 hover:text-slate-900' }} min-w-[56px] text-[10px]">
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
                class="flex flex-col items-center justify-center p-2 {{ request()->routeIs('siswa.*') ? 'text-blue-900 font-bold' : 'text-slate-500 hover:text-slate-900' }} min-w-[56px] text-[10px]">
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
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect width="5" height="5" x="3" y="3" rx="1" />
                    <rect width="5" height="5" x="16" y="3" rx="1" />
                    <rect width="5" height="5" x="3" y="16" rx="1" />
                </svg>
            </a>

            <!-- 4. Keuangan & SPP -->
            <a href="{{ route('spp.index') }}"
                class="flex flex-col items-center justify-center p-2 {{ request()->routeIs('spp.*') ? 'text-blue-900 font-bold' : 'text-slate-500 hover:text-slate-900' }} min-w-[56px] text-[10px]">
                <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect width="20" height="14" x="2" y="5" rx="2" />
                    <line x1="2" x2="22" y1="10" y2="10" />
                </svg>
                <span>SPP</span>
            </a>

            <!-- 5. Menu Drawer -->
            <button type="button" onclick="toggleMobileMenu()"
                class="flex flex-col items-center justify-center p-2 text-slate-500 hover:text-slate-900 min-w-[56px] text-[10px]">
                <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span>Menu</span>
            </button>
        </div>
    </nav>

    <!-- Global Helpers & Scripts -->
    <script>
        function toggleMobileMenu(forceState) {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('mobile-backdrop');
            const isCurrentlyClosed = sidebar.classList.contains('-translate-x-full');

            const shouldOpen = typeof forceState === 'boolean' ? forceState : isCurrentlyClosed;

            if (shouldOpen) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
            }
        }

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