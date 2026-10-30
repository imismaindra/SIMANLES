@extends('layouts.admin')

@section('title', 'Tagihan & SPP Siswa — LKP Sang Siroju Lillah')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Row -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-6 shadow-2xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-200/70">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Tagihan & SPP Siswa</h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-900 border border-blue-200/70">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-700"></span>
                        {{ $currentCabang['nama'] }}
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">
                        Periode: {{ $namaBulanList[$selectedBulan] ?? $selectedBulan }} {{ $selectedTahun }}
                    </span>
                </div>
                <p class="mt-1 text-xs sm:text-sm text-slate-500">
                    Otomasi tagihan SPP bulanan idempoten, pencatatan pelunasan, penerbitan nomor kwitansi resmi, dan pengiriman notifikasi WhatsApp ke orang tua.
                </p>
            </div>

            <!-- Trigger Generate Billing Massal -->
            <button type="button" onclick="openModal('modal-generate-billing')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 active:bg-blue-950 text-white font-semibold text-xs shadow-xs transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span>Generate Tagihan Massal</span>
            </button>
        </div>

        <!-- 4 Metric SPP Strip -->
        <div class="mt-4 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Metric 1: Total Tagihan Periode Ini -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Tagihan Siswa</p>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-slate-900 num-tabular">{{ $totalTagihan }}</span>
                    <span class="text-xs text-slate-500">tagihan</span>
                </div>
                <p class="mt-1 text-[11px] text-slate-400 num-tabular">
                    Nominal: Rp {{ number_format($nominalTotal, 0, ',', '.') }}
                </p>
            </div>

            <!-- Metric 2: Sudah Lunas -->
            <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-200/60">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold text-emerald-900 uppercase tracking-wider">Sudah Lunas</p>
                    <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-emerald-200 text-emerald-950">{{ $persentaseLunas }}%</span>
                </div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-emerald-950 num-tabular">{{ $totalLunasCount }}</span>
                    <span class="text-xs text-emerald-700">siswa</span>
                </div>
                <p class="mt-1 text-[11px] text-emerald-800 font-semibold num-tabular">
                    Terkumpul: Rp {{ number_format($nominalLunas, 0, ',', '.') }}
                </p>
            </div>

            <!-- Metric 3: Belum Bayar -->
            <div class="p-3.5 rounded-xl {{ $totalBelumBayarCount > 0 ? 'bg-amber-50 border border-amber-200' : 'bg-slate-50 border border-slate-200/70' }}">
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-semibold {{ $totalBelumBayarCount > 0 ? 'text-amber-900' : 'text-slate-500' }} uppercase tracking-wider">Belum Bayar</p>
                    @if($totalBelumBayarCount > 0)
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded bg-amber-200 text-amber-950">Jatuh Tempo</span>
                    @endif
                </div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold {{ $totalBelumBayarCount > 0 ? 'text-amber-950' : 'text-slate-900' }} num-tabular">{{ $totalBelumBayarCount }}</span>
                    <span class="text-xs {{ $totalBelumBayarCount > 0 ? 'text-amber-800' : 'text-slate-500' }}">siswa</span>
                </div>
                <p class="mt-1 text-[11px] {{ $totalBelumBayarCount > 0 ? 'text-amber-800' : 'text-slate-400' }} font-semibold num-tabular">
                    Piutang: Rp {{ number_format($nominalBelumBayar, 0, ',', '.') }}
                </p>
            </div>

            <!-- Metric 4: Progress Pelunasan -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Efektivitas Penagihan</p>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-slate-900 num-tabular">{{ $persentaseLunas }}%</span>
                </div>
                <div class="mt-2 w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-emerald-600 h-full rounded-full transition-all" style="width: {{ $persentaseLunas }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Period Selector Toolbar -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 shadow-2xs">
        <form method="GET" action="{{ route('spp.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="hidden" name="cabang_id" value="{{ request('cabang_id', $currentCabang['id']) }}">

            <!-- Search -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Cari Siswa / Ortu</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama siswa..."
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
            </div>

            <!-- Month -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Bulan</label>
                <select name="bulan" onchange="this.form.submit()"
                    class="w-full py-2 px-3 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                    @foreach($namaBulanList as $num => $namaBln)
                        <option value="{{ $num }}" {{ $selectedBulan === $num ? 'selected' : '' }}>{{ $namaBln }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Year -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Tahun</label>
                <select name="tahun" onchange="this.form.submit()"
                    class="w-full py-2 px-3 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                    @for($y = 2025; $y <= 2028; $y++)
                        <option value="{{ $y }}" {{ $selectedTahun === $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status Pelunasan</label>
                <select name="status" onchange="this.form.submit()"
                    class="w-full py-2 px-3 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <option value="semua" {{ $selectedStatus === 'semua' ? 'selected' : '' }}>Semua Status</option>
                    <option value="belum_bayar" {{ $selectedStatus === 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="lunas" {{ $selectedStatus === 'lunas' ? 'selected' : '' }}>Sudah Lunas</option>
                </select>
            </div>

            <!-- Reset / Action -->
            <div class="flex items-end">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- SPP Billing Table -->
    <div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-slate-600 border-collapse">
                <thead class="bg-slate-50/90 text-slate-700 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200/80">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">Periode</th>
                        <th class="py-3 px-4">Nominal SPP</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4">No. Kwitansi Resmi</th>
                        <th class="py-3 px-4">Tanggal Bayar / Penyetor</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/70">
                    @forelse($sppList as $idx => $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4 text-center text-xs text-slate-400 num-tabular">
                                {{ $sppList->firstItem() + $idx }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $item->siswa->nama ?? 'Siswa' }}</div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $item->siswa->jenjang ?? '' }} · Kelas {{ $item->siswa->kelas ?? '' }}
                                </div>
                            </td>
                            <td class="py-3 px-4 text-xs font-semibold text-slate-800">
                                {{ $namaBulanList[$item->bulan] ?? $item->bulan }} {{ $item->tahun }}
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-900 num-tabular">
                                Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($item->status === 'lunas')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        &check; Lunas
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        Belum Bayar
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($item->no_kwitansi)
                                    <span class="font-mono text-xs font-bold text-blue-900 bg-blue-50 px-2 py-0.5 rounded border border-blue-200/70">
                                        {{ $item->no_kwitansi }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-xs">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-xs">
                                @if($item->tanggal_bayar)
                                    <div class="font-semibold text-slate-900">{{ \Illuminate\Support\Carbon::parse($item->tanggal_bayar)->format('d/m/Y') }}</div>
                                    <div class="text-[11px] text-slate-500">Penyetor: {{ $item->nama_pembayar ?: '-' }}</div>
                                @else
                                    <span class="text-slate-400 italic text-xs">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    @if($item->status !== 'lunas')
                                        <!-- Tombol Bayar SPP -->
                                        <button type="button" onclick="openBayarModal('{{ $item->id }}', '{{ $item->siswa->nama ?? 'Siswa' }}', '{{ $item->nominal }}', '{{ $item->siswa->nama_ibu ?? '' }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-2xs transition-colors">
                                            &check; Bayar
                                        </button>
                                    @else
                                        <!-- Cetak Kwitansi Resmi -->
                                        <a href="{{ route('web.spp.kwitansi', $item->id) }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold" title="Cetak Kwitansi">
                                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                            <span class="hidden sm:inline">Kwitansi</span>
                                        </a>

                                        <!-- Kirim WhatsApp Kwitansi -->
                                        @if($item->siswa && $item->siswa->no_telp_ortu)
                                            <a href="{{ $item->generateWhatsAppLink() }}" target="_blank"
                                                onclick="markWaSent('{{ $item->id }}')"
                                                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-semibold" title="Kirim Kwitansi ke WhatsApp Orang Tua">
                                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2z" />
                                                </svg>
                                                <span>WA</span>
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-sm">
                                Tidak ada tagihan SPP untuk periode {{ $namaBulanList[$selectedBulan] ?? $selectedBulan }} {{ $selectedTahun }}.
                                <div class="mt-2">
                                    <button type="button" onclick="openModal('modal-generate-billing')" class="text-blue-900 font-semibold underline text-xs">
                                        Klik untuk men-generate tagihan massal bulan ini
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sppList->hasPages())
            <div class="px-4 py-3 bg-slate-50 border-t border-slate-200/80">
                {{ $sppList->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal 1: Bayar SPP -->
<div id="modal-bayar-spp" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-2xl p-5 space-y-4">
        <div>
            <h3 class="font-bold text-slate-900 text-base">Pelunasan Tagihan SPP</h3>
            <p class="text-xs text-slate-500 mt-0.5" id="bayar-modal-sub">Siswa: -</p>
        </div>

        <form id="form-bayar-spp" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Tagihan</label>
                <input type="text" id="bayar-nominal-display" readonly
                    class="w-full px-3 py-2 text-xs font-bold text-slate-900 bg-slate-100 rounded-xl border border-slate-200 num-tabular">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pembayar / Penyetor *</label>
                <input type="text" name="nama_pembayar" id="bayar-nama-pembayar" required
                    placeholder="Contoh: Ibu Fatimah"
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Transaksi Pembayaran *</label>
                <input type="date" name="tanggal_bayar" required value="{{ date('Y-m-d') }}"
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
            </div>

            <div class="p-3 rounded-xl bg-blue-50 text-[11px] text-blue-900">
                Setelah pembayaran disimpan, nomor kwitansi resmi (format: <strong>KW/CAB/{{ date('Y/m') }}/xxxx</strong>) akan diterbitkan dan jurnal kas masuk tercatat secara otomatis.
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeModal('modal-bayar-spp')"
                    class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs">
                    Konfirmasi Pelunasan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Generate Tagihan Massal Idempoten -->
<div id="modal-generate-billing" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-2xl p-5 space-y-4">
        <div>
            <h3 class="font-bold text-slate-900 text-base">Generate Tagihan SPP Massal</h3>
            <p class="text-xs text-slate-500 mt-0.5">Membuat tagihan untuk seluruh siswa aktif di cabang ini</p>
        </div>

        <form method="POST" action="{{ route('web.spp.billing') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="cabang_id" value="{{ $currentCabang['id'] }}">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Bulan *</label>
                    <select name="bulan" required
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                        @foreach($namaBulanList as $num => $namaBln)
                            <option value="{{ $num }}" {{ $selectedBulan === $num ? 'selected' : '' }}>{{ $namaBln }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun *</label>
                    <input type="number" name="tahun" required value="{{ $selectedTahun }}" min="2020" max="2040"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 text-[11px] text-slate-500 space-y-1">
                <p><strong>Aturan Idempoten:</strong> Jika tagihan siswa untuk bulan dan tahun tersebut sudah pernah dibuat, sistem tidak akan menduplikasi baris data.</p>
                <p>Siswa yang sedang cuti atau keluar otomatis dilewati.</p>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeModal('modal-generate-billing')"
                    class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 text-white text-xs font-bold shadow-xs">
                    Jalankan Generate Tagihan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBayarModal(sppId, namaSiswa, nominal, namaIbu) {
        document.getElementById('bayar-modal-sub').textContent = 'Siswa: ' + namaSiswa;
        document.getElementById('bayar-nominal-display').value = 'Rp ' + Number(nominal).toLocaleString('id-ID');
        document.getElementById('bayar-nama-pembayar').value = namaIbu || 'Orang Tua Siswa';

        const form = document.getElementById('form-bayar-spp');
        form.action = '/spp/bayar/' + sppId;

        openModal('modal-bayar-spp');
    }

    function markWaSent(sppId) {
        fetch('/spp/sent/' + sppId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
    }
</script>
@endsection

