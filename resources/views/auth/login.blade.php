<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk Sistem — LKP Sang Siroju Lillah</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    @fonts

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();

        function toggleDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }
    </script>
</head>

<body class="h-full font-sans antialiased bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100 transition-colors duration-300 flex flex-col justify-between selection:bg-blue-100 selection:text-blue-900">

    <!-- Top Bar: Brand & Dark Mode Toggle -->
    <header class="w-full max-w-6xl mx-auto px-4 py-4 sm:py-6 flex items-center justify-between z-10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center justify-center p-1">
                <img src="{{ asset('logoo.png') }}" alt="Logo LKP Sang Siroju Lillah" class="w-full h-full object-contain">
            </div>
            <div>
                <h1 class="text-sm font-bold tracking-tight text-slate-900 dark:text-white leading-tight">Sang Siroju Lillah</h1>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Sistem Manajemen Bimbel Multi-Cabang</p>
            </div>
        </div>

        <button type="button" onclick="toggleDarkMode()"
            class="p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-2xs"
            title="Ganti Tema (Gelap/Terang)">
            <!-- Sun icon -->
            <svg class="w-5 h-5 hidden dark:block text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <!-- Moon icon -->
            <svg class="w-5 h-5 block dark:hidden text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>
    </header>

    <!-- Main Container -->
    <main class="w-full max-w-md mx-auto px-4 py-6 sm:py-8 my-auto">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/90 dark:border-slate-800 shadow-xl relative overflow-hidden">
            <!-- Subtle Header Accent -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-900 dark:text-blue-300 border border-blue-200/60 dark:border-blue-900/40 mb-3">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    Otentikasi Aman SIMANLES
                </div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Selamat Datang</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Masukkan Email atau NIP untuk mengakses portal</p>
            </div>

            <!-- Flash Error / Success -->
            @if(session('success'))
                <div class="mb-4 p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Identifier (Email / NIP) -->
                <div>
                    <label for="identifier" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Email atau NIP
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="text" name="identifier" id="identifier" value="{{ old('identifier', 'admin@lespintar.id') }}" required autofocus
                            placeholder="contoh: admin@lespintar.id / NIP-PNK-001"
                            class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Kata Sandi
                        </label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" name="password" id="password" value="admin123" required
                            placeholder="••••••••"
                            class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all">
                        <button type="button" onclick="togglePasswordVisibility()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg id="eye-icon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600 dark:text-slate-400">
                        <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-blue-900 focus:ring-blue-600">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full mt-2 py-3 rounded-xl bg-blue-900 hover:bg-blue-800 active:bg-blue-950 text-white font-bold text-sm tracking-wide shadow-md transition-all flex items-center justify-center gap-2">
                    <span>Masuk ke Sistem</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Demo Accounts Quick Select for Academic Evaluator -->
        <div class="mt-6">
            <div class="flex items-center justify-between mb-3 px-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Akses Cepat Pengujian (Demo):</span>
                <span class="text-[10px] text-slate-400">Klik kartu untuk isi otomatis</span>
            </div>
            <div class="grid grid-cols-1 gap-2">
                @foreach($demoAccounts as $acc)
                    <div onclick="selectDemoAccount('{{ $acc['email'] }}', '{{ $acc['password'] }}')"
                        class="p-3 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 hover:border-blue-400 dark:hover:border-blue-500 cursor-pointer transition-all shadow-2xs hover:shadow-xs group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-2 h-2 rounded-full {{ $acc['role'] === 'pusat' ? 'bg-indigo-600' : ($acc['role'] === 'cabang' ? 'bg-emerald-500' : 'bg-amber-500') }} shrink-0"></span>
                                <div class="truncate">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        {{ $acc['title'] }}
                                    </p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate font-mono">
                                        {{ $acc['email'] }} <span class="text-slate-400 dark:text-slate-500">({{ $acc['nip'] }})</span> · Password: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $acc['password'] }}</span>
                                    </p>
                                </div>
                            </div>
                            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full {{ $acc['role'] === 'pusat' ? 'bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300' : ($acc['role'] === 'cabang' ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300' : 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300') }} shrink-0">
                                {{ $acc['role'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full py-4 text-center text-xs text-slate-400 dark:text-slate-600">
        &copy; 2026 LKP Sang Siroju Lillah — Sistem Informasi Manajemen Bimbel Multi-Cabang
    </footer>

    <script>
        function togglePasswordVisibility() {
            const pwd = document.getElementById('password');
            if (pwd.type === 'password') {
                pwd.type = 'text';
            } else {
                pwd.type = 'password';
            }
        }

        function selectDemoAccount(identifier, password) {
            const idInput = document.getElementById('identifier');
            const pwdInput = document.getElementById('password');
            idInput.value = identifier;
            pwdInput.value = password;

            idInput.focus();
            idInput.classList.add('ring-2', 'ring-blue-500');
            pwdInput.classList.add('ring-2', 'ring-blue-500');
            setTimeout(() => {
                idInput.classList.remove('ring-2', 'ring-blue-500');
                pwdInput.classList.remove('ring-2', 'ring-blue-500');
            }, 600);
        }
    </script>
</body>

</html>

