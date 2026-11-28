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
                    @if(! $isGuru)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-900 border border-blue-200/70">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-700"></span>
                            {{ $currentCabang['nama'] }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            @if(! $isGuru)
            <div class="flex items-center gap-2 shrink-0">
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
            </div>
            @endif
        </div>

        <!-- Metric KPI Strip -->
        <div class="mt-4 grid grid-cols-2 {{ $isGuru ? '' : 'lg:grid-cols-4' }} gap-3 sm:gap-4">
            <!-- Metric 1: Total Sesi Hadir -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Kehadiran</p>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-slate-900 num-tabular">{{ $totalHadir }}</span>
                    <span class="text-xs text-slate-500">sesi tercatat</span>
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Total akumulasi presensi</p>
            </div>

            @if(! $isGuru)
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
            @endif

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
        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
            @if($isGuru)
                @php
                    $selectedBulan = request('bulan');
                    $selectedTahun = request('tahun', now()->year);
                    $isBulanIni = (string) $selectedBulan === (string) now()->month && (string) $selectedTahun === (string) now()->year;
                    $isSemuaBulan = empty($selectedBulan);
                @endphp
                <form method="GET" action="{{ route('presensi.riwayat') }}" class="flex items-center gap-2 overflow-x-auto flex-nowrap pb-0.5 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                    <a href="{{ route('presensi.riwayat') }}"
                        class="shrink-0 whitespace-nowrap px-3.5 py-1.5 rounded-full text-xs font-semibold transition-colors {{ $isSemuaBulan ? 'bg-blue-900 text-white shadow-2xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Semua Bulan
                    </a>
                    <a href="{{ route('presensi.riwayat', ['bulan' => now()->month, 'tahun' => now()->year]) }}"
                        class="shrink-0 whitespace-nowrap px-3.5 py-1.5 rounded-full text-xs font-semibold transition-colors {{ $isBulanIni ? 'bg-blue-900 text-white shadow-2xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Bulan Ini
                    </a>

                    <select name="bulan" onchange="this.form.submit()"
                        class="shrink-0 py-1.5 px-3 text-xs font-semibold rounded-full bg-slate-100 border border-slate-200 text-slate-700 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="">Pilih Bulan...</option>
                        @for($m = 1; $m <= 12; $m++)
                            @php $monthName = \Carbon\Carbon::create(2026, $m, 1)->locale('id')->translatedFormat('F'); @endphp
                            <option value="{{ $m }}" {{ (string) $selectedBulan === (string) $m ? 'selected' : '' }}>{{ $monthName }}</option>
                        @endfor
                    </select>

                    <select name="tahun" onchange="this.form.submit()"
                        class="shrink-0 py-1.5 px-3 text-xs font-semibold rounded-full bg-slate-100 border border-slate-200 text-slate-700 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                        @for($y = now()->year; $y >= now()->year - 2; $y--)
                            <option value="{{ $y }}" {{ (string) $selectedTahun === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
            @else
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
                                @php $monthName = \Carbon\Carbon::create(2026, $m, 1)->locale('id')->translatedFormat('F'); @endphp
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
            @endif
        </div>
    </div>

    <!-- Attendance History Card List -->
    @php
        $groupedPresensi = $riwayatPresensi->getCollection()->groupBy(
            fn ($item) => $item->tanggal ? $item->tanggal->format('Y-m') : 'lainnya'
        );
    @endphp

    <div class="space-y-5">
        @forelse($groupedPresensi as $monthKey => $items)
            @php
                $firstDate = $items->first()?->tanggal;
                $monthLabel = $firstDate ? $firstDate->locale('id')->translatedFormat('F Y') : 'Lainnya';
                $validCount = $items->filter(fn ($i) => $i->validitas === 'valid')->count();
                $otherCount = $items->count() - $validCount;
            @endphp

            <div class="space-y-2.5">
                <div class="flex items-center justify-between px-1">
                    <h3 class="font-bold text-sm text-slate-900">{{ $monthLabel }}</h3>
                    <div class="text-xs font-semibold">
                        <span class="text-emerald-700">{{ $validCount }} valid</span>
                        <span class="text-slate-400 mx-1">·</span>
                        <span class="text-slate-500">{{ $otherCount }} lainnya</span>
                    </div>
                </div>

                <div class="space-y-2.5">
                    @foreach($items as $abs)
                        @php
                            $durasi = null;
                            if ($abs->jam_datang && $abs->jam_pulang) {
                                $durasi = (int) $abs->jam_datang->diffInMinutes($abs->jam_pulang);
                            }
                            $jamDurasi = $durasi !== null ? intdiv($durasi, 60) : 0;
                            $menitDurasi = $durasi !== null ? ($durasi % 60) : 0;
                        @endphp

                        <div class="bg-white border border-slate-200/90 rounded-2xl p-3.5 sm:p-4 shadow-2xs flex items-center justify-between gap-3 sm:gap-4 hover:border-blue-200 transition-colors">
                            <!-- Left: Date Badge -->
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-blue-50/80 border border-blue-200/70 flex flex-col items-center justify-center shrink-0">
                                <span class="text-base sm:text-lg font-bold text-blue-950 leading-none num-tabular">
                                    {{ $abs->tanggal ? $abs->tanggal->format('d') : '--' }}
                                </span>
                                <span class="text-[11px] font-semibold text-blue-800 mt-1">
                                    {{ $abs->tanggal ? $abs->tanggal->locale('id')->isoFormat('ddd') : '-' }}
                                </span>
                            </div>

                            <!-- Center: Timeline & Duration -->
                            <div class="flex-1 min-w-0">
                                @if(! $isGuru)
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <span class="font-bold text-xs text-slate-900 truncate">{{ $abs->guru->nama ?? 'Guru' }}</span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-semibold shrink-0">{{ $abs->cabang->nama ?? 'Cabang' }}</span>
                                    </div>
                                @endif

                                <div class="flex items-center gap-2.5">
                                    <span class="text-sm font-bold text-slate-900 font-mono num-tabular shrink-0">
                                        {{ $abs->jam_datang ? $abs->jam_datang->format('H:i') : '--:--' }}
                                    </span>
                                    <div class="flex-1 h-1 rounded-full {{ $abs->jam_pulang ? 'bg-blue-900' : ($abs->jam_datang ? 'bg-amber-400' : 'bg-slate-200') }}"></div>
                                    <span class="text-sm font-bold text-slate-900 font-mono num-tabular shrink-0">
                                        {{ $abs->jam_pulang ? $abs->jam_pulang->format('H:i') : '--:--' }}
                                    </span>
                                </div>

                                <div class="mt-1.5 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                    @if($durasi !== null)
                                        <span class="font-medium text-slate-600 num-tabular">
                                            {{ $jamDurasi > 0 ? $jamDurasi . ' jam ' : '' }}{{ $menitDurasi }} menit
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/80 num-tabular">
                                            <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>{{ $jamDurasi > 0 ? $jamDurasi . 'j ' : '' }}{{ $menitDurasi }}m</span>
                                        </span>
                                    @elseif($abs->jam_datang)
                                        <span class="text-amber-700 font-medium">Sedang berlangsung</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif

                                    @if($isGuru && $abs->cabang)
                                        <span class="text-[11px] text-slate-400">· {{ $abs->cabang->nama }}</span>
                                    @endif
                                </div>

                                @if($abs->is_manual_override)
                                    <div class="text-[10.5px] text-blue-800 font-medium mt-1">
                                        Manual: {{ $abs->alasan_validasi ?: 'Diubah Admin' }}
                                    </div>
                                @elseif($abs->validitas === 'tidak_valid' && $abs->keterangan_validitas)
                                    <div class="text-[10.5px] text-slate-500 font-medium mt-1">
                                        {{ $abs->keterangan_validitas }}
                                    </div>
                                @endif
                            </div>

                            <!-- Right: Status Pill & Optional Admin Action -->
                            <div class="flex flex-col items-end gap-1.5 shrink-0">
                                @if($abs->validitas === 'valid')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200" title="{{ $abs->keterangan_validitas }}">
                                        VALID
                                    </span>
                                @elseif($abs->validitas === 'sedang_mengajar')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200" title="{{ $abs->keterangan_validitas }}">
                                        AKTIF
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200" title="{{ $abs->keterangan_validitas }}">
                                        TIDAK VALID
                                    </span>
                                @endif

                                @if(! $isGuru)
                                    <button type="button"
                                        onclick="openValidasiModal('{{ $abs->id }}', '{{ addslashes($abs->guru->nama ?? 'Guru') }}', '{{ $abs->validitas }}', '{{ addslashes($abs->alasan_validasi ?? '') }}')"
                                        class="p-1 rounded-lg text-slate-400 hover:text-blue-900 hover:bg-slate-100 transition-colors"
                                        title="Ubah Status Validasi">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white border border-slate-200/90 rounded-2xl py-12 px-4 text-center text-slate-400 text-sm shadow-2xs">
                <div class="w-10 h-10 mx-auto rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 mb-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="font-semibold text-slate-700">Belum Ada Catatan Presensi</p>
                <p class="text-xs text-slate-400 mt-0.5">Catatan kehadiran akan muncul otomatis saat Anda melakukan scan QR presensi.</p>
            </div>
        @endforelse

        @if($riwayatPresensi->hasPages())
        <div class="bg-white border border-slate-200/90 rounded-2xl px-5 py-3 shadow-2xs">
            {{ $riwayatPresensi->links() }}
        </div>
        @endif
    </div>

    @if(! $isGuru)
    <!-- Modal Validasi Manual Override -->
    <div id="modal-validasi" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Sesuaikan Status Validasi Presensi</h3>
                <button type="button" onclick="closeValidasiModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="form-validasi" method="POST" action="" class="mt-4 space-y-4">
                @csrf
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-xs">
                    <p class="text-slate-500">Guru: <span id="validasi-guru-nama" class="font-bold text-slate-800"></span></p>
                    <p class="text-[11px] text-slate-400 mt-1">Ubah status menjadi <strong>Valid</strong> jika terjadi kendala sistem atau jaringan pada jam mengajar (18:00 - 20:00).</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Validasi *</label>
                    <select name="status_validasi" id="validasi-status-select" required
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="valid">Valid (Disetujui Hadir Penuh)</option>
                        <option value="tidak_valid">Tidak Valid</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan / Catatan Penyesuaian</label>
                    <input type="text" name="alasan_validasi" id="validasi-alasan-input"
                        placeholder="Contoh: Gangguan server/jaringan saat checkout 20:00"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeValidasiModal()"
                        class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 text-white text-xs font-bold shadow-xs">
                        Simpan Validasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function openValidasiModal(absensiId, guruNama, currentValiditas, alasan) {
        const modal = document.getElementById('modal-validasi');
        const form = document.getElementById('form-validasi');
        const nameEl = document.getElementById('validasi-guru-nama');
        const selectEl = document.getElementById('validasi-status-select');
        const alasanEl = document.getElementById('validasi-alasan-input');

        form.action = '/presensi/' + absensiId + '/validasi';
        nameEl.textContent = guruNama;
        selectEl.value = (currentValiditas === 'valid') ? 'valid' : 'tidak_valid';
        alasanEl.value = alasan || '';

        modal.classList.remove('hidden');
    }
    function closeValidasiModal() {
        document.getElementById('modal-validasi').classList.add('hidden');
    }
    </script>
    @endpush
    @endif

</div>
@endsection
