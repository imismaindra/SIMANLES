@extends('layouts.admin')

@section('title', 'Master Data & Pengaturan — LKP Sang Siroju Lillah')

@section('content')
<div class="space-y-6">

    <!-- Header & Status Row -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-6 shadow-2xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-200/70">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Master Data & Pengaturan Cabang</h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-900 border border-indigo-200/70">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-700"></span>
                        Otoritas Pusat & Cabang
                    </span>
                </div>
                <p class="mt-1 text-xs sm:text-sm text-slate-500">
                    Pengaturan tarif SPP baku per jenjang pendidikan, biaya registrasi pendaftaran siswa baru, dan manajemen multi-cabang fisik LKP Sang Siroju Lillah.
                </p>
            </div>

            <!-- Action: Tambah Cabang Button -->
            <button type="button" onclick="openModal('modal-tambah-cabang')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 active:bg-blue-950 text-white font-semibold text-xs shadow-xs transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                <span>+ Tambah Cabang Baru</span>
            </button>
        </div>

        <div class="mt-3 flex items-center gap-2 text-xs text-slate-500">
            <span>Lingkup Pengaturan: <strong>Berlaku Global di Seluruh Cabang</strong></span>
        </div>
    </div>

    <!-- Section 1: Daftar Cabang Fisik -->
    <div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs">
        <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
            <div>
                <h2 class="font-bold text-slate-900 text-sm">Daftar Cabang Fisik Aktif</h2>
                <p class="text-xs text-slate-500">Struktur multi-cabang dengan isolasi operasional dan buku kas masing-masing</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-900 num-tabular">
                {{ $cabangs->count() }} Cabang Terdaftar
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-slate-600 border-collapse">
                <thead class="bg-slate-50 text-slate-700 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200/80">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4">Kode & Nama Cabang</th>
                        <th class="py-3 px-4">Alamat Fisik Cabang</th>
                        <th class="py-3 px-4 text-center">Siswa Aktif</th>
                        <th class="py-3 px-4 text-center">Guru Pengajar</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/70">
                    @foreach($cabangs as $idx => $cb)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4 text-center text-xs text-slate-400 num-tabular">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 flex items-center gap-2">
                                    <span>{{ $cb->nama }}</span>
                                    <span class="font-mono text-[10px] bg-slate-100 px-1.5 py-0.5 rounded text-slate-700 font-bold">
                                        {{ $cb->kode }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-600 max-w-sm">
                                {{ $cb->alamat ?: 'Alamat belum diatur' }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-900 num-tabular">
                                {{ $cb->siswa_count }} Siswa
                            </td>
                            <td class="py-3 px-4 text-center font-semibold text-slate-800 num-tabular">
                                {{ $cb->guru_count }} Guru
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    {{ ucfirst($cb->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Master Tarif SPP Global & Biaya Pendaftaran -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-2xs space-y-5">
        <div class="pb-3 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="font-bold text-slate-900 text-base">Master Tarif SPP Global & Biaya Pendaftaran Baku</h2>
                <p class="text-xs text-slate-500">Tarif baku acuan sistem saat pendaftaran baru dan penagihan bulanan otomatis</p>
            </div>
            <span class="text-[11px] text-amber-800 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-lg font-semibold">
                Sifat: Forward-Looking (Hanya untuk tagihan mendatang)
            </span>
        </div>

        <form method="POST" action="{{ route('web.pengaturan.tarif') }}" class="space-y-6">
            @csrf

            <!-- 4 Jenjang Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($tarifSpps as $tarif)
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900 text-sm">{{ $tarif->jenjang }}</span>
                            <span class="text-[10px] font-semibold bg-white border border-slate-200 px-2 py-0.5 rounded text-slate-600">
                                Kelas {{ $tarif->kelas_min }} - {{ $tarif->kelas_max }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-slate-500 mb-1">Tarif Bulanan (Rp)</label>
                            <input type="number" name="tarif[{{ $tarif->jenjang }}]" value="{{ (int) $tarif->nominal }}" required min="0" step="5000"
                                class="w-full px-3 py-2 text-xs font-bold text-slate-900 rounded-xl bg-white border border-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-600 num-tabular">
                        </div>
                        <p class="text-[10px] text-slate-400">
                            @if($tarif->jenjang === 'TK')
                                Untuk TK A & TK B
                            @elseif($tarif->jenjang === 'SD')
                                Untuk Siswa Kelas 1 s/d 6
                            @elseif($tarif->jenjang === 'SMP')
                                Untuk Siswa Kelas 7 s/d 9
                            @else
                                Untuk Siswa Kelas 10 s/d 12
                            @endif
                        </p>
                    </div>
                @endforeach
            </div>

            <!-- Biaya Pendaftaran Baku -->
            <div class="p-4 rounded-xl bg-blue-50/60 border border-blue-200/70 max-w-md">
                <label class="block text-xs font-bold text-blue-950 mb-1">Biaya Pendaftaran Siswa Baru Baku (Rp)</label>
                <div class="flex items-center gap-2">
                    <input type="number" name="biaya_pendaftaran" value="{{ (int) $biayaPendaftaran }}" required min="0" step="10000"
                        class="w-full px-3 py-2 text-xs font-bold text-slate-900 rounded-xl bg-white border border-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-600 num-tabular">
                </div>
                <p class="text-[11px] text-blue-800 mt-1">Biaya dasar sebelum pemotongan diskon manual saat registrasi pendaftaran.</p>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-blue-900 hover:bg-blue-800 active:bg-blue-950 text-white text-xs font-bold shadow-xs transition-colors">
                    Simpan Perubahan Tarif Master
                </button>
            </div>
        </form>
    </div>

    <!-- Section 3: Ringkasan Kebijakan Sistem (Invariants) -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-2xs space-y-4">
        <div class="pb-3 border-b border-slate-200/80">
            <h2 class="font-bold text-slate-900 text-base">Aturan Inti Sistem (Business Invariants)</h2>
            <p class="text-xs text-slate-500">Kebijakan arsitektur resmi yang ditegakkan secara ketat pada backend</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <!-- Invariant 1 -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 space-y-1.5">
                <div class="flex items-center gap-2 font-bold text-slate-900">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    <span>1. Kuota Maksimal 6 Siswa / Guru</span>
                </div>
                <p class="text-slate-600 leading-relaxed">
                    Setiap guru pengajar hanya boleh membimbing maksimal 6 siswa aktif secara akumulatif di seluruh cabang. Dilindungi dengan <code>DB::transaction()</code> dan <code>lockForUpdate()</code> untuk mencegah race condition.
                </p>
            </div>

            <!-- Invariant 2 -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 space-y-1.5">
                <div class="flex items-center gap-2 font-bold text-slate-900">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    <span>2. Idempotensi Tagihan SPP Bulanan</span>
                </div>
                <p class="text-slate-600 leading-relaxed">
                    Scheduler otomatis setiap tanggal 1 dini hari (00:05). Jika dijalankan berulang kali dalam bulan yang sama, sistem tidak akan menduplikasi baris tagihan dengan proteksi constraint unik <code>UNIQUE(siswa_id, bulan, tahun)</code>.
                </p>
            </div>

            <!-- Invariant 3 -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 space-y-1.5">
                <div class="flex items-center gap-2 font-bold text-slate-900">
                    <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                    <span>3. Deteksi Bentrok Jadwal Lintas Cabang</span>
                </div>
                <p class="text-slate-600 leading-relaxed">
                    Guru tidak dapat dijadwalkan pada 2 sesi yang bersinggungan waktu pada hari yang sama meskipun berada di cabang yang berbeda: <code>(jam_mulai_baru &lt; jam_selesai_lama) AND (jam_selesai_baru &gt; jam_mulai_lama)</code>.
                </p>
            </div>

            <!-- Invariant 4 -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 space-y-1.5">
                <div class="flex items-center gap-2 font-bold text-slate-900">
                    <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                    <span>4. Presensi QR Dinamis Berumur Pendek</span>
                </div>
                <p class="text-slate-600 leading-relaxed">
                    Token QR hanya berumur 15 s/d 30 detik di tabel <code>qr_token</code> dan langsung digantikan saat token baru diterbitkan untuk mencegah titip absen dan screenshot jarak jauh.
                </p>
            </div>
        </div>
    </div>

</div>

<!-- Modal: Tambah Cabang Baru -->
<div id="modal-tambah-cabang" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-2xl p-5 space-y-4">
        <div>
            <h3 class="font-bold text-slate-900 text-base">Tambah Cabang Baru</h3>
            <p class="text-xs text-slate-500 mt-0.5">Membuka unit fisik cabang bimbingan belajar baru</p>
        </div>

        <form method="POST" action="{{ route('web.pengaturan.cabang') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Cabang *</label>
                <input type="text" name="nama" required placeholder="Contoh: Cabang Surabaya Barat"
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Unik Cabang *</label>
                <input type="text" name="kode" required placeholder="Contoh: CAB-SBY" maxlength="20"
                    class="w-full px-3 py-2 text-xs font-mono font-bold uppercase rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                <p class="text-[10px] text-slate-400 mt-1">Kode digunakan pada penomoran resmi kwitansi: KW/{KODE}/YYYY/MM/xxxx</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Fisik</label>
                <textarea name="alamat" rows="2" placeholder="Jl. Raya..., Kota/Kabupaten..."
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeModal('modal-tambah-cabang')"
                    class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 text-white text-xs font-bold shadow-xs">
                    Simpan Cabang Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

