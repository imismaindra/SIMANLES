@extends('layouts.admin')

@section('title', 'Audit Log & Keamanan Akses')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200 dark:border-slate-800">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-sky-50 dark:bg-sky-950/50 text-sky-800 dark:text-sky-300 border border-sky-200/60 dark:border-sky-800/40 mb-2">
                <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                Security & System Audit (KF-30 / KNF-01)
            </div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                <svg class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Audit Log & Keamanan Akses
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Catatan riwayat aktivitas penting sistem (Otentikasi login, CRUD siswa & guru, pembayaran SPP, perubahan cabang & tarif).
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs font-mono font-bold px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                Total: {{ number_format($totalLogs) }} Entri
            </span>
        </div>
    </div>

    <!-- Security Metric Cards (Sesuai Konsep AuditLogView Asal) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Password Hash -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center gap-3.5">
            <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <div>
                <span class="text-[10px] uppercase font-mono tracking-wider text-slate-400">Password Hashing</span>
                <p class="text-sm font-bold text-slate-900 dark:text-white">Bcrypt Hash (KNF-01)</p>
                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">Tervalidasi Aktif</span>
            </div>
        </div>

        <!-- Card 2: Isolasi Cabang -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center gap-3.5">
            <div class="p-3 rounded-xl bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div>
                <span class="text-[10px] uppercase font-mono tracking-wider text-slate-400">Isolasi Cabang</span>
                <p class="text-sm font-bold text-slate-900 dark:text-white">Role & Context Middleware</p>
                <span class="text-[10px] text-sky-600 dark:text-sky-400 font-semibold">Tervalidasi Aktif</span>
            </div>
        </div>

        <!-- Card 3: Soft Delete -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center gap-3.5">
            <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <div>
                <span class="text-[10px] uppercase font-mono tracking-wider text-slate-400">Integritas Data</span>
                <p class="text-sm font-bold text-slate-900 dark:text-white">Soft Delete Siswa (KNF-08)</p>
                <span class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold">Riwayat Finansial Utuh</span>
            </div>
        </div>

        <!-- Card 4: Hari Ini -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center gap-3.5">
            <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <span class="text-[10px] uppercase font-mono tracking-wider text-slate-400">Aktivitas Hari Ini</span>
                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $todayLogs }} Event Tercatat</p>
                <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-semibold">{{ $totalUsersLogged }} Akun Aktif</span>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
        <form method="GET" action="{{ route('audit.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama pengguna, aksi, atau rincian..."
                    class="w-full pl-9 pr-3.5 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-blue-600">
            </div>

            <div class="w-full sm:w-48">
                <select name="role" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <option value="">Semua Peran</option>
                    <option value="pusat" {{ request('role') === 'pusat' ? 'selected' : '' }}>Admin Pusat</option>
                    <option value="cabang" {{ request('role') === 'cabang' ? 'selected' : '' }}>Admin Cabang</option>
                    <option value="guru" {{ request('role') === 'guru' ? 'selected' : '' }}>Guru</option>
                </select>
            </div>

            <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-blue-900 text-white text-xs font-bold hover:bg-blue-800 transition-colors shadow-2xs">
                Filter
            </button>
            @if(request()->hasAny(['q', 'role']))
                <a href="{{ route('audit.index') }}" class="w-full sm:w-auto px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-medium text-center hover:bg-slate-200 transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Riwayat Aktivitas & Event Sistem</h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Semua aksi perubahan data sensitif tercatat secara immutable</p>
            </div>
            <span class="text-[10px] font-mono font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                AUDIT IMMUTABLE
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-950 text-slate-500 dark:text-slate-400 uppercase font-mono text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="p-3.5">Waktu</th>
                        <th class="p-3.5">Pengguna</th>
                        <th class="p-3.5">Aksi / Event</th>
                        <th class="p-3.5">Rincian Perubahan</th>
                        <th class="p-3.5">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="p-3.5 font-mono text-[11px] text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i:s') }} WIB
                            </td>
                            <td class="p-3.5 whitespace-nowrap">
                                <div class="font-bold text-slate-900 dark:text-white text-xs">{{ $log->user_nama }}</div>
                                <span class="text-[9px] uppercase font-bold px-1.5 py-0.2 rounded {{ $log->role === 'pusat' ? 'bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300' : ($log->role === 'cabang' ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300' : 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300') }}">
                                    {{ $log->role }}
                                </span>
                            </td>
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="font-mono font-bold text-[11px] px-2.5 py-1 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-200/60 dark:border-sky-800/40">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="p-3.5 text-xs text-slate-600 dark:text-slate-300 leading-relaxed max-w-md">
                                {{ $log->details }}
                            </td>
                            <td class="p-3.5 font-mono text-[11px] text-slate-400 whitespace-nowrap">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 text-xs">
                                Belum ada riwayat audit log yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

