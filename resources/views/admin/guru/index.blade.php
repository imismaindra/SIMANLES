@extends('layouts.admin')

@section('title', 'Distribusi & Manajemen Guru — LKP Sang Siroju Lillah')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Row -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-6 shadow-2xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-200/70">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Distribusi & Kapasitas Guru</h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-900 border border-blue-200/70">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-700"></span>
                        {{ $currentCabang['nama'] }}
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-900 border border-amber-200">
                        Batas Ketat: Max 6 Siswa / Guru
                    </span>
                </div>
            </div>

            <!-- Action Buttons: Import & Tambah Guru -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <!-- Trigger Import Excel Guru -->
                <button type="button" onclick="openModal('modal-import-guru')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 active:bg-emerald-200 text-emerald-800 font-semibold text-xs border border-emerald-200/80 transition-colors">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                    </svg>
                    <span>Import Excel</span>
                </button>

                <!-- Trigger Tambah Guru Baru -->
                <button type="button" onclick="openModal('modal-tambah-guru')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 active:bg-blue-950 text-white font-semibold text-xs shadow-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    <span>+ Tambah Guru</span>
                </button>
            </div>
        </div>

        <!-- 4 Metric Capacity Strip -->
        <div class="mt-4 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Metric 1: Total Pengajar -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Guru Pengajar</p>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-slate-900 num-tabular">{{ $totalGuru }}</span>
                    <span class="text-xs text-slate-500">guru aktif</span>
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Terdaftar di cabang ini</p>
            </div>

            <!-- Metric 2: Kapasitas Slot Terpakai -->
            <div class="p-3.5 rounded-xl bg-blue-50/60 border border-blue-200/60">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold text-blue-900 uppercase tracking-wider">Kapasitas Slot</p>
                    <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-blue-200 text-blue-950">{{ $persentaseKapasitas }}%</span>
                </div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-blue-950 num-tabular">{{ $slotTerpakai }}</span>
                    <span class="text-xs text-blue-600">/ {{ $kapasitasSlotTotal }} slot total</span>
                </div>
                <div class="mt-2 w-full bg-blue-200/60 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-blue-800 h-full rounded-full transition-all" style="width: {{ $persentaseKapasitas }}%"></div>
                </div>
            </div>

            <!-- Metric 3: Guru Penuh (6/6) -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Guru Kuota Penuh (6/6)</p>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-slate-900 num-tabular">{{ $guruPenuh }}</span>
                    <span class="text-xs text-slate-500">dari {{ $totalGuru }} guru</span>
                </div>
                <p class="mt-1 text-[11px] text-emerald-700 font-semibold num-tabular">
                    Sisa {{ $slotTersedia }} slot bimbingan tersedia
                </p>
            </div>

            <!-- Metric 4: Siswa Tanpa Guru -->
            <div class="p-3.5 rounded-xl {{ $unassignedStudents->isNotEmpty() ? 'bg-amber-50 border border-amber-200' : 'bg-slate-50 border border-slate-200/70' }}">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold {{ $unassignedStudents->isNotEmpty() ? 'text-amber-900' : 'text-slate-500' }} uppercase tracking-wider">Siswa Tanpa Guru</p>
                    @if($unassignedStudents->isNotEmpty())
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                    @endif
                </div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold {{ $unassignedStudents->isNotEmpty() ? 'text-amber-950' : 'text-slate-900' }} num-tabular">{{ $unassignedStudents->count() }}</span>
                    <span class="text-xs {{ $unassignedStudents->isNotEmpty() ? 'text-amber-800' : 'text-slate-500' }}">siswa antre</span>
                </div>
                <p class="mt-1 text-[11px] {{ $unassignedStudents->isNotEmpty() ? 'text-amber-800' : 'text-slate-400' }}">
                    {{ $unassignedStudents->isNotEmpty() ? 'Siap dipasangkan otomatis' : 'Semua siswa teralokasi' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Antrean Siswa Belum Ada Guru (Jika Ada) -->
    @if($unassignedStudents->isNotEmpty())
        <div class="bg-amber-50/70 border border-amber-200 rounded-2xl p-4 sm:p-5 shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-amber-200/70">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <h3 class="font-bold text-slate-900 text-sm">Antrean Siswa Belum Memiliki Guru Pembimbing ({{ $unassignedStudents->count() }})</h3>
                </div>
                <p class="text-xs text-amber-900">
                    Pasangkan pembimbing guru secara manual di bawah:
                </p>
            </div>

            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                @foreach($unassignedStudents as $uSiswa)
                    <div class="p-3 rounded-xl bg-white border border-amber-200/80 shadow-2xs flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-bold text-xs text-slate-900 truncate">{{ $uSiswa->nama }}</p>
                            <p class="text-[11px] text-slate-500">
                                <span class="font-semibold text-amber-900">{{ $uSiswa->jenjang }}</span> · Kelas {{ $uSiswa->kelas }}
                            </p>
                        </div>

                        <!-- Manual Quick Assign Select -->
                        <form method="POST" action="{{ route('web.guru.assign') }}" class="flex items-center gap-1 shrink-0">
                            @csrf
                            <input type="hidden" name="siswa_id" value="{{ $uSiswa->id }}">
                            <select name="guru_id" required
                                class="text-[11px] bg-slate-50 border border-slate-200 rounded-lg py-1 px-2 focus:ring-1 focus:ring-blue-600 max-w-[130px] truncate">
                                <option value="">Pilih Guru...</option>
                                @foreach($guruList as $g)
                                    @php
                                        $canTeach = $g->canTeachJenjang($uSiswa->jenjang);
                                        $isFull = $g->siswa->count() >= 6;
                                    @endphp
                                    @if($canTeach && !$isFull)
                                        <option value="{{ $g->id }}">{{ $g->nama }} ({{ $g->siswa->count() }}/6)</option>
                                    @endif
                                @endforeach
                            </select>
                            <button type="submit" class="px-2 py-1 rounded-lg bg-blue-900 text-white font-bold text-[11px] hover:bg-blue-800" title="Tugaskan Siswa">
                                &check;
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Teacher Capacity Grid -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-base text-slate-900">Daftar Guru & Siswa Bimbingan</h2>
                <span class="text-xs text-slate-500" id="guru-counter-text">Menampilkan {{ $guruList->count() }} pengajar</span>
            </div>

            <!-- Search Input Nama Guru -->
            <div class="relative w-full sm:w-72">
                <input type="text" id="search-guru-input" onkeyup="filterGuruCards()" placeholder="Cari nama guru..."
                    class="w-full pl-9 pr-8 py-2 text-xs rounded-xl bg-white border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600 shadow-2xs placeholder:text-slate-400">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <button type="button" id="clear-search-guru" onclick="clearGuruSearch()" class="hidden absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div id="no-guru-found" class="hidden py-12 text-center text-slate-400 text-sm bg-white rounded-2xl border border-slate-200">
            Tidak ditemukan guru dengan nama tersebut.
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($guruList as $guru)
                @php
                    $assignedCount = $guru->siswa->count();
                    $isFull = $assignedCount >= 6;
                    $percent = round(($assignedCount / 6) * 100);
                @endphp
                <div data-guru-name="{{ strtolower($guru->nama) }}" class="guru-card-item bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-2xs flex flex-col justify-between space-y-4">
                    <!-- Top: Avatar & Info -->
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-900 font-bold text-sm flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($guru->nama, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-slate-900 text-sm truncate">{{ $guru->nama }}</h3>
                                    <p class="text-[11px] text-slate-400 font-mono">{{ $guru->no_pegawai ?: 'NIP-3100' }}</p>
                                </div>
                            </div>

                            <!-- Actions & Status Badges (Full = Hijau, Belum Full = Orange) -->
                            <div class="flex items-center gap-1 shrink-0">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $isFull ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                    {{ $isFull ? '6/6 PENUH' : 'Tersisa ' . (6 - $assignedCount) . ' Slot' }}
                                </span>
                                <!-- Edit Guru -->
                                <button type="button" onclick='openEditGuru(@json($guru))'
                                    class="p-1 text-slate-400 hover:text-blue-700 hover:bg-blue-50 rounded transition-colors" title="Edit Profil & Kualifikasi Guru">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <!-- Hapus / Nonaktifkan Guru -->
                                <form method="POST" action="{{ route('web.guru.destroy', $guru->id) }}" onsubmit="return confirm('Nonaktifkan guru {{ $guru->nama }}? Seluruh siswa bimbingan akan dilepas ke antrean.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded transition-colors" title="Nonaktifkan Guru">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Kualifikasi Jenjang Badges -->
                        <div class="mt-3 flex items-center gap-1.5 flex-wrap">
                            <span class="text-[10px] font-semibold text-slate-400 uppercase">Kualifikasi:</span>
                            @foreach($guru->jenjang_list as $jj)
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] font-bold border border-slate-200/60">
                                    {{ $jj }}
                                </span>
                            @endforeach
                        </div>

                        <!-- Kuota Progress Bar (Full = Hijau, Belum Full = Orange) -->
                        <div class="mt-3.5 space-y-1">
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500 font-medium">Beban Bimbingan:</span>
                                <span class="font-bold {{ $isFull ? 'text-emerald-700' : 'text-amber-800' }} num-tabular">{{ $assignedCount }} / 6 Siswa</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all {{ $isFull ? 'bg-emerald-500' : 'bg-amber-500' }}"
                                    style="width: {{ $percent }}%"></div>
                            </div>
                        </div>

                        <!-- Tombol Detail Siswa Bimbingan -->
                        <div class="mt-3.5 pt-3 border-t border-slate-100">
                            <button type="button" onclick="openDetailSiswaGuru('{{ $guru->id }}')"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50 hover:bg-blue-50/80 border border-slate-200/80 hover:border-blue-200 text-xs font-semibold text-slate-700 hover:text-blue-900 transition-all group">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-900 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </span>
                                    <span class="truncate">Siswa Bimbingan ({{ $assignedCount }})</span>
                                </div>
                                <span class="text-[11px] font-bold text-blue-700 group-hover:text-blue-800 flex items-center gap-1 shrink-0">
                                    Lihat Detail &rarr;
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Bottom Quick Action -->
                    <div class="pt-3 border-t border-slate-100">
                        @if(!$isFull)
                            <button type="button" onclick="openAssignModal('{{ $guru->id }}', '{{ $guru->nama }}', '{{ implode(',', $guru->jenjang_list) }}')"
                                class="w-full py-1.5 px-3 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-900 font-semibold text-xs transition-colors flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                <span>+ Tambah Siswa Bimbingan</span>
                            </button>
                        @else
                            <div class="text-center py-1.5 text-xs text-emerald-800 font-semibold bg-emerald-50/80 border border-emerald-200/80 rounded-xl">
                                &check; Kapasitas Penuh (Maksimal 6)
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400 text-sm bg-white rounded-2xl border border-slate-200">
                    Belum ada data guru terdaftar di cabang ini.
                </div>
            @endforelse
        </div>
    </div>

</div>

<!-- Modal: Assign Siswa ke Guru Tertentu -->
<div id="modal-assign-guru" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-2xl p-5 space-y-4">
        <div>
            <h3 class="font-bold text-slate-900 text-base" id="modal-assign-title">Tugaskan Siswa ke Guru</h3>
            <p class="text-xs text-slate-500 mt-0.5" id="modal-assign-subtitle">Pilih siswa yang kompatibel</p>
        </div>

        <form method="POST" action="{{ route('web.guru.assign') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="guru_id" id="assign-guru-id">

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Siswa Tanpa Guru *</label>
                <select name="siswa_id" id="assign-siswa-select" required
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($unassignedStudents as $uS)
                        <option value="{{ $uS->id }}" data-jenjang="{{ $uS->jenjang }}">
                            {{ $uS->nama }} ({{ $uS->jenjang }} - Kelas {{ $uS->kelas }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Hanya siswa dengan jenjang yang cocok dengan guru ini yang dapat ditugaskan.</p>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeModal('modal-assign-guru')"
                    class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 text-white text-xs font-bold shadow-xs">
                    Tugaskan Siswa
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Tambah Guru Baru -->
<div id="modal-tambah-guru" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-xl w-full border border-slate-200 shadow-2xl overflow-hidden my-8">
        <div class="px-5 py-4 border-b border-slate-200/80 flex items-center justify-between bg-slate-50/70">
            <div>
                <h3 class="font-bold text-base text-slate-900">Tambah Guru Baru</h3>
                <p class="text-xs text-slate-500">Cabang Penempatan: <strong>{{ $currentCabang['nama'] }}</strong></p>
            </div>
            <button type="button" onclick="closeModal('modal-tambah-guru')" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('web.guru.store') }}" class="p-5 sm:p-6 space-y-4">
            @csrf
            <input type="hidden" name="cabang_id" value="{{ $currentCabang['id'] }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Guru & Gelar *</label>
                    <input type="text" name="nama" required placeholder="Contoh: Rina Novitasari, S.Pd."
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Pengguna (Untuk Login)</label>
                    <input type="email" name="email" placeholder="guru@lespintar.id"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Pegawai / NIP</label>
                    <input type="text" name="no_pegawai" placeholder="NIP-3105"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">No. WhatsApp / HP</label>
                    <input type="text" name="no_telp" placeholder="08xxxxxxxxxx"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Lahir (Password Default Akun)</label>
                    <input type="date" name="tanggal_lahir"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kualifikasi Jenjang Mengajar *</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach(['TK', 'SD', 'SMP', 'SMA_SMK'] as $jenjangOpt)
                            <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 border border-slate-200/80 text-xs font-medium cursor-pointer hover:bg-slate-100">
                                <input type="checkbox" name="kualifikasi[]" value="{{ $jenjangOpt }}" class="rounded text-blue-900 focus:ring-blue-600">
                                <span>{{ $jenjangOpt === 'SMA_SMK' ? 'SMA / SMK' : $jenjangOpt }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Tempat Tinggal</label>
                    <textarea name="alamat" rows="2" placeholder="Jl. Mawar No..."
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modal-tambah-guru')"
                    class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 text-white text-xs font-bold shadow-xs">
                    Simpan Data Guru
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Edit Profil & Kualifikasi Guru -->
<div id="modal-edit-guru" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-xl w-full border border-slate-200 shadow-2xl overflow-hidden my-8">
        <div class="px-5 py-4 border-b border-slate-200/80 flex items-center justify-between bg-slate-50/70">
            <div>
                <h3 class="font-bold text-base text-slate-900" id="modal-edit-guru-title">Edit Profil Guru</h3>
                <p class="text-xs text-slate-500">Perbarui kontak dan kualifikasi jenjang mengajar</p>
            </div>
            <button type="button" onclick="closeModal('modal-edit-guru')" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="form-edit-guru" method="POST" action="" class="p-5 sm:p-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Guru & Gelar *</label>
                    <input type="text" name="nama" id="edit-guru-nama" required
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">No. WhatsApp / HP</label>
                    <input type="text" name="no_telp" id="edit-guru-telp" placeholder="08xxxxxxxxxx"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kualifikasi Jenjang Mengajar *</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach(['TK', 'SD', 'SMP', 'SMA_SMK'] as $jenjangOpt)
                            <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 border border-slate-200/80 text-xs font-medium cursor-pointer hover:bg-slate-100">
                                <input type="checkbox" name="kualifikasi[]" value="{{ $jenjangOpt }}" id="edit-kualifikasi-{{ $jenjangOpt }}" class="rounded text-blue-900 focus:ring-blue-600">
                                <span>{{ $jenjangOpt === 'SMA_SMK' ? 'SMA / SMK' : $jenjangOpt }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Tempat Tinggal</label>
                    <textarea name="alamat" id="edit-guru-alamat" rows="2"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modal-edit-guru')"
                    class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 text-white text-xs font-bold shadow-xs">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 4: Import Guru Excel -->
<div id="modal-import-guru" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-2xl p-5 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200/80">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Import Data Guru (Excel / CSV)</h3>
                <p class="text-xs text-slate-500">Cabang Penempatan: <strong>{{ $currentCabang['nama'] }}</strong></p>
            </div>
            <button type="button" onclick="closeModal('modal-import-guru')" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('web.guru.import') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="cabang_id" value="{{ $currentCabang['id'] }}">

            <div class="p-3 bg-blue-50/70 border border-blue-200/70 rounded-xl text-xs text-blue-900 space-y-1">
                <p class="font-bold">Format Kolom Header Excel:</p>
                <p class="font-mono text-[11px] bg-white p-1.5 rounded border border-blue-200">
                    nama, email, no_pegawai, no_telp, tanggal_lahir, alamat, kualifikasi
                </p>
                <p class="text-[11px] text-blue-800 mt-1">Kolom kualifikasi dapat diisi dipisahkan koma, contoh: <code>SD,SMP</code>.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih File (.xlsx, .xls, .csv) *</label>
                <input type="file" name="file" required accept=".xlsx,.xls,.csv"
                    class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-900 hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-xl p-1 bg-slate-50">
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeModal('modal-import-guru')"
                    class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold shadow-xs">
                    Mulai Import
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 5: Detail Siswa Bimbingan Guru -->
<div id="modal-detail-siswa-guru" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden my-8">
        <!-- Header -->
        <div class="px-5 py-4 border-b border-slate-200/80 flex items-center justify-between bg-slate-50/70">
            <div class="flex items-center gap-3 min-w-0">
                <div id="modal-detail-guru-avatar" class="w-10 h-10 rounded-xl bg-blue-100 text-blue-900 font-bold text-sm flex items-center justify-center shrink-0">
                    GU
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-bold text-base text-slate-900 truncate" id="modal-detail-guru-nama">Detail Guru</h3>
                        <span id="modal-detail-guru-badge" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold"></span>
                    </div>
                    <p class="text-xs text-slate-400 font-mono" id="modal-detail-guru-nip">NIP-3100</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modal-detail-siswa-guru')" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="p-5 space-y-4">
            <!-- Kualifikasi -->
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kualifikasi:</span>
                <div id="modal-detail-guru-kualifikasi" class="flex items-center gap-1.5 flex-wrap"></div>
            </div>

            <!-- List Siswa -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-700">Daftar Siswa Bimbingan Aktif:</p>
                    <span id="modal-detail-guru-counter" class="text-xs text-slate-500 font-medium"></span>
                </div>
                <div id="modal-detail-siswa-list" class="max-h-[360px] overflow-y-auto space-y-2 pr-1">
                    <!-- Dinamis via JS -->
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-5 py-3.5 bg-slate-50/70 border-t border-slate-200/80 flex items-center justify-between gap-2.5">
            <button type="button" id="modal-detail-add-btn"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 text-white font-semibold text-xs shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                <span>+ Tambah Siswa</span>
            </button>
            <button type="button" onclick="closeModal('modal-detail-siswa-guru')"
                class="px-4 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200/80 text-xs font-semibold ml-auto transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    const guruDataMap = @json($guruList->keyBy('id'));

    function openDetailSiswaGuru(guruId) {
        const guru = guruDataMap[guruId];
        if (!guru) return;

        document.getElementById('modal-detail-guru-avatar').textContent = (guru.nama || 'GU').substring(0, 2).toUpperCase();
        document.getElementById('modal-detail-guru-nama').textContent = guru.nama;
        document.getElementById('modal-detail-guru-nip').textContent = guru.no_pegawai || 'NIP-3100';

        // Kualifikasi badges
        const kualifikasiContainer = document.getElementById('modal-detail-guru-kualifikasi');
        kualifikasiContainer.innerHTML = '';
        (guru.jenjang_list || []).forEach(function(jj) {
            const span = document.createElement('span');
            span.className = 'px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] font-bold border border-slate-200/60';
            span.textContent = jj;
            kualifikasiContainer.appendChild(span);
        });

        // Kapasitas Badge (Full = Hijau, Belum Full = Orange)
        const count = (guru.siswa || []).length;
        const isFull = count >= 6;
        const badge = document.getElementById('modal-detail-guru-badge');
        badge.className = 'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold ' +
            (isFull ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200');
        badge.textContent = isFull ? '6/6 PENUH' : 'Tersisa ' + (6 - count) + ' Slot';

        document.getElementById('modal-detail-guru-counter').textContent = count + ' / 6 Siswa';

        // Siswa list
        const listContainer = document.getElementById('modal-detail-siswa-list');
        listContainer.innerHTML = '';

        if (!guru.siswa || guru.siswa.length === 0) {
            listContainer.innerHTML = '<div class="py-6 text-center text-slate-400 text-xs italic bg-slate-50 rounded-xl border border-dashed border-slate-200">Belum ada siswa yang dibimbing oleh guru ini.</div>';
        } else {
            const ul = document.createElement('ul');
            ul.className = 'space-y-2';

            guru.siswa.forEach(function(s, idx) {
                const li = document.createElement('li');
                li.className = 'flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 hover:bg-slate-100/70 transition-colors';

                const unassignRoute = '/guru/unassign/' + s.id;
                const csrfToken = '{{ csrf_token() }}';

                li.innerHTML = `
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-900 font-bold text-xs flex items-center justify-center shrink-0">
                            ${idx + 1}
                        </span>
                        <div class="min-w-0">
                            <p class="font-bold text-xs text-slate-900 truncate">${s.nama}</p>
                            <p class="text-[11px] text-slate-500">
                                <span class="font-semibold text-blue-900">${s.jenjang}</span> · Kelas ${s.kelas}
                            </p>
                        </div>
                    </div>
                    <form method="POST" action="${unassignRoute}" onsubmit="return confirm('Lepas siswa ' + ${JSON.stringify(s.nama)} + ' dari guru ' + ${JSON.stringify(guru.nama)} + '?');" class="shrink-0">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <button type="submit" class="inline-flex items-center gap-1 text-[11px] text-slate-500 hover:text-rose-600 bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-200 px-2 py-1 rounded-lg font-semibold transition-colors" title="Lepas dari guru ini">
                            <span>Lepas</span>
                            <span>&times;</span>
                        </button>
                    </form>
                `;
                ul.appendChild(li);
            });
            listContainer.appendChild(ul);
        }

        // Add button logic
        const addBtn = document.getElementById('modal-detail-add-btn');
        if (!isFull) {
            addBtn.style.display = 'inline-flex';
            addBtn.onclick = function() {
                closeModal('modal-detail-siswa-guru');
                openAssignModal(guru.id, guru.nama, (guru.jenjang_list || []).join(','));
            };
        } else {
            addBtn.style.display = 'none';
        }

        openModal('modal-detail-siswa-guru');
    }

    function openEditGuru(data) {
        document.getElementById('form-edit-guru').action = '/guru/' + data.id;
        document.getElementById('modal-edit-guru-title').textContent = 'Edit Guru: ' + data.nama;
        document.getElementById('edit-guru-nama').value = data.nama || '';
        document.getElementById('edit-guru-telp').value = data.no_telp || '';
        document.getElementById('edit-guru-alamat').value = data.alamat || '';

        // Checkboxes
        ['TK', 'SD', 'SMP', 'SMA_SMK'].forEach(function(jj) {
            const cb = document.getElementById('edit-kualifikasi-' + jj);
            if (cb) {
                cb.checked = (data.jenjang_list || []).includes(jj);
            }
        });

        openModal('modal-edit-guru');
    }
    function openAssignModal(guruId, guruNama, guruJenjangStr) {
        document.getElementById('assign-guru-id').value = guruId;
        document.getElementById('modal-assign-title').textContent = 'Tugaskan Siswa ke ' + guruNama;
        document.getElementById('modal-assign-subtitle').textContent = 'Kualifikasi Guru: ' + guruJenjangStr;

        const allowedJenjangs = guruJenjangStr.split(',');
        const select = document.getElementById('assign-siswa-select');

        for (let i = 0; i < select.options.length; i++) {
            const opt = select.options[i];
            if (!opt.value) continue;
            const jj = opt.getAttribute('data-jenjang');
            if (allowedJenjangs.includes(jj)) {
                opt.style.display = '';
                opt.disabled = false;
            } else {
                opt.style.display = 'none';
                opt.disabled = true;
            }
        }

        openModal('modal-assign-guru');
    }

    function filterGuruCards() {
        const query = (document.getElementById('search-guru-input').value || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.guru-card-item');
        const clearBtn = document.getElementById('clear-search-guru');
        let visibleCount = 0;

        if (clearBtn) {
            if (query.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }

        cards.forEach(function(card) {
            const name = (card.getAttribute('data-guru-name') || '').toLowerCase();
            if (name.includes(query)) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const noResult = document.getElementById('no-guru-found');
        if (noResult) {
            noResult.classList.toggle('hidden', visibleCount > 0);
        }

        const counter = document.getElementById('guru-counter-text');
        if (counter) {
            counter.textContent = 'Menampilkan ' + visibleCount + ' pengajar' + (query ? ' (difilter)' : '');
        }
    }

    function clearGuruSearch() {
        const input = document.getElementById('search-guru-input');
        if (input) {
            input.value = '';
            filterGuruCards();
            input.focus();
        }
    }
</script>
@endsection

