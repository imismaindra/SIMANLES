<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kwitansi Resmi #{{ $spp->no_kwitansi ?? $spp->id }} — LKP Sang Siroju Lillah</title>
    <link rel="icon" type="image/png" href="{{ asset('logoo.png') }}">
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background-color: white !important; }
            .print-border { border: 1.5px solid #1e3a8a !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased p-4 sm:p-8 min-h-screen flex flex-col items-center justify-center">

    <!-- Action Bar (Hidden on Print) -->
    <div class="no-print mb-6 flex items-center justify-between gap-4 max-w-2xl w-full">
        <a href="{{ route('spp.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 inline-flex items-center gap-1">
            &larr; Kembali ke Daftar SPP
        </a>
        <button type="button" onclick="window.print()" class="px-4 py-2 bg-blue-900 text-white rounded-xl text-xs font-bold hover:bg-blue-800 shadow-xs flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Kwitansi (PDF / Printer)</span>
        </button>
    </div>

    <!-- Official Kwitansi Paper Canvas -->
    <div class="bg-white border-2 border-blue-900/30 rounded-2xl max-w-2xl w-full p-6 sm:p-8 shadow-xl relative overflow-hidden print-border">
        <!-- Watermark -->
        <div class="absolute inset-0 flex items-center justify-center opacity-4 pointer-events-none">
            <img src="{{ asset('logoo.png') }}" class="w-80 h-80 object-contain">
        </div>

        <!-- Kwitansi Header -->
        <div class="flex items-start justify-between pb-4 border-b-2 border-blue-900 gap-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('logoo.png') }}" alt="Logo LKP" class="w-12 h-12 object-contain rounded-xl border border-slate-200 p-1">
                <div>
                    <h1 class="text-base sm:text-lg font-black tracking-tight text-blue-950 uppercase">LKP Sang Siroju Lillah</h1>
                    <p class="text-xs text-slate-600 font-medium">Lembaga Kursus & Bimbingan Belajar Privat</p>
                    <p class="text-[10px] text-slate-400">{{ $spp->cabang->nama ?? 'Cabang Ponokawan' }} · {{ $spp->cabang->alamat ?? 'Krian, Sidoarjo' }}</p>
                </div>
            </div>

            <div class="text-right shrink-0">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Bukti Pembayaran</div>
                <div class="text-sm sm:text-base font-black text-blue-950">KWITANSI RESMI</div>
                <div class="text-xs font-mono font-bold text-blue-900 mt-1 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                    {{ $spp->no_kwitansi ?? 'KW/CAB-PNK/2026/10/0001' }}
                </div>
            </div>
        </div>

        <!-- Body Details -->
        <div class="my-6 space-y-3.5 text-xs sm:text-sm">
            <div class="flex flex-col sm:flex-row sm:items-center py-1 border-b border-slate-100">
                <span class="w-40 text-slate-500 font-medium">Telah Diterima Dari</span>
                <span class="font-bold text-slate-900">: {{ $spp->nama_pembayar ?: ($spp->siswa->nama_ibu ?? 'Orang Tua Siswa') }}</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center py-1 border-b border-slate-100">
                <span class="w-40 text-slate-500 font-medium">Nama Siswa / Jenjang</span>
                <span class="font-bold text-slate-900">: {{ $spp->siswa->nama ?? 'Siswa' }} ({{ $spp->siswa->jenjang ?? '' }} · Kelas {{ $spp->siswa->kelas ?? '' }})</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center py-1 border-b border-slate-100">
                <span class="w-40 text-slate-500 font-medium">Untuk Pembayaran</span>
                <span class="font-semibold text-slate-800">: Uang Les / SPP Periode Bulan {{ $spp->bulan }} Tahun {{ $spp->tahun }}</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center py-1 border-b border-slate-100">
                <span class="w-40 text-slate-500 font-medium">Tanggal Transaksi</span>
                <span class="font-semibold text-slate-800">: {{ \Illuminate\Support\Carbon::parse($spp->tanggal_bayar ?? now())->translatedFormat('d F Y') }}</span>
            </div>

            <!-- Big Nominal Box -->
            <div class="mt-5 p-4 rounded-xl bg-blue-50/80 border border-blue-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-900 block">Jumlah Uang Yang Dibayar:</span>
                    <span class="text-2xl font-black text-blue-950 num-tabular">
                        Rp {{ number_format($spp->nominal, 0, ',', '.') }},-
                    </span>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900 border border-emerald-300">
                        &check; LUNAS
                    </span>
                </div>
            </div>
        </div>

        <!-- Footer Signatures -->
        <div class="pt-6 border-t border-slate-200 grid grid-cols-2 gap-6 text-xs text-center">
            <div>
                <p class="text-slate-400">Penyetor / Orang Tua,</p>
                <div class="h-16 flex items-end justify-center">
                    <p class="font-semibold text-slate-800 underline underline-offset-4">{{ $spp->nama_pembayar ?: ($spp->siswa->nama_ibu ?? 'Wali Siswa') }}</p>
                </div>
            </div>

            <div>
                <p class="text-slate-400">{{ $spp->cabang->nama ?? 'Cabang' }}, {{ \Illuminate\Support\Carbon::parse($spp->tanggal_bayar ?? now())->translatedFormat('d F Y') }}</p>
                <p class="text-slate-400">Bendahara Cabang,</p>
                <div class="h-16 flex items-end justify-center">
                    <div class="text-center">
                        <span class="font-bold text-blue-950 underline underline-offset-4">Petugas Administrasi</span>
                        <p class="text-[10px] text-slate-400">LKP Sang Siroju Lillah</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-3 border-t border-slate-100 text-[9px] text-slate-400 text-center">
            Kwitansi ini diterbitkan secara sah oleh Sistem Manajemen Terpadu LKP Sang Siroju Lillah dan dilindungi hak cipta.
        </div>
    </div>

</body>
</html>

