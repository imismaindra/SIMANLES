@extends('layouts.admin')

@section('title', 'Jadwal Mengajar — LKP Sang Siroju Lillah')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Row -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-6 shadow-2xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-200/70">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Jadwal Mengajar</h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-900 border border-blue-200/70">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-700"></span>
                        {{ $currentCabang['nama'] }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Proteksi Anti-Bentrok Lintas Cabang Aktif
                    </span>
                </div>
                <p class="mt-1 text-xs sm:text-sm text-slate-500">
                    Atur jadwal sesi privat mingguan (Senin s/d Sabtu). Sistem secara otomatis mendeteksi dan mencegah bentrok jam mengajar guru meskipun berada di cabang yang berbeda.
                </p>
            </div>

            <!-- Add Schedule Trigger Button -->
            <button type="button" onclick="openModal('modal-tambah-jadwal')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 active:bg-blue-950 text-white font-semibold text-xs shadow-xs transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                <span>+ Buat Sesi Jadwal</span>
            </button>
        </div>

        <!-- Filter Toolbar -->
        <div class="mt-4">
            <form method="GET" action="{{ route('jadwal.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <input type="hidden" name="cabang_id" value="{{ request('cabang_id', $currentCabang['id']) }}">

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Filter Hari</label>
                    <select name="hari" onchange="this.form.submit()"
                        class="w-full py-2 px-3 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="">Semua Hari (Senin - Sabtu)</option>
                        @foreach($hariList as $h)
                            <option value="{{ $h }}" {{ request('hari') === $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                </div>

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

                <div class="flex items-end gap-2">
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 w-full flex items-center justify-between text-xs">
                        <span class="text-slate-500">Total Sesi:</span>
                        <span class="font-bold text-slate-900 num-tabular">{{ $totalSesi }} Sesi ({{ $totalGuruTerjadwal }} Guru)</span>
                    </div>
                    @if(request()->hasAny(['hari', 'guru_id']))
                        <a href="{{ route('jadwal.index', ['cabang_id' => request('cabang_id', $currentCabang['id'])]) }}"
                            class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 text-xs font-semibold shrink-0">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Weekly Matrix: 6 Columns / Days -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3.5">
        @foreach($hariList as $hari)
            @php
                $sesiHari = $jadwalPerHari[$hari] ?? collect();
            @endphp
            <div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs flex flex-col h-full">
                <!-- Day Header -->
                <div class="px-3.5 py-3 bg-slate-50 border-b border-slate-200/80 flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <span class="font-bold text-xs text-slate-900">{{ $hari }}</span>
                    </div>
                    <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-slate-200 text-slate-700 num-tabular">
                        {{ $sesiHari->count() }}
                    </span>
                </div>

                <!-- Sessions List -->
                <div class="p-2.5 space-y-2 flex-1 overflow-y-auto max-h-[500px]">
                    @forelse($sesiHari as $j)
                        <div class="p-2.5 rounded-xl border border-slate-200/80 bg-slate-50/60 hover:bg-white hover:shadow-xs transition-all text-xs group relative">
                            <!-- Time Badge -->
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-mono text-[11px] font-bold text-blue-900 bg-blue-50 px-1.5 py-0.5 rounded num-tabular">
                                    {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}
                                </span>

                                <!-- Delete Action Form -->
                                <form method="POST" action="{{ route('web.jadwal.destroy', $j->id) }}" onsubmit="return confirm('Hapus sesi jadwal ini?');" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 text-[10px] font-bold" title="Hapus Jadwal">
                                        &times;
                                    </button>
                                </form>
                            </div>

                            <!-- Teacher Name -->
                            <div class="font-bold text-slate-900 truncate" title="{{ $j->guru->nama ?? 'Guru' }}">
                                {{ $j->guru->nama ?? 'Guru' }}
                            </div>

                            <!-- Student Info -->
                            <div class="text-[11px] text-slate-500 truncate mt-0.5">
                                @if($j->siswa)
                                    <span>{{ $j->siswa->nama }}</span> ({{ $j->siswa->jenjang }})
                                @else
                                    <span class="italic text-slate-400">Sesi Privat Bebas</span>
                                @endif
                            </div>

                            <!-- Branch Tag if crosses -->
                            @if($j->cabang && $j->cabang_id !== $currentCabang['id'])
                                <div class="mt-1 text-[9px] font-bold text-indigo-700 bg-indigo-50 px-1 py-0.2 rounded inline-block">
                                    {{ $j->cabang->nama }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs italic">
                            Tidak ada jadwal
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <!-- Complete Table View -->
    <div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs">
        <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
            <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700">Tabel Rincian Sesi Mengajar</h3>
            <span class="text-xs text-slate-500 num-tabular">{{ $jadwalList->count() }} total sesi aktif</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-slate-600 border-collapse">
                <thead class="bg-slate-50 text-slate-700 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200/80">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4">Hari & Jam</th>
                        <th class="py-3 px-4">Guru Pengajar</th>
                        <th class="py-3 px-4">Siswa Bimbingan</th>
                        <th class="py-3 px-4">Cabang</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/70">
                    @forelse($jadwalList as $idx => $jd)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4 text-center text-xs text-slate-400 num-tabular">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900">{{ $jd->hari }}</span>
                                <div class="text-[11px] font-mono font-semibold text-blue-900 num-tabular">
                                    {{ substr($jd->jam_mulai, 0, 5) }} - {{ substr($jd->jam_selesai, 0, 5) }} WIB
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $jd->guru->nama ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $jd->guru->no_pegawai ?? 'NIP' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                @if($jd->siswa)
                                    <div class="font-semibold text-slate-800">{{ $jd->siswa->nama }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $jd->siswa->jenjang }} · Kelas {{ $jd->siswa->kelas }}</div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Sesi Umum / Bebas</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $jd->cabang->nama ?? $currentCabang['nama'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <form method="POST" action="{{ route('web.jadwal.destroy', $jd->id) }}" onsubmit="return confirm('Hapus sesi jadwal {{ $jd->hari }} pukul {{ $jd->jam_mulai }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded" title="Hapus Jadwal">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 text-sm">
                                Tidak ada jadwal mengajar pada kriteria ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Buat Jadwal Baru -->
<div id="modal-tambah-jadwal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in duration-150">
        <div class="px-5 py-4 border-b border-slate-200/80 flex items-center justify-between bg-slate-50/70">
            <div>
                <h3 class="font-bold text-base text-slate-900">Buat Sesi Jadwal Mengajar</h3>
                <p class="text-xs text-slate-500">Deteksi bentrok waktu otomatis di seluruh cabang</p>
            </div>
            <button type="button" onclick="closeModal('modal-tambah-jadwal')" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('web.jadwal.store') }}" class="p-5 space-y-4">
            @csrf
            <input type="hidden" name="cabang_id" value="{{ $currentCabang['id'] }}">

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Guru Pengajar *</label>
                <select name="guru_id" required
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <option value="">-- Pilih Guru --</option>
                    @foreach($daftarGuru as $g)
                        <option value="{{ $g->id }}">{{ $g->nama }} (Kualifikasi: {{ implode(', ', $g->jenjang_list) }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Siswa Bimbingan (Opsional)</label>
                <select name="siswa_id"
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <option value="">-- Sesi Bebas / Mandiri --</option>
                    @foreach($daftarSiswa as $sw)
                        <option value="{{ $sw->id }}">{{ $sw->nama }} ({{ $sw->jenjang }} - Kelas {{ $sw->kelas }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Hari *</label>
                    <select name="hari" required
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                        @foreach($hariList as $h)
                            <option value="{{ $h }}">{{ $h }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Mulai *</label>
                    <input type="time" name="jam_mulai" required value="15:00"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Selesai *</label>
                    <input type="time" name="jam_selesai" required value="16:30"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-500">
                <p><strong>Aturan Anti-Bentrok:</strong> Guru tidak dapat memiliki dua sesi jadwal yang bersinggungan waktu pada hari yang sama di cabang ini maupun cabang lainnya.</p>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeModal('modal-tambah-jadwal')"
                    class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 text-white text-xs font-bold shadow-xs">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

