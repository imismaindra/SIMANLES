@extends('layouts.admin')

@section('title', 'Presensi Guru & Layar QR — LKP Sang Siroju Lillah')

@push('styles')
    <style>
        /* Scale the generated QR SVG to its container instead of its fixed pixel size */
        #qr-code-wrapper svg,
        #kiosk-qr-wrapper svg {
            display: block;
            width: 100%;
            height: auto;
        }
    </style>
@endpush

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
                <p class="text-[11px] text-slate-300">Tercatat di sistem cabang</p>
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
                        Smart QR Aktif
                    </span>
                </div>
                <p class="mt-1 text-xs sm:text-sm text-slate-500">
                    Sistem presensi mandiri guru berbasis QR dinamis dengan deteksi otomatis datang/pulang dan sinkronisasi jadwal harian.
                </p>
            </div>

            <!-- Action Controls (Kiosk Mode & Scanner Link) -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('presensi.scanner', ['cabang_id' => $currentCabang['id']]) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs border border-slate-200/80 transition-colors shadow-2xs">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>Layar Scanner HP Guru</span>
                </a>

                <button type="button" onclick="openKioskMode()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white font-semibold text-xs shadow-xs transition-colors">
                    <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                    </svg>
                    <span>Layar Penuh Kiosk Piket</span>
                </button>
            </div>
        </div>

        <!-- 4 Metric Attendance Strip -->
        <div class="mt-4 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Metric 1: Kehadiran -->
            <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-200/60">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold text-emerald-900 uppercase tracking-wider">Tingkat Hadir</p>
                    <span id="metric-persentase" class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-200 text-emerald-950">{{ $persentaseKehadiran }}%</span>
                </div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span id="metric-sudah-datang" class="text-2xl font-bold text-emerald-950 num-tabular">{{ $sudahDatang }}</span>
                    <span class="text-xs text-emerald-700">/ <span id="metric-total-guru">{{ $totalGuruBertugas }}</span> guru bertugas</span>
                </div>
                <p class="mt-1 text-[10px] text-emerald-800 font-medium">
                    {{ $totalGuruTerjadwal > 0 ? "Berdasarkan {$totalGuruTerjadwal} guru terjadwal hari ini" : "Berdasarkan seluruh guru cabang" }}
                </p>
            </div>

            <!-- Metric 2: Sedang Mengajar -->
            <div class="p-3.5 rounded-xl bg-blue-50/60 border border-blue-200/60">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold text-blue-900 uppercase tracking-wider">Sedang Mengajar</p>
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                </div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span id="metric-sedang-mengajar" class="text-2xl font-bold text-blue-950 num-tabular">{{ $sedangMengajar }}</span>
                    <span class="text-xs text-blue-700">guru aktif</span>
                </div>
                <p class="mt-1 text-[11px] text-blue-800">Sudah absen datang & belum pulang</p>
            </div>

            <!-- Metric 3: Sudah Pulang -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Sudah Pulang</p>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span id="metric-sudah-pulang" class="text-2xl font-bold text-slate-900 num-tabular">{{ $sudahPulang }}</span>
                    <span class="text-xs text-slate-500">guru</span>
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Checkout sesi mengajar selesai</p>
            </div>

            <!-- Metric 4: Belum Hadir / Izin -->
            <div class="p-3.5 rounded-xl {{ $belumHadir > 0 ? 'bg-amber-50 border border-amber-200' : 'bg-slate-50 border border-slate-200/70' }}">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold {{ $belumHadir > 0 ? 'text-amber-900' : 'text-slate-500' }} uppercase tracking-wider">Belum Hadir</p>
                    @if($izinSakit > 0)
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-200 text-amber-900">{{ $izinSakit }} Izin/Sakit</span>
                    @endif
                </div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span id="metric-belum-hadir" class="text-2xl font-bold {{ $belumHadir > 0 ? 'text-amber-950' : 'text-slate-900' }} num-tabular">{{ $belumHadir }}</span>
                    <span class="text-xs {{ $belumHadir > 0 ? 'text-amber-800' : 'text-slate-500' }}">guru</span>
                </div>
                <p class="mt-1 text-[11px] {{ $belumHadir > 0 ? 'text-amber-800' : 'text-slate-400' }}">
                    Total guru cabang: {{ $totalGuruCabang }} orang
                </p>
            </div>
        </div>
    </div>

    <!-- Main Live QR & Admin Manual Form Grid -->
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
                            <p class="text-xs text-slate-500">Tayangkan layar ini di meja piket / tablet resepsionis cabang</p>
                        </div>
                    </div>

                    <!-- Mode Selector Badge -->
                    <span id="label-sesi-badge" class="px-2.5 py-1 rounded-full text-xs font-bold uppercase transition-colors bg-emerald-100 text-emerald-900">
                        Mode: <span id="text-sesi-tipe">{{ strtoupper($activeQr->tipe ?? 'auto') }}</span>
                    </span>
                </div>

                <!-- Big QR Visual Box -->
                <div class="my-6 flex flex-col items-center justify-center p-6 bg-slate-950 text-white rounded-2xl border-4 border-slate-800 relative overflow-hidden text-center shadow-xl">
                    <div class="absolute -top-12 -right-12 w-32 h-32 bg-blue-600/20 rounded-full blur-2xl"></div>
                    <div class="absolute -bottom-12 -left-12 w-32 h-32 bg-emerald-500/20 rounded-full blur-2xl"></div>

                    <!-- Real SVG QR Code Container -->
                    <div id="qr-code-wrapper" style="width: 100%; max-width: 256px;" class="p-4 bg-white rounded-2xl shadow-2xl flex flex-col items-center justify-center transition-all duration-300">
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
                        <span>Kedaluwarsa otomatis dalam: <strong id="qr-countdown" class="text-amber-300 num-tabular font-mono text-sm">60 detik</strong></span>
                    </div>
                </div>
            </div>

            <!-- Controls: Regenerate & Switch Mode -->
            <div class="pt-3 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-1.5">
                    <button type="button" onclick="refreshQrToken('auto')" id="btn-sesi-auto"
                        class="px-3 py-1.5 rounded-xl font-bold text-xs transition-colors {{ ($activeQr->tipe ?? 'auto') === 'auto' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        Otomatis (Smart)
                    </button>
                    <button type="button" onclick="refreshQrToken('datang')" id="btn-sesi-datang"
                        class="px-3 py-1.5 rounded-xl font-bold text-xs transition-colors {{ ($activeQr->tipe ?? 'auto') === 'datang' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        Datang Saja
                    </button>
                    <button type="button" onclick="refreshQrToken('pulang')" id="btn-sesi-pulang"
                        class="px-3 py-1.5 rounded-xl font-bold text-xs transition-colors {{ ($activeQr->tipe ?? 'auto') === 'pulang' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        Pulang Saja
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="refreshQrToken()" id="btn-manual-refresh"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-900 hover:bg-blue-800 text-white font-semibold text-xs shadow-xs transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Perbarui Token</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Column 2: Admin Manual Attendance Entry (5 cols) -->
        <div class="lg:col-span-5 bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-2xs flex flex-col justify-between space-y-4">
            <div>
                <div class="pb-3 border-b border-slate-200/70 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-slate-900 text-base">Catat Presensi Manual Admin</h2>
                        <p class="text-xs text-slate-500">Input kehadiran jika guru lupa scan HP, izin, atau sakit</p>
                    </div>
                    <span class="p-1.5 bg-slate-100 text-slate-700 rounded-lg">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </span>
                </div>

                <form method="POST" action="{{ route('web.presensi.manual') }}" class="mt-4 space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Guru Pengajar *</label>
                        <select name="guru_id" id="manual-guru-select" required
                            class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                            <option value="">-- Pilih Guru Pengajar --</option>
                            @foreach($guruCabang as $g)
                                <option value="{{ $g->id }}" {{ old('guru_id') == $g->id ? 'selected' : '' }}>
                                    {{ $g->nama }} ({{ $g->no_pegawai ?: 'NIP Guru' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal *</label>
                            <input type="date" name="tanggal" required value="{{ old('tanggal', $today) }}"
                                class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Status Kehadiran *</label>
                            <select name="status" id="manual-status-select" required onchange="toggleManualTimeFields(this.value)"
                                class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                                <option value="hadir" {{ old('status', 'hadir') == 'hadir' ? 'selected' : '' }}>Hadir (Mengajar)</option>
                                <option value="izin" {{ old('status') == 'izin' ? 'selected' : '' }}>Izin Resmi</option>
                                <option value="sakit" {{ old('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="alpa" {{ old('status') == 'alpa' ? 'selected' : '' }}>Tanpa Keterangan (Alpa)</option>
                            </select>
                        </div>
                    </div>

                    <div id="manual-time-row" class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Datang</label>
                            <input type="time" name="jam_datang" value="{{ old('jam_datang', \Illuminate\Support\Carbon::now()->format('H:i')) }}"
                                class="w-full px-3 py-2 text-xs font-mono rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Pulang (Opsional)</label>
                            <input type="time" name="jam_pulang" value="{{ old('jam_pulang') }}"
                                class="w-full px-3 py-2 text-xs font-mono rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Catatan</label>
                        <input type="text" name="keterangan" value="{{ old('keterangan') }}"
                            placeholder="Contoh: Menggantikan sesi kelas 6 SD atau Surat izin dokter"
                            class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                    </div>

                    <button type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-blue-900 hover:bg-blue-800 active:bg-blue-950 text-white font-bold text-xs shadow-xs transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Presensi Manual</span>
                    </button>
                </form>
            </div>

            <!-- Notes on Anti-Fraud -->
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-500 space-y-1">
                <p class="font-bold text-slate-700">Petunjuk Presensi:</p>
                <p>1. <strong>Mode Otomatis (Smart)</strong>: Guru cukup scan QR, sistem otomatis mencatat Datang pada scan pertama, dan mencatat Pulang pada scan berikutnya.</p>
                <p>2. Guru yang tidak membawa HP dapat dicatat secara manual melalui formulir di atas.</p>
            </div>
        </div>
    </div>

    <!-- Attendance Sections: Tab Log Hari Ini vs Riwayat Presensi -->
    <div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs">
        <!-- Tab Navigation Header -->
        <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <button type="button" onclick="switchViewTab('today')" id="tab-today-btn"
                    class="px-3.5 py-1.5 rounded-xl font-bold text-xs bg-white text-blue-900 shadow-2xs border border-slate-200/80 transition-all flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Log Hari Ini ({{ \Illuminate\Support\Carbon::parse($today)->translatedFormat('d M Y') }})</span>
                </button>
                <button type="button" onclick="switchViewTab('history')" id="tab-history-btn"
                    class="px-3.5 py-1.5 rounded-xl font-bold text-xs text-slate-600 hover:text-slate-900 transition-all flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Riwayat Presensi Guru</span>
                </button>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 num-tabular">
                    <span id="log-count-text">{{ $absensiHariIni->count() }} Guru Sudah Tercatat Hari Ini</span>
                </span>
            </div>
        </div>

        <!-- VIEW TAB 1: Today's Live Attendance Table -->
        <div id="view-today-panel">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 text-slate-700 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200/80">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">#</th>
                            <th class="py-3 px-4">Nama Guru</th>
                            <th class="py-3 px-4">Jam Datang</th>
                            <th class="py-3 px-4">Jam Pulang</th>
                            <th class="py-3 px-4">Durasi Mengajar</th>
                            <th class="py-3 px-4 text-center">Metode</th>
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
                                    @if($abs->keterangan)
                                        <div class="text-[10px] text-amber-700 italic mt-0.5">{{ $abs->keterangan }}</div>
                                    @endif
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
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-800 border border-blue-200">
                                            Sedang Mengajar
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
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ ($abs->metode ?? 'qr') === 'manual' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ strtoupper($abs->metode ?? 'qr') }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($abs->status && $abs->status !== 'hadir')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                            {{ ucfirst($abs->status) }}
                                        </span>
                                    @elseif($abs->jam_pulang)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                            Selesai Mengajar
                                        </span>
                                    @elseif($abs->jam_datang)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                            Sedang Mengajar
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-row">
                                <td colspan="7" class="py-10 text-center text-slate-400 text-sm">
                                    Belum ada catatan absensi untuk hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- VIEW TAB 2: Attendance History & Date Filter -->
        <div id="view-history-panel" class="hidden">
            <!-- Filter Bar -->
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <form method="GET" action="{{ route('presensi.index') }}" class="flex flex-wrap items-center gap-2 text-xs">
                    <input type="hidden" name="cabang_id" value="{{ $currentCabang['id'] }}">
                    <div class="flex items-center gap-1.5">
                        <span class="font-semibold text-slate-600">Tanggal:</span>
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                            class="px-2.5 py-1.5 text-xs rounded-lg bg-white border border-slate-300">
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-semibold text-slate-600">Guru:</span>
                        <select name="guru_id" class="px-2.5 py-1.5 text-xs rounded-lg bg-white border border-slate-300">
                            <option value="">Semua Guru</option>
                            @foreach($guruCabang as $g)
                                <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>
                                    {{ $g->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-3 py-1.5 bg-slate-900 text-white font-bold rounded-lg hover:bg-slate-800 transition-colors">
                        Filter Riwayat
                    </button>
                    @if(request()->hasAny(['tanggal', 'guru_id']))
                        <a href="{{ route('presensi.index', ['cabang_id' => $currentCabang['id']]) }}" class="text-xs text-blue-700 hover:underline">
                            Reset Filter
                        </a>
                    @endif
                </form>

                <span class="text-xs text-slate-500 font-medium">Menampilkan {{ $riwayatAbsensi->count() }} catatan riwayat</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 text-slate-700 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200/80">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">#</th>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Nama Guru</th>
                            <th class="py-3 px-4">Jam Datang</th>
                            <th class="py-3 px-4">Jam Pulang</th>
                            <th class="py-3 px-4">Durasi</th>
                            <th class="py-3 px-4 text-center">Metode</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/70">
                        @forelse($riwayatAbsensi as $idx => $abs)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 text-center text-xs text-slate-400 num-tabular">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-800">
                                    {{ \Illuminate\Support\Carbon::parse($abs->tanggal)->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900">{{ $abs->guru->nama ?? 'Guru' }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $abs->guru->no_pegawai ?? '-' }}</div>
                                    @if($abs->keterangan)
                                        <div class="text-[10px] text-amber-700 italic mt-0.5">{{ $abs->keterangan }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-mono font-medium">
                                    {{ $abs->jam_datang ? $abs->jam_datang->format('H:i') . ' WIB' : '-' }}
                                </td>
                                <td class="py-3 px-4 font-mono font-medium">
                                    {{ $abs->jam_pulang ? $abs->jam_pulang->format('H:i') . ' WIB' : '-' }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($abs->jam_datang && $abs->jam_pulang)
                                        @php
                                            $diffMinutes = $abs->jam_datang->diffInMinutes($abs->jam_pulang);
                                            $h = floor($diffMinutes / 60);
                                            $m = $diffMinutes % 60;
                                        @endphp
                                        <span class="text-xs font-semibold text-slate-800">{{ $h > 0 ? "{$h}j " : '' }}{{ $m }}m</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ ($abs->metode ?? 'qr') === 'manual' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ strtoupper($abs->metode ?? 'qr') }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($abs->status && $abs->status !== 'hadir')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            {{ ucfirst($abs->status) }}
                                        </span>
                                    @elseif($abs->jam_pulang)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Hadir
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-slate-400 text-sm">
                                    Belum ada data riwayat presensi yang sesuai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Fullscreen Kiosk Overlay Modal -->
<div id="kiosk-overlay" class="fixed inset-0 z-50 bg-slate-950 text-white hidden flex-col justify-between p-4 sm:p-10 select-none overflow-y-auto">
    <!-- Kiosk Header -->
    <div class="flex flex-col gap-4 border-b border-slate-800 pb-4 sm:flex-row sm:items-center sm:justify-between sm:pb-5">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-slate-900 border border-slate-700 flex items-center justify-center p-1.5 shadow-md shrink-0">
                <img src="{{ asset('logoo.png') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
            <div class="min-w-0">
                <h2 class="text-lg sm:text-2xl font-bold tracking-tight text-white flex flex-wrap items-center gap-2">
                    LKP Sang Siroju Lillah
                    <span class="text-[10px] sm:text-xs px-2 py-0.5 rounded-full bg-blue-950 text-blue-300 border border-blue-800">
                        {{ $currentCabang['nama'] }}
                    </span>
                </h2>
                <p class="text-[11px] sm:text-xs text-slate-400">Layar Presensi Mandiri Guru — Meja Piket Cabang</p>
            </div>
        </div>

        <div class="flex items-center justify-between gap-3 sm:justify-end sm:gap-4">
            <!-- Digital Clock -->
            <div class="text-left sm:text-right">
                <div id="kiosk-clock" class="text-xl sm:text-3xl font-mono font-bold text-white tracking-widest num-tabular">
                    00:00:00 WIB
                </div>
                <div class="text-[10px] sm:text-xs text-slate-400 font-medium">
                    {{ \Illuminate\Support\Carbon::parse($today)->translatedFormat('l, d F Y') }}
                </div>
            </div>

            <!-- Exit Kiosk Button -->
            <button type="button" onclick="closeKioskMode()"
                class="px-3 sm:px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-colors flex items-center gap-1.5 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>Keluar<span class="hidden sm:inline"> Kiosk (ESC)</span></span>
            </button>
        </div>
    </div>

    <!-- Center QR Visual Area -->
    <div class="flex-1 flex flex-col items-center justify-center my-4 sm:my-6">
        <!-- Session Indicator Pill -->
        <div id="kiosk-session-badge" class="mb-4 px-6 py-2 rounded-full font-black text-sm tracking-wider uppercase shadow-lg transition-all bg-emerald-600 text-white">
            MODE: <span id="kiosk-session-text">{{ strtoupper($activeQr->tipe ?? 'auto') }} (SMART)</span>
        </div>

        <!-- Big Kiosk QR Box -->
        <div class="w-full max-w-sm p-4 sm:p-8 bg-white rounded-3xl shadow-2xl flex items-center justify-center border-4 border-slate-700 relative">
            <div id="kiosk-qr-wrapper" class="w-full flex items-center justify-center" style="aspect-ratio: 1 / 1;">
                {!! $qrSvg !!}
            </div>
        </div>

        <!-- Big Token Display -->
        <div class="mt-4 sm:mt-6 flex flex-col items-center max-w-full">
            <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">KODE TOKEN PRESENSI:</span>
            <div id="kiosk-token-display" class="mt-1 font-mono text-2xl sm:text-4xl font-black text-emerald-400 bg-slate-900 px-4 sm:px-6 py-1.5 rounded-2xl border border-slate-700 tracking-widest shadow-inner">
                {{ $activeQr->token ?? 'SIROJU-8492' }}
            </div>
        </div>

        <!-- Animated Progress / Countdown Indicator -->
        <div class="mt-4 flex items-center gap-2 text-xs sm:text-sm text-slate-300 text-center">
            <span class="w-3 h-3 rounded-full bg-amber-400 animate-ping shrink-0"></span>
            <span>Kode berubah otomatis dalam: <strong id="kiosk-countdown" class="text-amber-300 font-mono text-base font-bold">60 detik</strong></span>
        </div>
    </div>

    <!-- Kiosk Footer Controls -->
    <div class="flex flex-col gap-3 border-t border-slate-800 pt-4 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:gap-4 sm:pt-5">
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <button type="button" onclick="refreshQrToken('auto')"
                class="flex-1 sm:flex-none px-3 sm:px-5 py-2.5 rounded-xl font-bold text-xs transition-colors bg-emerald-600 hover:bg-emerald-500 text-white shadow-md">
                Mode Otomatis (Smart)
            </button>
            <button type="button" onclick="refreshQrToken('datang')"
                class="flex-1 sm:flex-none px-3 sm:px-5 py-2.5 rounded-xl font-bold text-xs transition-colors bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 shadow-md">
                Datang Saja
            </button>
            <button type="button" onclick="refreshQrToken('pulang')"
                class="flex-1 sm:flex-none px-3 sm:px-5 py-2.5 rounded-xl font-bold text-xs transition-colors bg-blue-600 hover:bg-blue-500 text-white shadow-md">
                Pulang Saja
            </button>
            <button type="button" onclick="refreshQrToken()"
                class="flex-1 sm:flex-none px-3 sm:px-4 py-2.5 rounded-xl font-bold text-xs transition-colors bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700">
                Perbarui Token Sekarang
            </button>
        </div>

        <div class="text-[11px] sm:text-xs text-slate-400 break-all">
            Scan langsung dengan kamera HP guru atau akses <span class="text-emerald-400 font-mono">{{ route('presensi.scanner') }}</span>
        </div>
    </div>
</div>

<script>
    let currentTipe = '{{ $activeQr->tipe ?? 'auto' }}';
    let countdownSec = 60;
    let timerInterval = null;
    let livePollInterval = null;
    let clockInterval = null;

    function startTimer() {
        clearInterval(timerInterval);
        countdownSec = 60;
        updateCountdownDisplay();

        timerInterval = setInterval(() => {
            countdownSec--;
            updateCountdownDisplay();
            if (countdownSec <= 0) {
                refreshQrToken();
            }
        }, 1000);
    }

    function updateCountdownDisplay() {
        const text = countdownSec + ' detik';
        const el = document.getElementById('qr-countdown');
        const kioskEl = document.getElementById('kiosk-countdown');
        if (el) el.textContent = text;
        if (kioskEl) kioskEl.textContent = text;
    }

    async function refreshQrToken(forcedTipe) {
        if (forcedTipe) {
            currentTipe = forcedTipe;
            updateSessionUI(forcedTipe);
        }

        const refreshBtn = document.getElementById('btn-manual-refresh');
        if (refreshBtn) refreshBtn.classList.add('opacity-50');

        try {
            const resp = await fetch('{{ route('web.presensi.qr') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    cabang_id: '{{ $currentCabang['id'] }}',
                    tipe: currentTipe,
                    duration: 60
                })
            });

            const json = await resp.json();
            if (json.success) {
                const token = json.data.token;
                const qrSvg = json.data.qr_svg;

                // Update token displays
                document.getElementById('display-qr-token').textContent = token;
                const kioskToken = document.getElementById('kiosk-token-display');
                if (kioskToken) kioskToken.textContent = token;

                // Update real SVG QR Code containers
                if (qrSvg) {
                    const qrWrapper = document.getElementById('qr-code-wrapper');
                    if (qrWrapper) qrWrapper.innerHTML = qrSvg;
                    const kioskQrWrapper = document.getElementById('kiosk-qr-wrapper');
                    if (kioskQrWrapper) kioskQrWrapper.innerHTML = qrSvg;
                }

                startTimer();
            }
        } catch (e) {
            console.error('Failed refreshing token:', e);
        } finally {
            if (refreshBtn) refreshBtn.classList.remove('opacity-50');
        }
    }

    function updateSessionUI(tipe) {
        const upper = tipe.toUpperCase();
        document.getElementById('text-sesi-tipe').textContent = upper;
        const kioskSessionText = document.getElementById('kiosk-session-text');
        if (kioskSessionText) kioskSessionText.textContent = upper + (tipe === 'auto' ? ' (SMART)' : '');

        const badge = document.getElementById('label-sesi-badge');
        const kioskBadge = document.getElementById('kiosk-session-badge');
        const btnAuto = document.getElementById('btn-sesi-auto');
        const btnDatang = document.getElementById('btn-sesi-datang');
        const btnPulang = document.getElementById('btn-sesi-pulang');

        // Reset classes
        if (btnAuto) btnAuto.className = 'px-3 py-1.5 rounded-xl font-bold text-xs transition-colors bg-slate-100 hover:bg-slate-200 text-slate-700';
        if (btnDatang) btnDatang.className = 'px-3 py-1.5 rounded-xl font-bold text-xs transition-colors bg-slate-100 hover:bg-slate-200 text-slate-700';
        if (btnPulang) btnPulang.className = 'px-3 py-1.5 rounded-xl font-bold text-xs transition-colors bg-slate-100 hover:bg-slate-200 text-slate-700';

        if (tipe === 'auto') {
            if (badge) badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold uppercase transition-colors bg-emerald-100 text-emerald-900';
            if (kioskBadge) kioskBadge.className = 'mb-4 px-6 py-2 rounded-full font-black text-sm tracking-wider uppercase shadow-lg transition-all bg-emerald-600 text-white';
            if (btnAuto) btnAuto.className = 'px-3 py-1.5 rounded-xl font-bold text-xs transition-colors bg-emerald-600 text-white shadow-xs';
        } else if (tipe === 'datang') {
            if (badge) badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold uppercase transition-colors bg-emerald-100 text-emerald-900';
            if (kioskBadge) kioskBadge.className = 'mb-4 px-6 py-2 rounded-full font-black text-sm tracking-wider uppercase shadow-lg transition-all bg-emerald-600 text-white';
            if (btnDatang) btnDatang.className = 'px-3 py-1.5 rounded-xl font-bold text-xs transition-colors bg-emerald-600 text-white shadow-xs';
        } else {
            if (badge) badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold uppercase transition-colors bg-blue-100 text-blue-900';
            if (kioskBadge) kioskBadge.className = 'mb-4 px-6 py-2 rounded-full font-black text-sm tracking-wider uppercase shadow-lg transition-all bg-blue-600 text-white';
            if (btnPulang) btnPulang.className = 'px-3 py-1.5 rounded-xl font-bold text-xs transition-colors bg-blue-600 text-white shadow-xs';
        }
    }

    function toggleManualTimeFields(status) {
        const timeRow = document.getElementById('manual-time-row');
        if (status === 'hadir') {
            timeRow.classList.remove('opacity-40');
        } else {
            timeRow.classList.add('opacity-40');
        }
    }

    function switchViewTab(tab) {
        const todayBtn = document.getElementById('tab-today-btn');
        const historyBtn = document.getElementById('tab-history-btn');
        const todayPanel = document.getElementById('view-today-panel');
        const historyPanel = document.getElementById('view-history-panel');

        if (tab === 'today') {
            todayBtn.className = 'px-3.5 py-1.5 rounded-xl font-bold text-xs bg-white text-blue-900 shadow-2xs border border-slate-200/80 transition-all flex items-center gap-1.5';
            historyBtn.className = 'px-3.5 py-1.5 rounded-xl font-bold text-xs text-slate-600 hover:text-slate-900 transition-all flex items-center gap-1.5';
            todayPanel.classList.remove('hidden');
            historyPanel.classList.add('hidden');
        } else {
            historyBtn.className = 'px-3.5 py-1.5 rounded-xl font-bold text-xs bg-white text-blue-900 shadow-2xs border border-slate-200/80 transition-all flex items-center gap-1.5';
            todayBtn.className = 'px-3.5 py-1.5 rounded-xl font-bold text-xs text-slate-600 hover:text-slate-900 transition-all flex items-center gap-1.5';
            historyPanel.classList.remove('hidden');
            todayPanel.classList.add('hidden');
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
                const s = data.summary;
                document.getElementById('metric-persentase').textContent = s.persentaseKehadiran + '%';
                document.getElementById('metric-sudah-datang').textContent = s.sudahDatang;
                document.getElementById('metric-sudah-pulang').textContent = s.sudahPulang;
                document.getElementById('metric-sedang-mengajar').textContent = s.sedangMengajar ?? Math.max(0, s.sudahDatang - s.sudahPulang);
                document.getElementById('metric-belum-hadir').textContent = s.belumHadir;
                document.getElementById('metric-total-guru').textContent = s.totalGuruBertugas;

                renderAttendanceTable(data.absensiHariIni);

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
        document.getElementById('log-count-text').textContent = `${list.length} Guru Sudah Tercatat Hari Ini`;

        if (!list || list.length === 0) {
            tbody.innerHTML = `
                <tr id="empty-row">
                    <td colspan="7" class="py-10 text-center text-slate-400 text-sm">
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
                : `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-800 border border-blue-200">Sedang Mengajar</span>`;

            let durasiHtml = `<span class="text-slate-400 text-xs">-</span>`;
            if (abs.durasi_menit !== null && abs.durasi_menit !== undefined) {
                const hours = Math.floor(abs.durasi_menit / 60);
                const mins = abs.durasi_menit % 60;
                durasiHtml = `<span class="text-xs font-semibold text-slate-800 num-tabular">${hours > 0 ? hours + ' jam ' : ''}${mins} menit</span>`;
            }

            let statusBadge = `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">Selesai Mengajar</span>`;
            if (abs.raw_status && abs.raw_status !== 'hadir') {
                statusBadge = `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">${abs.status}</span>`;
            } else if (!abs.jam_pulang && abs.jam_datang) {
                statusBadge = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span> Sedang Mengajar</span>`;
            }

            const metodeBadge = `<span class="text-[10px] font-bold px-2 py-0.5 rounded ${abs.metode === 'manual' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700'}">${(abs.metode || 'QR').toUpperCase()}</span>`;

            const ketHtml = abs.keterangan ? `<div class="text-[10px] text-amber-700 italic mt-0.5">${abs.keterangan}</div>` : '';

            html += `
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3 px-4 text-center text-xs text-slate-400 num-tabular">${idx + 1}</td>
                    <td class="py-3 px-4">
                        <div class="font-bold text-slate-900">${abs.guru_nama}</div>
                        <div class="text-[10px] text-slate-400 font-mono">${abs.no_pegawai}</div>
                        ${ketHtml}
                    </td>
                    <td class="py-3 px-4">${datangHtml}</td>
                    <td class="py-3 px-4">${pulangHtml}</td>
                    <td class="py-3 px-4">${durasiHtml}</td>
                    <td class="py-3 px-4 text-center">${metodeBadge}</td>
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

        // Jika ada filter di query string, buka tab history
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('tanggal') || urlParams.has('guru_id')) {
            switchViewTab('history');
        }
    });
</script>
@endsection
