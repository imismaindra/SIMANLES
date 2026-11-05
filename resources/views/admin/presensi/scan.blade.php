<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Presensi Mandiri Guru — LKP Sang Siroju Lillah</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- HTML5 QR Code Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <style>
        @keyframes scanLaser {
            0% { top: 5%; opacity: 0.8; }
            50% { top: 90%; opacity: 1; }
            100% { top: 5%; opacity: 0.8; }
        }
        .laser-line {
            position: absolute;
            left: 5%;
            right: 5%;
            height: 2px;
            background: linear-gradient(90deg, rgba(16,185,129,0) 0%, #10b981 50%, rgba(16,185,129,0) 100%);
            box-shadow: 0 0 12px #10b981;
            animation: scanLaser 2.2s infinite ease-in-out;
            pointer-events: none;
            z-index: 10;
        }
    </style>
</head>
<body class="h-full bg-slate-950 font-sans text-slate-100 antialiased flex flex-col justify-between selection:bg-emerald-500 selection:text-slate-950">

    <!-- Top Navigation Header -->
    <header class="bg-slate-900/90 border-b border-slate-800/80 backdrop-blur-md px-4 py-3.5 sticky top-0 z-30">
        <div class="max-w-md mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center p-1.5 shadow-xs">
                    <img src="{{ asset('logoo.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="font-bold text-sm text-white flex items-center gap-2">
                        Presensi Guru
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-950 text-emerald-300 border border-emerald-800">
                            Mandiri
                        </span>
                    </h1>
                    <p class="text-[11px] text-slate-400 font-medium">{{ $currentCabang['nama'] }}</p>
                </div>
            </div>

            <div class="text-right">
                <div id="live-time" class="text-xs font-mono font-bold text-emerald-400">--:--:-- WIB</div>
                <div class="text-[10px] text-slate-400">{{ \Illuminate\Support\Carbon::now()->translatedFormat('d M Y') }}</div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-md w-full mx-auto p-4 flex flex-col justify-start space-y-4">

        <!-- Result / Notification Alert Modal -->
        <div id="result-box" class="hidden rounded-2xl p-4 border transition-all duration-300 shadow-xl">
            <div class="flex items-start gap-3">
                <div id="result-icon-container" class="p-2 rounded-xl shrink-0 mt-0.5"></div>
                <div class="flex-1">
                    <h3 id="result-title" class="font-bold text-sm"></h3>
                    <p id="result-message" class="text-xs mt-1 leading-relaxed"></p>
                    <div id="result-details" class="mt-2 text-[11px] font-mono p-2 rounded-lg bg-slate-900/60 hidden"></div>
                </div>
                <button type="button" onclick="closeResultBox()" class="text-slate-400 hover:text-white p-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Teacher Selection Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 shadow-xl">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                1. Pilih Akun Guru Pengajar *
            </label>
            <div class="relative">
                <select id="select-guru" onchange="onGuruSelected(this.value)"
                    class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl bg-slate-950 border border-slate-700 text-slate-100 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    <option value="">-- Pilih Nama Anda --</option>
                    @foreach($guruList as $g)
                        <option value="{{ $g->id }}" data-nama="{{ $g->nama }}">
                            {{ $g->nama }} ({{ $g->no_pegawai ?: 'NIP Guru' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Saved Hint & Teacher Status Today Strip -->
            <div id="guru-status-container" class="mt-2.5 hidden">
                <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span id="guru-status-dot" class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                        <span id="guru-status-text" class="text-slate-300 font-medium">Memuat status...</span>
                    </div>
                    <span id="guru-action-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-800 text-slate-300">
                        Otomatis
                    </span>
                </div>
            </div>
        </div>

        <!-- If Token is provided in URL via Kiosk QR scan -->
        @if(!empty($token))
        <div id="quick-confirm-card" class="bg-emerald-950/40 border-2 border-emerald-600/70 rounded-2xl p-4 shadow-xl space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="text-xs font-bold text-emerald-300 uppercase tracking-wider">Token QR Terdeteksi</span>
                </div>
                <span class="font-mono text-xs font-black text-emerald-400 bg-slate-900 px-2.5 py-1 rounded-lg border border-slate-700">
                    {{ $token }}
                </span>
            </div>

            <p class="text-xs text-slate-300 leading-relaxed">
                Kamera Anda telah memindai QR layar cabang. Tekan tombol di bawah untuk menyelesaikan pencatatan kehadiran.
            </p>

            <button type="button" onclick="submitScannedToken('{{ $token }}')" id="btn-quick-confirm"
                class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-bold text-xs shadow-lg transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                <span id="quick-btn-label">Konfirmasi Presensi Sekarang</span>
            </button>
        </div>
        @endif

        <!-- Attendance Action Tabs (Camera Scan vs Manual Input) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 shadow-xl space-y-4">
            <!-- Tab Buttons -->
            <div class="flex items-center p-1 bg-slate-950 rounded-xl border border-slate-800">
                <button type="button" id="tab-btn-camera" onclick="switchTab('camera')"
                    class="flex-1 py-2 text-xs font-bold rounded-lg text-white bg-emerald-600 transition-all flex items-center justify-center gap-1.5 shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Scan Kamera</span>
                </button>
                <button type="button" id="tab-btn-manual" onclick="switchTab('manual')"
                    class="flex-1 py-2 text-xs font-bold rounded-lg text-slate-400 hover:text-white transition-all flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Input Kode</span>
                </button>
            </div>

            <!-- TAB 1: Camera Scanner -->
            <div id="panel-camera" class="space-y-3">
                <div class="relative w-full aspect-square bg-slate-950 rounded-2xl border-2 border-slate-700 overflow-hidden flex items-center justify-center shadow-inner">
                    <!-- Scanner Viewport Container -->
                    <div id="qr-reader" class="w-full h-full object-cover"></div>

                    <!-- Overlay Laser animation -->
                    <div id="scanner-laser" class="laser-line"></div>

                    <!-- Targeting Crosshair Box -->
                    <div class="absolute inset-8 border border-white/20 rounded-xl pointer-events-none flex flex-col justify-between p-2">
                        <div class="flex justify-between">
                            <span class="w-4 h-4 border-t-2 border-l-2 border-emerald-400 rounded-tl"></span>
                            <span class="w-4 h-4 border-t-2 border-r-2 border-emerald-400 rounded-tr"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="w-4 h-4 border-b-2 border-l-2 border-emerald-400 rounded-bl"></span>
                            <span class="w-4 h-4 border-b-2 border-r-2 border-emerald-400 rounded-br"></span>
                        </div>
                    </div>

                    <!-- Loading / Placeholder Message when camera is off -->
                    <div id="camera-loading" class="absolute inset-0 bg-slate-950/80 flex flex-col items-center justify-center p-4 text-center">
                        <svg class="w-10 h-10 text-emerald-400 animate-pulse mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <p class="text-xs font-semibold text-slate-200">Menginisialisasi Kamera HP...</p>
                        <p class="text-[11px] text-slate-400 mt-1">Arahkan lensa ke kode QR di layar cabang</p>
                    </div>
                </div>

                <!-- Camera Controls -->
                <div class="flex items-center justify-between text-xs text-slate-400 pt-1">
                    <span id="camera-status" class="flex items-center gap-1.5 text-emerald-400 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        Kamera Aktif
                    </span>
                    <button type="button" onclick="toggleCameraFacing()" 
                        class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium border border-slate-700 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Ganti Kamera</span>
                    </button>
                </div>
            </div>

            <!-- TAB 2: Manual Code Input -->
            <div id="panel-manual" class="space-y-3 hidden">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                        Kode Token Presensi *
                    </label>
                    <input type="text" id="manual-token-input" value="{{ $token ?? '' }}"
                        placeholder="Contoh: SIROJU-8492"
                        class="w-full px-3.5 py-2.5 text-sm font-mono font-bold tracking-wider uppercase rounded-xl bg-slate-950 border border-slate-700 text-emerald-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    <p class="text-[11px] text-slate-400 mt-1">Masukkan 4-8 karakter token yang tampil di layar meja piket cabang.</p>
                </div>

                <button type="button" onclick="submitManualAttendance()" id="btn-submit-manual"
                    class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-bold text-xs shadow-lg transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Konfirmasi Presensi Sekarang</span>
                </button>
            </div>
        </div>

        <!-- Instructions & Security Footer -->
        <div class="p-3.5 rounded-xl bg-slate-900/50 border border-slate-800 text-[11px] text-slate-400 space-y-1">
            <p class="font-bold text-slate-300">Panduan Presensi Mandiri:</p>
            <p>1. Pilih nama pengajar Anda. Sistem otomatis mengingat pilihan di perangkat ini.</p>
            <p>2. Arahkan kamera ke layar cabang atau masukkan kode token.</p>
            <p>3. Sistem otomatis mencatat <strong>Jam Datang</strong> pada kedatangan awal dan <strong>Jam Pulang</strong> saat selesai mengajar.</p>
        </div>
    </main>

    <!-- Bottom Footer -->
    <footer class="py-3 text-center text-[11px] text-slate-500 border-t border-slate-900">
        &copy; {{ date('Y') }} LKP Sang Siroju Lillah — Sistem Informasi Manajemen Les
    </footer>

    <script>
        let html5QrCode = null;
        let currentFacingMode = "environment";
        let isProcessing = false;
        let activeTab = 'camera';

        document.addEventListener('DOMContentLoaded', () => {
            startClock();

            const savedGuruId = localStorage.getItem('simanles_guru_id');
            const selectGuru = document.getElementById('select-guru');
            if (savedGuruId && selectGuru) {
                for (let opt of selectGuru.options) {
                    if (opt.value === savedGuruId) {
                        selectGuru.value = savedGuruId;
                        fetchTeacherStatus(savedGuruId);
                        break;
                    }
                }
            }

            const urlToken = "{{ $token ?? '' }}";
            if (urlToken) {
                // Jangan paksa tab camera jika sudah ada token dari scan luar
                switchTab('manual');
                document.getElementById('manual-token-input').value = urlToken;
            } else {
                startCameraScanner();
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
                        dot.className = 'w-2.5 h-2.5 rounded-full bg-amber-400';
                        badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-950 text-amber-300 border border-amber-800';
                        badge.textContent = 'Aksi Berikutnya: Datang';
                    } else if (d.next_action === 'pulang') {
                        dot.className = 'w-2.5 h-2.5 rounded-full bg-blue-400 animate-pulse';
                        badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-950 text-blue-300 border border-blue-800';
                        badge.textContent = 'Aksi Berikutnya: Pulang';
                    } else {
                        dot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-400';
                        badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-950 text-emerald-300 border border-emerald-800';
                        badge.textContent = 'Presensi Lengkap';
                    }

                    const quickLabel = document.getElementById('quick-btn-label');
                    if (quickLabel) {
                        if (d.next_action === 'datang') quickLabel.textContent = 'Konfirmasi Presensi Datang';
                        else if (d.next_action === 'pulang') quickLabel.textContent = 'Konfirmasi Presensi Pulang';
                        else quickLabel.textContent = 'Presensi Hari Ini Sudah Selesai';
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
                btnCam.className = 'flex-1 py-2 text-xs font-bold rounded-lg text-white bg-emerald-600 transition-all flex items-center justify-center gap-1.5 shadow-xs';
                btnMan.className = 'flex-1 py-2 text-xs font-bold rounded-lg text-slate-400 hover:text-white transition-all flex items-center justify-center gap-1.5';
                panelCam.classList.remove('hidden');
                panelMan.classList.add('hidden');
                startCameraScanner();
            } else {
                btnMan.className = 'flex-1 py-2 text-xs font-bold rounded-lg text-white bg-emerald-600 transition-all flex items-center justify-center gap-1.5 shadow-xs';
                btnCam.className = 'flex-1 py-2 text-xs font-bold rounded-lg text-slate-400 hover:text-white transition-all flex items-center justify-center gap-1.5';
                panelMan.classList.remove('hidden');
                panelCam.classList.add('hidden');
                stopCameraScanner();
            }
        }

        async function startCameraScanner() {
            if (activeTab !== 'camera') return;
            const loadingEl = document.getElementById('camera-loading');
            if (loadingEl) loadingEl.classList.remove('hidden');

            try {
                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("qr-reader");
                }

                await html5QrCode.start(
                    { facingMode: currentFacingMode },
                    {
                        fps: 15,
                        qrbox: { width: 220, height: 220 }
                    },
                    (decodedText, decodedResult) => {
                        onScanSuccess(decodedText);
                    },
                    (errorMessage) => {}
                );

                if (loadingEl) loadingEl.classList.add('hidden');
                document.getElementById('camera-status').innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> Kamera Aktif';
            } catch (err) {
                console.warn('Camera start error:', err);
                if (loadingEl) {
                    loadingEl.innerHTML = `
                        <p class="text-xs font-bold text-amber-400">Akses Kamera Tidak Tersedia</p>
                        <p class="text-[11px] text-slate-400 mt-1">Izinkan izin kamera pada browser Anda atau gunakan tab "Input Kode" secara manual.</p>
                        <button type="button" onclick="switchTab('manual')" class="mt-3 px-3 py-1.5 rounded-lg bg-emerald-600 text-white font-bold text-xs">
                            Gunakan Input Manual
                        </button>
                    `;
                }
                document.getElementById('camera-status').innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-400"></span> Kamera Off';
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
            startCameraScanner();
        }

        async function onScanSuccess(decodedText) {
            if (isProcessing) return;
            isProcessing = true;

            if (navigator.vibrate) navigator.vibrate(80);

            let token = decodedText.trim();
            const match = token.match(/SIROJU-[\w\d]+/i);
            if (match) {
                token = match[0].toUpperCase();
            }

            const guruId = document.getElementById('select-guru').value;
            if (!guruId) {
                showResult(false, 'Pilih Guru Terlebih Dahulu', 'Silakan pilih nama Anda pada menu pilihan di atas sebelum melakukan scan QR.');
                setTimeout(() => { isProcessing = false; }, 2000);
                return;
            }

            await executeAttendance(token, guruId);
            setTimeout(() => { isProcessing = false; }, 3000);
        }

        async function submitScannedToken(token) {
            const guruId = document.getElementById('select-guru').value;
            if (!guruId) {
                showResult(false, 'Pilih Guru Terlebih Dahulu', 'Silakan pilih nama Anda pada menu pilihan di atas terlebih dahulu.');
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
            const token = document.getElementById('manual-token-input').value.trim();
            const guruId = document.getElementById('select-guru').value;

            if (!guruId) {
                showResult(false, 'Pilih Guru Terlebih Dahulu', 'Silakan pilih nama Anda pada menu pilihan di atas.');
                return;
            }

            if (!token) {
                showResult(false, 'Kode Token Kosong', 'Silakan masukkan kode token presensi yang tertera di layar cabang.');
                return;
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
                        `Halo <strong>${res.guru_nama}</strong>, kehadiran sesi <strong>${sessionName}</strong> Anda telah tercatat pada pukul <strong>${res.jam} WIB</strong> di ${res.cabang_nama}.`,
                        `Token: ${token} | Tanggal: ${res.tanggal}`
                    );
                    fetchTeacherStatus(guruId);
                } else {
                    const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Gagal mencatat presensi');
                    showResult(false, 'Presensi Gagal', msg);
                }
            } catch (err) {
                console.error(err);
                showResult(false, 'Terjadi Kesalahan', 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.');
            }
        }

        function showResult(isSuccess, title, message, details = null) {
            const box = document.getElementById('result-box');
            const iconContainer = document.getElementById('result-icon-container');
            const titleEl = document.getElementById('result-title');
            const messageEl = document.getElementById('result-message');
            const detailsEl = document.getElementById('result-details');

            box.classList.remove('hidden', 'bg-emerald-950/80', 'border-emerald-700', 'text-emerald-200', 'bg-rose-950/80', 'border-rose-700', 'text-rose-200');

            if (isSuccess) {
                box.classList.add('bg-emerald-950/80', 'border-emerald-700', 'text-emerald-200');
                iconContainer.className = 'p-2 rounded-xl bg-emerald-900/60 text-emerald-400 shrink-0';
                iconContainer.innerHTML = `
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                `;
            } else {
                box.classList.add('bg-rose-950/80', 'border-rose-700', 'text-rose-200');
                iconContainer.className = 'p-2 rounded-xl bg-rose-900/60 text-rose-400 shrink-0';
                iconContainer.innerHTML = `
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
</body>
</html>
