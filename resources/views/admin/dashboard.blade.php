@extends('layouts.admin')

@section('title', 'Dashboard Operasional Akademik')

@section('content')
<div class="space-y-6">

    <!-- Page Title & Scope Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 pb-2 border-b border-slate-200/70">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Dashboard Operasional</h1>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                    Sesi Aktif
                </span>
            </div>
            <p class="mt-1 text-sm text-slate-600 max-w-3xl leading-relaxed">
                Hari ini terdapat <span class="font-semibold text-slate-800">24 sesi pembelajaran</span> terjadwal di 6 ruang & bilik privat, dengan tingkat kehadiran tentor 100% dan <span class="text-amber-800 font-semibold underline underline-offset-2">2 pengajuan reschedule</span> yang memerlukan tindakan admin.
            </p>
        </div>

        <!-- Scope & Date Filters -->
        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap shrink-0">
            <!-- Program Filter Dropdown/Tabs -->
            <div class="inline-flex p-1 rounded-lg bg-slate-200/70 text-xs font-medium text-slate-600">
                <button type="button" class="px-2.5 py-1 rounded-md bg-white text-slate-900 font-semibold shadow-2xs">Semua Program</button>
                <button type="button" class="px-2.5 py-1 rounded-md hover:text-slate-900 transition-colors">Privat 1-on-1</button>
                <button type="button" class="px-2.5 py-1 rounded-md hover:text-slate-900 transition-colors">Reguler</button>
                <button type="button" class="px-2.5 py-1 rounded-md hover:text-slate-900 transition-colors">SNBT</button>
            </div>

            <!-- Quick Date Filter -->
            <div class="flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white border border-slate-200/90 text-xs font-medium text-slate-700 shadow-2xs">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                    <line x1="16" x2="16" y1="2" y2="6"/>
                    <line x1="8" x2="8" y1="2" y2="6"/>
                    <line x1="3" x2="21" y1="10" y2="10"/>
                </svg>
                <span>Jumat, 30 Okt 2026</span>
            </div>
        </div>
    </div>

    <!-- KPI Strip: Unified 4-Column Divided Container (Anti-Slop Pattern) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 bg-white border border-slate-200/90 rounded-xl divide-y sm:divide-y-0 sm:divide-x divide-slate-200/80 shadow-2xs">
        
        <!-- KPI 1: Sesi Hari Ini -->
        <div class="p-5">
            <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
                <span>Sesi Belajar Hari Ini</span>
                <span class="text-[11px] text-slate-400">Jumat</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-3xl font-bold tracking-tight text-slate-900 num-tabular">{{ $kpi['sesi_hari_ini']['total'] }}</span>
                <span class="text-xs text-slate-500 font-medium">sesi</span>
            </div>
            <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <div class="flex items-center gap-1.5 num-tabular">
                    <span class="text-emerald-700 font-semibold">{{ $kpi['sesi_hari_ini']['selesai'] }}</span> selesai
                    <span class="text-slate-300">·</span>
                    <span class="text-blue-700 font-semibold">{{ $kpi['sesi_hari_ini']['berlangsung'] }}</span> aktif
                    <span class="text-slate-300">·</span>
                    <span class="text-slate-600 font-semibold">{{ $kpi['sesi_hari_ini']['siap_mulai'] }}</span> nanti
                </div>
            </div>
        </div>

        <!-- KPI 2: Siswa Aktif Terdaftar -->
        <div class="p-5">
            <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
                <span>Siswa Aktif Terdaftar</span>
                <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">+18 baru</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-3xl font-bold tracking-tight text-slate-900 num-tabular">{{ $kpi['siswa_aktif']['total'] }}</span>
                <span class="text-xs text-slate-500 font-medium">siswa</span>
            </div>
            <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center gap-2 text-xs text-slate-500 num-tabular">
                <span><strong class="text-slate-800">{{ $kpi['siswa_aktif']['privat'] }}</strong> Privat</span>
                <span class="text-slate-300">·</span>
                <span><strong class="text-slate-800">{{ $kpi['siswa_aktif']['reguler'] }}</strong> Reguler</span>
                <span class="text-slate-300">·</span>
                <span><strong class="text-slate-800">{{ $kpi['siswa_aktif']['intensif'] }}</strong> SNBT</span>
            </div>
        </div>

        <!-- KPI 3: Presensi Kehadiran Siswa -->
        <div class="p-5">
            <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
                <span>Tingkat Presensi Siswa</span>
                <span class="text-[11px] text-slate-400">Target ≥95%</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-3xl font-bold tracking-tight text-slate-900 num-tabular">{{ $kpi['presensi_siswa']['persentase'] }}%</span>
                <span class="text-xs text-emerald-700 font-medium">+2.2% di atas target</span>
            </div>
            <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 num-tabular">
                <span>{{ $kpi['presensi_siswa']['hadir'] }} dari {{ $kpi['presensi_siswa']['terjadwal'] }} hadir</span>
                <span class="text-slate-400">{{ $kpi['presensi_siswa']['izin'] }} izin · {{ $kpi['presensi_siswa']['dispensasi'] }} lomba</span>
            </div>
        </div>

        <!-- KPI 4: Penerimaan SPP & Sesi -->
        <div class="p-5">
            <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
                <span>Realisasi SPP & Paket Les</span>
                <span class="text-[11px] text-slate-400">Okt 2026</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 num-tabular">Rp {{ number_format($kpi['keuangan']['terkumpul'] / 1000000, 1, ',', '.') }}jt</span>
                <span class="text-xs text-slate-500 font-medium">/ 90jt</span>
            </div>
            <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 num-tabular">
                <span class="font-semibold text-slate-800">{{ $kpi['keuangan']['persentase'] }}% tercapai</span>
                <span class="text-amber-800 font-medium">{{ $kpi['keuangan']['jatuh_tempo'] }} invoice tempo</span>
            </div>
        </div>

    </div>

    <!-- Main Grid: 8 Columns Main Content + 4 Columns Rail -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT / MAIN COLUMN (8 COLS) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Panel 1: Monitoring Sesi Belajar Hari Ini -->
            <section id="jadwal" class="bg-white border border-slate-200/90 rounded-xl overflow-hidden shadow-2xs">
                <!-- Card Header -->
                <div class="p-5 border-b border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-slate-900">Sesi Pembelajaran Hari Ini</h2>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-700 num-tabular">8 sesi terpantau</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Monitoring live ruangan, tentor bertugas, dan status kehadiran siswa.</p>
                    </div>

                    <!-- Room / Program Quick Filter -->
                    <div class="flex items-center gap-1.5">
                        <select class="text-xs bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 text-slate-700 font-medium focus:outline-none focus:border-blue-500">
                            <option value="semua">Semua Ruangan</option>
                            <option value="galileo">Ruang Galileo</option>
                            <option value="newton">Ruang Newton</option>
                            <option value="einstein">Ruang Einstein</option>
                            <option value="privat">Bilik Privat 01-04</option>
                        </select>
                        <button 
                            type="button" 
                            onclick="openModal('modal-sesi-baru')"
                            class="px-2.5 py-1.5 text-xs font-semibold rounded-lg bg-blue-50 text-blue-900 hover:bg-blue-100 transition-colors shrink-0"
                        >
                            + Tambah
                        </button>
                    </div>
                </div>

                <!-- Table of Sessions -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200/80 bg-slate-50/50 text-slate-500 text-xs font-medium">
                                <th class="py-3 px-4">Waktu & Ruangan</th>
                                <th class="py-3 px-4">Mata Pelajaran & Jenjang</th>
                                <th class="py-3 px-4">Tentor & Siswa</th>
                                <th class="py-3 px-4">Program</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @foreach($sessions as $session)
                            <tr class="hover:bg-slate-50/60 transition-colors group">
                                <!-- Time & Room -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 num-tabular text-sm">
                                        {{ $session['waktu_mulai'] }} <span class="text-slate-400 font-normal">s/d</span> {{ $session['waktu_selesai'] }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 font-medium flex items-center gap-1 mt-0.5">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <rect width="18" height="18" x="3" y="3" rx="2"/>
                                            <path d="M3 9h18"/>
                                            <path d="M9 21V9"/>
                                        </svg>
                                        <span>{{ $session['ruangan'] }}</span>
                                    </div>
                                </td>

                                <!-- Subject & Level -->
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-900 text-xs sm:text-[13px]">{{ $session['mata_pelajaran'] }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $session['tingkat'] }}</div>
                                </td>

                                <!-- Tentor & Student -->
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-slate-800 flex items-center gap-1">
                                        <span class="text-blue-900 font-semibold">{{ $session['tentor'] }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-600 mt-0.5 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                        </svg>
                                        <span class="truncate max-w-[150px]">{{ $session['siswa'] }}</span>
                                    </div>
                                </td>

                                <!-- Program Badge -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if(str_contains($session['program'], 'Privat'))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-900 border border-blue-200/50">
                                            1-on-1 Privat
                                        </span>
                                    @elseif(str_contains($session['program'], 'Reguler'))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200/60">
                                            Reguler
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-900 border border-amber-200/60">
                                            Intensif SNBT
                                        </span>
                                    @endif
                                </td>

                                <!-- Status Badge -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($session['status'] === 'selesai')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                            <svg class="w-2.5 h-2.5 text-emerald-600" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                            Selesai
                                        </span>
                                    @elseif($session['status'] === 'berlangsung')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-800 border border-blue-200/70">
                                            <span class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></span>
                                            Berlangsung
                                        </span>
                                    @elseif($session['status'] === 'siap_mulai')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200/70">
                                            <svg class="w-2.5 h-2.5 text-amber-600" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                            Siap Mulai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200/60">
                                            <svg class="w-2.5 h-2.5 text-slate-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                            Terjadwal
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <button 
                                        type="button" 
                                        onclick="showSessionDetail('{{ $session['id'] }}', '{{ $session['mata_pelajaran'] }}', '{{ $session['tentor'] }}', '{{ $session['siswa'] }}', '{{ $session['waktu_mulai'] }} - {{ $session['waktu_selesai'] }}', '{{ $session['ruangan'] }}', '{{ addslashes($session['catatan_belajar']) }}')"
                                        class="px-2.5 py-1 text-[11px] font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-md transition-colors"
                                    >
                                        Catatan
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Footer Summary Bar -->
                <div class="p-3.5 bg-slate-50 border-t border-slate-200/80 flex items-center justify-between text-xs text-slate-500">
                    <span>Menampilkan 8 dari 24 sesi pembelajaran hari ini</span>
                    <a href="#semua-jadwal" class="font-semibold text-blue-900 hover:underline flex items-center gap-1">
                        <span>Lihat Jadwal Lengkap</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </section>

            <!-- Panel 2: Analisis Beban Sesi Mingguan & Kapasitas Ruangan -->
            <section class="grid grid-cols-1 md:grid-cols-12 gap-6 bg-white border border-slate-200/90 rounded-xl p-5 shadow-2xs">
                
                <!-- Chart: Beban Sesi Mingguan (7 cols) -->
                <div class="md:col-span-7 space-y-3">
                    <div class="flex items-baseline justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Beban Sesi Pembelajaran Mingguan</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Senin s/d Sabtu · Target kapasitas maksimum 28 sesi/hari</p>
                        </div>
                        <span class="text-xs font-semibold text-blue-950 num-tabular">Total 139 Sesi</span>
                    </div>

                    <!-- Clean SVG Bar Chart with Exact Reference Line -->
                    <div class="pt-2">
                        <div class="h-44 w-full flex items-end gap-3 sm:gap-4 px-2 pt-6 pb-2 border-b border-slate-200 relative">
                            <!-- Reference Line: Kapasitas Maksimum 28 Sesi -->
                            <div class="absolute inset-x-0 top-6 border-b border-dashed border-slate-300 pointer-events-none flex justify-end pr-2">
                                <span class="text-[10px] text-slate-400 bg-white px-1 -mt-2.5">Kapasitas Maksimal (28 Sesi)</span>
                            </div>

                            @foreach($weeklyLoad as $load)
                            <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end group relative">
                                <!-- Tooltip on Hover -->
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute -top-8 px-2 py-1 bg-slate-900 text-white text-[10px] rounded font-medium shadow-md pointer-events-none whitespace-nowrap z-10">
                                    {{ $load['hari'] }}: {{ $load['sesi'] }} Sesi ({{ round(($load['sesi'] / $load['kapasitas']) * 100) }}%)
                                </div>

                                <!-- Bar Fill -->
                                <div class="w-full max-w-[36px] bg-slate-100 rounded-t flex flex-col justify-end overflow-hidden" style="height: 100%;">
                                    <div 
                                        class="{{ !empty($load['is_today']) ? 'bg-blue-900' : 'bg-slate-700 hover:bg-slate-800' }} rounded-t transition-all duration-300"
                                        style="height: {{ ($load['sesi'] / 30) * 100 }}%;"
                                    ></div>
                                </div>

                                <!-- Value Label -->
                                <span class="text-[11px] font-semibold {{ !empty($load['is_today']) ? 'text-blue-900 font-bold' : 'text-slate-600' }} num-tabular">
                                    {{ $load['sesi'] }}
                                </span>
                            </div>
                            @endforeach
                        </div>

                        <!-- X-Axis Labels -->
                        <div class="flex items-center gap-3 sm:gap-4 px-2 mt-2">
                            @foreach($weeklyLoad as $load)
                            <div class="flex-1 text-center">
                                <span class="text-xs font-medium {{ !empty($load['is_today']) ? 'text-blue-900 font-bold underline underline-offset-4' : 'text-slate-500' }}">
                                    {{ $load['label'] }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Metric: Okupansi Ruang Hari Ini (5 cols) -->
                <div class="md:col-span-5 md:border-l md:border-slate-200/80 md:pl-6 space-y-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Utilisasi Ruangan Hari Ini</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Rasio pemakaian ruang dan bilik privat.</p>
                    </div>

                    <div class="space-y-3.5">
                        @foreach($roomOccupancy as $room)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-medium text-slate-800 truncate">{{ $room['nama'] }}</span>
                                <span class="font-bold text-slate-900 num-tabular">{{ $room['persen'] }}%</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div 
                                    class="h-full rounded-full {{ $room['persen'] >= 90 ? 'bg-blue-900' : ($room['persen'] >= 80 ? 'bg-blue-700' : 'bg-slate-600') }}"
                                    style="width: {{ $room['persen'] }}%;"
                                ></div>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-slate-400 mt-0.5 num-tabular">
                                <span>{{ $room['sesi_terisi'] }} dari {{ $room['total_sesi'] }} slot sesi terisi</span>
                                <span>Maks {{ $room['kapasitas_kursi'] }} kursi</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </section>

        </div>

        <!-- RIGHT / RAIL COLUMN (4 COLS) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Card 1: Prioritas & Tindakan Mendesak -->
            <section id="urgent-actions-panel" class="bg-white border border-slate-200/90 rounded-xl p-5 shadow-2xs transition-all duration-300">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <h2 class="text-sm font-bold text-slate-900">Perlu Tindakan Admin</h2>
                    </div>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded bg-amber-50 text-amber-900 border border-amber-200/60 num-tabular">
                        3 item
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach($urgentActions as $action)
                    <div class="py-3.5 first:pt-3 last:pb-1">
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-xs font-semibold text-slate-900">{{ $action['judul'] }}</span>
                            <span class="text-[10px] text-slate-400 whitespace-nowrap">{{ $action['waktu'] }}</span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            {{ $action['deskripsi'] }}
                        </p>
                        <div class="mt-2.5 flex items-center justify-between">
                            <span class="text-[11px] font-medium text-slate-500">
                                Status: <strong class="text-slate-800">{{ $action['urgensi'] }}</strong>
                            </span>
                            <button 
                                type="button" 
                                onclick="openModal('modal-verifikasi')"
                                class="px-2.5 py-1 text-xs font-semibold rounded bg-slate-900 text-white hover:bg-slate-800 transition-colors"
                            >
                                {{ $action['aksi'] }}
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            <!-- Card 2: Tentor Bertugas Hari Ini -->
            <section id="tentor" class="bg-white border border-slate-200/90 rounded-xl p-5 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Tentor On-Duty Hari Ini</h2>
                        <p class="text-xs text-slate-500 mt-0.5">5 tentor terkonfirmasi mengajar</p>
                    </div>
                    <a href="#semua-tentor" class="text-xs font-semibold text-blue-900 hover:underline">Semua</a>
                </div>

                <div class="divide-y divide-slate-100 mt-1">
                    @foreach($tentorsOnDuty as $tentor)
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <!-- Initials Avatar -->
                            <div class="w-8 h-8 rounded-full {{ $tentor['warna_avatar'] }} flex items-center justify-center text-xs font-bold shrink-0">
                                {{ $tentor['inisial'] }}
                            </div>
                            <div class="truncate">
                                <p class="text-xs font-semibold text-slate-900 truncate">{{ $tentor['nama'] }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ $tentor['bidang'] }}</p>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="inline-block text-[11px] font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded num-tabular">
                                {{ $tentor['total_sesi_hari_ini'] }} Sesi
                            </span>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $tentor['ruangan_saat_ini'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            <!-- Card 3: Mutasi Pembayaran & Tagihan SPP Terbaru -->
            <section id="keuangan" class="bg-white border border-slate-200/90 rounded-xl p-5 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Pembayaran & Kuitansi</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Mutasi paket les & SPP terkini</p>
                    </div>
                    <button 
                        type="button" 
                        onclick="openModal('modal-bayar-baru')"
                        class="text-xs font-semibold text-blue-900 hover:underline"
                    >
                        + Input
                    </button>
                </div>

                <div class="divide-y divide-slate-100 mt-1">
                    @foreach($recentTransactions as $tx)
                    <div class="py-3 flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs font-bold text-slate-900 truncate">{{ $tx['nama_siswa'] }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $tx['kode_inv'] }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 truncate mt-0.5">{{ $tx['paket'] }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $tx['metode'] }} · {{ $tx['waktu'] }}</p>
                        </div>

                        <div class="text-right shrink-0">
                            <div class="text-xs font-bold text-slate-900 num-tabular">
                                Rp {{ number_format($tx['nominal'], 0, ',', '.') }}
                            </div>
                            <div class="mt-1">
                                @if($tx['status'] === 'lunas')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                        Lunas
                                    </span>
                                @elseif($tx['status'] === 'menunggu_verifikasi')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-900 border border-amber-200/60">
                                        Verifikasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-red-50 text-red-800 border border-red-200/60">
                                        Jatuh Tempo
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="pt-3 border-t border-slate-100 mt-2 text-center">
                    <a href="#rekap-keuangan" class="text-xs font-semibold text-slate-700 hover:text-blue-900 transition-colors">
                        Lihat Buku Kas & Rekapitulasi →
                    </a>
                </div>
            </section>

        </div>

    </div>

</div>

<!-- MODAL: CATAT SESI BARU -->
<div id="modal-sesi-baru" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-slate-200" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div>
                <h3 class="text-base font-bold text-slate-900">Catat Sesi Pembelajaran Baru</h3>
                <p class="text-xs text-slate-500 mt-0.5">Jadwalkan sesi les privat atau kelas reguler bimbingan belajar.</p>
            </div>
            <button type="button" onclick="closeModal('modal-sesi-baru')" class="p-1 text-slate-400 hover:text-slate-600 rounded">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form class="mt-4 space-y-4" onsubmit="event.preventDefault(); alert('Sesi pembelajaran berhasil dijadwalkan!'); closeModal('modal-sesi-baru');">
            <!-- Program & Jenjang -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Program Belajar</label>
                    <select class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2 focus:ring-1 focus:ring-blue-600">
                        <option>Privat 1-on-1</option>
                        <option>Reguler Kelompok</option>
                        <option>Intensif SNBT & Kedinasan</option>
                        <option>English Language Prep</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jenjang Pendidikan</label>
                    <select class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2 focus:ring-1 focus:ring-blue-600">
                        <option>SMA Kelas XII</option>
                        <option>SMA Kelas XI</option>
                        <option>SMA Kelas X</option>
                        <option>SMP Kelas IX</option>
                        <option>SMP Kelas VII - VIII</option>
                        <option>SD Kelas IV - VI</option>
                    </select>
                </div>
            </div>

            <!-- Mata Pelajaran & Ruangan -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mata Pelajaran</label>
                    <input type="text" value="Matematika Saintek" class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2 focus:ring-1 focus:ring-blue-600" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Ruangan / Bilik</label>
                    <select class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2 focus:ring-1 focus:ring-blue-600">
                        <option>Bilik Privat 01</option>
                        <option>Bilik Privat 02</option>
                        <option>Bilik Privat 03</option>
                        <option>Bilik Privat 04</option>
                        <option>Ruang Galileo (Reguler)</option>
                        <option>Ruang Newton (Reguler)</option>
                        <option>Ruang Einstein (Intensif)</option>
                    </select>
                </div>
            </div>

            <!-- Tentor & Siswa -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tentor Pengajar</label>
                    <select class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2 focus:ring-1 focus:ring-blue-600">
                        <option>Kak Fadhil Ramadhan, S.Si.</option>
                        <option>Kak Sarah Az-Zahra, M.Pd.</option>
                        <option>Mr. Edward Salim, B.Ed.</option>
                        <option>Kak Dian Maharani, S.Pd.</option>
                        <option>Kak Nurul Hidayah, S.Ked.</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Siswa / Kelompok</label>
                    <input type="text" placeholder="Contoh: Dimas Arya Pratama" class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2 focus:ring-1 focus:ring-blue-600" required>
                </div>
            </div>

            <!-- Tanggal & Jam -->
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal</label>
                    <input type="date" value="2026-10-30" class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2 focus:ring-1 focus:ring-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Mulai</label>
                    <input type="time" value="16:00" class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2 focus:ring-1 focus:ring-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Selesai</label>
                    <input type="time" value="17:30" class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2 focus:ring-1 focus:ring-blue-600">
                </div>
            </div>

            <!-- Catatan Materi -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Rencana Materi / Catatan Khusus</label>
                <textarea rows="2" placeholder="Tuliskan target bab atau kisi-kisi materi..." class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2 focus:ring-1 focus:ring-blue-600"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('modal-sesi-baru')" class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 rounded-lg bg-blue-900 text-white text-xs font-semibold hover:bg-blue-800">
                    Simpan Sesi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: CATATAN BELAJAR DETAIL -->
<div id="modal-catatan-detail" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-slate-200" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div>
                <h3 class="text-base font-bold text-slate-900" id="detail-mapel">Catatan Pembelajaran</h3>
                <p class="text-xs text-slate-500 mt-0.5" id="detail-sesi-id">ID: SES-101</p>
            </div>
            <button type="button" onclick="closeModal('modal-catatan-detail')" class="p-1 text-slate-400 hover:text-slate-600 rounded">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="mt-4 space-y-3 text-xs">
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500">Waktu & Tempat:</span>
                    <strong class="text-slate-800" id="detail-waktu">08:30 - 10:00 (Bilik 01)</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Tentor Pengajar:</span>
                    <strong class="text-slate-800" id="detail-tentor">Kak Fadhil</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Siswa / Kelompok:</span>
                    <strong class="text-slate-800" id="detail-siswa">Dimas Arya</strong>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Log & Evaluasi Belajar Tentor:</label>
                <div class="p-3 rounded-lg bg-blue-50/60 border border-blue-200/70 text-slate-800 leading-relaxed text-xs" id="detail-catatan">
                    Pembahasan materi berlangsung baik.
                </div>
            </div>
        </div>

        <div class="mt-5 pt-3 border-t border-slate-200 flex items-center justify-end">
            <button type="button" onclick="closeModal('modal-catatan-detail')" class="px-4 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- MODAL: VERIFIKASI PEMBAYARAN -->
<div id="modal-verifikasi" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-slate-200" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div>
                <h3 class="text-base font-bold text-slate-900">Verifikasi Pembayaran SPP / Les</h3>
                <p class="text-xs text-slate-500 mt-0.5">Periksa bukti transfer dan konfirmasi penerimaan dana.</p>
            </div>
            <button type="button" onclick="closeModal('modal-verifikasi')" class="p-1 text-slate-400 hover:text-slate-600 rounded">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="mt-4 space-y-3 text-xs">
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500">Invoice:</span>
                    <strong class="text-slate-900 font-mono">INV-202610-090</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Siswa / Wali:</span>
                    <strong class="text-slate-900">Jonathan Christian (Ibu Linda)</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Paket Bimbingan:</span>
                    <strong class="text-slate-900">Paket Intensif SNBT 2027</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Nominal Transfer:</span>
                    <strong class="text-blue-900 font-bold text-sm">Rp 4.500.000</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Bank Tujuan:</span>
                    <span class="text-slate-700">Bank Jatim · Rek. 014299812</span>
                </div>
            </div>

            <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900">
                <p class="font-semibold">Bukti Transfer Valid Terunggah</p>
                <p class="text-[11px] mt-0.5 text-emerald-800">Nomor referensi bank cocok dengan mutasi kas cabang.</p>
            </div>
        </div>

        <div class="mt-5 pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
            <button type="button" onclick="closeModal('modal-verifikasi')" class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                Nanti
            </button>
            <button 
                type="button" 
                onclick="alert('Pembayaran berhasil diverifikasi! Kuitansi PDF telah diterbitkan ke WhatsApp Wali.'); closeModal('modal-verifikasi');" 
                class="px-4 py-1.5 rounded-lg bg-emerald-800 text-white text-xs font-semibold hover:bg-emerald-700"
            >
                Verifikasi & Cetak Kuitansi
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showSessionDetail(id, mapel, tentor, siswa, waktu, ruangan, catatan) {
        document.getElementById('detail-sesi-id').textContent = 'ID: ' + id;
        document.getElementById('detail-mapel').textContent = mapel;
        document.getElementById('detail-waktu').textContent = waktu + ' (' + ruangan + ')';
        document.getElementById('detail-tentor').textContent = tentor;
        document.getElementById('detail-siswa').textContent = siswa;
        document.getElementById('detail-catatan').textContent = catatan;
        openModal('modal-catatan-detail');
    }
</script>
@endpush
@endsection

