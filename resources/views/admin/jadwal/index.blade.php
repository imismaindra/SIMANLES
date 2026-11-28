@extends('layouts.admin')

@section('title', 'Jadwal Mengajar & Siswa Bimbingan — LKP Sang Siroju Lillah')

@section('content')
<div class="space-y-6 pb-6 lg:pb-0">

    <!-- Header & Action Row -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-6 shadow-2xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-200/70">
            <div>
                @if($isGuru)
                    @php
                        $jamSekarang = now()->hour;
                        $salamWaktu = match (true) {
                            $jamSekarang >= 4 && $jamSekarang < 11 => 'Selamat Pagi',
                            $jamSekarang >= 11 && $jamSekarang < 15 => 'Selamat Siang',
                            $jamSekarang >= 15 && $jamSekarang < 18 => 'Selamat Sore',
                            default => 'Selamat Malam',
                        };
                        $namaGuruSapaan = trim($loggedGuru->nama ?? auth()->user()->name);
                        $namaGuruSapaan = preg_match('/^(bu|ibu)\b/i', $namaGuruSapaan) ? $namaGuruSapaan : 'Bu ' . $namaGuruSapaan;
                    @endphp
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                        {{ $salamWaktu }}, {{ $namaGuruSapaan }}
                    </h1>
                @else
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                            Jadwal Mengajar & Siswa Bimbingan
                        </h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-900 border border-blue-200/70">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-700"></span>
                            {{ $currentCabang['nama'] }}
                        </span>
                    </div>
                @endif
            </div>

            @if(! $isGuru)
            <!-- Action Cluster -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <input type="text" id="search-jadwal-input" onkeyup="filterJadwalCards()" placeholder="Cari guru atau nama siswa..."
                        class="w-full pl-9 pr-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600 shadow-2xs placeholder:text-slate-400">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <button type="button" onclick="openModal('modal-tambah-jadwal')"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-900 hover:bg-blue-800 active:bg-blue-950 text-white font-semibold text-xs shadow-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    <span>+ Buat Sesi Jadwal</span>
                </button>
            </div>
            @endif
        </div>

        <!-- Stat Siswa Terbimbing -->
        <div class="mt-4">
            <div class="p-3.5 rounded-xl bg-blue-50/60 border border-blue-200/60 flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold text-blue-900 uppercase tracking-wider">Siswa Terbimbing</p>
                    <div class="mt-0.5 flex items-baseline gap-1.5">
                        <span class="text-xl sm:text-2xl font-bold text-blue-950 num-tabular">{{ $totalSiswaTerjadwal }}</span>
                        <span class="text-xs text-blue-700">anak</span>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Aktif
                </span>
            </div>
        </div>
    </div>

    <!-- Cards Grid: Setiap Guru & List Siswa yang Diajar -->
    @php
        $isSingleGuru = $isGuru || $jadwalPerGuru->count() === 1;
    @endphp

    <div id="jadwal-grid-container" class="{{ $isSingleGuru ? 'w-full' : 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4.5' }}">
        @forelse($jadwalPerGuru as $j)
            @php
                $guru = $isGuru ? ($loggedGuru ?? $j->guru) : $j->guru;
                $muridList = ($isGuru && isset($assignedStudents))
                    ? $assignedStudents
                    : ($guru ? $guru->siswa->where('status', 'aktif') : collect());
                if ($muridList->isEmpty() && $j->siswa) {
                    $muridList = collect([$j->siswa]);
                }
            @endphp
            <div data-search-target="{{ strtolower(($guru->nama ?? '').' '.$muridList->pluck('nama')->implode(' ')) }}"
                class="jadwal-card-item w-full bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-6 shadow-2xs flex flex-col justify-between space-y-4">
                
                <div>
                    @if(! $isGuru)
                    <!-- Top: Teacher Header (Avatar, Name, NIP & Status Kuota) -->
                    <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3 min-w-0">
                            @php
                                $cardFotoUrl = $guru?->foto_url ?? ($guru?->user?->foto_url ?? ($isGuru ? auth()->user()?->foto_url : null));
                            @endphp
                            @if($cardFotoUrl)
                                <div class="relative overflow-hidden w-11 h-11 rounded-xl shrink-0 border border-slate-200 dark:border-slate-700 shadow-xs bg-slate-200 dark:bg-slate-800">
                                    <div class="skeleton-shimmer absolute inset-0"></div>
                                    <img src="{{ $cardFotoUrl }}" alt="{{ $guru->nama ?? 'Guru Pengajar' }}" loading="lazy" decoding="async" class="w-full h-full object-cover relative z-10 opacity-0 transition-opacity duration-300" onload="this.classList.remove('opacity-0'); this.previousElementSibling?.remove();" onerror="this.parentElement.style.display='none'; this.parentElement.nextElementSibling?.classList.remove('hidden');">
                                </div>
                                <div class="hidden w-11 h-11 rounded-xl bg-blue-100 text-blue-900 font-bold text-base flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($guru->nama ?? 'GU', 0, 2)) }}
                                </div>
                            @else
                                <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-900 font-bold text-base flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($guru->nama ?? 'GU', 0, 2)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h3 class="font-bold text-slate-900 text-sm sm:text-base truncate">{{ $guru->nama ?? 'Guru Pengajar' }}</h3>
                                <span class="text-[10.5px] font-mono px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 font-bold">
                                    {{ $guru->no_pegawai ?: 'NIP-3101' }}
                                </span>
                            </div>
                        </div>

                        <!-- Status Badge (Tanpa Jam Sesi) -->
                        <span class="text-xs font-bold px-2.5 py-1.5 rounded-xl shrink-0 {{ $muridList->count() >= 6 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                            {{ $muridList->count() }}/6 Siswa
                        </span>
                    </div>
                    @endif

                    @if($isGuru)
                        <!-- Daftar Lengkap Siswa Bimbingan (Langsung Tampil untuk Guru) -->
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-blue-900 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <span>Daftar Siswa Bimbingan</span>
                                </h4>
                            </div>

                            @if($muridList->isNotEmpty())
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($muridList as $sw)
                                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 space-y-2 transition-all hover:border-blue-300 hover:shadow-xs">
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="font-bold text-slate-900 dark:text-white text-sm truncate">
                                                    {{ $sw->nama }}
                                                </div>
                                                <span class="shrink-0 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-blue-100 dark:bg-blue-950 text-blue-900 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                                    Kelas {{ $sw->kelas }}
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-300">
                                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.275.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z" />
                                                </svg>
                                                @if($sw->no_telp_ortu)
                                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sw->no_telp_ortu) }}" target="_blank"
                                                        class="font-mono text-emerald-700 dark:text-emerald-400 font-semibold hover:underline truncate"
                                                        title="Hubungi Orang Tua via WhatsApp">
                                                        {{ $sw->no_telp_ortu }}
                                                    </a>
                                                    @if($sw->nama_ibu)
                                                        <span class="text-[11px] text-slate-400 truncate">({{ $sw->nama_ibu }})</span>
                                                    @endif
                                                @else
                                                    <span class="font-mono text-slate-400">-</span>
                                                @endif
                                            </div>

                                            <div class="flex items-start gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span class="line-clamp-2 leading-relaxed" title="{{ $sw->alamat }}">
                                                    {{ $sw->alamat ?: 'Alamat belum diisi' }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="py-8 text-center rounded-xl border border-dashed border-slate-200 dark:border-slate-800 text-slate-400 text-xs italic">
                                    Belum ada siswa bimbingan dialokasikan untuk Anda.
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Trigger Popup Modal List Siswa -->
                        <button type="button" 
                            onclick="openModal('modal-siswa-{{ $j->id }}')" 
                            class="w-full mt-3 flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-slate-50 hover:bg-blue-50/90 active:bg-blue-100 text-slate-700 hover:text-blue-900 border border-slate-200/90 font-semibold text-xs transition-all shadow-2xs group cursor-pointer">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-900 group-hover:scale-110 transition-transform shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span>List Siswa ({{ $muridList->count() }})</span>
                            </span>
                            <span class="flex items-center gap-1 text-[11px] font-semibold text-blue-700 group-hover:underline">
                                <span>Lihat List</span>
                                <svg class="w-3.5 h-3.5 text-blue-600 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </button>
                    @endif
                </div>

                @if(! $isGuru)
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-400">
                    <span>{{ $j->cabang->nama ?? $currentCabang['nama'] }}</span>
                    <form method="POST" action="{{ route('web.jadwal.destroy', $j->id) }}" onsubmit="return confirm('Hapus sesi jadwal {{ $guru->nama ?? 'Guru' }}?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rose-500 hover:text-rose-700 font-semibold text-[11px] transition-colors" title="Hapus Jadwal Guru Ini">
                            Hapus Sesi
                        </button>
                    </form>
                </div>
                @endif
            </div>

            @if(! $isGuru)
            <!-- Popup Modal List Siswa -->
            <div id="modal-siswa-{{ $j->id }}" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-3 sm:p-4">
                <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden flex flex-col max-h-[85vh] animate-in fade-in duration-150" onclick="event.stopPropagation()">
                    <!-- Modal Header -->
                    <div class="px-5 py-4 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-800/50 shrink-0">
                        <div>
                            <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                                <span>Daftar Siswa Bimbingan</span>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-900 dark:bg-blue-950 dark:text-blue-300 font-bold">
                                    {{ $muridList->count() }} Siswa
                                </span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $guru->nama ?? 'Guru Pengajar' }} ({{ $guru->no_pegawai ?: 'NIP-3101' }})</p>
                        </div>
                        <button type="button" onclick="closeModal('modal-siswa-{{ $j->id }}')" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body (Scrollable List Siswa) -->
                    <div class="p-4 sm:p-5 overflow-y-auto space-y-2.5 flex-1">
                        @forelse($muridList as $sw)
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-700/60 space-y-2 transition-all hover:border-blue-300 hover:shadow-xs">
                                <!-- Nama Siswa & Kelas -->
                                <div class="flex items-center justify-between gap-2">
                                    <div class="font-bold text-slate-900 dark:text-white text-sm truncate">
                                        {{ $sw->nama }}
                                    </div>
                                    <span class="shrink-0 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-blue-100 dark:bg-blue-950 text-blue-900 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        Kelas {{ $sw->kelas }}
                                    </span>
                                </div>

                                <!-- Kontak WA Orang Tua -->
                                <div class="flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-300">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.275.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z" />
                                    </svg>
                                    @if($sw->no_telp_ortu)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sw->no_telp_ortu) }}" target="_blank"
                                            class="font-mono text-emerald-700 dark:text-emerald-400 font-semibold hover:underline truncate"
                                            title="Hubungi Orang Tua via WhatsApp">
                                            {{ $sw->no_telp_ortu }}
                                        </a>
                                        @if($sw->nama_ibu)
                                            <span class="text-[11px] text-slate-400">({{ $sw->nama_ibu }})</span>
                                        @endif
                                    @else
                                        <span class="font-mono text-slate-400">-</span>
                                    @endif
                                </div>

                                <!-- Alamat Lengkap -->
                                <div class="flex items-start gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="line-clamp-2 leading-relaxed" title="{{ $sw->alamat }}">
                                        {{ $sw->alamat ?: 'Alamat belum diisi' }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center rounded-xl border border-dashed border-slate-200 dark:border-slate-800 text-slate-400 text-xs italic">
                                Belum ada siswa bimbingan dialokasikan untuk pengajar ini.
                            </div>
                        @endforelse
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800 flex justify-end bg-slate-50/50 dark:bg-slate-800/30 shrink-0">
                        <button type="button" onclick="closeModal('modal-siswa-{{ $j->id }}')" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 font-semibold text-xs transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
            @endif
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-slate-200">
                <div class="w-12 h-12 mx-auto rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="font-bold text-slate-900 text-sm">Belum Ada Jadwal Mengajar</h3>
                <p class="text-xs text-slate-400 mt-1">Tambahkan sesi jadwal mengajar untuk guru pengajar.</p>
            </div>
        @endforelse
    </div>

</div>

@if(! $isGuru)
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
            <input type="hidden" name="hari" value="Senin">

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

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Mulai *</label>
                    <input type="time" name="jam_mulai" required value="18:00"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Selesai *</label>
                    <input type="time" name="jam_selesai" required value="20:00"
                        class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-600">
                </div>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-500">
                <p><strong>Info Sesi:</strong> Jadwal mengajar berlaku konsisten untuk 5 hari kerja (Senin s/d Jumat). Siswa bimbingan otomatis terhubung dari data alokasi guru.</p>
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

@endif

@push('scripts')
<script>
    function filterJadwalCards() {
        const query = (document.getElementById('search-jadwal-input')?.value || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.jadwal-card-item');

        cards.forEach(card => {
            const target = card.getAttribute('data-search-target') || '';
            if (!query || target.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endpush
@endsection
