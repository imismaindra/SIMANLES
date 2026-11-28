@extends('layouts.admin')

@section('title', 'Profil Pengajar & Keamanan Akun — LKP Sang Siroju Lillah')

@section('content')
@php
    $headerAvatarUrl = $guru?->foto_url ?? $user->foto_url;
    $profilFotoUrl = $headerAvatarUrl;
    $tglLahirRaw = $guru?->tanggal_lahir ?? $user?->tanggal_lahir;
    $tglLahirVal = $tglLahirRaw ? (is_string($tglLahirRaw) ? substr($tglLahirRaw, 0, 10) : $tglLahirRaw->format('Y-m-d')) : '';
    $nipDisplay = $guru->no_pegawai ?: ($user->no_pegawai ?: 'NIP-3101');
    $openProfilSection = $errors->hasAny(['nama', 'no_telp', 'alamat', 'foto']);
    $openPasswordSection = $errors->hasAny(['current_password', 'password', 'password_confirmation']);
@endphp

<div class="space-y-5 max-w-xl mx-auto pb-8">

    <!-- Centered Profile Hero (Tanpa tombol kembali ke jadwal) -->
    <div class="pt-2 pb-1 text-center flex flex-col items-center">
        <form id="form-avatar-upload" method="POST" action="{{ route('guru.profil.update') }}" enctype="multipart/form-data" class="hidden">
            @csrf
            @method('PUT')
            <input type="hidden" name="nama" value="{{ $guru->nama ?? $user->name }}">
            <input type="hidden" name="no_telp" value="{{ $guru->no_telp ?? '' }}">
            <input type="hidden" name="alamat" value="{{ $guru->alamat ?? '' }}">
            <input type="file" name="foto" id="foto-input" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="submitAvatarFile(this)">
            <input type="hidden" name="hapus_foto" id="hapus-foto-input" value="0">
        </form>

        <button type="button" onclick="document.getElementById('foto-input').click()"
            title="Ketuk untuk ganti foto profil"
            class="relative group cursor-pointer focus:outline-none">
            @if($headerAvatarUrl)
                <div id="hero-avatar-container" class="relative overflow-hidden w-24 h-24 rounded-full shrink-0 shadow-md shadow-blue-950/15 border-2 border-white dark:border-slate-800 bg-slate-200 dark:bg-slate-800">
                    <div class="skeleton-shimmer absolute inset-0"></div>
                    <img id="hero-avatar-img" src="{{ $headerAvatarUrl }}" alt="{{ $guru->nama ?? $user->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover relative z-10 opacity-0 transition-opacity duration-300" onload="this.classList.remove('opacity-0'); this.previousElementSibling?.remove();" onerror="this.parentElement.style.display='none'; this.parentElement.nextElementSibling?.classList.remove('hidden');">
                </div>
                <div id="hero-avatar-placeholder" class="hidden w-24 h-24 rounded-full bg-blue-900 text-white font-bold text-2xl flex items-center justify-center shrink-0 shadow-md shadow-blue-950/15 border-2 border-white dark:border-slate-800">
                    {{ strtoupper(substr($guru->nama ?? $user->name, 0, 2)) }}
                </div>
            @else
                <div id="hero-avatar-container" class="hidden relative overflow-hidden w-24 h-24 rounded-full shrink-0 shadow-md shadow-blue-950/15 border-2 border-white dark:border-slate-800 bg-slate-200 dark:bg-slate-800">
                    <img id="hero-avatar-img" src="" alt="{{ $guru->nama ?? $user->name }}" class="w-full h-full object-cover">
                </div>
                <div id="hero-avatar-placeholder" class="w-24 h-24 rounded-full bg-blue-900 text-white font-bold text-2xl flex items-center justify-center shrink-0 shadow-md shadow-blue-950/15 border-2 border-white dark:border-slate-800 group-hover:bg-blue-800 transition-colors">
                    {{ strtoupper(substr($guru->nama ?? $user->name, 0, 2)) }}
                </div>
            @endif

            <span class="absolute bottom-0 right-0 w-7 h-7 rounded-full bg-blue-900 group-hover:bg-blue-800 text-white flex items-center justify-center shadow-sm border-2 border-white dark:border-slate-900 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </span>
        </button>

        @if($headerAvatarUrl)
            <button type="button" onclick="deleteAvatarNow()"
                class="mt-1.5 text-[11px] font-semibold text-slate-400 hover:text-rose-600 transition-colors cursor-pointer">
                Hapus Foto
            </button>
        @endif

        <h1 class="mt-3 text-xl font-bold tracking-tight text-slate-900 dark:text-white">
            {{ $guru->nama ?? $user->name }}
        </h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            {{ $user->email }} | {{ $guru->no_telp ?: '-' }}
        </p>
        <span class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200/70 dark:border-emerald-900">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            Guru Pengajar Aktif
        </span>
    </div>

    <!-- Detail Akun Card (Style referensi dengan warna SIMANLES) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl p-5 shadow-2xs space-y-4">
        <div class="flex items-center justify-between gap-2">
            <span class="text-sm font-bold text-slate-900 dark:text-white">Detail Akun</span>
            <span class="text-xs font-semibold text-blue-900 dark:text-blue-400">
                {{ $currentCabang['nama'] ?? 'Cabang Ponokawan' }}
            </span>
        </div>

        <div class="flex items-center justify-between gap-2">
            <p class="text-sm font-medium text-slate-700 dark:text-slate-300 truncate">
                {{ $guru->nama ?? $user->name }} <span class="font-mono text-slate-500">({{ $nipDisplay }})</span>
            </p>
            <button type="button" onclick="toggleProfileSection('section-edit-profil')"
                class="p-1.5 rounded-lg text-slate-400 hover:text-blue-900 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shrink-0"
                title="Edit Profil">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </button>
        </div>

        <div class="grid grid-cols-2 gap-4 pt-1 border-t border-slate-100 dark:border-slate-800">
            <div>
                <p class="text-[11px] text-slate-400">Tanggal Lahir</p>
                <p class="text-sm font-bold text-slate-900 dark:text-white font-mono mt-0.5">
                    {{ $tglLahirVal ?: '-' }}
                </p>
            </div>
            <div>
                <p class="text-[11px] text-slate-400">Kualifikasi Jenjang</p>
                <div class="flex items-center gap-1 flex-wrap mt-1">
                    @if($guru && !empty($guru->jenjang_list))
                        @foreach($guru->jenjang_list as $jj)
                            <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-blue-50 text-blue-900 dark:bg-blue-950 dark:text-blue-200 border border-blue-200 dark:border-blue-800">
                                {{ $jj === 'SMA_SMK' ? 'SMA / SMK' : $jj }}
                            </span>
                        @endforeach
                    @else
                        <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-blue-50 text-blue-900 border border-blue-200">
                            {{ $guru->jenjang ?? 'SD' }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Menu List Cards -->
    <div class="space-y-3">

        <!-- Item 1: Informasi Profil & Kontak -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl shadow-2xs overflow-hidden">
            <button type="button" onclick="toggleProfileSection('section-edit-profil')"
                class="w-full p-4 flex items-center justify-between gap-3 text-left hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors cursor-pointer">
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-900 dark:text-blue-300 flex items-center justify-center shrink-0 border border-blue-100 dark:border-blue-900">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Informasi Profil & Kontak</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">Perbarui nama lengkap, nomor WhatsApp, dan alamat.</p>
                    </div>
                </div>
                <svg id="chevron-section-edit-profil" class="w-4 h-4 text-slate-400 shrink-0 transition-transform {{ $openProfilSection ? 'rotate-90' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <div id="section-edit-profil" class="{{ $openProfilSection ? '' : 'hidden' }} px-4 pb-5 pt-2 border-t border-slate-100 dark:border-slate-800">
                <form method="POST" action="{{ route('guru.profil.update') }}" class="space-y-4 mt-2">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
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
                            <input type="text" value="{{ $nipDisplay }}" readonly disabled
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

                        <!-- Tanggal Lahir (Read-only) -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Tanggal Lahir</label>
                                <span class="text-[10px] text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-1.5 py-0.2 rounded font-medium border border-amber-200/60 dark:border-amber-900">
                                    Hanya lihat
                                </span>
                            </div>
                            <input type="date" value="{{ $tglLahirVal }}" readonly tabindex="-1"
                                class="w-full px-3 py-2 text-xs rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 cursor-not-allowed select-none pointer-events-none focus:outline-none">
                        </div>

                        <!-- No WhatsApp / HP -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                No. WhatsApp / HP <span class="text-rose-500">*</span>
                            </label>
                            <input type="tel" name="no_telp" required placeholder="Contoh: 081234567890"
                                inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                value="{{ old('no_telp', $guru->no_telp ?? '') }}"
                                class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:bg-white dark:focus:bg-slate-900 focus:outline-none focus:ring-1 focus:ring-blue-600 text-slate-900 dark:text-white shadow-2xs font-mono">
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

                    <div class="flex items-center justify-end pt-2">
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-900 hover:bg-blue-800 active:bg-blue-950 text-white font-semibold text-xs shadow-xs transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Perubahan Profil</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Item 2: Ganti Password Akun -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl shadow-2xs overflow-hidden">
            <button type="button" onclick="toggleProfileSection('section-ganti-password')"
                class="w-full p-4 flex items-center justify-between gap-3 text-left hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors cursor-pointer">
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-10 h-10 rounded-full bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 flex items-center justify-center shrink-0 border border-amber-100 dark:border-amber-900">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Ganti Password Akun</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">Ubah password login Anda dari sini.</p>
                    </div>
                </div>
                <svg id="chevron-section-ganti-password" class="w-4 h-4 text-slate-400 shrink-0 transition-transform {{ $openPasswordSection ? 'rotate-90' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <div id="section-ganti-password" class="{{ $openPasswordSection ? '' : 'hidden' }} px-4 pb-5 pt-2 border-t border-slate-100 dark:border-slate-800">
                <form method="POST" action="{{ route('guru.profil.password') }}" class="space-y-3.5 mt-2">
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
                        class="w-full py-2.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-900 active:bg-slate-950 text-white font-semibold text-xs shadow-xs transition-colors flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        <span>Perbarui Password</span>
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>

<script>
    function toggleProfileSection(sectionId) {
        const section = document.getElementById(sectionId);
        const chevron = document.getElementById('chevron-' + sectionId);
        if (!section) return;

        const isHidden = section.classList.contains('hidden');
        section.classList.toggle('hidden', !isHidden);
        if (chevron) {
            chevron.classList.toggle('rotate-90', isHidden);
        }
    }
    function submitAvatarFile(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2 MB.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const container = document.getElementById('hero-avatar-container');
            const img = document.getElementById('hero-avatar-img');
            const placeholder = document.getElementById('hero-avatar-placeholder');
            if (container) {
                container.querySelector('.skeleton-shimmer')?.remove();
                container.classList.remove('hidden');
            }
            if (img) {
                img.src = e.target.result;
                img.classList.remove('hidden', 'opacity-0');
            }
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);

        const hapusInput = document.getElementById('hapus-foto-input');
        if (hapusInput) hapusInput.value = '0';
        document.getElementById('form-avatar-upload')?.submit();
    }

    function deleteAvatarNow() {
        const fileInput = document.getElementById('foto-input');
        const hapusInput = document.getElementById('hapus-foto-input');
        if (fileInput) fileInput.value = '';
        if (hapusInput) hapusInput.value = '1';
        document.getElementById('form-avatar-upload')?.submit();
    }
</script>
@endsection
