@extends('layouts.admin')

@section('title', 'Presensi Guru & Layar QR — LKP Sang Siroju Lillah')

@section('content')
<div class="space-y-6">

    <!-- Live Toast Notification Banner for Realtime Attendance -->
    <div id="live-scan-toast" class="hidden fixed bottom-6 right-6 z-50 max-w-sm w-full bg-slate-900 border border-emerald-500/60 text-white rounded-2xl p-4 shadow-2xl transition-all duration-300 transform translate-y-4">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </span>
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold text-emerald-400 uppercase tracking-wider" id="toast-tipe">Presensi Datang</p>
                    <span class="text-[10px] text-slate-400 font-mono" id="toast-jam">14:02:15 WIB</span>
                </div>
                <p class="text-sm font-bold text-white truncate" id="toast-nama">Nama Guru</p>
                <p class="text-[11px] text-slate-300">Tercatat di sistem</p>
            </div>
            <button type="button" onclick="hideToast()" class="text-slate-400 hover:text-white p-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Header & Status Row -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-6 shadow-2xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-200/70">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Presensi Guru & Layar QR Code</h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-900 border border-blue-200/70">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-700"></span>
                        {{ $currentCabang['nama'] }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        Sesi Presensi Aktif
                    </span>
                </div>
                <p class="mt-1 text-xs sm:text-sm text-slate-500">
                    Mencegah kecurangan absensi menggunakan token QR dinamis berumur pendek (20–30 detik). Guru melakukan scan QR mandiri di lokasi cabang.
                </p>
            </div>

            <!-- Action Controls (Kiosk Mode & Scanner Link) -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('presensi.scanner', ['cabang_id' => $currentCabang['id']]) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs border border-slate-200/80 transition-colors shadow-2xs">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Buka Scanner Kamera Guru</span>
                </a>

                <button type="button" onclick="openKioskMode()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white font-semibold text-xs shadow-xs transition-colors">
                    <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                    </svg>
                    <span>Mode Layar Penuh (Kiosk Piket)</span>
                </button>
            </div>
        </div>

        <!-- 4 Metric Attendance Strip -->
        <div class="mt-4 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Metric 1: Kehadiran -->
            <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-200/60">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold text-emerald-900 uppercase tracking-wider">Tingkat Hadir</p>
                    <span id="metric-persentase" class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-emerald-200 text-emerald-950">{{ $persentaseKehadiran }}%</span>
                </div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span id="metric-sudah-datang" class="text-2xl font-bold text-emerald-950 num-tabular">{{ $sudahDatang }}</span>
                    <span class="text-xs text-emerald-700">/ <span id="metric-total-guru">{{ $totalGuruBertugas }}</span> guru hadir</span>
                </div>
                <p class="mt-1 text-[10px] text-emerald-800 font-medium">Jam Datang Tercatat</p>
            </div>

            <!-- Metric 2: Sudah Pulang -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Sudah Pulang</p>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span id="metric-sudah-pulang" class="text-2xl font-bold text-slate-900 num-tabular">{{ $sudahPulang }}</span>
                    <span class="text-xs text-slate-500">guru</span>
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Checkout sesi mengajar</p>
            </div>

            <!-- Metric 3: Belum Hadir -->
            <div class="p-3.5 rounded-xl {{ $belumHadir > 0 ? 'bg-amber-50 border border-amber-200' : 'bg-slate-50 border border-slate-200/70' }}">
                <p class="text-[11px] font-semibold {{ $belumHadir > 0 ? 'text-amber-900' : 'text-slate-500' }} uppercase tracking-wider">Belum Presensi</p>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span id="metric-belum-hadir" class="text-2xl font-bold {{ $belumHadir > 0 ? 'text-amber-950' : 'text-slate-900' }} num-tabular">{{ $belumHadir }}</span>
                    <span class="text-xs {{ $belumHadir > 0 ? 'text-amber-800' : 'text-slate-500' }}">guru</span>
                </div>
                <p class="mt-1 text-[11px] {{ $belumHadir > 0 ? 'text-amber-800' : 'text-slate-400' }}">Menunggu jadwal masuk</p>
            </div>

            <!-- Metric 4: Total Guru Cabang -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Guru Terdaftar</p>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span id="metric-total-bertugas" class="text-2xl font-bold text-slate-900 num-tabular">{{ $totalGuruBertugas }}</span>
                    <span class="text-xs text-slate-500">guru aktif</span>
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Alokasi cabang ini</p>
            </div>
        </div>
    </div>

    <!-- Main Live QR & Attendance Simulator Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Column 1: Live Dynamic QR Display (7 cols) -->
        <div class="lg:col-span-7 bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-2xs flex flex-col justify-between space-y-6">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 bg-blue-100 text-blue-900 rounded-lg">
                            <svg class="w-5 h-5 text-blue-900" fill="none" viewBox="0 0 24 24" stroke="currentColor"
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
                        </span>
                        <div>
                            <h2 class="font-bold text-slate-900 text-base">Layar QR Code Presensi Dinamis</h2>
                            <p class="text-xs text-slate-500">Tayangkan layar ini di meja piket / tablet cabang</p>
                        </div>
                    </div>

                    <!-- Sesi Selector Badge -->
                    <span id="label-sesi-badge" class="px-2.5 py-1 rounded-full text-xs font-bold uppercase transition-colors {{ ($activeQr->tipe ?? 'datang') === 'pulang' ? 'bg-blue-100 text-blue-900' : 'bg-emerald-100 text-emerald-900' }}">
                        Sesi: <span id="text-sesi-tipe">{{ strtoupper($activeQr->tipe ?? 'datang') }}</span>
                    </span>
                </div>

                <!-- Big QR Visual Box -->
                <div class="my-6 flex flex-col items-center justify-center p-6 bg-slate-950 text-white rounded-2xl border-4 border-slate-800 relative overflow-hidden text-center shadow-xl">
                    <div class="absolute -top-12 -right-12 w-32 h-32 bg-blue-600/20 rounded-full blur-2xl"></div>
                    <div class="absolute -bottom-12 -left-12 w-32 h-32 bg-emerald-500/20 rounded-full blur-2xl"></div>

                    <!-- Real SVG QR Code Container -->
                    <div id="qr-code-wrapper" class="p-4 bg-white rounded-2xl shadow-2xl flex flex-col items-center justify-center transition-all duration-300">
                        {!! $qrSvg !!}
                    </div>

                    <!-- Token Code Box -->
                    <div class="mt-4 flex flex-col items-center">
                        <span class="text-xs text-slate-400 font-medium uppercase tracking-wider">Kode Token Presensi:</span>
                        <div class="mt-1 flex items-center gap-2">
                            <span id="display-qr-token" class="font-mono text-2xl sm:text-3xl font-black tracking-widest text-emerald-400 bg-slate-900/90 px-4 py-1 rounded-xl border border-slate-700/80">
                                {{ $activeQr->token ?? 'SIROJU-8492' }}
                            </span>
                            <button type="button" onclick="copyActiveToken()" title="Salin kode"
                                class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Countdown Indicator -->
                    <div class="mt-3 flex items-center gap-2 text-xs text-slate-300">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                        <span>Kedaluwarsa otomatis dalam: <strong id="qr-countdown" class="text-amber-300 num-tabular font-mono text-sm">30 detik</strong></span>
                    </div>
                </div>
            </div>

            <!-- Controls: Regenerate & Switch Datang / Pulang -->
            <div class="pt-3 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="refreshQrToken('datang')" id="btn-sesi-datang"
                        class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-colors {{ ($activeQr->tipe ?? 'datang') === 'datang' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-800' }}">
                        Sesi Datang
                    </button>
                    <button type="button" onclick="refreshQrToken('pulang')" id="btn-sesi-pulang"
                        class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-colors {{ ($activeQr->tipe ?? 'datang') === 'pulang' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-800' }}">
                        Sesi Pulang
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="refreshQrToken()" id="btn-manual-refresh"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-900 hover:bg-blue-800 text-white font-semibold text-xs shadow-xs transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Perbarui Token Baru</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Column 2: Self-Service Attendance Simulator (5 cols) -->
        <div class="lg:col-span-5 bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-2xs flex flex-col justify-between space-y-4">
            <div>
                <div class="pb-3 border-b border-slate-200/70">
                    <h2 class="font-bold text-slate-900 text-base">Simulasi Scan Presensi Guru</h2>
                    <p class="text-xs text-slate-500">Form input mandiri untuk guru yang sedang berada di lokasi</p>
                </div>

                <form method="POST" action="{{ route('web.presensi.scan') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Guru Pengajar *</label>
                        <select name="guru_id" id="sim-guru-select" required
                            class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                            <option value="">-- Pilih Guru Mengajar --</option>
                            @foreach($guruCabang as $g)
                                <option value="{{ $g->id }}" {{ old('guru_id') == $g->id ? 'selected' : '' }}>
                                    {{ $g->nama }} ({{ $g->no_pegawai ?: 'NIP Guru' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Token QR Tertera *</label>
                        <div class="flex gap-2">
                            <input type="text" name="token" id="sim-token-input" required value="{{ old('token', $activeQr->token ?? '') }}"
                                placeholder="Contoh: SIROJU-8492"
                                class="flex-1 px-3 py-2 text-xs font-mono font-bold uppercase rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                            <button type="button" onclick="copyActiveToken()"
                                class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs shrink-0" title="Salin token aktif">
                                Salin
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Masukkan token yang saat ini tampil di layar cabang.</p>
                    </div>

                    <button type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs shadow-xs transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        <span>Konfirmasi Scan Presensi</span>
                    </button>
                </form>
            </div>

            <!-- Notes on Anti-Fraud -->
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-500 space-y-1">
                <p class="font-bold text-slate-700">Proteksi Keamanan Sistem:</p>
                <p>1. Guru wajib terdaftar di cabang yang menayangkan token.</p>
                <p>2. Token kedaluwarsa otomatis dalam 25 detik sehingga mencegah screenshot dan titip absen.</p>
            </div>
        </div>
    </div>

    <!-- Attendance Log: Today's Recorded Presensi -->
    <div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs">
        <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700">Log Presensi Guru Hari Ini</h3>
                <p class="text-xs text-slate-400">Catatan realtime scan datang dan checkout pulang (diperbarui otomatis)</p>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 num-tabular">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span id="log-count-text">{{ $absensiHariIni->count() }} Guru Sudah Melakukan Absen</span>
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-slate-600 border-collapse">
                <thead class="bg-slate-50 text-slate-700 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200/80">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4">Nama Guru</th>
                        <th class="py-3 px-4">Jam Datang</th>
                        <th class="py-3 px-4">Jam Pulang</th>
                        <th class="py-3 px-4">Durasi Mengajar</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody id="attendance-tbody" class="divide-y divide-slate-200/70">
                    @forelse($absensiHariIni as $idx => $abs)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4 text-center text-xs text-slate-400 num-tabular">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $abs->guru->nama ?? 'Guru' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $abs->guru->no_pegawai ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                @if($abs->jam_datang)
                                    <span class="inline-flex items-center gap-1 font-mono font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded num-tabular">
                                        {{ $abs->jam_datang->format('H:i:s') }} WIB
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($abs->jam_pulang)
                                    <span class="inline-flex items-center gap-1 font-mono font-bold text-blue-800 bg-blue-50 px-2 py-0.5 rounded num-tabular">
                                        {{ $abs->jam_pulang->format('H:i:s') }} WIB
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        Masih Mengajar
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($abs->jam_datang && $abs->jam_pulang)
                                    @php
                                        $diffMinutes = $abs->jam_datang->diffInMinutes($abs->jam_pulang);
                                        $hours = floor($diffMinutes / 60);
                                        $mins = $diffMinutes % 60;
                                    @endphp
                                    <span class="text-xs font-semibold text-slate-800 num-tabular">
                                        {{ $hours > 0 ? "{$hours} jam " : '' }}{{ $mins }} menit
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($abs->jam_pulang)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                        Selesai Mengajar
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Tepat Waktu
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-row">
                            <td colspan="6" class="py-10 text-center text-slate-400 text-sm">
                                Belum ada catatan absensi untuk hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Fullscreen Kiosk Overlay Modal -->
<div id="kiosk-overlay" class="fixed inset-0 z-50 bg-slate-950 text-white hidden flex-col justify-between p-6 sm:p-10 select-none">
    <!-- Kiosk Header -->
    <div class="flex items-center justify-between border-b border-slate-800 pb-5">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-slate-900 border border-slate-700 flex items-center justify-center p-1.5 shadow-md">
                <img src="{{ asset('logoo.png') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                    LKP Sang Siroju Lillah
                    <span class="text-xs px-2 py-0.5 rounded-full bg-blue-950 text-blue-300 border border-blue-800">
                        {{ $currentCabang['nama'] }}
                    </span>
                </h2>
                <p class="text-xs text-slate-400">Layar Presensi Mandiri Guru — Meja Piket Cabang</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <!-- Digital Clock -->
            <div class="text-right">
                <div id="kiosk-clock" class="text-2xl sm:text-3xl font-mono font-bold text-white tracking-widest num-tabular">
                    00:00:00 WIB
                </div>
                <div class="text-xs text-slate-400 font-medium">
                    {{ \Illuminate\Support\Carbon::parse($today)->translatedFormat('l, d F Y') }}
                </div>
            </div>

            <!-- Exit Kiosk Button -->
            <button type="button" onclick="closeKioskMode()"
                class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>Keluar Kiosk (ESC)</span>
            </button>
        </div>
    </div>

    <!-- Center QR Visual Area -->
    <div class="flex-1 flex flex-col items-center justify-center my-6">
        <!-- Session Indicator Pill -->
        <div id="kiosk-session-badge" class="mb-4 px-6 py-2 rounded-full font-black text-sm tracking-wider uppercase shadow-lg transition-all {{ ($activeQr->tipe ?? 'datang') === 'pulang' ? 'bg-blue-600 text-white' : 'bg-emerald-600 text-white' }}">
            SESI: <span id="kiosk-session-text">{{ strtoupper($activeQr->tipe ?? 'datang') }}</span>
        </div>

        <!-- Big Kiosk QR Box -->
        <div class="p-6 sm:p-8 bg-white rounded-3xl shadow-2xl flex items-center justify-center border-4 border-slate-700 relative">
            <div id="kiosk-qr-wrapper" class="w-64 h-64 sm:w-80 sm:h-80 flex items-center justify-center">
                {!! $qrSvg !!}
            </div>
        </div>

        <!-- Big Token Display -->
        <div class="mt-6 flex flex-col items-center">
            <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">KODE TOKEN PRESENSI:</span>
            <div id="kiosk-token-display" class="mt-1 font-mono text-3xl sm:text-4xl font-black text-emerald-400 bg-slate-900 px-6 py-1.5 rounded-2xl border border-slate-700 tracking-widest shadow-inner">
                {{ $activeQr->token ?? 'SIROJU-8492' }}
            </div>
        </div>

        <!-- Animated Progress / Countdown Indicator -->
        <div class="mt-4 flex items-center gap-2 text-sm text-slate-300">
            <span class="w-3 h-3 rounded-full bg-amber-400 animate-ping"></span>
            <span>Kode berubah otomatis dalam: <strong id="kiosk-countdown" class="text-amber-300 font-mono text-base font-bold">30 detik</strong></span>
        </div>
    </div>

    <!-- Kiosk Footer Controls -->
    <div class="flex flex-wrap items-center justify-between gap-4 border-t border-slate-800 pt-5">
        <div class="flex items-center gap-3">
            <button type="button" onclick="refreshQrToken('datang')"
                class="px-5 py-2.5 rounded-xl font-bold text-xs transition-colors bg-emerald-600 hover:bg-emerald-500 text-white shadow-md">
                Ganti ke Sesi Datang
            </button>
            <button type="button" onclick="refreshQrToken('pulang')"
                class="px-5 py-2.5 rounded-xl font-bold text-xs transition-colors bg-blue-600 hover:bg-blue-500 text-white shadow-md">
                Ganti ke Sesi Pulang
            </button>
            <button type="button" onclick="refreshQrToken()"
                class="px-4 py-2.5 rounded-xl font-bold text-xs transition-colors bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700">
                Perbarui Token Sekarang
            </button>
        </div>

        <div class="text-xs text-slate-400">
            Arahkan kamera HP ke QR Code atau akses <span class="text-emerald-400 font-mono">{{ route('presensi.scanner') }}</span>
        </div>
    </div>
</div>

<script>
    let currentTipe = '{{ $activeQr->tipe ?? 'datang' }}';
    const initialExpires = new Date('{{ $activeQr?->expires_at?->toIso8601String() }}');
    const nowMs = new Date();
    let initialRemaining = Math.max(1, Math.round((initialExpires - nowMs) / 1000));
    let countdownSec = (initialRemaining > 0 && initialRemaining <= 30) ? initialRemaining : 30;
    let timerInterval = null;
    let livePollInterval = null;
    let clockInterval = null;
    let isRefreshing = false;

    function startTimer(initialSeconds = null) {
        clearInterval(timerInterval);
        countdownSec = (initialSeconds !== null) ? initialSeconds : 30;
        updateCountdownDisplay();

        timerInterval = setInterval(() => {
            countdownSec--;
            updateCountdownDisplay();
            if (countdownSec <= 0) {
                clearInterval(timerInterval);
                refreshQrToken();
            }
        }, 1000);
    }

    function updateCountdownDisplay() {
        const text = Math.max(0, countdownSec) + ' detik';
        const el = document.getElementById('qr-countdown');
        const kioskEl = document.getElementById('kiosk-countdown');
        if (el) el.textContent = text;
        if (kioskEl) kioskEl.textContent = text;
    }

    async function refreshQrToken(forcedTipe) {
        if (isRefreshing) return;
        isRefreshing = true;
        clearInterval(timerInterval);

        if (forcedTipe) {
            currentTipe = forcedTipe;
            updateSessionUI(forcedTipe);
        }

        const refreshBtn = document.getElementById('btn-manual-refresh');
        if (refreshBtn) refreshBtn.classList.add('opacity-50');

        try {
            const payload = {
                cabang_id: '{{ $currentCabang['id'] }}',
                tipe: currentTipe,
                duration: 30
            };

            let resp;
            try {
                resp = await fetch('{{ route('web.presensi.qr') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
            } catch (err) {
                // Fallback route API jika network/CSRF terputus
                resp = await fetch('{{ route('api.absensi.qr') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
            }

            if (!resp.ok && (resp.status === 419 || resp.status === 401 || resp.status === 403)) {
                resp = await fetch('{{ route('api.absensi.qr') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
            }

            const json = await resp.json();
            if (json.success && json.data) {
                const token = json.data.token;
                const qrSvg = json.data.qr_svg;

                // Update token displays
                const displayEl = document.getElementById('display-qr-token');
                if (displayEl) displayEl.textContent = token;
                const simInput = document.getElementById('sim-token-input');
                if (simInput) simInput.value = token;
                const kioskToken = document.getElementById('kiosk-token-display');
                if (kioskToken) kioskToken.textContent = token;

                // Update real SVG QR Code containers
                if (qrSvg) {
                    const qrWrapper = document.getElementById('qr-code-wrapper');
                    if (qrWrapper) qrWrapper.innerHTML = qrSvg;
                    const kioskQrWrapper = document.getElementById('kiosk-qr-wrapper');
                    if (kioskQrWrapper) kioskQrWrapper.innerHTML = qrSvg;
                }

                startTimer(30);
            } else {
                // Retry token refresh in 3s on error
                setTimeout(() => startTimer(3), 3000);
            }
        } catch (e) {
            console.error('Failed refreshing token:', e);
            setTimeout(() => startTimer(3), 3000);
        } finally {
            isRefreshing = false;
            if (refreshBtn) refreshBtn.classList.remove('opacity-50');
        }
    }

    function updateSessionUI(tipe) {
        const upper = tipe.toUpperCase();
        document.getElementById('text-sesi-tipe').textContent = upper;
        const kioskSessionText = document.getElementById('kiosk-session-text');
        if (kioskSessionText) kioskSessionText.textContent = upper;

        const badge = document.getElementById('label-sesi-badge');
        const kioskBadge = document.getElementById('kiosk-session-badge');
        const btnDatang = document.getElementById('btn-sesi-datang');
        const btnPulang = document.getElementById('btn-sesi-pulang');

        if (tipe === 'datang') {
            if (badge) badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold uppercase transition-colors bg-emerald-100 text-emerald-900';
            if (kioskBadge) kioskBadge.className = 'mb-4 px-6 py-2 rounded-full font-black text-sm tracking-wider uppercase shadow-lg transition-all bg-emerald-600 text-white';
            if (btnDatang) btnDatang.className = 'px-3.5 py-1.5 rounded-xl font-bold text-xs transition-colors bg-emerald-600 text-white shadow-xs';
            if (btnPulang) btnPulang.className = 'px-3.5 py-1.5 rounded-xl font-bold text-xs transition-colors bg-slate-100 hover:bg-slate-200 text-slate-800';
        } else {
            if (badge) badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold uppercase transition-colors bg-blue-100 text-blue-900';
            if (kioskBadge) kioskBadge.className = 'mb-4 px-6 py-2 rounded-full font-black text-sm tracking-wider uppercase shadow-lg transition-all bg-blue-600 text-white';
            if (btnDatang) btnDatang.className = 'px-3.5 py-1.5 rounded-xl font-bold text-xs transition-colors bg-slate-100 hover:bg-slate-200 text-slate-800';
            if (btnPulang) btnPulang.className = 'px-3.5 py-1.5 rounded-xl font-bold text-xs transition-colors bg-blue-600 text-white shadow-xs';
        }
    }

    function copyActiveToken() {
        const token = document.getElementById('display-qr-token').textContent.trim();
        navigator.clipboard.writeText(token).then(() => {
            alert('Kode token berhasil disalin: ' + token);
        });
    }

    // Kiosk Fullscreen Mode
    function openKioskMode() {
        const overlay = document.getElementById('kiosk-overlay');
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
        startClock();

        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen().catch(() => {});
        }
    }

    function closeKioskMode() {
        const overlay = document.getElementById('kiosk-overlay');
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');

        if (document.fullscreenElement && document.exitFullscreen) {
            document.exitFullscreen().catch(() => {});
        }
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeKioskMode();
        }
    });

    function startClock() {
        clearInterval(clockInterval);
        const clockEl = document.getElementById('kiosk-clock');
        function tick() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            if (clockEl) clockEl.textContent = `${h}:${m}:${s} WIB`;
        }
        tick();
        clockInterval = setInterval(tick, 1000);
    }

    // Real-time Live Polling of Attendance Log
    let lastLatestScanTime = null;

    async function pollLiveData() {
        try {
            const resp = await fetch('{{ route('web.presensi.live-data', ['cabang_id' => $currentCabang['id']]) }}', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await resp.json();

            if (data.success) {
                // Update metrics
                const s = data.summary;
                document.getElementById('metric-persentase').textContent = s.persentaseKehadiran + '%';
                document.getElementById('metric-sudah-datang').textContent = s.sudahDatang;
                document.getElementById('metric-sudah-pulang').textContent = s.sudahPulang;
                document.getElementById('metric-belum-hadir').textContent = s.belumHadir;
                document.getElementById('metric-total-guru').textContent = s.totalGuruBertugas;
                document.getElementById('metric-total-bertugas').textContent = s.totalGuruBertugas;

                // Update table if records changed
                renderAttendanceTable(data.absensiHariIni);

                // Toast notification if someone scanned in last 15 seconds
                if (data.latestScan && data.latestScan.jam !== lastLatestScanTime) {
                    lastLatestScanTime = data.latestScan.jam;
                    showLiveToast(data.latestScan);
                }
            }
        } catch (e) {
            console.error('Polling error:', e);
        }
    }

    function renderAttendanceTable(list) {
        const tbody = document.getElementById('attendance-tbody');
        document.getElementById('log-count-text').textContent = `${list.length} Guru Sudah Melakukan Absen`;

        if (!list || list.length === 0) {
            tbody.innerHTML = `
                <tr id="empty-row">
                    <td colspan="6" class="py-10 text-center text-slate-400 text-sm">
                        Belum ada catatan absensi untuk hari ini.
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        list.forEach((abs, idx) => {
            const datangHtml = abs.jam_datang 
                ? `<span class="inline-flex items-center gap-1 font-mono font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded num-tabular">${abs.jam_datang} WIB</span>` 
                : `<span class="text-slate-400 italic">-</span>`;

            const pulangHtml = abs.jam_pulang 
                ? `<span class="inline-flex items-center gap-1 font-mono font-bold text-blue-800 bg-blue-50 px-2 py-0.5 rounded num-tabular">${abs.jam_pulang} WIB</span>` 
                : `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">Masih Mengajar</span>`;

            let durasiHtml = `<span class="text-slate-400 text-xs">-</span>`;
            if (abs.durasi_menit !== null) {
                const hours = Math.floor(abs.durasi_menit / 60);
                const mins = abs.durasi_menit % 60;
                durasiHtml = `<span class="text-xs font-semibold text-slate-800 num-tabular">${hours > 0 ? hours + ' jam ' : ''}${mins} menit</span>`;
            }

            const statusBadge = abs.jam_pulang
                ? `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">Selesai Mengajar</span>`
                : `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">Tepat Waktu</span>`;

            html += `
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3 px-4 text-center text-xs text-slate-400 num-tabular">${idx + 1}</td>
                    <td class="py-3 px-4">
                        <div class="font-bold text-slate-900">${abs.guru_nama}</div>
                        <div class="text-[10px] text-slate-400 font-mono">${abs.no_pegawai}</div>
                    </td>
                    <td class="py-3 px-4">${datangHtml}</td>
                    <td class="py-3 px-4">${pulangHtml}</td>
                    <td class="py-3 px-4">${durasiHtml}</td>
                    <td class="py-3 px-4 text-center">${statusBadge}</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function showLiveToast(scan) {
        const toast = document.getElementById('live-scan-toast');
        document.getElementById('toast-tipe').textContent = `Presensi ${scan.tipe.toUpperCase()} Berhasil`;
        document.getElementById('toast-nama').textContent = scan.guru_nama;
        document.getElementById('toast-jam').textContent = `${scan.jam} WIB`;

        toast.classList.remove('hidden', 'translate-y-4');
        setTimeout(() => {
            hideToast();
        }, 6000);
    }

    function hideToast() {
        const toast = document.getElementById('live-scan-toast');
        toast.classList.add('translate-y-4');
        setTimeout(() => toast.classList.add('hidden'), 300);
    }

    document.addEventListener('DOMContentLoaded', () => {
        startTimer();
        livePollInterval = setInterval(pollLiveData, 6000);
    });
</script>
@endsection
