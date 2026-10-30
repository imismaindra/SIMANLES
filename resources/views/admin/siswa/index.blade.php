@extends('layouts.admin')

@section('title', 'Data & Pendaftaran Siswa — LKP Sang Siroju Lillah')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Row -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-6 shadow-2xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-200/70">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Data & Pendaftaran Siswa</h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-900 border border-blue-200/70">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-700"></span>
                        {{ $currentCabang['nama'] }}
                    </span>
                </div>
                <p class="mt-1 text-xs sm:text-sm text-slate-500">
                    Kelola data siswa privat dan bimbel, status aktif/cuti, kenaikan kelas akhir semester, dan registrasi siswa baru.
                </p>
            </div>

            <!-- Header Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Trigger Kenaikan Kelas Massal -->
                <button type="button" onclick="openModal('modal-kenaikan-kelas')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200/80 active:bg-slate-300 text-slate-700 font-semibold text-xs border border-slate-200/80 transition-colors">
                    <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <span>Kenaikan Kelas Massal</span>
                </button>

                <!-- Trigger Modal Pendaftaran Siswa Baru -->
                <button type="button" onclick="openModal('modal-pendaftaran')"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 active:bg-blue-950 text-white font-semibold text-xs shadow-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    <span>+ Daftarkan Siswa Baru</span>
                </button>
            </div>
        </div>

        <!-- 4 Stat Summary Cards Strip -->
        <div class="mt-4 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Stat 1: Total Siswa -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Siswa</p>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-slate-900 num-tabular">{{ $totalSiswa }}</span>
                    <span class="text-xs text-slate-500">terdaftar</span>
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Termasuk cuti & alumni</p>
            </div>

            <!-- Stat 2: Siswa Aktif & Jenjang -->
            <div class="p-3.5 rounded-xl bg-blue-50/60 border border-blue-200/60">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold text-blue-900 uppercase tracking-wider">Siswa Aktif</p>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-blue-950 num-tabular">{{ $totalAktif }}</span>
                    <span class="text-xs text-blue-700">belajar aktif</span>
                </div>
                <div class="mt-1 text-[10px] text-blue-800 font-medium truncate num-tabular">
                    TK: {{ $countTk }} · SD: {{ $countSd }} · SMP: {{ $countSmp }} · SMA: {{ $countSma }}
                </div>
            </div>

            <!-- Stat 3: Belum Ada Guru -->
            <div class="p-3.5 rounded-xl {{ $totalBelumAdaGuru > 0 ? 'bg-amber-50/70 border border-amber-200' : 'bg-slate-50 border border-slate-200/70' }}">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold {{ $totalBelumAdaGuru > 0 ? 'text-amber-900' : 'text-slate-500' }} uppercase tracking-wider">Belum Ada Guru</p>
                    @if($totalBelumAdaGuru > 0)
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-amber-200 text-amber-950">Antre</span>
                    @endif
                </div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold {{ $totalBelumAdaGuru > 0 ? 'text-amber-950' : 'text-slate-900' }} num-tabular">{{ $totalBelumAdaGuru }}</span>
                    <span class="text-xs {{ $totalBelumAdaGuru > 0 ? 'text-amber-800' : 'text-slate-500' }}">siswa</span>
                </div>
                @if($totalBelumAdaGuru > 0)
                    <a href="{{ route('guru.index', ['cabang_id' => $currentCabang['id']]) }}" class="mt-1 inline-flex items-center gap-1 text-[11px] font-semibold text-amber-800 hover:underline">
                        <span>Buka Auto-Assign</span> &rarr;
                    </a>
                @else
                    <p class="mt-1 text-[11px] text-emerald-700 font-medium">Semua siswa teralokasi</p>
                @endif
            </div>

            <!-- Stat 4: Cuti / Keluar -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Cuti & Keluar</p>
                <div class="mt-1 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-slate-700 num-tabular">{{ $totalCuti + $totalKeluar }}</span>
                    <span class="text-xs text-slate-500">siswa</span>
                </div>
                <p class="mt-1 text-[11px] text-slate-500 num-tabular">
                    <span>Cuti: <strong>{{ $totalCuti }}</strong></span> ·
                    <span>Lulus: <strong>{{ $totalKeluar }}</strong></span>
                </p>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 shadow-2xs">
        <form method="GET" action="{{ route('siswa.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="hidden" name="cabang_id" value="{{ request('cabang_id', $currentCabang['id']) }}">

            <!-- Search box -->
            <div class="lg:col-span-2">
                <label for="q" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Cari Siswa / Ortu / WA</label>
                <div class="relative">
                    <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Ketik nama siswa atau no. WA..."
                        class="w-full pl-9 pr-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <circle cx="11" cy="11" r="8" stroke-width="2" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </div>
            </div>

            <!-- Filter Jenjang -->
            <div>
                <label for="jenjang" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Jenjang</label>
                <select name="jenjang" id="jenjang" onchange="this.form.submit()"
                    class="w-full py-2 px-3 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <option value="">Semua Jenjang</option>
                    <option value="TK" {{ request('jenjang') === 'TK' ? 'selected' : '' }}>TK (Kelas 0)</option>
                    <option value="SD" {{ request('jenjang') === 'SD' ? 'selected' : '' }}>SD (Kelas 1-6)</option>
                    <option value="SMP" {{ request('jenjang') === 'SMP' ? 'selected' : '' }}>SMP (Kelas 7-9)</option>
                    <option value="SMA_SMK" {{ request('jenjang') === 'SMA_SMK' ? 'selected' : '' }}>SMA/SMK (Kelas 10-12)</option>
                </select>
            </div>

            <!-- Filter Status -->
            <div>
                <label for="status" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status</label>
                <select name="status" id="status" onchange="this.form.submit()"
                    class="w-full py-2 px-3 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="cuti" {{ request('status') === 'cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="keluar" {{ request('status') === 'keluar' ? 'selected' : '' }}>Keluar / Lulus</option>
                </select>
            </div>

            <!-- Filter Guru / Reset -->
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label for="guru_filter" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Alokasi Guru</label>
                    <select name="guru_filter" id="guru_filter" onchange="this.form.submit()"
                        class="w-full py-2 px-3 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="">Semua Siswa</option>
                        <option value="unassigned" {{ request('guru_filter') === 'unassigned' ? 'selected' : '' }}>Belum Ada Guru</option>
                        <option value="assigned" {{ request('guru_filter') === 'assigned' ? 'selected' : '' }}>Sudah Berguru</option>
                    </select>
                </div>
                @if(request()->hasAny(['q', 'jenjang', 'status', 'guru_filter']))
                    <a href="{{ route('siswa.index', ['cabang_id' => request('cabang_id', $currentCabang['id'])]) }}"
                        class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 text-xs font-semibold shrink-0" title="Reset filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-slate-600 border-collapse">
                <thead class="bg-slate-50/90 text-slate-700 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200/80">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">Jenjang & Kelas</th>
                        <th class="py-3 px-4">Ibu / Kontak WA Ortu</th>
                        <th class="py-3 px-4">Guru Pembimbing</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/70">
                    @forelse($siswaList as $idx => $s)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4 text-center text-xs text-slate-400 num-tabular">
                                {{ $siswaList->firstItem() + $idx }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $s->nama }}</div>
                                <div class="text-[11px] text-slate-400 truncate max-w-xs" title="{{ $s->alamat ?: 'Alamat belum diisi' }}">
                                    {{ $s->alamat ?: 'Alamat belum diisi' }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $badgeStyle = match($s->jenjang) {
                                        'TK' => 'bg-purple-50 text-purple-900 border-purple-200/60',
                                        'SD' => 'bg-blue-50 text-blue-900 border-blue-200/60',
                                        'SMP' => 'bg-amber-50 text-amber-900 border-amber-200/60',
                                        default => 'bg-emerald-50 text-emerald-900 border-emerald-200/60',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $badgeStyle }}">
                                    {{ $s->jenjang }} {{ $s->kelas > 0 ? '· Kelas '.$s->kelas : '· TK' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-xs font-medium text-slate-800">{{ $s->nama_ibu ?: '-' }}</div>
                                @if($s->no_telp_ortu)
                                    @php
                                        $cleanWa = preg_replace('/[^0-9]/', '', $s->no_telp_ortu);
                                        if (str_starts_with($cleanWa, '0')) {
                                            $cleanWa = '62' . substr($cleanWa, 1);
                                        }
                                    @endphp
                                    <a href="https://wa.me/{{ $cleanWa }}" target="_blank"
                                        class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-700 hover:text-emerald-900 hover:underline num-tabular">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2z" />
                                        </svg>
                                        <span>{{ $s->no_telp_ortu }}</span>
                                    </a>
                                @else
                                    <span class="text-[11px] text-slate-400">Tidak ada WA</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($s->guru)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-900 font-bold text-[10px] flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($s->guru->nama, 0, 2)) }}
                                        </div>
                                        <div class="truncate">
                                            <div class="text-xs font-semibold text-slate-900 truncate">{{ $s->guru->nama }}</div>
                                            <div class="text-[10px] text-slate-400 font-mono">{{ $s->guru->no_pegawai ?: 'Guru Privat' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-900 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Belum Ada Guru
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @php
                                    $statusBadge = match($s->status) {
                                        'aktif' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                        'cuti' => 'bg-amber-50 text-amber-800 border-amber-200',
                                        default => 'bg-slate-100 text-slate-600 border-slate-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusBadge }}">
                                    {{ ucfirst($s->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <!-- Form Toggle Status (Aktif / Cuti / Keluar) -->
                                    <div class="relative inline-block text-left" x-data="{ open: false }">
                                        <select onchange="updateSiswaStatus('{{ $s->id }}', this.value)"
                                            class="text-[11px] bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg py-1 px-2 font-medium text-slate-700 cursor-pointer">
                                            <option value="aktif" {{ $s->status === 'aktif' ? 'selected' : '' }}>Set: Aktif</option>
                                            <option value="cuti" {{ $s->status === 'cuti' ? 'selected' : '' }}>Set: Cuti</option>
                                            <option value="keluar" {{ $s->status === 'keluar' ? 'selected' : '' }}>Set: Keluar</option>
                                        </select>
                                    </div>

                                    <!-- Soft Delete -->
                                    <form method="POST" action="{{ route('web.siswa.destroy', $s->id) }}" onsubmit="return confirm('Hapus siswa {{ $s->nama }}? Seluruh riwayat SPP dan kas masa lalu tetap aman tersimpan.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded" title="Hapus Siswa">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                                Tidak ada data siswa yang cocok dengan filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($siswaList->hasPages())
            <div class="px-4 py-3 bg-slate-50 border-t border-slate-200/80">
                {{ $siswaList->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal 1: Pendaftaran Siswa Baru -->
<div id="modal-pendaftaran" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-xl w-full border border-slate-200 shadow-2xl overflow-hidden my-8 animate-in fade-in duration-150">
        <div class="px-5 py-4 border-b border-slate-200/80 flex items-center justify-between bg-slate-50/70">
            <div>
                <h3 class="font-bold text-base text-slate-900">Form Pendaftaran Siswa Baru</h3>
                <p class="text-xs text-slate-500">Cabang: <strong>{{ $currentCabang['nama'] }}</strong> (Auto kas masuk & tagihan SPP)</p>
            </div>
            <button type="button" onclick="closeModal('modal-pendaftaran')" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('web.siswa.store') }}" class="p-5 space-y-4">
            @csrf
            <input type="hidden" name="cabang_id" value="{{ $currentCabang['id'] }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Siswa *</label>
                    <input type="text" name="nama" required placeholder="Contoh: Muhammad Al-Fatih"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jenjang Pendidikan *</label>
                    <select name="jenjang" id="reg-jenjang" required onchange="calculateFee()"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="TK">TK (Taman Kanak-kanak)</option>
                        <option value="SD" selected>SD (Sekolah Dasar)</option>
                        <option value="SMP">SMP (Sekolah Menengah Pertama)</option>
                        <option value="SMA_SMK">SMA / SMK</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kelas *</label>
                    <select name="kelas" id="reg-kelas" required
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="0">Kelas 0 (TK)</option>
                        <option value="1">Kelas 1</option>
                        <option value="2">Kelas 2</option>
                        <option value="3">Kelas 3</option>
                        <option value="4">Kelas 4</option>
                        <option value="5">Kelas 5</option>
                        <option value="6">Kelas 6</option>
                        <option value="7">Kelas 7</option>
                        <option value="8">Kelas 8</option>
                        <option value="9">Kelas 9</option>
                        <option value="10">Kelas 10</option>
                        <option value="11">Kelas 11</option>
                        <option value="12">Kelas 12</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Orang Tua (Ibu / Wali)</label>
                    <input type="text" name="nama_ibu" placeholder="Contoh: Ibu Fatimah"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">No. WhatsApp Orang Tua</label>
                    <input type="text" name="no_telp_ortu" placeholder="08123456789"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Domisili Siswa</label>
                    <textarea name="alamat" rows="2" placeholder="Dusun / Desa, RT/RW, Kecamatan..."
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Diskon Pendaftaran (Rp)</label>
                    <input type="number" name="diskon" id="reg-diskon" value="0" min="0" max="{{ $biayaPendaftaranBaku }}" oninput="calculateFee()"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Guru Pembimbing (Opsional)</label>
                    <select name="guru_id"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="">-- Alokasikan Nanti (Auto-Assign) --</option>
                        @foreach($daftarGuru as $g)
                            <option value="{{ $g->id }}" {{ $g->siswa_count >= 6 ? 'disabled' : '' }}>
                                {{ $g->nama }} ({{ $g->siswa_count }}/6 slot) {{ $g->siswa_count >= 6 ? '— PENUH' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Kalkulasi Biaya Ringkas -->
            <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200/80 space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Biaya Pendaftaran Baku:</span>
                    <span class="font-semibold num-tabular">Rp {{ number_format($biayaPendaftaranBaku, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Diskon Diberikan:</span>
                    <span class="text-rose-600 font-semibold num-tabular" id="display-diskon">- Rp 0</span>
                </div>
                <div class="pt-1.5 border-t border-blue-200 flex justify-between font-bold text-slate-900">
                    <span>Total Kas Masuk Pendaftaran:</span>
                    <span class="text-emerald-800 text-sm num-tabular" id="display-total-bayar">Rp {{ number_format($biayaPendaftaranBaku, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeModal('modal-pendaftaran')"
                    class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 text-white text-xs font-bold shadow-xs">
                    Simpan & Daftarkan Siswa
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Kenaikan Kelas Massal -->
<div id="modal-kenaikan-kelas" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-2xl p-5 space-y-4">
        <div class="flex items-start gap-3">
            <div class="p-2.5 rounded-xl bg-amber-100 text-amber-900 shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-base">Kenaikan Kelas Massal</h3>
                <p class="text-xs text-slate-500 mt-1">
                    Aksi ini akan menaikkan seluruh siswa aktif di <strong>{{ $currentCabang['nama'] }}</strong> setingkat lebih tinggi (+1 kelas). Siswa kelas 12 otomatis berstatus <strong>lulus (keluar)</strong>.
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('web.siswa.kenaikan-kelas') }}" class="pt-2 flex items-center justify-end gap-2.5">
            @csrf
            <input type="hidden" name="cabang_id" value="{{ $currentCabang['id'] }}">
            <button type="button" onclick="closeModal('modal-kenaikan-kelas')"
                class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                Batal
            </button>
            <button type="submit"
                class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white text-xs font-bold shadow-xs">
                Ya, Jalankan Kenaikan Kelas
            </button>
        </form>
    </div>
</div>

<script>
    const baku = {{ $biayaPendaftaranBaku }};

    function calculateFee() {
        const diskonEl = document.getElementById('reg-diskon');
        const diskonVal = Math.min(baku, Math.max(0, parseFloat(diskonEl.value) || 0));
        const total = Math.max(0, baku - diskonVal);

        document.getElementById('display-diskon').textContent = '- Rp ' + diskonVal.toLocaleString('id-ID');
        document.getElementById('display-total-bayar').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    function updateSiswaStatus(id, newStatus) {
        if (!confirm('Ubah status siswa ini menjadi: ' + newStatus + '?')) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/siswa/' + id + '/status';

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'PATCH';
        form.appendChild(method);

        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = newStatus;
        form.appendChild(statusInput);

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endsection

