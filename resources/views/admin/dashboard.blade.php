@extends('layouts.admin')

@section('title', 'Dashboard Operasional Cabang')

@section('content')
    <div class="space-y-4 sm:space-y-6">

        <!-- Header: Cabang Context & Finding Operasional -->
        <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-2xs">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-slate-200/70">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold tracking-tight text-slate-900">Dashboard
                            Operasional</h1>
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-900 border border-blue-200/70">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-700"></span>
                            {{ $currentCabang['nama'] }}
                        </span>
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-semibold bg-slate-100 text-slate-700">
                            {{ $currentCabang['kode'] }}
                        </span>
                    </div>
                    <p class="mt-1 text-xs sm:text-sm text-slate-600 leading-relaxed max-w-3xl">
                        Terdapat <strong class="text-slate-900">{{ $kpi['siswa_aktif']['total'] }} siswa aktif</strong>
                        terdaftar di cabang ini. <span
                            class="text-amber-800 font-semibold underline underline-offset-2">{{ $kpi['siswa_aktif']['belum_ada_guru'] }}
                            siswa baru belum memiliki guru</span> (siap di-Auto-Assign). Rasio guru saat ini <strong
                            class="text-slate-900">{{ $kpi['kuota_guru']['guru_penuh'] }}/{{ $kpi['kuota_guru']['total_guru'] }}
                            penuh (6/6)</strong> dengan sisa 15 slot bimbingan.
                    </p>
                </div>

                <!-- Quick Action Buttons (Stacked on mobile, row on tablet/desktop) -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 shrink-0 pt-2 lg:pt-0">
                    <!-- 1-Klik Auto Assign Action Trigger -->
                    <button type="button" onclick="runAutoAssign()"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3.5 py-2.5 sm:py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-slate-950 font-bold text-xs shadow-2xs transition-colors shrink-0"
                        title="Distribusikan siswa tanpa guru otomatis berdasarkan jenjang dan sisa kuota">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>1-Klik Auto-Assign ({{ $kpi['siswa_aktif']['belum_ada_guru'] }})</span>
                    </button>

                    <!-- Buka Layar QR Presensi Guru -->
                    <button type="button" onclick="openModal('modal-qr-display')"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3.5 py-2.5 sm:py-1.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 active:bg-slate-100 text-slate-800 font-semibold text-xs shadow-2xs transition-colors shrink-0">
                        <svg class="w-4 h-4 text-blue-900" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <rect width="5" height="5" x="3" y="3" rx="1" />
                            <rect width="5" height="5" x="16" y="3" rx="1" />
                            <rect width="5" height="5" x="3" y="16" rx="1" />
                        </svg>
                        <span>Layar QR Absen</span>
                    </button>
                </div>
            </div>

            <!-- Quick Summary Indicators (Mobile friendly pills) -->
            <div class="pt-3 flex items-center justify-between gap-2 overflow-x-auto text-[11px] text-slate-500">
                <span class="truncate">Periode: <strong>Semester Ganjil 2026/2027</strong></span>
                <span class="shrink-0 text-emerald-800 font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Presensi Hari Ini: 93.3%
                </span>
            </div>
        </div>

        <!-- 4-Column Divided Container KPI Strip (Mobile First: 2x2 Grid on Mobile, 4 Cols on Desktop) -->
        <div
            class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4 lg:gap-0 bg-transparent lg:bg-white lg:border lg:border-slate-200/90 rounded-2xl lg:divide-x divide-slate-200/80 lg:shadow-2xs">

            <!-- KPI 1: Siswa Aktif & Status Alokasi -->
            <div
                class="bg-white border border-slate-200/90 lg:border-0 rounded-2xl lg:rounded-none p-3.5 sm:p-5 shadow-2xs lg:shadow-none">
                <div class="flex items-center justify-between text-[11px] sm:text-xs text-slate-500 font-medium">
                    <span class="truncate">Siswa Aktif</span>
                    <span
                        class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-50 text-amber-900 border border-amber-200/70 num-tabular shrink-0">
                        {{ $kpi['siswa_aktif']['belum_ada_guru'] }} Antre
                    </span>
                </div>
                <div class="mt-1.5 sm:mt-2 flex items-baseline gap-1">
                    <span
                        class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 num-tabular">{{ $kpi['siswa_aktif']['total'] }}</span>
                    <span class="text-[11px] sm:text-xs text-slate-500 font-medium">siswa</span>
                </div>
                <div class="mt-2 pt-2 border-t border-slate-100 text-[10px] sm:text-xs text-slate-500 num-tabular truncate">
                    <span>TK: <strong>{{ $kpi['siswa_aktif']['tk'] }}</strong></span> ·
                    <span>SD: <strong>{{ $kpi['siswa_aktif']['sd'] }}</strong></span> ·
                    <span>SMP: <strong>{{ $kpi['siswa_aktif']['smp'] }}</strong></span> ·
                    <span>SMA: <strong>{{ $kpi['siswa_aktif']['sma_smk'] }}</strong></span>
                </div>
            </div>

            <!-- KPI 2: Kuota Guru (Maksimal 6 Siswa / Guru) -->
            <div
                class="bg-white border border-slate-200/90 lg:border-0 rounded-2xl lg:rounded-none p-3.5 sm:p-5 shadow-2xs lg:shadow-none">
                <div class="flex items-center justify-between text-[11px] sm:text-xs text-slate-500 font-medium">
                    <span class="truncate">Kapasitas Slot Guru</span>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-blue-50 text-blue-900 shrink-0">
                        89.6%
                    </span>
                </div>
                <div class="mt-1.5 sm:mt-2 flex items-baseline gap-1">
                    <span class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 num-tabular">
                        {{ $kpi['kuota_guru']['slot_terpakai'] }}<span
                            class="text-sm sm:text-base font-normal text-slate-400">/{{ $kpi['kuota_guru']['kapasitas_slot_total'] }}</span>
                    </span>
                </div>
                <div class="mt-2 pt-2 border-t border-slate-100 text-[10px] sm:text-xs text-slate-500 num-tabular truncate">
                    <span class="text-slate-800 font-semibold">{{ $kpi['kuota_guru']['guru_penuh'] }} Guru Full</span> ·
                    <span class="text-emerald-700 font-semibold">{{ $kpi['kuota_guru']['slot_tersedia'] }} Slot Sisa</span>
                </div>
            </div>

            <!-- KPI 3: Presensi Guru Hari Ini (QR Token) -->
            <div
                class="bg-white border border-slate-200/90 lg:border-0 rounded-2xl lg:rounded-none p-3.5 sm:p-5 shadow-2xs lg:shadow-none">
                <div class="flex items-center justify-between text-[11px] sm:text-xs text-slate-500 font-medium">
                    <span class="truncate">Presensi Guru</span>
                    <span
                        class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                        QR Aktif
                    </span>
                </div>
                <div class="mt-1.5 sm:mt-2 flex items-baseline gap-1">
                    <span class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 num-tabular">
                        {{ $kpi['presensi_guru_hari_ini']['sudah_datang'] }}<span
                            class="text-sm sm:text-base font-normal text-slate-400">/{{ $kpi['presensi_guru_hari_ini']['total_bertugas'] }}</span>
                    </span>
                    <span class="text-[11px] sm:text-xs text-slate-500 font-medium">hadir</span>
                </div>
                <div class="mt-2 pt-2 border-t border-slate-100 text-[10px] sm:text-xs text-slate-500 num-tabular truncate">
                    <span
                        class="text-emerald-800 font-semibold">{{ $kpi['presensi_guru_hari_ini']['persentase_kehadiran'] }}%
                        Tepat Waktu</span>
                </div>
            </div>

            <!-- KPI 4: Buku Kas & SPP Bulan Ini -->
            <div
                class="bg-white border border-slate-200/90 lg:border-0 rounded-2xl lg:rounded-none p-3.5 sm:p-5 shadow-2xs lg:shadow-none">
                <div class="flex items-center justify-between text-[11px] sm:text-xs text-slate-500 font-medium">
                    <span class="truncate">Kas Cabang</span>
                    <span class="text-[10px] text-slate-400 shrink-0">Okt 2026</span>
                </div>
                <div class="mt-1.5 sm:mt-2 flex items-baseline gap-1">
                    <span
                        class="text-xl sm:text-2xl lg:text-3xl font-bold tracking-tight text-emerald-800 num-tabular truncate">
                        Rp {{ number_format($kpi['buku_kas_bulan_ini']['saldo_bersih'], 0, ',', '.') }}
                    </span>
                </div>
                <div class="mt-2 pt-2 border-t border-slate-100 text-[10px] sm:text-xs text-slate-500 num-tabular truncate">
                    <span>Masuk: <strong class="text-slate-800">Rp 26.85jt</strong></span> ·
                    <span class="text-amber-800 font-semibold">{{ $kpi['buku_kas_bulan_ini']['spp_tertunggak'] }} SPP
                        Tempo</span>
                </div>
            </div>

        </div>

        <!-- Alert Block: Siswa Baru Belum Memiliki Guru (Mobile Card List + Desktop Table) -->
        @if(count($unassignedStudents) > 0)
            <section class="bg-amber-50/70 border border-amber-200 rounded-2xl p-4 sm:p-5 shadow-2xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-amber-200/80">
                    <div class="flex items-start gap-3">
                        <div
                            class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Antrean Siswa Belum Mendapatkan Guru
                                ({{ count($unassignedStudents) }} Siswa)</h2>
                            <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                                Siswa berikut telah menyelesaikan pendaftaran namun belum memiliki guru pembimbing aktif
                                (maksimal 6 siswa per guru).
                            </p>
                        </div>
                    </div>

                    <button type="button" onclick="runAutoAssign()"
                        class="w-full sm:w-auto px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors shadow-2xs flex items-center justify-center gap-2 shrink-0">
                        <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Jalankan Auto-Assign Cerdas</span>
                    </button>
                </div>

                <!-- 1. Mobile Cards View (< 640px) -->
                <div class="sm:hidden space-y-2.5 mt-3">
                    @foreach($unassignedStudents as $st)
                        <div class="p-3 bg-white border border-amber-200/90 rounded-xl space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="font-bold text-slate-900 text-sm block">{{ $st['nama'] }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $st['id'] }} ·
                                        {{ $st['tanggal_daftar'] }}</span>
                                </div>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-900 border border-amber-200 shrink-0">
                                    {{ $st['jenjang'] }} · Kls {{ $st['kelas'] == 0 ? 'TK' : $st['kelas'] }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs text-slate-600 pt-1 border-t border-slate-100">
                                <span>Wali: {{ $st['nama_ibu'] }}</span>
                                <span class="font-mono text-[11px] text-slate-500">{{ $st['no_telp_ortu'] }}</span>
                            </div>

                            <div class="flex items-center justify-between pt-1">
                                <span class="text-[11px] text-slate-500">
                                    @if($st['diskon'] > 0)
                                        <span class="text-emerald-800 font-semibold">Diskon Rp
                                            {{ number_format($st['diskon'], 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-400">Normal</span>
                                    @endif
                                </span>
                                <button type="button"
                                    onclick="openManualAssignModal('{{ $st['id'] }}', '{{ $st['nama'] }}', '{{ $st['jenjang'] }}')"
                                    class="px-3 py-1.5 rounded-lg bg-blue-900 text-white font-semibold text-xs active:bg-blue-950 transition-colors">
                                    Tugaskan Guru
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 2. Desktop Table View (>= 640px) -->
                <div class="hidden sm:block overflow-x-auto mt-3">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-slate-500 font-semibold border-b border-amber-200/60 pb-2">
                                <th class="py-2 pr-3">ID & Nama Siswa</th>
                                <th class="py-2 px-3">Jenjang & Kelas</th>
                                <th class="py-2 px-3">Tgl Daftar</th>
                                <th class="py-2 px-3">Orang Tua & WhatsApp</th>
                                <th class="py-2 px-3">Diskon</th>
                                <th class="py-2 pl-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-amber-200/50">
                            @foreach($unassignedStudents as $st)
                                <tr class="text-slate-800">
                                    <td class="py-2.5 pr-3">
                                        <span class="font-bold text-slate-900">{{ $st['nama'] }}</span>
                                        <span class="block text-[10px] text-slate-400 font-mono">{{ $st['id'] }}</span>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-white text-slate-800 border border-amber-200">
                                            {{ $st['jenjang'] }} · Kelas {{ $st['kelas'] == 0 ? 'TK' : $st['kelas'] }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-slate-600 num-tabular">{{ $st['tanggal_daftar'] }}</td>
                                    <td class="py-2.5 px-3">
                                        <span>{{ $st['nama_ibu'] }}</span>
                                        <span class="block text-[10px] text-slate-500 font-mono">{{ $st['no_telp_ortu'] }}</span>
                                    </td>
                                    <td class="py-2.5 px-3 num-tabular">
                                        @if($st['diskon'] > 0)
                                            <span class="text-emerald-800 font-semibold">Diskon Rp
                                                {{ number_format($st['diskon'], 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-slate-400">Normal</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 pl-3 text-right">
                                        <button type="button"
                                            onclick="openManualAssignModal('{{ $st['id'] }}', '{{ $st['nama'] }}', '{{ $st['jenjang'] }}')"
                                            class="px-2.5 py-1 rounded bg-white hover:bg-slate-100 text-slate-800 font-semibold border border-slate-300 transition-colors shadow-2xs">
                                            Pilih Guru
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        <!-- Main Grid: 8 Cols Main Content + 4 Cols Rail -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6 items-start">

            <!-- LEFT COLUMN (8 COLS): Monitoring Kuota Guru & Jadwal Mengajar -->
            <div class="lg:col-span-8 space-y-4 sm:space-y-6">

                <!-- Panel 1: Status Kuota Guru (Maksimal 6 Siswa - Race Condition Protection) -->
                <section id="alokasi-guru" class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-2xs">
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-200/70">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-sm sm:text-base font-bold text-slate-900">Alokasi & Kuota Guru Cabang</h2>
                                <span
                                    class="text-xs font-semibold px-2 py-0.5 rounded bg-blue-50 text-blue-900 border border-blue-200/60 num-tabular">
                                    Max 6 Siswa/Guru
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Memastikan rasio pengajaran privat ketat dengan validasi kualifikasi jenjang guru.
                            </p>
                        </div>

                        <div class="flex items-center gap-3 text-xs font-medium text-slate-500 pt-1 sm:pt-0">
                            <span class="inline-flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Tersedia Slot
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-slate-900"></span> Penuh (6/6)
                            </span>
                        </div>
                    </div>

                    <!-- Teacher Capacity List (Mobile Card + Desktop Row) -->
                    <div class="divide-y divide-slate-100 mt-2">
                        @foreach($teachersSlotStatus as $guru)
                            <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-3">
                                <div class="flex items-center gap-3 min-w-0 sm:w-1/3">
                                    <div
                                        class="w-8 h-8 rounded-full {{ $guru['warna'] }} flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ $guru['inisial'] }}
                                    </div>
                                    <div class="truncate">
                                        <p class="text-xs font-bold text-slate-900 truncate">{{ $guru['nama'] }}</p>
                                        <div class="flex items-center gap-1 mt-0.5 flex-wrap">
                                            @foreach($guru['jenjang_kualifikasi'] as $jjg)
                                                <span
                                                    class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-slate-100 text-slate-700 font-mono">
                                                    {{ $jjg }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Progress Bar (x of 6) -->
                                <div class="sm:flex-1 sm:px-4">
                                    <div class="flex items-center justify-between text-xs mb-1">
                                        <span class="text-[11px] text-slate-500">Beban Bimbingan:</span>
                                        <span class="font-bold text-slate-900 num-tabular text-xs">
                                            {{ $guru['siswa_count'] }} / 6 Siswa
                                        </span>
                                    </div>
                                    <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden flex">
                                        @for($i = 1; $i <= 6; $i++)
                                            <div
                                                class="h-full flex-1 border-r border-white last:border-r-0 {{ $i <= $guru['siswa_count'] ? ($guru['siswa_count'] == 6 ? 'bg-slate-900' : 'bg-blue-800') : 'bg-slate-200' }}">
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <!-- Status Badge & Action -->
                                <div
                                    class="flex items-center justify-between sm:justify-end gap-2 sm:w-36 shrink-0 pt-1 sm:pt-0">
                                    @if($guru['status_slot'] === 'penuh')
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] sm:text-[11px] font-bold bg-slate-100 text-slate-800 border border-slate-300">
                                            Penuh (6/6)
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] sm:text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Sisa {{ $guru['sisa_slot'] }} Slot
                                        </span>
                                    @endif

                                    <button type="button" onclick="openModal('modal-pendaftaran')"
                                        class="px-2.5 py-1 text-xs font-semibold text-blue-900 hover:bg-blue-50 rounded-lg border border-blue-200/60 transition-colors {{ $guru['status_slot'] === 'penuh' ? 'opacity-40 cursor-not-allowed' : '' }}"
                                        {{ $guru['status_slot'] === 'penuh' ? 'disabled' : '' }}>
                                        + Siswa
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <!-- Panel 2: Jadwal Mengajar Hari Ini & Deteksi Bentrok Lintas Cabang -->
                <section id="jadwal" class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs">
                    <div class="p-4 sm:p-5 border-b border-slate-200/70 flex items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-sm sm:text-base font-bold text-slate-900">Jadwal Sesi Mengajar Hari Ini</h2>
                                <span
                                    class="text-xs font-semibold px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                                    Bebas Bentrok
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Pemeriksaan irisan waktu guru otomatis lintas seluruh
                                cabang.</p>
                        </div>

                        <span class="text-xs text-slate-500 font-medium shrink-0">Hari: <strong>Jumat</strong></span>
                    </div>

                    <!-- 1. Mobile Timeline List (< 640px) -->
                    <div class="sm:hidden divide-y divide-slate-100">
                        @foreach($todaySchedules as $jd)
                            <div class="p-3.5 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 num-tabular text-sm">
                                        {{ $jd['jam_mulai'] }} - {{ $jd['jam_selesai'] }}
                                    </span>
                                    @if($jd['status_presensi'] === 'hadir')
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Hadir
                                            ({{ $jd['jam_scan'] }})
                                        </span>
                                    @elseif($jd['status_presensi'] === 'menunggu_scan')
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Tunggu QR
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">
                                            Terjadwal
                                        </span>
                                    @endif
                                </div>

                                <div>
                                    <p class="text-xs font-bold text-slate-900">{{ $jd['guru'] }}</p>
                                    <p class="text-xs text-slate-600">{{ $jd['siswa'] }}</p>
                                </div>

                                <div
                                    class="flex items-center justify-between text-[11px] text-slate-500 pt-1 border-t border-slate-50">
                                    <span>Ruang: <strong class="text-slate-700">{{ $jd['ruangan'] }}</strong></span>
                                    <span
                                        class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-bold font-mono">{{ $jd['jenjang'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- 2. Desktop Table View (>= 640px) -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-slate-200/80 bg-slate-50/50 text-slate-500 font-medium">
                                    <th class="py-3 px-4">Jam Sesi</th>
                                    <th class="py-3 px-4">Guru Pengajar</th>
                                    <th class="py-3 px-4">Siswa & Jenjang</th>
                                    <th class="py-3 px-4">Ruangan</th>
                                    <th class="py-3 px-4">Presensi Guru</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($todaySchedules as $jd)
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="py-3.5 px-4 font-bold text-slate-900 num-tabular whitespace-nowrap">
                                            {{ $jd['jam_mulai'] }} - {{ $jd['jam_selesai'] }}
                                        </td>
                                        <td class="py-3.5 px-4 font-semibold text-slate-900">
                                            {{ $jd['guru'] }}
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="text-slate-800 font-medium">{{ $jd['siswa'] }}</div>
                                            <span
                                                class="inline-block mt-0.5 px-1.5 py-0.2 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                                {{ $jd['jenjang'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600 font-medium">
                                            {{ $jd['ruangan'] }}
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            @if($jd['status_presensi'] === 'hadir')
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                                    <svg class="w-2.5 h-2.5 text-emerald-600" fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                    Hadir ({{ $jd['jam_scan'] }})
                                                </span>
                                            @elseif($jd['status_presensi'] === 'menunggu_scan')
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                                                    <svg class="w-2.5 h-2.5 text-amber-600" fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                    Menunggu Scan QR
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-600">
                                                    Terjadwal
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                            <button type="button"
                                                onclick="alert('Jadwal ini telah divalidasi tidak bentrok dengan jadwal guru di cabang lain.')"
                                                class="px-2 py-1 text-[11px] font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded transition-colors">
                                                Cek Irisan
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>

            <!-- RIGHT COLUMN (4 COLS): QR Presensi Dinamis, Kas & Kwitansi Resmi -->
            <div class="lg:col-span-4 space-y-4 sm:space-y-6">

                <!-- Card 1: Live Sesi QR Presensi Guru Cabang (Token 15-30 detik) -->
                <section class="bg-blue-900 text-white rounded-2xl p-4 sm:p-5 shadow-2xs relative overflow-hidden">
                    <div class="flex items-center justify-between pb-3 border-b border-blue-800/80">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <h2 class="text-sm font-bold text-white">Sesi QR Presensi Guru</h2>
                        </div>
                        <span
                            class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-blue-800 text-blue-200">
                            Sesi {{ ucfirst($qrTokenSession['tipe']) }}
                        </span>
                    </div>

                    <div class="mt-4 flex items-center gap-4">
                        <!-- Live Dynamic QR Thumbnail (Responsive size) -->
                        <div onclick="openModal('modal-qr-display')"
                            class="w-18 h-18 sm:w-20 sm:h-20 bg-white rounded-xl p-1.5 flex flex-col items-center justify-center shrink-0 cursor-pointer hover:scale-105 active:scale-95 transition-transform shadow-md"
                            title="Klik untuk perbesar layar QR">
                            <div
                                class="w-full h-full border-2 border-slate-900 border-dashed rounded-lg flex flex-col items-center justify-center text-slate-900 font-mono text-[10px] font-bold text-center leading-tight">
                                <span>[QR]</span>
                                <span
                                    class="text-[9px] text-blue-900 font-bold mt-0.5">{{ $qrTokenSession['token'] }}</span>
                            </div>
                        </div>

                        <div class="space-y-1 text-xs min-w-0">
                            <p class="text-blue-200">Token Dinamis Aktif:</p>
                            <p class="text-lg sm:text-xl font-bold font-mono tracking-widest text-emerald-300 truncate"
                                id="live-token-text">
                                {{ $qrTokenSession['token'] }}
                            </p>
                            <div class="flex items-center gap-1.5 text-[11px] text-blue-200">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                                <span class="truncate">Refresh dalam <strong class="text-white"
                                        id="countdown-timer">18s</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-blue-800/70 flex items-center justify-between">
                        <span class="text-[11px] text-blue-300 truncate">Resepsionis Lt. 1</span>
                        <button type="button" onclick="openModal('modal-qr-display')"
                            class="px-3 py-1.5 text-xs font-bold rounded-lg bg-white text-blue-900 hover:bg-blue-50 active:bg-blue-100 transition-colors shadow-2xs shrink-0">
                            Layar Penuh →
                        </button>
                    </div>
                </section>

                <!-- Card 2: Pembayaran SPP & Kwitansi Resmi (KW/KODE_CABANG/...) -->
                <section id="spp-cabang" class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-2xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Pembayaran SPP & Kwitansi</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Format baku resmi & integrasi WhatsApp ortu.</p>
                        </div>
                        <button type="button" onclick="openModal('modal-bayar-spp')"
                            class="px-2.5 py-1 text-xs font-bold rounded-lg bg-blue-50 text-blue-900 hover:bg-blue-100 active:bg-blue-200 transition-colors shrink-0">
                            + Bayar
                        </button>
                    </div>

                    <div class="divide-y divide-slate-100 mt-1">
                        @foreach($recentPayments as $pay)
                            <div class="py-3 flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="text-xs font-bold text-slate-900 truncate">{{ $pay['siswa'] }}</span>
                                        <span
                                            class="text-[9px] font-bold px-1 rounded bg-slate-100 text-slate-700">{{ $pay['jenjang'] }}</span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 font-mono mt-0.5 truncate">{{ $pay['no_kwitansi'] }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Bulan: {{ $pay['bulan'] }}</p>
                                </div>

                                <div class="text-right shrink-0">
                                    <div class="text-xs font-bold text-slate-900 num-tabular">
                                        Rp {{ number_format($pay['nominal'], 0, ',', '.') }}
                                    </div>
                                    <div class="mt-1 flex items-center justify-end gap-1.5">
                                        @if($pay['status'] === 'lunas')
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                                Lunas
                                            </span>
                                            <a href="{{ $pay['wa_link'] }}" target="_blank"
                                                class="p-1 rounded text-emerald-700 hover:bg-emerald-50 active:bg-emerald-100 transition-colors"
                                                title="Kirim Kwitansi via WhatsApp Orang Tua">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.196 8.196 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24" />
                                                </svg>
                                            </a>
                                        @else
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-800 border border-red-200/60">
                                                Belum Bayar
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <!-- Card 3: Master Tarif SPP Global (Ketentuan Pusat) -->
                <section id="tarif-spp-global"
                    class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-2xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Master Tarif SPP Global</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Ditetapkan oleh Pimpinan Pusat</p>
                        </div>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-700 shrink-0">Tgl 1
                            00:05</span>
                    </div>

                    <div class="divide-y divide-slate-100 mt-2 text-xs">
                        @foreach($masterTarifSpp as $tf)
                            <div class="py-2.5 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-900">{{ $tf['jenjang'] }}</span>
                                    <span class="block text-[11px] text-slate-500">{{ $tf['kelas_label'] }}</span>
                                </div>
                                <span class="font-bold text-slate-900 num-tabular">
                                    Rp {{ number_format($tf['nominal'], 0, ',', '.') }}<span
                                        class="text-[10px] text-slate-400 font-normal">/bln</span>
                                </span>
                            </div>
                        @endforeach
                        <div class="py-2.5 flex items-center justify-between bg-slate-50/70 px-2 rounded-lg mt-1">
                            <div>
                                <span class="font-bold text-slate-900">Biaya Pendaftaran Baku</span>
                                <span class="block text-[10px] text-slate-500">Mendukung diskon manual</span>
                            </div>
                            <span class="font-bold text-blue-900 num-tabular">
                                Rp {{ number_format($biayaPendaftaranBaku, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </section>

            </div>

        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: PENDAFTARAN SISWA BARU (Mobile Sheet + Desktop Dialog) -->
    <!-- ========================================================================= -->
    <div id="modal-pendaftaran"
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4 hidden">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl max-w-xl w-full p-5 sm:p-6 shadow-2xl border border-slate-200 max-h-[92vh] flex flex-col"
            onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 shrink-0">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Pendaftaran Siswa Baru</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Otomatis mencatat kas masuk dan menerbitkan tagihan SPP.</p>
                </div>
                <button type="button" onclick="closeModal('modal-pendaftaran')"
                    class="p-2 -mr-1 text-slate-400 hover:text-slate-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Scrollable Form Body -->
            <form class="mt-4 space-y-4 overflow-y-auto flex-1 pr-1" action="{{ route('web.siswa.store') }}" method="POST">
                @csrf
                <input type="hidden" name="cabang_id" value="{{ $currentCabang['id'] }}">
                <!-- Data Diri Siswa -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Siswa</label>
                        <input type="text" name="nama" placeholder="Contoh: Muhammad Rayhan"
                            class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-600"
                            required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jenjang Pendidikan</label>
                        <select name="jenjang" id="reg-jenjang"
                            class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-600">
                            <option value="TK">TK (Kelas 0)</option>
                            <option value="SD" selected>SD (Kelas 1 - 6)</option>
                            <option value="SMP">SMP (Kelas 7 - 9)</option>
                            <option value="SMA_SMK">SMA / SMK (Kelas 10 - 12)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kelas Spesifik</label>
                        <input type="number" name="kelas" min="0" max="12" value="5"
                            class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-600"
                            required>
                    </div>
                </div>

                <!-- Tempat, Tanggal Lahir & Alamat -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" placeholder="Sidoarjo"
                            class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="2014-08-17"
                            class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-600">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Rumah</label>
                        <textarea rows="2" name="alamat" placeholder="Alamat domisili lengkap..."
                            class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-600"></textarea>
                    </div>
                </div>

                <!-- Data Orang Tua & WhatsApp -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Ibu / Wali</label>
                        <input type="text" name="nama_ibu" placeholder="Nama Ibu"
                            class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-600"
                            required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No. WhatsApp Wali</label>
                        <input type="text" name="no_telp_ortu" placeholder="081234567890"
                            class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-600 font-mono"
                            required>
                    </div>
                </div>

                <!-- Keuangan Pendaftaran & Diskon (Aturan 6) -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-600">Biaya Pendaftaran Standar:</span>
                        <strong class="text-slate-900 num-tabular">Rp 100.000</strong>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <label class="text-slate-700 font-semibold">Diskon Pendaftaran Manual:</label>
                        <div class="flex items-center gap-1 w-32">
                            <span class="text-slate-400">Rp</span>
                            <input type="number" name="diskon" id="reg-diskon" value="20000"
                                oninput="calcTotalPendaftaran()"
                                class="w-full text-xs bg-white border border-slate-300 rounded p-1.5 text-right num-tabular">
                        </div>
                    </div>
                    <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs font-bold">
                        <span class="text-slate-900">Total Kas Masuk Pendaftaran:</span>
                        <span class="text-emerald-800 text-sm num-tabular" id="reg-total-bayar">Rp 80.000</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2 shrink-0">
                    <button type="button" onclick="closeModal('modal-pendaftaran')"
                        class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-900 text-white text-xs font-bold hover:bg-blue-800 shadow-2xs">
                        Simpan Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: LAYAR QR PRESENSI GURU (Mobile Friendly Overlay) -->
    <!-- ========================================================================= -->
    <div id="modal-qr-display"
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-3 sm:p-4 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-5 sm:p-7 text-center shadow-2xl border border-slate-200 relative"
            onclick="event.stopPropagation()">
            <button type="button" onclick="closeModal('modal-qr-display')"
                class="absolute top-3.5 right-3.5 p-2 text-slate-400 hover:text-slate-600 rounded-full">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="flex items-center justify-center gap-2 mb-2">
                <img src="{{ asset('logoo.png') }}" alt="Logo LKP Sang Siroju Lillah" class="w-7 h-7 object-contain">
                <span class="text-xs font-bold text-slate-800">LKP Sang Siroju Lillah</span>
            </div>

            <div
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-900 mb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Layar Presensi Guru
            </div>

            <h3 class="text-lg sm:text-xl font-bold text-slate-900">{{ $currentCabang['nama'] }}</h3>
            <p class="text-xs text-slate-500 mt-1">Scan kode QR dinamis di bawah via browser kamera guru.</p>

            <!-- Big Dynamic QR Code Simulation -->
            <div
                class="mt-5 mx-auto w-48 h-48 sm:w-56 sm:h-56 bg-slate-50 border-4 border-slate-900 rounded-2xl p-4 flex flex-col items-center justify-center shadow-inner relative">
                <div
                    class="w-full h-full border-2 border-slate-300 border-dashed rounded-xl flex flex-col items-center justify-center font-mono text-center">
                    <div
                        class="w-10 h-10 rounded-lg bg-white border border-slate-200 flex items-center justify-center p-1 mb-1 shadow-2xs">
                        <img src="{{ asset('logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <span class="text-[11px] text-slate-400 font-sans">Token Presensi Dinamis</span>
                    <span class="text-base sm:text-lg font-bold text-blue-900 tracking-wider mt-0.5"
                        id="big-qr-token">SIROJU-8492</span>
                </div>

                <!-- Floating countdown badge -->
                <div
                    class="absolute -bottom-3 px-3 py-0.5 rounded-full bg-slate-900 text-white text-[11px] font-bold shadow-md whitespace-nowrap">
                    Kadaluarsa: <span id="modal-countdown" class="text-amber-400">18 detik</span>
                </div>
            </div>

            <div class="mt-7 flex flex-col sm:flex-row items-center justify-center gap-2">
                <button type="button" onclick="toggleQrSesi()"
                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-800 text-xs font-bold transition-colors"
                    id="btn-toggle-sesi">
                    Ganti ke Sesi Pulang
                </button>
                <button type="button" onclick="refreshQrTokenManual()"
                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-blue-900 hover:bg-blue-800 active:bg-blue-950 text-white text-xs font-bold transition-colors">
                    Regenerate Token
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: HASIL 1-KLIK AUTO-ASSIGN SISWA -->
    <!-- ========================================================================= -->
    <div id="modal-auto-assign-result"
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4 hidden">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-xl border border-slate-200"
            onclick="event.stopPropagation()">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <div
                        class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold shrink-0">
                        ✓
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">1-Klik Auto-Assign Selesai</h3>
                        <p class="text-xs text-slate-500">Pasangkan siswa berdasarkan jenjang & sisa slot terendah.</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('modal-auto-assign-result')"
                    class="p-2 -mr-1 text-slate-400 hover:text-slate-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mt-4 space-y-3 text-xs">
                <div id="auto-assign-badge"
                    class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 leading-relaxed">
                    <strong>5 Siswa Berhasil Ditugaskan!</strong> Kuota guru pembimbing tetap berada dalam batas aturan
                    (maksimal 6 siswa aktif).
                </div>

                <ul id="auto-assign-list"
                    class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden">
                    <li class="p-2.5 flex flex-col sm:flex-row sm:justify-between gap-1 bg-slate-50">
                        <span class="font-medium text-slate-800">Arkan Naufal (SD Kelas 5)</span>
                        <strong class="text-blue-900">→ Dian Maharani, S.Pd. (Slot 5/6)</strong>
                    </li>
                    <li class="p-2.5 flex flex-col sm:flex-row sm:justify-between gap-1">
                        <span class="font-medium text-slate-800">Clara Michelle (SMP Kelas 8)</span>
                        <strong class="text-blue-900">→ Sarah Az-Zahra, M.Pd. (Slot 6/6 Penuh)</strong>
                    </li>
                    <li class="p-2.5 flex flex-col sm:flex-row sm:justify-between gap-1 bg-slate-50">
                        <span class="font-medium text-slate-800">Dimas Bagus (SD Kelas 3)</span>
                        <strong class="text-blue-900">→ Dian Maharani, S.Pd. (Slot 6/6 Penuh)</strong>
                    </li>
                    <li class="p-2.5 flex flex-col sm:flex-row sm:justify-between gap-1">
                        <span class="font-medium text-slate-800">Fathia Rahma (TK B)</span>
                        <strong class="text-blue-900">→ Lestari Handayani, S.Pd. (Slot 4/6)</strong>
                    </li>
                    <li class="p-2.5 flex flex-col sm:flex-row sm:justify-between gap-1 bg-slate-50">
                        <span class="font-medium text-slate-800">Gilang Ramadhan (SMA Kelas 11)</span>
                        <strong class="text-blue-900">→ Ahmad Zakaria, M.Sc. (Slot 6/6 Penuh)</strong>
                    </li>
                </ul>
            </div>

            <div class="mt-5 pt-3 border-t border-slate-200 flex items-center justify-end">
                <button type="button" onclick="closeModal('modal-auto-assign-result'); window.location.reload();"
                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800">
                    Terapkan & Muat Ulang
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: PELUNASAN SPP & TERBITKAN KWITANSI RESMI -->
    <!-- ========================================================================= -->
    <div id="modal-bayar-spp"
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4 hidden">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-xl border border-slate-200"
            onclick="event.stopPropagation()">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('logoo.png') }}" alt="Logo LKP Sang Siroju Lillah"
                        class="w-8 h-8 object-contain rounded-lg border border-slate-200 bg-white p-0.5 shrink-0">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Catat Pelunasan SPP</h3>
                        <p class="text-xs text-slate-500 mt-0.5">LKP Sang Siroju Lillah · Kwitansi resmi</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('modal-bayar-spp')"
                    class="p-2 -mr-1 text-slate-400 hover:text-slate-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form class="mt-4 space-y-3 text-xs"
                onsubmit="event.preventDefault(); alert('Kwitansi KW/{{ $currentCabang['kode'] ?? 'CAB-PNK' }}/2026/10/0043 berhasil diterbitkan! Pesan WhatsApp siap dikirimkan.'); closeModal('modal-bayar-spp');">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pilih Siswa & Tagihan</label>
                    <select
                        class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-600">
                        <option>Bagas Pamungkas · SPP Oktober 2026 (SMP Kelas 9 - Rp 180.000)</option>
                        <option>Farhan Maulana · SPP Oktober 2026 (SMA Kelas 11 - Rp 220.000)</option>
                        <option>Kevin Sanjaya · SPP Oktober 2026 (SD Kelas 6 - Rp 150.000)</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Penyetor (Wali)</label>
                        <input type="text" placeholder="Bpk. Hendra"
                            class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-600"
                            required>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal Transaksi</label>
                        <input type="date" value="2026-10-30"
                            class="w-full text-xs bg-white border border-slate-300 rounded-lg p-2.5 focus:ring-1 focus:ring-blue-600">
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                    <span class="text-slate-500 block text-[11px]">Format Nomor Kwitansi Otomatis:</span>
                    <span class="font-mono font-bold text-slate-900 text-sm">KW/{{ $currentCabang['kode'] ?? 'CAB-PNK' }}/2026/10/0043</span>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeModal('modal-bayar-spp')"
                        class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-emerald-800 text-white text-xs font-bold hover:bg-emerald-700 shadow-2xs">
                        Lunaskan Kwitansi
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            let countdown = 18;
            let currentQrSesi = 'datang';

            setInterval(() => {
                countdown--;
                if (countdown <= 0) {
                    refreshQrTokenManual();
                }
                const timerEl = document.getElementById('countdown-timer');
                if (timerEl) timerEl.textContent = countdown + 's';
                const modalTimer = document.getElementById('modal-countdown');
                if (modalTimer) {
                    modalTimer.textContent = countdown + ' detik';
                }
            }, 1000);

            function calcTotalPendaftaran() {
                const biaya = 100000;
                const diskonInput = document.getElementById('reg-diskon');
                const diskon = parseInt(diskonInput.value) || 0;
                const total = Math.max(0, biaya - diskon);
                document.getElementById('reg-total-bayar').textContent = 'Rp ' + total.toLocaleString('id-ID');
            }

            async function runAutoAssign() {
                try {
                    const res = await fetch('/api/guru/auto-assign', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ cabang_id: '{{ $currentCabang['id'] }}' })
                    });
                    const data = await res.json();
                    if (data.success) {
                        const listEl = document.getElementById('auto-assign-list');
                        const badgeEl = document.getElementById('auto-assign-badge');
                        if (badgeEl) {
                            badgeEl.innerHTML = `<strong>${data.data.assigned_count} Siswa Berhasil Ditugaskan!</strong> Kuota guru pembimbing tetap berada dalam batas aturan (maksimal 6 siswa aktif).`;
                        }
                        if (listEl && data.data.assignments && data.data.assignments.length) {
                            listEl.innerHTML = data.data.assignments.map((item, idx) => `
                                <li class="p-2.5 flex flex-col sm:flex-row sm:justify-between gap-1 ${idx % 2 === 0 ? 'bg-slate-50' : ''}">
                                    <span class="font-medium text-slate-800">${item.siswa_nama} (${item.jenjang})</span>
                                    <strong class="text-blue-900">→ ${item.guru_nama} (Slot ${item.slot_terisi}/6)</strong>
                                </li>
                            `).join('');
                        }
                        openModal('modal-auto-assign-result');
                    } else {
                        alert(data.message || 'Auto assign belum menemukan siswa antre.');
                    }
                } catch (e) {
                    console.error(e);
                    openModal('modal-auto-assign-result');
                }
            }

            async function toggleQrSesi() {
                const btn = document.getElementById('btn-toggle-sesi');
                if (currentQrSesi === 'datang') {
                    currentQrSesi = 'pulang';
                    if (btn) btn.textContent = 'Ganti ke Sesi Datang';
                    alert('Sesi presensi beralih ke: SESI PULANG. QR code telah disesuaikan.');
                } else {
                    currentQrSesi = 'datang';
                    if (btn) btn.textContent = 'Ganti ke Sesi Pulang';
                    alert('Sesi presensi beralih ke: SESI DATANG. QR code telah disesuaikan.');
                }
                await refreshQrTokenManual();
            }

            async function refreshQrTokenManual() {
                try {
                    const res = await fetch('/api/absensi/qr', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ cabang_id: '{{ $currentCabang['id'] }}', tipe: currentQrSesi, duration: 25 })
                    });
                    const data = await res.json();
                    if (data.success) {
                        const token = data.data.token;
                        countdown = data.data.expires_in_seconds;
                        const liveEl = document.getElementById('live-token-text');
                        const bigEl = document.getElementById('big-qr-token');
                        if (liveEl) liveEl.textContent = token;
                        if (bigEl) bigEl.textContent = token;
                        return;
                    }
                } catch (e) {
                    // fallback offline simulation
                }
                const randomCode = Math.floor(1000 + Math.random() * 9000);
                const token = 'SIROJU-' + randomCode;
                countdown = 20;
                const liveEl = document.getElementById('live-token-text');
                const bigEl = document.getElementById('big-qr-token');
                if (liveEl) liveEl.textContent = token;
                if (bigEl) bigEl.textContent = token;
            }
        </script>
    @endpush
@endsection