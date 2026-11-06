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
                <p class="mt-1 text-xs sm:text-sm text-slate-500">
                    Pantau utilisasi slot bimbingan privat, kualifikasi multi-jenjang pengajar, dan jalankan algoritma load-balancing 1-Klik Auto-Assign.
                </p>
            </div>

            <!-- Action Buttons: Import, Tambah Guru & Auto-Assign -->
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

                <form method="POST" action="{{ route('web.guru.auto-assign') }}" class="inline">
                    @csrf
                    <input type="hidden" name="cabang_id" value="{{ $currentCabang['id'] }}">
                    <button type="submit" {{ $unassignedStudents->isEmpty() ? 'disabled' : '' }}
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl {{ $unassignedStudents->isNotEmpty() ? 'bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold' : 'bg-slate-100 text-slate-400 cursor-not-allowed' }} text-xs shadow-xs transition-colors">
                        <svg class="w-4 h-4 text-slate-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>1-Klik Auto-Assign ({{ $unassignedStudents->count() }} Antre)</span>
                    </button>
                </form>
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
                    Klik <strong>1-Klik Auto-Assign</strong> di atas atau pasangkan manual di bawah:
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
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-base text-slate-900">Daftar Guru & Siswa Bimbingan</h2>
            <span class="text-xs text-slate-500">Menampilkan {{ $guruList->count() }} pengajar</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($guruList as $guru)
                @php
                    $assignedCount = $guru->siswa->count();
                    $isFull = $assignedCount >= 6;
                    $percent = round(($assignedCount / 6) * 100);
                @endphp
                <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-2xs flex flex-col justify-between space-y-4">
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

                            <!-- Actions & Status Badges -->
                            <div class="flex items-center gap-1 shrink-0">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $isFull ? 'bg-rose-50 text-rose-800 border border-rose-200' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' }}">
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

                        <!-- Kuota Progress Bar -->
                        <div class="mt-3.5 space-y-1">
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500 font-medium">Beban Bimbingan:</span>
                                <span class="font-bold text-slate-900 num-tabular">{{ $assignedCount }} / 6 Siswa</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all {{ $isFull ? 'bg-amber-500' : 'bg-blue-600' }}"
                                    style="width: {{ $percent }}%"></div>
                            </div>
                        </div>

                        <!-- Siswa Bimbingan List -->
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Siswa Bimbingan ({{ $assignedCount }}):</p>
                            @if($guru->siswa->isEmpty())
                                <p class="text-xs text-slate-400 italic py-2">Belum ada siswa yang dibimbing.</p>
                            @else
                                <ul class="space-y-1.5">
                                    @foreach($guru->siswa as $bSiswa)
                                        <li class="flex items-center justify-between p-2 rounded-lg bg-slate-50 hover:bg-slate-100/80 text-xs transition-colors">
                                            <div class="min-w-0">
                                                <span class="font-semibold text-slate-800 truncate block">{{ $bSiswa->nama }}</span>
                                                <span class="text-[10px] text-slate-400">{{ $bSiswa->jenjang }} (Kls {{ $bSiswa->kelas }})</span>
                                            </div>
                                            <!-- Lepas Siswa Form -->
                                            <form method="POST" action="{{ route('web.guru.unassign', $bSiswa->id) }}" onsubmit="return confirm('Lepas siswa {{ $bSiswa->nama }} dari guru {{ $guru->nama }}?');" class="shrink-0">
                                                @csrf
                                                <button type="submit" class="text-[10px] text-slate-400 hover:text-rose-600 px-1.5 py-0.5 rounded hover:bg-rose-50 font-semibold" title="Lepas dari guru ini">
                                                    Lepas &times;
                                                </button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
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
                            <div class="text-center py-1.5 text-xs text-rose-700 font-semibold bg-rose-50/60 rounded-xl">
                                Kapasitas Penuh (Maksimal 6)
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

<script>
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
</script>
@endsection

