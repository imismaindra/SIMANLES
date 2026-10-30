<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard Admin') — SIMANLES</title>

    @fonts

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="h-full font-sans text-slate-800 antialiased bg-slate-50/80 selection:bg-blue-100 selection:text-blue-900">
    <div x-data="{ mobileMenuOpen: false, searchOpen: false }" class="min-h-full flex flex-col lg:flex-row">
        
        <!-- Mobile Sidebar Backdrop -->
        <div 
            id="mobile-backdrop"
            class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-xs lg:hidden hidden transition-opacity duration-200"
            onclick="toggleMobileMenu()"
        ></div>

        <!-- Sidebar Navigation -->
        <aside 
            id="admin-sidebar"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-50 border-r border-slate-200/90 flex flex-col justify-between transition-transform duration-200 ease-in-out -translate-x-full lg:translate-x-0 lg:static lg:h-screen lg:sticky lg:top-0"
        >
            <div class="flex flex-col h-full overflow-y-auto px-4 py-5">
                <!-- Brand Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-200/70">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                        <!-- Custom Academic Monogram Emblem -->
                        <div class="w-10 h-10 rounded-lg bg-blue-900 flex items-center justify-center text-white shadow-xs group-hover:bg-blue-800 transition-colors">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                                <path d="M6 6h10"/>
                                <path d="M6 10h10"/>
                                <path d="M6 14h6"/>
                                <polygon points="12 2 15 8 9 8" fill="currentColor" stroke="none" opacity="0.3"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-base tracking-tight text-slate-900">SIMANLES</span>
                                <span class="text-[10px] uppercase font-semibold px-1.5 py-0.5 rounded bg-blue-100 text-blue-800">Bimbel</span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium">Sistem Manajemen Les</p>
                        </div>
                    </a>

                    <!-- Close button on mobile -->
                    <button 
                        type="button" 
                        onclick="toggleMobileMenu()" 
                        class="p-1.5 text-slate-400 hover:text-slate-600 rounded-md lg:hidden"
                        aria-label="Tutup menu"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Academic Scope Switcher -->
                <div class="mt-4 p-2.5 rounded-lg bg-white border border-slate-200/90 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></div>
                            <div class="truncate">
                                <p class="text-xs font-semibold text-slate-900 truncate">{{ $currentBranch ?? 'Cabang Utama Diponegoro' }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ $academicYear ?? 'Tahun Ajaran 2026/2027' }} · {{ $currentSemester ?? 'Semester Ganjil' }}</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                        </svg>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="mt-5 space-y-6 flex-1 text-sm">
                    <!-- Kelompok: Utama -->
                    <div>
                        <p class="px-2 text-xs font-medium text-slate-400 uppercase tracking-wider mb-2">Utama</p>
                        <ul class="space-y-1">
                            <li>
                                <a 
                                    href="{{ route('admin.dashboard') }}" 
                                    class="flex items-center justify-between px-3 py-2 rounded-lg bg-blue-900 text-white font-medium shadow-xs transition-colors"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <rect width="7" height="9" x="3" y="3" rx="1"/>
                                            <rect width="7" height="5" x="14" y="3" rx="1"/>
                                            <rect width="7" height="9" x="14" y="12" rx="1"/>
                                            <rect width="7" height="5" x="3" y="16" rx="1"/>
                                        </svg>
                                        <span>Dashboard</span>
                                    </div>
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                </a>
                            </li>
                            <li>
                                <a 
                                    href="#jadwal" 
                                    class="flex items-center justify-between px-3 py-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 transition-colors font-medium"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                                            <line x1="16" x2="16" y1="2" y2="6"/>
                                            <line x1="8" x2="8" y1="2" y2="6"/>
                                            <line x1="3" x2="21" y1="10" y2="10"/>
                                        </svg>
                                        <span>Jadwal & Sesi Les</span>
                                    </div>
                                    <span class="text-[11px] font-semibold px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 num-tabular">24 hari ini</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Kelompok: Akademik & Pengajaran -->
                    <div>
                        <p class="px-2 text-xs font-medium text-slate-400 uppercase tracking-wider mb-2">Akademik</p>
                        <ul class="space-y-1">
                            <li>
                                <a 
                                    href="#siswa" 
                                    class="flex items-center justify-between px-3 py-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 transition-colors font-medium"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                        <span>Data Siswa & Wali</span>
                                    </div>
                                    <span class="text-[11px] text-slate-400 num-tabular">342</span>
                                </a>
                            </li>
                            <li>
                                <a 
                                    href="#tentor" 
                                    class="flex items-center justify-between px-3 py-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 transition-colors font-medium"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                        </svg>
                                        <span>Tentor & Pengajar</span>
                                    </div>
                                    <span class="text-[11px] text-slate-400 num-tabular">28</span>
                                </a>
                            </li>
                            <li>
                                <a 
                                    href="#ruangan" 
                                    class="flex items-center justify-between px-3 py-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 transition-colors font-medium"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <rect width="18" height="18" x="3" y="3" rx="2"/>
                                            <path d="M3 9h18"/>
                                            <path d="M9 21V9"/>
                                        </svg>
                                        <span>Ruangan & Bilik Les</span>
                                    </div>
                                    <span class="text-[11px] text-slate-400 num-tabular">8 ruang</span>
                                </a>
                            </li>
                            <li>
                                <a 
                                    href="#presensi" 
                                    class="flex items-center justify-between px-3 py-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 transition-colors font-medium"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <polyline points="9 11 12 14 22 4"/>
                                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                        </svg>
                                        <span>Presensi & Log Belajar</span>
                                    </div>
                                    <span class="text-[11px] font-semibold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 num-tabular">97.2%</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Kelompok: Administrasi & Keuangan -->
                    <div>
                        <p class="px-2 text-xs font-medium text-slate-400 uppercase tracking-wider mb-2">Keuangan & SPP</p>
                        <ul class="space-y-1">
                            <li>
                                <a 
                                    href="#keuangan" 
                                    class="flex items-center justify-between px-3 py-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 transition-colors font-medium"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <rect width="20" height="14" x="2" y="5" rx="2"/>
                                            <line x1="2" x2="22" y1="10" y2="10"/>
                                        </svg>
                                        <span>Tagihan & Pembayaran</span>
                                    </div>
                                    <span class="text-[11px] font-semibold px-1.5 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200/60 num-tabular">3 verif</span>
                                </a>
                            </li>
                            <li>
                                <a 
                                    href="#paket" 
                                    class="flex items-center justify-between px-3 py-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 transition-colors font-medium"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                        </svg>
                                        <span>Paket Belajar & Tarif</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a 
                                    href="#laporan" 
                                    class="flex items-center justify-between px-3 py-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 transition-colors font-medium"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <line x1="18" x2="18" y1="20" y2="10"/>
                                            <line x1="12" x2="12" y1="20" y2="4"/>
                                            <line x1="6" x2="6" y1="20" y2="14"/>
                                        </svg>
                                        <span>Laporan Rekapitulasi</span>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Sidebar Footer: Logged in User -->
                <div class="pt-4 border-t border-slate-200/80 mt-auto">
                    <div class="flex items-center justify-between p-2 rounded-lg hover:bg-white transition-colors border border-transparent hover:border-slate-200/80">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-900 font-semibold flex items-center justify-center text-xs shrink-0 border border-blue-200">
                                NR
                            </div>
                            <div class="truncate">
                                <p class="text-xs font-semibold text-slate-900 truncate">Nisa Rahmadani</p>
                                <p class="text-[11px] text-slate-500 truncate">Admin Akademik</p>
                            </div>
                        </div>
                        <button 
                            type="button" 
                            title="Keluar" 
                            class="p-1.5 text-slate-400 hover:text-slate-600 rounded-md transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Bar Header -->
            <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-xs border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Mobile Hamburger -->
                    <button 
                        type="button" 
                        onclick="toggleMobileMenu()" 
                        class="p-2 -ml-2 text-slate-500 hover:text-slate-700 rounded-lg lg:hidden"
                        aria-label="Buka menu navigasi"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <!-- Academic Breadcrumb -->
                    <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500 font-medium">
                        <span class="text-slate-800 font-semibold">SIMANLES Academia</span>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-600">Diponegoro</span>
                        <span class="text-slate-300">/</span>
                        <span class="text-blue-900 font-medium bg-blue-50 px-2 py-0.5 rounded text-[11px]">Ganjil 2026/2027</span>
                    </div>
                </div>

                <!-- Right Action Cluster -->
                <div class="flex items-center gap-2.5 sm:gap-3">
                    <!-- Search Trigger / Quick Command -->
                    <div class="relative hidden md:block">
                        <div class="flex items-center gap-2 px-3 py-1.5 text-xs text-slate-400 bg-slate-50 border border-slate-200 rounded-lg hover:border-slate-300 transition-colors cursor-pointer w-64">
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" x2="16.65" y1="21" y2="16.65"/>
                            </svg>
                            <span class="truncate">Cari siswa, tentor, kuitansi...</span>
                            <kbd class="ml-auto text-[10px] bg-white border border-slate-200 rounded px-1 text-slate-500">⌘K</kbd>
                        </div>
                    </div>

                    <!-- Freshness / Clock Status -->
                    <div class="hidden xl:flex items-center gap-2 text-xs text-slate-500 border-r border-slate-200 pr-3 mr-1">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="num-tabular font-medium text-slate-600">30 Okt 2026 · 14:15 WIB</span>
                    </div>

                    <!-- Notifications Dropdown Trigger -->
                    <div class="relative">
                        <button 
                            type="button" 
                            onclick="toggleUrgentPanel()"
                            class="relative p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors border border-transparent hover:border-slate-200"
                            title="Pemberitahuan Mendesak"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-600"></span>
                        </button>
                    </div>

                    <!-- Primary Quick Action: + Catat Sesi Baru -->
                    <button 
                        type="button" 
                        onclick="openModal('modal-sesi-baru')"
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg bg-blue-900 text-white text-xs sm:text-sm font-semibold hover:bg-blue-800 active:bg-blue-950 transition-all shadow-xs shrink-0"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        <span class="hidden sm:inline">Catat Sesi Baru</span>
                        <span class="sm:hidden">Sesi</span>
                    </button>
                </div>
            </header>

            <!-- Main Canvas Body -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                @yield('content')
            </main>

            <!-- Footer Meta -->
            <footer class="border-t border-slate-200/80 px-4 sm:px-6 lg:px-8 py-4 bg-white/60 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2 mt-auto">
                <p>© 2026 SIMANLES — Sistem Informasi Manajemen Bimbingan Belajar & Les Privat. Hak Cipta Dilindungi.</p>
                <div class="flex items-center gap-4 text-slate-400">
                    <span>Versi 2.4.0</span>
                    <span>·</span>
                    <span>Server: Aktif (24ms)</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function toggleMobileMenu() {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('mobile-backdrop');
            const isClosed = sidebar.classList.contains('-translate-x-full');
            
            if (isClosed) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
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

        function toggleUrgentPanel() {
            const panel = document.getElementById('urgent-actions-panel');
            if (panel) {
                panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                panel.classList.add('ring-2', 'ring-blue-500');
                setTimeout(() => {
                    panel.classList.remove('ring-2', 'ring-blue-500');
                }, 2000);
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
