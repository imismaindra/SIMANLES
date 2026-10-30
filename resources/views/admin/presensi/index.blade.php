@extends('layouts.admin')

@section('title', 'Presensi Guru & Layar QR — LKP Sang Siroju Lillah')

@section('content')
<div class="space-y-6">

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
                    Mencegah kecurangan absensi menggunakan token QR dinamis berumur pendek (15–30 detik). Guru melakukan scan QR mandiri di lokasi cabang.
                </p>
            </div>

            <!-- Date Indicator -->
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 bg-slate-100/80 px-3.5 py-2 rounded-xl border border-slate-200">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                    <line x1="16" x2="16" y1="2" y2="6" />
                    <line x1="8" x2="8" y1="2" y2="6" />
                    <line x1="3" x2="21" y1="10" y2="10" />
                </svg>
                <span>Hari Ini: {{ \Illuminate\Support\Carbon::parse($today)->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>

        <!-- 4 Metric Attendance Strip -->
        <div class="mt-4 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Metric 1: Kehadiran -->
            <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-200/60">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold text-emerald-900 uppercase tracking-wider">Tingkat Hadir</p>
                    <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-emerald-200 text-emerald-950">{{ $persentaseKehadiran }}%</span>
                </div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-emerald-950 num-tabular">{{ $sudahDatang }}</span>
                    <span class="text-xs text-emerald-700">/ {{ $totalGuruBertugas }} guru hadir</span>
                </div>
                <p class="mt-1 text-[10px] text-emerald-800 font-medium">Jam Datang Tercatat</p>
            </div>

            <!-- Metric 2: Sudah Pulang -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Sudah Pulang</p>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-slate-900 num-tabular">{{ $sudahPulang }}</span>
                    <span class="text-xs text-slate-500">guru</span>
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Checkout sesi mengajar</p>
            </div>

            <!-- Metric 3: Belum Hadir -->
            <div class="p-3.5 rounded-xl {{ $belumHadir > 0 ? 'bg-amber-50 border border-amber-200' : 'bg-slate-50 border border-slate-200/70' }}">
                <p class="text-[11px] font-semibold {{ $belumHadir > 0 ? 'text-amber-900' : 'text-slate-500' }} uppercase tracking-wider">Belum Presensi</p>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold {{ $belumHadir > 0 ? 'text-amber-950' : 'text-slate-900' }} num-tabular">{{ $belumHadir }}</span>
                    <span class="text-xs {{ $belumHadir > 0 ? 'text-amber-800' : 'text-slate-500' }}">guru</span>
                </div>
                <p class="mt-1 text-[11px] {{ $belumHadir > 0 ? 'text-amber-800' : 'text-slate-400' }}">Menunggu jadwal masuk</p>
            </div>

            <!-- Metric 4: Total Guru Cabang -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Guru Terdaftar</p>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-slate-900 num-tabular">{{ $totalGuruBertugas }}</span>
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
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <rect width="5" height="5" x="3" y="3" rx="1" />
                                <rect width="5" height="5" x="16" y="3" rx="1" />
                                <rect width="5" height="5" x="3" y="16" rx="1" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="font-bold text-slate-900 text-base">Layar QR Code Presensi Dinamis</h2>
                            <p class="text-xs text-slate-500">Tayangkan layar ini di meja piket / tablet cabang</p>
                        </div>
                    </div>

                    <!-- Sesi Selector Badge -->
                    <span id="label-sesi-badge" class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-900 uppercase">
                        Sesi: <span id="text-sesi-tipe">{{ strtoupper($activeQr->tipe ?? 'datang') }}</span>
                    </span>
                </div>

                <!-- Big QR Visual Box -->
                <div class="my-6 flex flex-col items-center justify-center p-6 bg-slate-950 text-white rounded-2xl border-4 border-blue-900/40 relative overflow-hidden text-center shadow-lg">
                    <div class="absolute -top-12 -right-12 w-32 h-32 bg-blue-600/20 rounded-full blur-2xl"></div>
                    <div class="absolute -bottom-12 -left-12 w-32 h-32 bg-emerald-500/20 rounded-full blur-2xl"></div>

                    <!-- Simulated Visual QR Glyph Container -->
                    <div class="p-4 bg-white rounded-2xl shadow-xl flex flex-col items-center justify-center">
                        <!-- SVG Simulated High Contrast QR -->
                        <svg class="w-44 h-44 text-slate-950" viewBox="0 0 100 100" fill="currentColor">
                            <!-- Corners -->
                            <rect x="5" y="5" width="25" height="25" rx="3" fill="#0f172a" />
                            <rect x="9" y="9" width="17" height="17" rx="2" fill="#ffffff" />
                            <rect x="13" y="13" width="9" height="9" fill="#1e3a8a" />

                            <rect x="70" y="5" width="25" height="25" rx="3" fill="#0f172a" />
                            <rect x="74" y="9" width="17" height="17" rx="2" fill="#ffffff" />
                            <rect x="78" y="13" width="9" height="9" fill="#1e3a8a" />

                            <rect x="5" y="70" width="25" height="25" rx="3" fill="#0f172a" />
                            <rect x="9" y="74" width="17" height="17" rx="2" fill="#ffffff" />
                            <rect x="13" y="78" width="9" height="9" fill="#1e3a8a" />

                            <!-- Dynamic center dots and bars -->
                            <rect x="35" y="10" width="8" height="8" rx="1" />
                            <rect x="48" y="12" width="6" height="6" rx="1" />
                            <rect x="58" y="8" width="6" height="12" rx="1" />

                            <rect x="10" y="35" width="8" height="8" rx="1" />
                            <rect x="12" y="48" width="6" height="6" rx="1" />
                            <rect x="8" y="58" width="12" height="6" rx="1" />

                            <rect x="35" y="35" width="30" height="30" rx="4" fill="#1e3a8a" />
                            <rect x="42" y="42" width="16" height="16" rx="2" fill="#ffffff" />
                            <rect x="46" y="46" width="8" height="8" rx="1" fill="#0f172a" />

                            <rect x="70" y="40" width="10" height="6" rx="1" />
                            <rect x="85" y="45" width="6" height="12" rx="1" />
                            <rect x="75" y="60" width="18" height="8" rx="1" />

                            <rect x="40" y="75" width="12" height="6" rx="1" />
                            <rect x="58" y="72" width="6" height="18" rx="1" />
                            <rect x="72" y="80" width="12" height="12" rx="2" />
                        </svg>
                    </div>

                    <!-- Token Code Box -->
                    <div class="mt-4 flex flex-col items-center">
                        <span class="text-xs text-slate-400 font-medium uppercase tracking-wider">Kode Token Presensi:</span>
                        <span id="display-qr-token" class="mt-1 font-mono text-2xl sm:text-3xl font-black tracking-widest text-emerald-400 bg-slate-900/90 px-4 py-1 rounded-xl border border-slate-700/80">
                            {{ $activeQr->token ?? 'SIROJU-8492' }}
                        </span>
                    </div>

                    <!-- Countdown Indicator -->
                    <div class="mt-3 flex items-center gap-2 text-xs text-slate-300">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                        <span>Kedaluwarsa otomatis dalam: <strong id="qr-countdown" class="text-amber-300 num-tabular font-mono text-sm">25 detik</strong></span>
                    </div>
                </div>
            </div>

            <!-- Controls: Regenerate & Switch Datang / Pulang -->
            <div class="pt-3 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="refreshQrToken('datang')"
                        class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs transition-colors">
                        Sesi Datang
                    </button>
                    <button type="button" onclick="refreshQrToken('pulang')"
                        class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs transition-colors">
                        Sesi Pulang
                    </button>
                </div>

                <button type="button" onclick="refreshQrToken()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-900 hover:bg-blue-800 text-white font-semibold text-xs shadow-xs transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Perbarui Token Baru</span>
                </button>
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
                                <option value="{{ $g->id }}">{{ $g->nama }} ({{ $g->no_pegawai ?: 'NIP' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Token QR Tertera *</label>
                        <div class="flex gap-2">
                            <input type="text" name="token" id="sim-token-input" required value="{{ $activeQr->token ?? '' }}"
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
                <p>2. Token kedaluwarsa otomatis dalam 20–30 detik sehingga mencegah screenshot dan titip absen.</p>
            </div>
        </div>
    </div>

    <!-- Attendance Log: Today's Recorded Presensi -->
    <div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs">
        <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700">Log Presensi Guru Hari Ini</h3>
                <p class="text-xs text-slate-400">Catatan realtime scan datang dan checkout pulang</p>
            </div>
            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-emerald-100 text-emerald-900 num-tabular">
                {{ $absensiHariIni->count() }} Guru Sudah Melakukan Absen
            </span>
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
                <tbody class="divide-y divide-slate-200/70">
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
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    Tepat Waktu
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
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

<script>
    let currentTipe = '{{ $activeQr->tipe ?? 'datang' }}';
    let countdownSec = 25;
    let timerInterval = null;

    function startTimer() {
        clearInterval(timerInterval);
        countdownSec = 25;
        const el = document.getElementById('qr-countdown');

        timerInterval = setInterval(() => {
            countdownSec--;
            if (countdownSec <= 0) {
                refreshQrToken();
            } else {
                if (el) el.textContent = countdownSec + ' detik';
            }
        }, 1000);
    }

    async function refreshQrToken(forcedTipe) {
        if (forcedTipe) {
            currentTipe = forcedTipe;
            document.getElementById('text-sesi-tipe').textContent = forcedTipe.toUpperCase();
        }

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
                    duration: 25
                })
            });

            const json = await resp.json();
            if (json.success) {
                const token = json.data.token;
                document.getElementById('display-qr-token').textContent = token;
                document.getElementById('sim-token-input').value = token;
                startTimer();
            }
        } catch (e) {
            console.error('Failed refreshing token:', e);
        }
    }

    function copyActiveToken() {
        const token = document.getElementById('display-qr-token').textContent.trim();
        navigator.clipboard.writeText(token).then(() => {
            alert('Kode token berhasil disalin: ' + token);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        startTimer();
    });
</script>
@endsection

