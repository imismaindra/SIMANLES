@extends('layouts.admin')

@section('title', 'Dashboard Konsolidasi Seluruh Cabang — LKP Sang Siroju Lillah')

@section('content')
<div class="space-y-6">

    <!-- Header Konsolidasi Eksekutif -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-6 shadow-2xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-200/70">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Dashboard Pusat — Semua Cabang</h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-200/80">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                        Admin Pusat / Pengelola Utama
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-900 border border-blue-200">
                        {{ $totalCabangAktif }} Cabang Aktif
                    </span>
                </div>
                <p class="mt-1 text-xs sm:text-sm text-slate-500">
                    Laporan eksekutif konsolidasi seluruh cabang: rekap siswa, utilisasi kapasitas guru, arus kas gabungan, dan performa cabang.
                </p>
            </div>

            <!-- Tombol Aksi Header -->
            <div class="flex flex-wrap items-center justify-end gap-2.5 w-full lg:w-auto">
                <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200/80 active:bg-slate-300 text-slate-700 font-semibold text-xs border border-slate-200/80 transition-colors">
                    <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Laporan</span>
                </button>

                <a href="{{ route('admin.dashboard', ['cabang_id' => $currentCabang['id'] ?? 'cab-ponokawan']) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 text-white font-semibold text-xs shadow-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                    <span>Buka Dashboard Cabang</span>
                </a>
            </div>
        </div>

        <!-- 4 Global Executive KPI Cards Strip -->
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- KPI 1: Total Siswa Seluruh Cabang -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Siswa Aktif</p>
                    @if($totalSiswaAntreKonsolidasi > 0)
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                            {{ $totalSiswaAntreKonsolidasi }} Antre
                        </span>
                    @else
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-900 border border-emerald-200">
                            Terbimbing
                        </span>
                    @endif
                </div>
                <div class="mt-2 flex items-baseline gap-1.5">
                    <span class="text-3xl font-extrabold text-slate-900 num-tabular">{{ $totalSiswaKonsolidasi }}</span>
                    <span class="text-xs text-slate-500">siswa di {{ $totalCabangAktif }} cabang</span>
                </div>
                <div class="mt-2 text-[11px] text-slate-500 font-medium num-tabular truncate">
                    TK: {{ $jenjangKonsolidasi['TK'] }} · SD: {{ $jenjangKonsolidasi['SD'] }} · SMP: {{ $jenjangKonsolidasi['SMP'] }} · SMA: {{ $jenjangKonsolidasi['SMA_SMK'] }}
                </div>
            </div>

            <!-- KPI 2: Total Guru & Kapasitas Jaringan -->
            <div class="p-4 rounded-xl bg-blue-50/60 border border-blue-200/60 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold text-blue-900 uppercase tracking-wider">Kapasitas Slot Guru</p>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $utilisasiGlobal >= 90 ? 'bg-amber-100 text-amber-900 border border-amber-200' : 'bg-emerald-100 text-emerald-900 border border-emerald-200' }}">
                        {{ $utilisasiGlobal }}% Terpakai
                    </span>
                </div>
                <div class="mt-2 flex items-baseline gap-1.5">
                    <span class="text-3xl font-extrabold text-blue-950 num-tabular">{{ $totalSlotTerpakaiKonsolidasi }}</span>
                    <span class="text-xs text-blue-700">/ {{ $totalSlotTotalKonsolidasi }} slot total</span>
                </div>
                <div class="mt-2 flex items-center justify-between text-[11px] text-blue-800">
                    <span>{{ $totalGuruKonsolidasi }} guru bertugas</span>
                    <span class="font-semibold text-emerald-700">Sisa {{ $totalSlotSisaKonsolidasi }} slot</span>
                </div>
            </div>

            <!-- KPI 3: Total Kas Bersih Konsolidasi -->
            <div class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-200/60 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold text-emerald-900 uppercase tracking-wider">Kas Bersih Konsolidasi</p>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-900 border border-emerald-200">
                        {{ now()->translatedFormat('M Y') }}
                    </span>
                </div>
                <div class="mt-2">
                    <span class="text-2xl sm:text-3xl font-extrabold text-emerald-950 num-tabular">
                        Rp {{ number_format($totalSaldoKasKonsolidasi, 0, ',', '.') }}
                    </span>
                </div>
                <div class="mt-2 flex items-center justify-between text-[11px] text-emerald-800 truncate">
                    <span>Masuk: Rp {{ number_format($totalKasMasukBulanIni, 0, ',', '.') }}</span>
                    <span>Keluar: Rp {{ number_format($totalKasKeluarBulanIni, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- KPI 4: Penerimaan & Piutang SPP Konsolidasi -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Penerimaan SPP Bulan Ini</p>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-blue-100 text-blue-900 border border-blue-200">
                        {{ $persenSppLunasGlobal }}% Lunas
                    </span>
                </div>
                <div class="mt-2">
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 num-tabular">
                        Rp {{ number_format($totalSppLunasBulanIni, 0, ',', '.') }}
                    </span>
                </div>
                <div class="mt-2 flex items-center justify-between text-[11px] text-slate-500">
                    <span>Piutang/Tempo:</span>
                    <span class="font-bold text-amber-700">Rp {{ number_format($totalSppPiutangBulanIni, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Visual Charts Row: Keuangan Konsolidasi & Sebaran Cabang -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart 1: Arus Kas 6 Bulan Konsolidasi (2 Cols) -->
        <div class="lg:col-span-2 bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-2xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Trend Arus Kas Konsolidasi (6 Bulan Terakhir)</h3>
                    <p class="text-xs text-slate-500">Perbandingan pemasukan & pengeluaran dari seluruh cabang</p>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold">
                    <span class="inline-flex items-center gap-1.5 text-emerald-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Kas Masuk
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-rose-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Kas Keluar
                    </span>
                </div>
            </div>
            <div class="mt-3">
                <div id="chart-konsolidasi-cashflow" class="w-full h-[280px]"></div>
            </div>
        </div>

        <!-- Chart 2: Komposisi Sebaran Jenjang Seluruh Cabang (1 Col) -->
        <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-2xs flex flex-col justify-between">
            <div class="pb-3 border-b border-slate-200/70">
                <h3 class="font-bold text-slate-900 text-sm sm:text-base">Demografi Siswa Seluruh Cabang</h3>
                <p class="text-xs text-slate-500">Sebaran jenjang studi dari total {{ $totalSiswaKonsolidasi }} siswa aktif</p>
            </div>
            <div class="my-auto py-2">
                <div id="chart-konsolidasi-jenjang" class="w-full flex items-center justify-center"></div>
            </div>
            <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                    <p class="text-[10px] text-slate-400 uppercase font-semibold">TK</p>
                    <p class="text-sm font-bold text-slate-800">{{ $jenjangKonsolidasi['TK'] }} siswa</p>
                </div>
                <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                    <p class="text-[10px] text-slate-400 uppercase font-semibold">SD</p>
                    <p class="text-sm font-bold text-slate-800">{{ $jenjangKonsolidasi['SD'] }} siswa</p>
                </div>
                <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                    <p class="text-[10px] text-slate-400 uppercase font-semibold">SMP</p>
                    <p class="text-sm font-bold text-slate-800">{{ $jenjangKonsolidasi['SMP'] }} siswa</p>
                </div>
                <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                    <p class="text-[10px] text-slate-400 uppercase font-semibold">SMA/SMK</p>
                    <p class="text-sm font-bold text-slate-800">{{ $jenjangKonsolidasi['SMA_SMK'] }} siswa</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Komparasi Seluruh Cabang (Konsolidasi Report Table) -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-2xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-base text-slate-900">Rekapitulasi Kinerja Per Cabang</h3>
                <p class="text-xs text-slate-500">Perbandingan menyeluruh siswa, kapasitas slot guru, arus kas berjalan, dan piutang SPP</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                Total {{ $cabangReports->count() }} Cabang
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4 min-w-[200px]">Cabang & Pimpinan</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Siswa Aktif</th>
                        <th class="py-3 px-4 min-w-[160px]">Guru & Kapasitas Slot</th>
                        <th class="py-3 px-4 text-right">Kas Masuk (Bulan Ini)</th>
                        <th class="py-3 px-4 text-right">Kas Keluar (Bulan Ini)</th>
                        <th class="py-3 px-4 text-right">Saldo Kas Bersih</th>
                        <th class="py-3 px-4 min-w-[140px]">SPP Bulan Ini</th>
                        <th class="py-3 px-4 text-center min-w-[120px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($cabangReports as $idx => $cab)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="py-3.5 px-4 text-center text-slate-400 font-semibold num-tabular">
                                {{ $loop->iteration }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ $cab['nama'] }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5 font-mono">{{ $cab['kode'] }} · {{ $cab['pimpinan'] }}</div>
                                <div class="text-[10px] text-slate-400 truncate max-w-[220px]">{{ $cab['alamat'] }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $cab['status'] === 'aktif' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    {{ strtoupper($cab['status']) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="text-sm font-extrabold text-slate-900 num-tabular">{{ $cab['siswa_aktif'] }}</span>
                                <span class="block text-[10px] text-slate-400">
                                    {{ $cab['siswa_antre'] > 0 ? $cab['siswa_antre'].' antre guru' : 'Semua terbimbing' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center justify-between text-[11px] mb-1">
                                    <span class="font-semibold text-slate-700">{{ $cab['guru_count'] }} Guru</span>
                                    <span class="font-bold {{ $cab['utilisasi'] >= 90 ? 'text-amber-800' : 'text-blue-900' }} num-tabular">
                                        {{ $cab['slot_terpakai'] }}/{{ $cab['slot_total'] }} ({{ $cab['utilisasi'] }}%)
                                    </span>
                                </div>
                                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $cab['utilisasi'] >= 90 ? 'bg-amber-500' : 'bg-emerald-500' }}"
                                        style="width: {{ $cab['utilisasi'] }}%"></div>
                                </div>
                                <span class="block text-[10px] text-slate-400 mt-1">Sisa {{ $cab['slot_sisa'] }} slot</span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-semibold text-emerald-700 num-tabular">
                                +Rp {{ number_format($cab['kas_masuk_bulan_ini'], 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-semibold text-rose-700 num-tabular">
                                -Rp {{ number_format($cab['kas_keluar_bulan_ini'], 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <span class="text-xs sm:text-sm font-bold text-slate-900 num-tabular">
                                    Rp {{ number_format($cab['saldo_kas'], 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-[11px] font-semibold text-emerald-800">
                                    Lunas: Rp {{ number_format($cab['spp_lunas_nominal'], 0, ',', '.') }} ({{ $cab['spp_lunas_count'] }})
                                </div>
                                @if($cab['spp_piutang_nominal'] > 0)
                                    <div class="text-[10px] font-semibold text-amber-700 mt-0.5">
                                        Tempo: Rp {{ number_format($cab['spp_piutang_nominal'], 0, ',', '.') }} ({{ $cab['spp_belum_count'] }})
                                    </div>
                                @else
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        Tidak ada tunggakan
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('admin.dashboard', ['cabang_id' => $cab['id']]) }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-900 font-semibold text-[11px] border border-blue-200/80 transition-colors"
                                    title="Masuk ke Dashboard Operasional {{ $cab['nama'] }}">
                                    <span>Buka Cabang</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-400 text-xs">
                                Belum ada data cabang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof ApexCharts === 'undefined') {
            console.error('ApexCharts library not loaded');
            return;
        }

        let isDark = document.documentElement.classList.contains('dark');

        // 1. Chart Arus Kas Konsolidasi 6 Bulan (Area Chart)
        const cashflowOptions = {
            chart: {
                type: 'area',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'Inter, system-ui, sans-serif',
                background: 'transparent'
            },
            theme: {
                mode: isDark ? 'dark' : 'light'
            },
            series: [
                {
                    name: 'Kas Masuk Konsolidasi',
                    data: @json($chartKonsolidasiMasuk)
                },
                {
                    name: 'Kas Keluar Konsolidasi',
                    data: @json($chartKonsolidasiKeluar)
                }
            ],
            colors: ['#10b981', '#f43f5e'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.05,
                    stops: [0, 95, 100]
                }
            },
            xaxis: {
                categories: @json($chartKonsolidasiBulan),
                labels: { style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '11px' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                        if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                        return 'Rp ' + value;
                    },
                    style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '11px' }
                }
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                y: {
                    formatter: function (val) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            },
            grid: { borderColor: isDark ? '#1e293b' : '#f1f5f9' },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                labels: { colors: isDark ? '#cbd5e1' : '#475569' }
            }
        };

        const cashflowEl = document.querySelector("#chart-konsolidasi-cashflow");
        let cashflowChart = null;
        if (cashflowEl) {
            cashflowChart = new ApexCharts(cashflowEl, cashflowOptions);
            cashflowChart.render();
        }

        // 2. Chart Demografi Jenjang Konsolidasi (Donut Chart)
        const jenjangOptions = {
            chart: {
                type: 'donut',
                height: 240,
                fontFamily: 'Inter, system-ui, sans-serif',
                background: 'transparent'
            },
            theme: {
                mode: isDark ? 'dark' : 'light'
            },
            series: [
                {{ $jenjangKonsolidasi['TK'] }},
                {{ $jenjangKonsolidasi['SD'] }},
                {{ $jenjangKonsolidasi['SMP'] }},
                {{ $jenjangKonsolidasi['SMA_SMK'] }}
            ],
            labels: ['TK', 'SD', 'SMP', 'SMA / SMK'],
            colors: ['#fbbf24', '#3b82f6', '#10b981', '#8b5cf6'],
            legend: {
                position: 'bottom',
                fontSize: '11px',
                labels: { colors: isDark ? '#cbd5e1' : '#475569' }
            },
            dataLabels: { enabled: true },
            stroke: { width: 2, colors: [isDark ? '#0f172a' : '#ffffff'] },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Siswa',
                                fontSize: '11px',
                                color: isDark ? '#94a3b8' : '#64748b',
                                formatter: function () {
                                    return '{{ $totalSiswaKonsolidasi }}';
                                }
                            },
                            value: {
                                fontSize: '18px',
                                fontWeight: 700,
                                color: isDark ? '#ffffff' : '#0f172a',
                                offsetY: -2
                            }
                        }
                    }
                }
            }
        };

        const jenjangEl = document.querySelector("#chart-konsolidasi-jenjang");
        let jenjangChart = null;
        if (jenjangEl) {
            jenjangChart = new ApexCharts(jenjangEl, jenjangOptions);
            jenjangChart.render();
        }

        window.addEventListener('theme-changed', function(e) {
            const dark = e.detail.isDark;
            if (cashflowChart) {
                cashflowChart.updateOptions({
                    theme: { mode: dark ? 'dark' : 'light' },
                    xaxis: { labels: { style: { colors: dark ? '#94a3b8' : '#64748b' } } },
                    yaxis: { labels: { style: { colors: dark ? '#94a3b8' : '#64748b' } } },
                    grid: { borderColor: dark ? '#1e293b' : '#f1f5f9' },
                    legend: { labels: { colors: dark ? '#cbd5e1' : '#475569' } },
                    tooltip: { theme: dark ? 'dark' : 'light' }
                });
            }
            if (jenjangChart) {
                jenjangChart.updateOptions({
                    theme: { mode: dark ? 'dark' : 'light' },
                    stroke: { colors: [dark ? '#0f172a' : '#ffffff'] },
                    legend: { labels: { colors: dark ? '#cbd5e1' : '#475569' } },
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    total: { color: dark ? '#94a3b8' : '#64748b' },
                                    value: { color: dark ? '#ffffff' : '#0f172a' }
                                }
                            }
                        }
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection
