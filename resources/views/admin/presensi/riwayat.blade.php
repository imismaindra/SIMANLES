@extends('layouts.admin')

@section('title', 'Riwayat Kehadiran — LKP Sang Siroju Lillah')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Row -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-6 shadow-2xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-200/70">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                        {{ $isGuru ? 'Riwayat Kehadiran Saya' : 'Riwayat Kehadiran Guru' }}
                    </h1>
                    @if($isGuru)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $loggedGuru->nama ?? auth()->user()->name }} (Pengajar)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-900 border border-blue-200/70">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-700"></span>
                            {{ $currentCabang['nama'] }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 shrink-0">
                @if($isGuru)
                    <a href="{{ route('presensi.scanner') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-xs transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Scan Presensi QR</span>
                    </a>
                @else
                    <a href="{{ route('presensi.index', ['cabang_id' => $currentCabang['id']]) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 text-white font-semibold text-xs shadow-xs transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect x="7" y="7" width="3" height="3" rx="0.5" />
                            <rect x="14" y="7" width="3" height="3" rx="0.5" />
                            <rect x="7" y="14" width="3" height="3" rx="0.5" />
                            <path d="M14 14h.01" />
                            <path d="M17 14v3" />
                            <path d="M14 17h3" />
                        </svg>
                        <span>Layar QR Kiosk Presensi</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- 4 Metric KPI Strip -->
        <div class="mt-4 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Metric 1: Total Sesi Hadir -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Kehadiran</p>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-slate-900 num-tabular">{{ $totalHadir }}</span>
                    <span class="text-xs text-slate-500">sesi tercatat</span>
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Total akumulasi presensi</p>
            </div>

            <!-- Metric 2: Selesai Mengajar -->
            <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-200/60">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold text-emerald-900 uppercase tracking-wider">Selesai Mengajar</p>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-emerald-950 num-tabular">{{ $totalSelesai }}</span>
                    <span class="text-xs text-emerald-700">checkout tuntas</span>
                </div>
                <p class="mt-1 text-[11px] text-emerald-800 font-medium">Absen datang & pulang lengkap</p>
            </div>

            <!-- Metric 3: Sedang Mengajar -->
            <div class="p-3.5 rounded-xl {{ $totalAktifMengajar > 0 ? 'bg-amber-50/70 border border-amber-200' : 'bg-slate-50 border border-slate-200/70' }}">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold {{ $totalAktifMengajar > 0 ? 'text-amber-900' : 'text-slate-500' }} uppercase tracking-wider">Sedang Mengajar</p>
                    @if($totalAktifMengajar > 0)
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-amber-200 text-amber-950 animate-pulse">Live</span>
                    @endif
                </div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold {{ $totalAktifMengajar > 0 ? 'text-amber-950' : 'text-slate-900' }} num-tabular">{{ $totalAktifMengajar }}</span>
                    <span class="text-xs {{ $totalAktifMengajar > 0 ? 'text-amber-800' : 'text-slate-500' }}">sesi aktif</span>
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Sudah check-in, belum check-out</p>
            </div>

            <!-- Metric 4: Rata-rata Durasi -->
            <div class="p-3.5 rounded-xl bg-blue-50/60 border border-blue-200/60">
                <p class="text-[11px] font-semibold text-blue-900 uppercase tracking-wider">Rata-rata Durasi</p>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-blue-950 num-tabular">{{ $avgDurasi }}</span>
                    <span class="text-xs text-blue-700">menit / sesi</span>
                </div>
                <p class="mt-1 text-[11px] text-blue-800 font-medium">Durasi bimbingan efektif</p>
            </div>
        </div>

        <!-- Filter Toolbar -->
        @if(! $isGuru)
        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
            <form method="GET" action="{{ route('presensi.riwayat') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <input type="hidden" name="cabang_id" value="{{ request('cabang_id', $currentCabang['id']) }}">

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Filter Guru</label>
                    <select name="guru_id" onchange="this.form.submit()"
                        class="w-full py-2 px-3 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="">Semua Guru Pengajar</option>
                        @foreach($daftarGuru as $g)
                            <option value="{{ $g->id }}" {{ request('guru_id') === $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Filter Bulan</label>
                    <select name="bulan" onchange="this.form.submit()"
                        class="w-full py-2 px-3 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="">Semua Bulan</option>
                        @for($m = 1; $m <= 12; $m++)
                            @php $monthName = \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F'); @endphp
                            <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>{{ $monthName }}</option>
                        @endfor
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 w-full flex items-center justify-between text-xs">
                        <span class="text-slate-500">Total Log:</span>
                        <span class="font-bold text-slate-900 num-tabular">{{ $riwayatPresensi->total() }} Catatan</span>
                    </div>
                    @if(request()->hasAny(['guru_id', 'bulan', 'tahun']))
                        <a href="{{ route('presensi.riwayat', ['cabang_id' => request('cabang_id', $currentCabang['id'])]) }}"
                            class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 text-xs font-semibold shrink-0">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
        @endif
    </div>

    <!-- Attendance History Table -->
    <div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs">
        <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
            <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700">Daftar Log Riwayat Presensi</h3>
            <span class="text-xs text-slate-500 num-tabular">{{ $riwayatPresensi->total() }} catatan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-slate-600 border-collapse">
                <thead class="bg-slate-50 text-slate-700 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200/80">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4">Tanggal & Hari</th>
                        @if(! $isGuru)
                            <th class="py-3 px-4">Guru Pengajar</th>
                        @endif
                        <th class="py-3 px-4">Jam Datang</th>
                        <th class="py-3 px-4">Jam Pulang</th>
                        <th class="py-3 px-4">Durasi Sesi</th>
                        <th class="py-3 px-4">Cabang</th>
                        <th class="py-3 px-4 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($riwayatPresensi as $idx => $abs)
                        @php
                            $durasi = null;
                            if ($abs->jam_datang && $abs->jam_pulang) {
                                $durasi = (int) $abs->jam_datang->diffInMinutes($abs->jam_pulang);
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="py-3.5 px-4 text-center text-xs text-slate-400 num-tabular">
                                {{ $riwayatPresensi->firstItem() + $idx }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-900 whitespace-nowrap">
                                {{ $abs->tanggal ? $abs->tanggal->isoFormat('dddd, D MMMM Y') : '-' }}
                            </td>
                            @if(! $isGuru)
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $abs->guru->nama ?? 'Guru' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $abs->guru->no_pegawai ?? 'NIP' }}</div>
                            </td>
                            @endif
                            <td class="py-3.5 px-4 font-mono font-semibold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                {{ $abs->jam_datang ? $abs->jam_datang->format('H:i:s') . ' WIB' : '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-semibold text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                {{ $abs->jam_pulang ? $abs->jam_pulang->format('H:i:s') . ' WIB' : '-' }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($durasi !== null)
                                    <span class="font-bold text-slate-900 num-tabular">{{ $durasi }} menit</span>
                                @elseif($abs->jam_datang)
                                    <span class="text-amber-600 dark:text-amber-400 font-medium text-xs">Sedang berlangsung</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $abs->cabang->nama ?? 'Cabang' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                @if($abs->jam_pulang)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Selesai Mengajar
                                    </span>
                                @elseif($abs->jam_datang)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Sedang Mengajar
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">
                                        Tercatat
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isGuru ? '7' : '8' }}" class="py-12 text-center text-slate-400 text-sm">
                                <div class="w-10 h-10 mx-auto rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="font-semibold text-slate-700 dark:text-slate-200">Belum Ada Catatan Presensi</p>
                                <p class="text-xs text-slate-400 mt-0.5">Catatan kehadiran akan muncul otomatis saat Anda melakukan scan QR presensi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($riwayatPresensi->hasPages())
        <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800">
            {{ $riwayatPresensi->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
