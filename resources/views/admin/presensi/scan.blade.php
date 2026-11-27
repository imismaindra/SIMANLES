@extends('layouts.admin')

@section('title', 'Scan QR Presensi Guru — LKP Sang Siroju Lillah')

@push('styles')
    <!-- HTML5 QR Code Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <style>
        @keyframes scanLaser {
            0% { top: 6%; opacity: 0.8; }
            50% { top: 92%; opacity: 1; }
            100% { top: 6%; opacity: 0.8; }
        }
        .laser-line {
            position: absolute;
            left: 6%;
            right: 6%;
            height: 2px;
            background: linear-gradient(90deg, rgba(16,185,129,0) 0%, #10b981 50%, rgba(16,185,129,0) 100%);
            box-shadow: 0 0 10px #10b981;
            animation: scanLaser 2.2s infinite ease-in-out;
            pointer-events: none;
            z-index: 10;
        }
        #qr-reader {
            width: 100% !important;
            height: 100% !important;
            border: none !important;
        }
        #qr-reader video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            border-radius: 1rem;
        }
    </style>
@endpush

@section('content')
<div class="max-w-md mx-auto space-y-4">

    <!-- Header Ringkas & Info Pengajar -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-2xs">
        @php
            $loggedGuru = auth()->user()?->guru;
        @endphp

        @if($loggedGuru)
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200/80 text-blue-900 flex items-center justify-center font-bold text-sm shrink-0">
                        {{ strtoupper(substr($loggedGuru->nama, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-sm font-bold text-slate-900 truncate">{{ $loggedGuru->nama }}</h2>
                        <p class="text-[11px] text-slate-500 font-medium font-mono">
                            {{ $loggedGuru->no_pegawai ?: 'Pengajar' }} · {{ $currentCabang['nama'] }}
                        </p>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <div id="live-time" class="text-xs font-mono font-bold text-blue-900">--:--:-- WIB</div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Aktif
                    </span>
                </div>
            </div>
            <input type="hidden" id="select-guru" value="{{ $loggedGuru->id }}">
        @else
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Pilih Guru Pengajar *
                    </label>
                    <div id="live-time" class="text-xs font-mono font-bold text-blue-900">--:--:-- WIB</div>
                </div>
                <select id="select-guru" onchange="onGuruSelected(this.value)"
                    class="w-full px-3 py-2 text-xs font-semibold rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white">
                    <option value="">-- Pilih Nama Anda --</option>
                    @foreach($guruList as $g)
                        <option value="{{ $g->id }}" data-nama="{{ $g->nama }}">
                            {{ $g->nama }} ({{ $g->no_pegawai ?: 'NIP Guru' }})
                        </option>
                    @endforeach
                </select>
                <p id="guru-saved-hint" class="text-[10px] text-emerald-700 mt-1.5 hidden flex items-center gap-1 font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Nama tersimpan otomatis di perangkat ini
                </p>
            </div>
        @endif

        <!-- Teacher Status Today Strip -->
        <div id="guru-status-container" class="mt-3 pt-3 border-t border-slate-100 hidden">
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 min-w-0">
                    <span id="guru-status-dot" class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
                    <span id="guru-status-text" class="text-slate-700 font-medium truncate">Memuat status...</span>
                </div>
                <span id="guru-action-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white text-slate-700 border border-slate-200 shrink-0">
                    Otomatis
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Confirm Card jika ada Token via URL -->
    @if(!empty($token))
    <div id="quick-confirm-card" class="bg-blue-50/80 border border-blue-200 rounded-2xl p-4 shadow-2xs space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></span>
                <span class="text-xs font-bold text-blue-900">Token Terdeteksi</span>
            </div>
            <span class="font-mono text-xs font-bold text-blue-950 bg-white px-2.5 py-0.5 rounded-lg border border-blue-200">
                {{ $token }}
            </span>
        </div>
        <p class="text-xs text-slate-600 leading-relaxed">
            Kamera memindai QR cabang. Tekan tombol di bawah untuk konfirmasi kehadiran.
        </p>
        <button type="button" onclick="submitScannedToken('{{ $token }}')" id="btn-quick-confirm"
            class="w-full py-2.5 rounded-xl bg-blue-900 hover:bg-blue-800 text-white font-bold text-xs shadow-xs transition-colors flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
            <span id="quick-btn-label">Konfirmasi Presensi Sekarang</span>
        </button>
    </div>
    @endif

    <!-- Result / Notification Alert Box -->
    <div id="result-box" class="hidden rounded-2xl p-4 border transition-all duration-300 shadow-sm">
        <div class="flex items-start gap-3">
            <div id="result-icon-container" class="p-2 rounded-xl shrink-0 mt-0.5"></div>
            <div class="flex-1 min-w-0">
                <h3 id="result-title" class="font-bold text-sm"></h3>
                <p id="result-message" class="text-xs mt-1 leading-relaxed"></p>
                <div id="result-details" class="mt-2 text-[11px] font-mono p-2 rounded-lg bg-white/70 border border-slate-200 hidden"></div>
            </div>
            <button type="button" onclick="closeResultBox()" class="text-slate-400 hover:text-slate-600 p-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Scanner / Input Container Card -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-2xs space-y-3.5">
        <!-- Switch Tab: Scan Kamera vs Input Kode -->
        <div class="flex items-center p-1 bg-slate-100 rounded-xl border border-slate-200/60">
            <button type="button" id="tab-btn-camera" onclick="switchTab('camera')"
                class="flex-1 py-1.5 text-xs font-bold rounded-lg text-white bg-blue-900 transition-all flex items-center justify-center gap-1.5 shadow-2xs">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Scan Kamera</span>
            </button>
            <button type="button" id="tab-btn-manual" onclick="switchTab('manual')"
                class="flex-1 py-1.5 text-xs font-bold rounded-lg text-slate-600 hover:text-slate-900 transition-all flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Input Kode</span>
            </button>
        </div>

        <!-- TAB 1: Camera Scanner -->
        <div id="panel-camera" class="space-y-3">
            <!-- Alert HTTPS jika diperlukan -->
            <div id="insecure-context-alert" class="hidden p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1">
                <div class="flex items-center gap-1.5 font-bold text-amber-900">
                    <svg class="w-4 h-4 shrink-0 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Gunakan Tombol Foto (Bebas HTTPS)</span>
                </div>
                <p class="text-[11px] text-amber-800">
                    Live camera dibatasi browser jika belum HTTPS. Anda bisa langsung memakai tombol <strong>Ambil Foto QR</strong> di bawah.
                </p>
            </div>

            <!-- Viewport Kamera Minimalis -->
            <div class="relative w-full aspect-square max-w-[320px] mx-auto bg-slate-950 rounded-2xl border-2 border-slate-200/90 overflow-hidden flex items-center justify-center shadow-inner">
                <!-- Scanner Viewport Container -->
                <div id="qr-reader" class="w-full h-full object-cover"></div>

                <!-- Laser animation -->
                <div id="scanner-laser" class="laser-line"></div>

                <!-- Targeting Crosshair Box -->
                <div id="scanner-crosshair" class="absolute inset-7 border border-white/20 rounded-xl pointer-events-none flex flex-col justify-between p-2">
                    <div class="flex justify-between">
                        <span class="w-3.5 h-3.5 border-t-2 border-l-2 border-emerald-400 rounded-tl"></span>
                        <span class="w-3.5 h-3.5 border-t-2 border-r-2 border-emerald-400 rounded-tr"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="w-3.5 h-3.5 border-b-2 border-l-2 border-emerald-400 rounded-bl"></span>
                        <span class="w-3.5 h-3.5 border-b-2 border-r-2 border-emerald-400 rounded-br"></span>
                    </div>
                </div>

                <!-- Camera Loading / Prompt -->
                <div id="camera-loading" class="absolute inset-0 bg-slate-900/90 flex flex-col items-center justify-center p-4 text-center z-20">
                    <svg class="w-8 h-8 text-emerald-400 animate-pulse mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    <p class="text-xs font-semibold text-white">Menghubungkan Kamera...</p>
                    <p class="text-[11px] text-slate-300 mt-0.5">Arahkan lensa ke kode QR di layar cabang</p>
                </div>
            </div>

            <!-- Camera Controls & Switch -->
            <div class="flex items-center justify-between text-xs pt-1 px-1">
                <span id="camera-status" class="inline-flex items-center gap-1.5 text-emerald-700 font-semibold text-[11px]">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    Kamera Aktif
                </span>
                <button type="button" onclick="toggleCameraFacing()"
                    class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] border border-slate-200 transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Ganti Kamera</span>
                </button>
            </div>

            <!-- Alternative Quick Scan Via Native Camera File Input -->
            <div class="pt-2 border-t border-slate-100">
                <input type="file" id="qr-file-camera" accept="image/*" capture="environment" class="hidden" onchange="handleQrFile(this)">
                <input type="file" id="qr-file-gallery" accept="image/*" class="hidden" onchange="handleQrFile(this)">

                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="document.getElementById('qr-file-camera').click()"
                        class="py-2 px-3 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold text-xs border border-slate-200 flex items-center justify-center gap-1.5 transition-colors">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Ambil Foto QR</span>
                    </button>
                    <button type="button" onclick="document.getElementById('qr-file-gallery').click()"
                        class="py-2 px-3 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold text-xs border border-slate-200 flex items-center justify-center gap-1.5 transition-colors">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Pilih Gambar QR</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 2: Manual Code Input -->
        <div id="panel-manual" class="space-y-3 hidden">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Kode Token Presensi *
                </label>
                <input type="text" id="manual-token-input" value="{{ $token ?? '' }}"
                    placeholder="Contoh: SIROJU-8492 atau 8492"
                    class="w-full px-3.5 py-2.5 text-sm font-mono font-bold tracking-wider uppercase rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white">
                <p class="text-[11px] text-slate-500 mt-1">Masukkan 4 digit kode token yang tertera pada layar cabang.</p>
            </div>

            <button type="button" onclick="submitManualAttendance()" id="btn-submit-manual"
                class="w-full py-2.5 rounded-xl bg-blue-900 hover:bg-blue-800 text-white font-bold text-xs shadow-xs transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Konfirmasi Presensi Sekarang</span>
            </button>
        </div>
    </div>

    <!-- Log Riwayat Singkat Presensi Saya -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 shadow-2xs space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Log Kehadiran Saya</span>
            </h3>
            <a href="{{ route('presensi.riwayat') }}" class="text-[11px] text-blue-800 hover:underline font-semibold">
                Lihat Lengkap &rarr;
            </a>
        </div>

        <div id="log-scan-empty" class="{{ isset($riwayatPresensi) && $riwayatPresensi->isNotEmpty() ? 'hidden' : '' }} text-center py-3 text-xs text-slate-400">
            Belum ada catatan presensi terbaru.
        </div>

        <div id="log-scan-list" class="space-y-2 max-h-48 overflow-y-auto pr-0.5">
            @if(isset($riwayatPresensi) && $riwayatPresensi->isNotEmpty())
                @foreach($riwayatPresensi as $item)
                    @php
                        $durasi = ($item->jam_datang && $item->jam_pulang) ? (int)$item->jam_datang->diffInMinutes($item->jam_pulang) : null;
                    @endphp
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between gap-2 text-xs">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-slate-900">{{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}</span>
                                <span class="text-[10px] text-slate-500">· {{ $item->cabang->nama ?? 'Cabang' }}</span>
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-2 font-mono">
                                <span class="text-emerald-700 font-semibold">Masuk: {{ $item->jam_datang ? $item->jam_datang->format('H:i') : '-' }}</span>
                                <span>·</span>
                                <span class="text-blue-700 font-semibold">Pulang: {{ $item->jam_pulang ? $item->jam_pulang->format('H:i') : '-' }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            @if($item->jam_pulang)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    {{ $durasi ? $durasi . ' mnt' : 'Selesai' }}
                                </span>
                            @elseif($item->jam_datang)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 animate-pulse">
                                    Mengajar
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">
                                    Tercatat
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

</div>

@push('scripts')
<script>
    let html5QrCode = null;
    let currentFacingMode = "environment";
    let isProcessing = false;
    let scanCooldownUntil = 0;
    let scanCooldownTimer = null;
    const SCAN_SUCCESS_COOLDOWN_MS = 10000;
    const SCAN_ERROR_COOLDOWN_MS = 4000;
    let activeTab = 'camera';

    function isSecureContextEnvironment() {
        if (window.isSecureContext) return true;
        const host = window.location.hostname;
        return window.location.protocol === 'https:' || host === 'localhost' || host === '127.0.0.1';
    }

    function isInAppBrowser() {
        const ua = navigator.userAgent || navigator.vendor || window.opera;
        return /FBAN|FBAV|Instagram|WhatsApp|Line/i.test(ua);
    }

    document.addEventListener('DOMContentLoaded', () => {
        startClock();

        if (!isSecureContextEnvironment()) {
            document.getElementById('insecure-context-alert')?.classList.remove('hidden');
        }

        @if($loggedGuru)
            fetchTeacherStatus('{{ $loggedGuru->id }}');
        @endif

        const savedGuruId = localStorage.getItem('simanles_guru_id');
        const selectGuru = document.getElementById('select-guru');
        if (savedGuruId && selectGuru && selectGuru.options) {
            for (let opt of selectGuru.options) {
                if (opt.value === savedGuruId) {
                    selectGuru.value = savedGuruId;
                    fetchTeacherStatus(savedGuruId);
                    document.getElementById('guru-saved-hint')?.classList.remove('hidden');
                    break;
                }
            }
        }

        const urlToken = "{{ $token ?? '' }}";
        if (urlToken) {
            switchTab('manual');
            const manualInput = document.getElementById('manual-token-input');
            if (manualInput) manualInput.value = urlToken;

            const activeGuruId = selectGuru ? selectGuru.value : null;
            if (activeGuruId) {
                setTimeout(() => {
                    submitManualAttendance();
                }, 500);
            }
        } else {
            if (!isSecureContextEnvironment()) {
                renderInsecureContextHelp();
            } else {
                startCameraScanner().catch(err => handleCameraError(err));
            }
        }
    });

    function startClock() {
        function tick() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            const el = document.getElementById('live-time');
            if (el) el.textContent = `${h}:${m}:${s} WIB`;
        }
        tick();
        setInterval(tick, 1000);
    }

    async function onGuruSelected(guruId) {
        if (guruId) {
            localStorage.setItem('simanles_guru_id', guruId);
            await fetchTeacherStatus(guruId);
        } else {
            localStorage.removeItem('simanles_guru_id');
            document.getElementById('guru-status-container').classList.add('hidden');
        }
    }

    async function fetchTeacherStatus(guruId) {
        const container = document.getElementById('guru-status-container');
        const dot = document.getElementById('guru-status-dot');
        const text = document.getElementById('guru-status-text');
        const badge = document.getElementById('guru-action-badge');

        try {
            const resp = await fetch(`{{ route('api.absensi.guru-status') }}?guru_id=${guruId}&cabang_id={{ $currentCabang['id'] }}`);
            const res = await resp.json();

            if (res.success) {
                const d = res.data;
                container.classList.remove('hidden');
                text.textContent = d.status_label;

                if (d.next_action === 'datang') {
                    dot.className = 'w-2 h-2 rounded-full bg-amber-500';
                    badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200';
                    badge.textContent = 'Aksi: Datang';
                } else if (d.next_action === 'pulang') {
                    dot.className = 'w-2 h-2 rounded-full bg-blue-500 animate-pulse';
                    badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-800 border border-blue-200';
                    badge.textContent = 'Aksi: Pulang';
                } else {
                    dot.className = 'w-2 h-2 rounded-full bg-emerald-500';
                    badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200';
                    badge.textContent = 'Presensi Lengkap';
                }

                const quickLabel = document.getElementById('quick-btn-label');
                if (quickLabel) {
                    if (d.next_action === 'datang') quickLabel.textContent = 'Konfirmasi Presensi Datang';
                    else if (d.next_action === 'pulang') quickLabel.textContent = 'Konfirmasi Presensi Pulang';
                    else quickLabel.textContent = 'Presensi Hari Ini Lengkap';
                }
            }
        } catch (e) {
            console.warn('Failed fetching status:', e);
        }
    }

    function switchTab(tab) {
        activeTab = tab;
        const btnCam = document.getElementById('tab-btn-camera');
        const btnMan = document.getElementById('tab-btn-manual');
        const panelCam = document.getElementById('panel-camera');
        const panelMan = document.getElementById('panel-manual');

        if (tab === 'camera') {
            btnCam.className = 'flex-1 py-1.5 text-xs font-bold rounded-lg text-white bg-blue-900 transition-all flex items-center justify-center gap-1.5 shadow-2xs';
            btnMan.className = 'flex-1 py-1.5 text-xs font-bold rounded-lg text-slate-600 hover:text-slate-900 transition-all flex items-center justify-center gap-1.5';
            panelCam.classList.remove('hidden');
            panelMan.classList.add('hidden');
            if (!isSecureContextEnvironment()) {
                renderInsecureContextHelp();
            } else {
                startCameraScanner().catch(err => handleCameraError(err));
            }
        } else {
            btnMan.className = 'flex-1 py-1.5 text-xs font-bold rounded-lg text-white bg-blue-900 transition-all flex items-center justify-center gap-1.5 shadow-2xs';
            btnCam.className = 'flex-1 py-1.5 text-xs font-bold rounded-lg text-slate-600 hover:text-slate-900 transition-all flex items-center justify-center gap-1.5';
            panelMan.classList.remove('hidden');
            panelCam.classList.add('hidden');
            stopCameraScanner();
        }
    }

    async function requestCameraAccess(toggleFacing = false) {
        if (toggleFacing) {
            currentFacingMode = currentFacingMode === "environment" ? "user" : "environment";
        }

        if (!isSecureContextEnvironment()) {
            renderInsecureContextHelp();
            return;
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            renderInsecureContextHelp();
            return;
        }

        const loadingEl = document.getElementById('camera-loading');
        if (loadingEl) {
            loadingEl.classList.remove('hidden');
            loadingEl.innerHTML = `
                <svg class="w-8 h-8 text-emerald-400 animate-spin mb-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <p class="text-xs font-semibold text-white">Menghubungkan Kamera...</p>
                <p class="text-[11px] text-slate-300 mt-0.5">Pilih "Izinkan" jika muncul popup izin kamera</p>
            `;
        }

        try {
            const testStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: currentFacingMode }
            });
            testStream.getTracks().forEach(track => track.stop());
            await startCameraScanner();
        } catch (err) {
            console.warn('Request camera access error:', err);
            handleCameraError(err);
        }
    }

    async function startCameraScanner() {
        if (activeTab !== 'camera') return;

        if (!isSecureContextEnvironment()) {
            renderInsecureContextHelp();
            return;
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            renderInsecureContextHelp();
            return;
        }

        const loadingEl = document.getElementById('camera-loading');
        const laser = document.getElementById('scanner-laser');
        const crosshair = document.getElementById('scanner-crosshair');

        if (loadingEl) {
            loadingEl.classList.remove('hidden');
            loadingEl.innerHTML = `
                <svg class="w-8 h-8 text-emerald-400 animate-pulse mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                <p class="text-xs font-semibold text-white">Menghubungkan Kamera...</p>
            `;
        }

        try {
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("qr-reader");
            } else if (html5QrCode.isScanning) {
                try { await html5QrCode.stop(); } catch(e) {}
            }

            const scanConfig = {
                fps: 15,
                qrbox: function(viewfinderWidth, viewfinderHeight) {
                    const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                    const edge = Math.max(160, Math.floor(minEdge * 0.72));
                    return { width: edge, height: edge };
                }
            };

            try {
                await html5QrCode.start(
                    { facingMode: currentFacingMode },
                    scanConfig,
                    (decodedText) => onScanSuccess(decodedText),
                    () => {}
                );
            } catch (firstErr) {
                console.warn('FacingMode start failed, trying cameras list fallback:', firstErr);
                const cameras = await Html5Qrcode.getCameras();
                if (cameras && cameras.length > 0) {
                    const backCam = cameras.find(c => /back|belakang|rear|environment/i.test(c.label));
                    const chosenCameraId = backCam ? backCam.id : cameras[0].id;
                    await html5QrCode.start(
                        chosenCameraId,
                        scanConfig,
                        (decodedText) => onScanSuccess(decodedText),
                        () => {}
                    );
                } else {
                    throw firstErr;
                }
            }

            if (loadingEl) loadingEl.classList.add('hidden');
            if (laser) laser.classList.remove('hidden');
            if (crosshair) crosshair.classList.remove('hidden');
            document.getElementById('camera-status').innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span> Kamera Aktif';
        } catch (err) {
            console.warn('Camera start error:', err);
            handleCameraError(err);
        }
    }

    function handleCameraError(err) {
        const loadingEl = document.getElementById('camera-loading');
        const laser = document.getElementById('scanner-laser');
        const crosshair = document.getElementById('scanner-crosshair');
        if (laser) laser.classList.add('hidden');
        if (crosshair) crosshair.classList.add('hidden');
        document.getElementById('camera-status').innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-500"></span> Kamera Off';

        if (!loadingEl) return;
        loadingEl.classList.remove('hidden');

        const errStr = err ? (err.name || err.message || err.toString()) : '';
        const isPermissionDenied = /NotAllowedError|PermissionDeniedError|NotAllowed|permission/i.test(errStr);
        const isNotFound = /NotFoundError|DevicesNotFoundError/i.test(errStr);

        if (isPermissionDenied) {
            loadingEl.innerHTML = `
                <div class="space-y-2 p-3 text-center">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center mx-auto">
                        <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h4 class="text-xs font-bold text-white">Izin Kamera Belum Diberikan</h4>
                    <p class="text-[11px] text-slate-300 leading-relaxed">
                        Pilih <strong>Ambil Foto QR</strong> di bawah untuk memindai tanpa batasan izin kamera.
                    </p>
                    <div class="flex flex-col gap-1.5 pt-1">
                        <button type="button" onclick="document.getElementById('qr-file-camera').click()" class="w-full py-2 px-3 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs">
                            📸 Ambil Foto QR
                        </button>
                        <button type="button" onclick="requestCameraAccess()" class="w-full py-1.5 px-3 rounded-lg bg-slate-800 text-slate-300 font-semibold text-xs border border-slate-700">
                            Coba Buka Kamera Lagi
                        </button>
                    </div>
                </div>
            `;
        } else if (isNotFound) {
            loadingEl.innerHTML = `
                <div class="space-y-2 p-3 text-center">
                    <p class="text-xs font-bold text-rose-300">Kamera Tidak Ditemukan</p>
                    <p class="text-[11px] text-slate-300">Perangkat tidak memiliki kamera aktif.</p>
                    <button type="button" onclick="document.getElementById('qr-file-camera').click()" class="mt-1 w-full py-2 rounded-lg bg-emerald-600 text-white font-bold text-xs">
                        📸 Ambil Foto QR
                    </button>
                </div>
            `;
        } else {
            loadingEl.innerHTML = `
                <div class="space-y-2.5 p-3 text-center">
                    <h4 class="text-xs font-bold text-white">Buka Kamera untuk Scan</h4>
                    <p class="text-[11px] text-slate-300">Ketuk tombol di bawah untuk mengaktifkan kamera.</p>
                    <button type="button" onclick="requestCameraAccess()" class="w-full py-2 px-3 rounded-xl bg-blue-900 hover:bg-blue-800 text-white font-bold text-xs shadow-xs">
                        Izinkan & Buka Kamera
                    </button>
                    <div class="pt-0.5 flex gap-2">
                        <button type="button" onclick="document.getElementById('qr-file-camera').click()" class="flex-1 py-1.5 px-2 rounded-lg bg-slate-800 text-emerald-300 text-[11px] font-semibold border border-slate-700">
                            Foto QR
                        </button>
                        <button type="button" onclick="switchTab('manual')" class="flex-1 py-1.5 px-2 rounded-lg bg-slate-800 text-slate-300 text-[11px] font-semibold border border-slate-700">
                            Input Kode
                        </button>
                    </div>
                </div>
            `;
        }
    }

    function renderInsecureContextHelp() {
        const loadingEl = document.getElementById('camera-loading');
        const laser = document.getElementById('scanner-laser');
        const crosshair = document.getElementById('scanner-crosshair');
        if (laser) laser.classList.add('hidden');
        if (crosshair) crosshair.classList.add('hidden');
        document.getElementById('camera-status').innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-500"></span> Mode Foto';

        if (!loadingEl) return;
        loadingEl.classList.remove('hidden');
        loadingEl.innerHTML = `
            <div class="space-y-2.5 p-3 text-center">
                <div class="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-300 flex items-center justify-center mx-auto">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    </svg>
                </div>
                <h4 class="text-xs font-bold text-white">Scan Menggunakan Foto</h4>
                <p class="text-[11px] text-slate-300 leading-relaxed">
                    Browser membatasi streaming kamera pada koneksi lokal. Gunakan tombol foto langsung dari kamera HP:
                </p>
                <button type="button" onclick="document.getElementById('qr-file-camera').click()" class="w-full py-2 px-3 rounded-xl bg-blue-900 hover:bg-blue-800 text-white font-bold text-xs shadow-xs">
                    📸 Ambil Foto QR Sekarang
                </button>
                <button type="button" onclick="switchTab('manual')" class="w-full py-1.5 px-3 rounded-lg bg-slate-800 text-slate-300 text-xs font-semibold border border-slate-700">
                    Input Kode Token Manual
                </button>
            </div>
        `;
    }

    async function handleQrFile(input) {
        if (!input.files || input.files.length === 0) return;
        const file = input.files[0];

        const loadingEl = document.getElementById('camera-loading');
        if (loadingEl) {
            loadingEl.classList.remove('hidden');
            loadingEl.innerHTML = `
                <svg class="w-8 h-8 text-emerald-400 animate-spin mb-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <p class="text-xs font-semibold text-white">Membaca Kode QR...</p>
            `;
        }

        try {
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("qr-reader");
            } else if (html5QrCode.isScanning) {
                try { await html5QrCode.stop(); } catch(e) {}
            }

            const decodedText = await html5QrCode.scanFile(file, true);
            if (loadingEl) loadingEl.classList.add('hidden');
            onScanSuccess(decodedText);
        } catch (err) {
            console.warn('Scan file error:', err);
            if (loadingEl) loadingEl.classList.add('hidden');
            showResult(false, 'Kode QR Tidak Terbaca', 'Pastikan foto QR terlihat utuh, tegak, dan tidak buram.');
        } finally {
            input.value = '';
        }
    }

    async function stopCameraScanner() {
        if (html5QrCode && html5QrCode.isScanning) {
            try {
                await html5QrCode.stop();
            } catch (e) {
                console.error('Stop camera failed:', e);
            }
        }
    }

    async function toggleCameraFacing() {
        await stopCameraScanner();
        currentFacingMode = currentFacingMode === "environment" ? "user" : "environment";
        await requestCameraAccess();
    }

    function startScanCooldown(durationMs) {
        scanCooldownUntil = Date.now() + durationMs;
        if (scanCooldownTimer) clearInterval(scanCooldownTimer);

        const updateStatus = () => {
            const remainingSec = Math.ceil((scanCooldownUntil - Date.now()) / 1000);
            const statusEl = document.getElementById('camera-status');
            if (!statusEl) return;

            if (remainingSec > 0) {
                statusEl.innerHTML = `<span class="w-2 h-2 rounded-full bg-amber-500"></span> Jeda scan (${remainingSec}d)...`;
            } else {
                clearInterval(scanCooldownTimer);
                scanCooldownTimer = null;
                if (html5QrCode && html5QrCode.isScanning) {
                    statusEl.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span> Kamera Aktif';
                }
            }
        };

        updateStatus();
        scanCooldownTimer = setInterval(updateStatus, 500);
    }

    async function onScanSuccess(decodedText) {
        if (isProcessing || Date.now() < scanCooldownUntil) return;
        isProcessing = true;

        if (navigator.vibrate) navigator.vibrate(80);

        let token = decodedText.trim();
        const match = token.match(/SIROJU-[\w\d]+/i);
        if (match) {
            token = match[0].toUpperCase();
        }

        const guruId = document.getElementById('select-guru').value;
        if (!guruId) {
            showResult(false, 'Pilih Guru Terlebih Dahulu', 'Silakan pilih nama Anda pada pilihan pengajar di atas.');
            startScanCooldown(SCAN_ERROR_COOLDOWN_MS);
            isProcessing = false;
            return;
        }

        const ok = await executeAttendance(token, guruId);
        startScanCooldown(ok ? SCAN_SUCCESS_COOLDOWN_MS : SCAN_ERROR_COOLDOWN_MS);
        isProcessing = false;
    }

    async function submitScannedToken(token) {
        const guruId = document.getElementById('select-guru').value;
        if (!guruId) {
            showResult(false, 'Pilih Guru Terlebih Dahulu', 'Silakan pilih nama Anda pada pilihan pengajar di atas.');
            return;
        }

        const btn = document.getElementById('btn-quick-confirm');
        btn.disabled = true;
        btn.innerHTML = 'Memproses...';

        await executeAttendance(token, guruId);

        btn.disabled = false;
        btn.innerHTML = `
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
            <span>Konfirmasi Presensi Sekarang</span>
        `;
    }

    async function submitManualAttendance() {
        let token = document.getElementById('manual-token-input').value.trim();
        const guruId = document.getElementById('select-guru').value;

        if (!guruId) {
            showResult(false, 'Pilih Guru Terlebih Dahulu', 'Silakan pilih nama Anda pada pilihan pengajar di atas.');
            return;
        }

        if (!token) {
            showResult(false, 'Kode Token Kosong', 'Silakan masukkan kode token presensi dari layar cabang.');
            return;
        }

        if (/^\d{4,6}$/.test(token)) {
            token = 'SIROJU-' + token;
        }

        const btn = document.getElementById('btn-submit-manual');
        btn.disabled = true;
        btn.innerHTML = 'Memproses Presensi...';

        await executeAttendance(token, guruId);

        btn.disabled = false;
        btn.innerHTML = `
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>Konfirmasi Presensi Sekarang</span>
        `;
    }

    async function executeAttendance(token, guruId) {
        try {
            const response = await fetch("{{ route('api.absensi.scan') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    token: token,
                    guru_id: guruId
                })
            });

            const data = await response.json();

            if (data.success) {
                const res = data.data;
                const sessionName = res.tipe.toUpperCase();
                showResult(true, `Presensi ${sessionName} Berhasil!`, 
                    `Presensi <strong>${sessionName}</strong> untuk <strong>${res.guru_nama}</strong> tercatat pukul <strong>${res.jam} WIB</strong> di ${res.cabang_nama}.`,
                    `Token: ${token} | Tanggal: ${res.tanggal}`
                );
                fetchTeacherStatus(guruId);

                const logList = document.getElementById('log-scan-list');
                const logEmpty = document.getElementById('log-scan-empty');
                if (logEmpty) logEmpty.classList.add('hidden');
                if (logList) {
                    const durasiText = res.durasi_menit ? `${res.durasi_menit} mnt` : (res.tipe === 'pulang' ? 'Selesai' : 'Mengajar');
                    const badgeClass = res.tipe === 'pulang' 
                        ? 'bg-emerald-50 text-emerald-800 border-emerald-200' 
                        : 'bg-amber-50 text-amber-800 border-amber-200 animate-pulse';

                    const newLogHtml = `
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-emerald-300 flex items-center justify-between gap-2 text-xs transition-all">
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-slate-900">Hari ini (${res.tanggal})</span>
                                    <span class="text-[10px] text-slate-500">· ${res.cabang_nama}</span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-2 font-mono">
                                    <span class="text-emerald-700 font-semibold">Masuk: ${res.jam_datang || '-'}</span>
                                    <span>·</span>
                                    <span class="text-blue-700 font-semibold">Pulang: ${res.jam_pulang || '-'}</span>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border ${badgeClass}">
                                    ${durasiText}
                                </span>
                            </div>
                        </div>
                    `;
                    logList.insertAdjacentHTML('afterbegin', newLogHtml);
                }
            } else {
                const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Gagal mencatat presensi');
                showResult(false, 'Presensi Gagal', msg);
                return false;
            }
            return true;
        } catch (err) {
            console.error(err);
            showResult(false, 'Terjadi Kesalahan', 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.');
            return false;
        }
    }

    function showResult(isSuccess, title, message, details = null) {
        const box = document.getElementById('result-box');
        const iconContainer = document.getElementById('result-icon-container');
        const titleEl = document.getElementById('result-title');
        const messageEl = document.getElementById('result-message');
        const detailsEl = document.getElementById('result-details');

        box.classList.remove('hidden', 'bg-emerald-50', 'border-emerald-300', 'text-emerald-950', 'bg-rose-50', 'border-rose-300', 'text-rose-950');

        if (isSuccess) {
            box.classList.add('bg-emerald-50', 'border-emerald-300', 'text-emerald-950');
            iconContainer.className = 'w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0';
            iconContainer.innerHTML = `
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            `;
        } else {
            box.classList.add('bg-rose-50', 'border-rose-300', 'text-rose-950');
            iconContainer.className = 'w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0';
            iconContainer.innerHTML = `
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            `;
        }

        titleEl.textContent = title;
        messageEl.innerHTML = message;

        if (details) {
            detailsEl.textContent = details;
            detailsEl.classList.remove('hidden');
        } else {
            detailsEl.classList.add('hidden');
        }

        box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function closeResultBox() {
        document.getElementById('result-box').classList.add('hidden');
    }
</script>
@endpush
@endsection
