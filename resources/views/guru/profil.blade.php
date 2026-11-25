@extends('layouts.admin')

@section('title', 'Profil Pengajar & Keamanan Akun — LKP Sang Siroju Lillah')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto pb-8">

    <!-- Header Banner -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                @php
                    $headerAvatarUrl = $guru?->foto_url ?? $user->foto_url;
                @endphp
                @if($headerAvatarUrl)
                    <div class="relative overflow-hidden w-12 h-12 rounded-2xl shrink-0 shadow-md shadow-blue-950/20 border border-slate-200 dark:border-slate-700 bg-slate-200 dark:bg-slate-800">
                        <div class="skeleton-shimmer absolute inset-0"></div>
                        <img src="{{ $headerAvatarUrl }}" alt="{{ $guru->nama ?? $user->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover relative z-10 opacity-0 transition-opacity duration-300" onload="this.classList.remove('opacity-0'); this.previousElementSibling?.remove();">
                    </div>
                @else
                    <div class="w-12 h-12 rounded-2xl bg-blue-900 text-white font-bold text-lg flex items-center justify-center shrink-0 shadow-md shadow-blue-950/20">
                        {{ strtoupper(substr($guru->nama ?? $user->name, 0, 2)) }}
                    </div>
                @endif
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                            {{ $guru->nama ?? $user->name }}
                        </h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200/70 dark:border-emerald-900">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Guru Pengajar Aktif
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $guru->no_pegawai ?: ($user->no_pegawai ?: 'NIP-3101') }} &bull; {{ $currentCabang['nama'] ?? 'Cabang Ponokawan' }}
                    </p>
                </div>
            </div>

            <a href="{{ route('jadwal.index') }}"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Jadwal</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-center gap-2.5 animate-in fade-in">
            <svg class="w-4.5 h-4.5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs space-y-1 animate-in fade-in">
            <p class="font-bold flex items-center gap-1.5">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Terjadi kesalahan pada formulir:
            </p>
            <ul class="list-disc list-inside pl-1 text-[11.5px] space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri: Informasi Profil & Kontak (2 Kolom) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-2xs">
                <div class="pb-4 border-b border-slate-200/80 dark:border-slate-800">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Informasi Profil & Kontak</span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Perbarui nama lengkap, nomor kontak WhatsApp, dan alamat domisili Anda.
                    </p>
                </div>

                <form method="POST" action="{{ route('guru.profil.update') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                    @csrf
                    @method('PUT')

                    @php
                        $profilFotoUrl = $guru?->foto_url ?? $user->foto_url;
                    @endphp

                    <!-- Foto Profil Pengajar -->
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Foto Profil Pengajar
                        </label>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="relative shrink-0">
                                <div id="avatar-preview-container" class="{{ $profilFotoUrl ? '' : 'hidden' }} relative overflow-hidden w-20 h-20 rounded-2xl shrink-0 border border-slate-200 dark:border-slate-700 shadow-xs bg-slate-200 dark:bg-slate-800">
                                    @if($profilFotoUrl)
                                        <div class="skeleton-shimmer absolute inset-0"></div>
                                    @endif
                                    <img id="avatar-preview-img" src="{{ $profilFotoUrl ?? '' }}" alt="Foto Profil" loading="lazy" decoding="async" class="w-full h-full object-cover relative z-10 {{ $profilFotoUrl ? 'opacity-0' : '' }} transition-opacity duration-300" onload="this.classList.remove('opacity-0'); this.previousElementSibling?.remove();">
                                </div>
                                <div id="avatar-preview-placeholder" class="{{ $profilFotoUrl ? 'hidden' : '' }} w-20 h-20 rounded-2xl bg-blue-900 text-white font-bold text-2xl flex items-center justify-center shadow-xs">
                                    {{ strtoupper(substr($guru->nama ?? $user->name, 0, 2)) }}
                                </div>
                            </div>

                            <div class="space-y-2 flex-1">
                                <input type="file" name="foto" id="foto-input" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" onchange="previewAvatar(this)">
                                <input type="hidden" name="hapus_foto" id="hapus-foto-input" value="0">

                                <div class="flex items-center gap-2 flex-wrap">
                                    <button type="button" onclick="document.getElementById('foto-input').click()"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-slate-100 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-semibold transition-colors border border-slate-200 dark:border-slate-600 shadow-2xs cursor-pointer">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>Pilih Foto Baru</span>
                                    </button>

                                    <button type="button" id="btn-hapus-foto" onclick="removeAvatar()"
                                        class="{{ $profilFotoUrl ? 'inline-flex' : 'hidden' }} items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 text-xs font-semibold transition-colors border border-rose-200/70 dark:border-rose-900 shadow-2xs cursor-pointer">
                                        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span>Hapus Foto</span>
                                    </button>

                                    <span id="foto-filename" class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-xs"></span>
                                </div>

                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Format: JPG, JPEG, PNG, WEBP. Maksimal ukuran 2 MB.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama Lengkap -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Nama Lengkap & Gelar <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="nama" required value="{{ old('nama', $guru->nama ?? $user->name) }}"
                                class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:bg-white dark:focus:bg-slate-900 focus:outline-none focus:ring-1 focus:ring-blue-600 text-slate-900 dark:text-white shadow-2xs">
                        </div>

                        <!-- NIP / No Pegawai (Read-only) -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Nomor Pegawai / NIP</label>
                                <span class="text-[10px] text-slate-400 font-medium">(Hanya lihat)</span>
                            </div>
                            <input type="text" value="{{ $guru->no_pegawai ?: ($user->no_pegawai ?: 'NIP-3101') }}" readonly disabled
                                class="w-full px-3 py-2 text-xs rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 cursor-not-allowed select-none">
                        </div>

                        <!-- Email Akun (Read-only) -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Email Login</label>
                                <span class="text-[10px] text-slate-400 font-medium">(Hanya lihat)</span>
                            </div>
                            <input type="email" value="{{ $user->email }}" readonly disabled
                                class="w-full px-3 py-2 text-xs rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 cursor-not-allowed select-none">
                        </div>

                        <!-- Tanggal Lahir (Read-only, Tidak Bisa Diedit) -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Tanggal Lahir</label>
                                <span class="text-[10px] text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-1.5 py-0.2 rounded font-medium border border-amber-200/60 dark:border-amber-900">
                                    Hanya lihat
                                </span>
                            </div>
                            @php
                                $tglLahirRaw = $guru?->tanggal_lahir ?? $user?->tanggal_lahir;
                                $tglLahirVal = $tglLahirRaw ? (is_string($tglLahirRaw) ? substr($tglLahirRaw, 0, 10) : $tglLahirRaw->format('Y-m-d')) : '';
                            @endphp
                            <input type="date" value="{{ $tglLahirVal }}" readonly tabindex="-1"
                                class="w-full px-3 py-2 text-xs rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 cursor-not-allowed select-none pointer-events-none focus:outline-none">
                            <p class="text-[10.5px] text-slate-400 mt-1">Tanggal lahir terdaftar sebagai identitas tetap dan tidak dapat diedit mandiri.</p>
                        </div>

                        <!-- No WhatsApp / HP (Hanya Angka) -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                No. WhatsApp / HP <span class="text-rose-500">*</span>
                            </label>
                            <input type="tel" name="no_telp" required placeholder="Contoh: 081234567890"
                                inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                value="{{ old('no_telp', $guru->no_telp ?? '') }}"
                                class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:bg-white dark:focus:bg-slate-900 focus:outline-none focus:ring-1 focus:ring-blue-600 text-slate-900 dark:text-white shadow-2xs font-mono">
                            <p class="text-[10.5px] text-slate-400 mt-1">Hanya angka yang dapat diinputkan (tanpa spasi/simbol).</p>
                        </div>

                        <!-- Alamat Tempat Tinggal -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Alamat Tempat Tinggal
                            </label>
                            <textarea name="alamat" rows="2" placeholder="Masukkan alamat domisili lengkap..."
                                class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:bg-white dark:focus:bg-slate-900 focus:outline-none focus:ring-1 focus:ring-blue-600 text-slate-900 dark:text-white shadow-2xs leading-relaxed">{{ old('alamat', $guru->alamat ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 active:bg-blue-950 text-white font-semibold text-xs shadow-xs transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Perubahan Profil</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kolom Kanan: Ganti Password & Info Penempatan (1 Kolom) -->
        <div class="space-y-6">

            <!-- Card Ganti Password -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-5 shadow-2xs">
                <div class="pb-3.5 border-b border-slate-200/80 dark:border-slate-800">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Ganti Password Akun</span>
                    </h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                        Gunakan kombinasi minimal 6 karakter demi keamanan akun Anda.
                    </p>
                </div>

                <form method="POST" action="{{ route('guru.profil.password') }}" class="mt-4 space-y-3.5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Password Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="current_password" required placeholder="Masukkan password saat ini"
                            class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:bg-white dark:focus:bg-slate-900 focus:outline-none focus:ring-1 focus:ring-blue-600 text-slate-900 dark:text-white shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                            class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:bg-white dark:focus:bg-slate-900 focus:outline-none focus:ring-1 focus:ring-blue-600 text-slate-900 dark:text-white shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Konfirmasi Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" required minlength="6" placeholder="Ulangi password baru"
                            class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:bg-white dark:focus:bg-slate-900 focus:outline-none focus:ring-1 focus:ring-blue-600 text-slate-900 dark:text-white shadow-2xs">
                    </div>

                    <div class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/70 dark:border-amber-900 text-[10.5px] text-amber-900 dark:text-amber-200 space-y-1">
                        <p class="font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Catatan Keamanan:</span>
                        </p>
                        <p class="leading-relaxed">
                            Password awal default sistem adalah tanggal lahir Anda (format: <code>YYYY-MM-DD</code>). Segera ubah demi privasi.
                        </p>
                    </div>

                    <button type="submit"
                        class="w-full py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-900 active:bg-slate-950 text-white font-semibold text-xs shadow-xs transition-colors flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        <span>Perbarui Password</span>
                    </button>
                </form>
            </div>

            <!-- Card Info Kualifikasi & Penempatan -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-5 shadow-2xs space-y-3">
                <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                    Kualifikasi & Penempatan
                </h3>

                <div>
                    <p class="text-[11px] text-slate-400 mb-1.5">Kualifikasi Jenjang Mengajar:</p>
                    <div class="flex items-center gap-1.5 flex-wrap">
                        @if($guru && !empty($guru->jenjang_list))
                            @foreach($guru->jenjang_list as $jj)
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 text-blue-900 dark:bg-blue-950 dark:text-blue-200 border border-blue-200 dark:border-blue-800">
                                    {{ $jj === 'SMA_SMK' ? 'SMA / SMK' : $jj }}
                                </span>
                            @endforeach
                        @else
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 text-blue-900 border border-blue-200">
                                {{ $guru->jenjang ?? 'SD' }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                    <p class="text-[11px] text-slate-400 mb-1">Cabang Penugasan:</p>
                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>{{ $currentCabang['nama'] ?? 'Cabang Ponokawan' }}</span>
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal 2 MB.');
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById('avatar-preview-container');
                const img = document.getElementById('avatar-preview-img');
                const placeholder = document.getElementById('avatar-preview-placeholder');
                const btnHapus = document.getElementById('btn-hapus-foto');
                const hapusInput = document.getElementById('hapus-foto-input');
                const filenameText = document.getElementById('foto-filename');

                if (container) {
                    const shimmer = container.querySelector('.skeleton-shimmer');
                    if (shimmer) shimmer.remove();
                    container.classList.remove('hidden');
                }
                if (img) {
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    img.classList.remove('opacity-0');
                }
                if (placeholder) placeholder.classList.add('hidden');
                if (btnHapus) btnHapus.classList.remove('hidden');
                if (hapusInput) hapusInput.value = '0';
                if (filenameText) filenameText.textContent = file.name;
            };
            reader.readAsDataURL(file);
        }
    }

    function removeAvatar() {
        const fileInput = document.getElementById('foto-input');
        const hapusInput = document.getElementById('hapus-foto-input');
        const container = document.getElementById('avatar-preview-container');
        const img = document.getElementById('avatar-preview-img');
        const placeholder = document.getElementById('avatar-preview-placeholder');
        const btnHapus = document.getElementById('btn-hapus-foto');
        const filenameText = document.getElementById('foto-filename');

        if (fileInput) fileInput.value = '';
        if (hapusInput) hapusInput.value = '1';
        if (img) {
            img.src = '';
            img.classList.add('hidden');
        }
        if (container) container.classList.add('hidden');
        if (placeholder) placeholder.classList.remove('hidden');
        if (btnHapus) btnHapus.classList.add('hidden');
        if (filenameText) filenameText.textContent = 'Foto akan dihapus saat disimpan';
    }
</script>
@endsection
