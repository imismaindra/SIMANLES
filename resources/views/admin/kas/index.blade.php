@extends('layouts.admin')

@section('title', 'Buku Kas & Keuangan — LKP Sang Siroju Lillah')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Row -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-6 shadow-2xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-200/70">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Buku Kas & Keuangan</h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-900 border border-blue-200/70">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-700"></span>
                        {{ $currentCabang['nama'] }}
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">
                        Periode: {{ $namaBulanList[$selectedBulan] ?? $selectedBulan }} {{ $selectedTahun }}
                    </span>
                </div>
                <p class="mt-1 text-xs sm:text-sm text-slate-500">
                    Pencatatan mutasi kas masuk (pendaftaran & SPP) dan kas keluar operasional, pemantauan saldo riil, serta rekapitulasi Laba/Rugi cabang.
                </p>
            </div>

            <!-- Trigger Modal Catat Kas -->
            <button type="button" onclick="openModal('modal-catat-kas')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 active:bg-blue-950 text-white font-semibold text-xs shadow-xs transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                <span>+ Catat Transaksi Kas</span>
            </button>
        </div>

        <!-- 3 Big Metric Cards: Masuk, Keluar, Saldo Bersih -->
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            <!-- Card 1: Total Pemasukan -->
            <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-200/80">
                <div class="flex items-center justify-between text-xs text-emerald-800 font-semibold uppercase tracking-wider">
                    <span>Kas Masuk (Debit)</span>
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                    </svg>
                </div>
                <div class="mt-2 text-2xl font-bold text-emerald-950 num-tabular">
                    Rp {{ number_format($rekap['total_masuk'], 0, ',', '.') }}
                </div>
                <p class="mt-1 text-[11px] text-emerald-700">Pendaftaran & SPP Siswa</p>
            </div>

            <!-- Card 2: Total Pengeluaran -->
            <div class="p-4 rounded-xl bg-rose-50/70 border border-rose-200/80">
                <div class="flex items-center justify-between text-xs text-rose-800 font-semibold uppercase tracking-wider">
                    <span>Kas Keluar (Kredit)</span>
                    <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                    </svg>
                </div>
                <div class="mt-2 text-2xl font-bold text-rose-950 num-tabular">
                    Rp {{ number_format($rekap['total_keluar'], 0, ',', '.') }}
                </div>
                <p class="mt-1 text-[11px] text-rose-700">Gaji, Sewa & Operasional</p>
            </div>

            <!-- Card 3: Saldo Kas Bersih -->
            <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-200/80">
                <div class="flex items-center justify-between text-xs text-blue-900 font-semibold uppercase tracking-wider">
                    <span>Saldo Kas Bersih</span>
                    <span class="w-2 h-2 rounded-full {{ $rekap['saldo_bersih'] >= 0 ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                </div>
                <div class="mt-2 text-2xl font-bold {{ $rekap['saldo_bersih'] >= 0 ? 'text-blue-950' : 'text-rose-900' }} num-tabular">
                    Rp {{ number_format($rekap['saldo_bersih'], 0, ',', '.') }}
                </div>
                <p class="mt-1 text-[11px] {{ $rekap['saldo_bersih'] >= 0 ? 'text-emerald-700 font-medium' : 'text-rose-700 font-bold' }}">
                    {{ $rekap['saldo_bersih'] >= 0 ? 'Surplus Operasional' : 'Defisit Anggaran' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 shadow-2xs">
        <form method="GET" action="{{ route('kas.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="hidden" name="cabang_id" value="{{ request('cabang_id', $currentCabang['id']) }}">

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Bulan</label>
                <select name="bulan" onchange="this.form.submit()"
                    class="w-full py-2 px-3 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                    @foreach($namaBulanList as $num => $namaBln)
                        <option value="{{ $num }}" {{ $selectedBulan === $num ? 'selected' : '' }}>{{ $namaBln }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Tahun</label>
                <select name="tahun" onchange="this.form.submit()"
                    class="w-full py-2 px-3 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                    @for($y = 2025; $y <= 2028; $y++)
                        <option value="{{ $y }}" {{ $selectedTahun === $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Arus Kas (Tipe)</label>
                <select name="tipe" onchange="this.form.submit()"
                    class="w-full py-2 px-3 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <option value="semua" {{ $selectedTipe === 'semua' ? 'selected' : '' }}>Semua Arus Kas</option>
                    <option value="masuk" {{ $selectedTipe === 'masuk' ? 'selected' : '' }}>Hanya Kas Masuk</option>
                    <option value="keluar" {{ $selectedTipe === 'keluar' ? 'selected' : '' }}>Hanya Kas Keluar</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Kategori</label>
                <select name="kategori" onchange="this.form.submit()"
                    class="w-full py-2 px-3 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <option value="semua">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" {{ $selectedKategori === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <a href="{{ route('kas.index', ['cabang_id' => request('cabang_id', $currentCabang['id'])]) }}"
                    class="w-full py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs text-center">
                    Reset Filter
                </a>
            </div>
        </form>
    </div>

    <!-- Main Content: Mutasi Table & Laba Rugi Widget Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Transaction History Table (8 cols) -->
        <div class="lg:col-span-8 bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs">
            <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
                <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700">Daftar Mutasi Buku Kas</h3>
                <span class="text-xs text-slate-500 num-tabular">{{ $transaksiList->total() }} transaksi</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm text-slate-600 border-collapse">
                    <thead class="bg-slate-50 text-slate-700 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200/80">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">#</th>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Tipe & Kategori</th>
                            <th class="py-3 px-4">Keterangan</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/70">
                        @forelse($transaksiList as $idx => $t)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 text-center text-xs text-slate-400 num-tabular">
                                    {{ $transaksiList->firstItem() + $idx }}
                                </td>
                                <td class="py-3 px-4 font-mono text-xs text-slate-700 num-tabular">
                                    {{ \Illuminate\Support\Carbon::parse($t->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-1.5">
                                        @if($t->tipe === 'masuk')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                + Masuk
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                                - Keluar
                                            </span>
                                        @endif
                                        <span class="font-semibold text-slate-800 text-xs">{{ $t->kategori }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-xs text-slate-700 max-w-xs truncate" title="{{ $t->keterangan }}">
                                    {{ $t->keterangan }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold {{ $t->tipe === 'masuk' ? 'text-emerald-800' : 'text-rose-800' }} num-tabular text-xs sm:text-sm">
                                    {{ $t->tipe === 'masuk' ? '+' : '-' }} Rp {{ number_format($t->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400 text-sm">
                                    Belum ada transaksi kas yang dicatat pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transaksiList->hasPages())
                <div class="px-4 py-3 bg-slate-50 border-t border-slate-200/80">
                    {{ $transaksiList->links() }}
                </div>
            @endif
        </div>

        <!-- Right: Laba Rugi Breakdown Widget (4 cols) -->
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-2xs space-y-4">
                <div class="pb-3 border-b border-slate-200/80">
                    <h3 class="font-bold text-slate-900 text-sm">Rekapitulasi Kategori</h3>
                    <p class="text-xs text-slate-500">Breakdown arus kas periode ini</p>
                </div>

                <div class="space-y-2 text-xs">
                    @forelse($rekap['rincian_kategori'] as $kat => $nom)
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between">
                            <span class="font-semibold text-slate-700 truncate mr-2">{{ $kat }}</span>
                            <span class="font-bold text-slate-900 num-tabular shrink-0">
                                Rp {{ number_format($nom, 0, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic text-center py-4">Belum ada rincian data.</p>
                    @endforelse
                </div>

                <div class="pt-3 border-t border-slate-200/80 text-xs text-slate-500 space-y-1">
                    <p class="font-bold text-slate-700">Audit Trail Keuangan:</p>
                    <p>Pendaftaran siswa baru dan pelunasan SPP secara otomatis memicu pembuatan baris transaksi pada buku kas cabang ini.</p>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal: Catat Transaksi Kas -->
<div id="modal-catat-kas" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-2xl p-5 space-y-4">
        <div>
            <h3 class="font-bold text-slate-900 text-base">Catat Transaksi Kas Baru</h3>
            <p class="text-xs text-slate-500 mt-0.5">Cabang: <strong>{{ $currentCabang['nama'] }}</strong></p>
        </div>

        <form method="POST" action="{{ route('web.kas.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="cabang_id" value="{{ $currentCabang['id'] }}">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Mutasi *</label>
                    <select name="tipe" required
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="masuk">Pemasukan (Masuk)</option>
                        <option value="keluar" selected>Pengeluaran (Keluar)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori *</label>
                    <select name="kategori" required
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                        <option value="Gaji Guru">Gaji Guru</option>
                        <option value="Sewa Tempat">Sewa Tempat</option>
                        <option value="Listrik & Air">Listrik & Air</option>
                        <option value="ATK & Modul">ATK & Modul Buku</option>
                        <option value="Promosi & Iklan">Promosi & Iklan</option>
                        <option value="Konsumsi & Kebersihan">Konsumsi & Kebersihan</option>
                        <option value="Pendaftaran">Pendaftaran (Manual)</option>
                        <option value="SPP">SPP (Manual)</option>
                        <option value="Operasional Lain">Operasional Lainnya</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Transaksi (Rp) *</label>
                <input type="number" name="nominal" required min="1" placeholder="Contoh: 250000"
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Transaksi *</label>
                <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}"
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan Rinci *</label>
                <textarea name="keterangan" rows="2" required placeholder="Contoh: Pembelian buku modul semester ganjil 20 eksemplar"
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeModal('modal-catat-kas')"
                    class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 text-white text-xs font-bold shadow-xs">
                    Simpan Transaksi Kas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

