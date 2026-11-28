<?php

namespace App\Http\Controllers;

use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir login.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->role === 'guru'
                ? redirect()->route('jadwal.index')
                : redirect()->route('admin.dashboard');
        }

        // Demo accounts for academic evaluation
        $demoAccounts = [
            [
                'role' => 'pusat',
                'title' => 'Admin Pusat',
                'email' => 'admin@lespintar.id',
                'nip' => 'NIP-PST-001',
                'password' => 'admin123',
                'desc' => 'Akses penuh seluruh cabang, tarif & audit log',
            ],
            [
                'role' => 'cabang',
                'title' => 'Pimpinan Cabang Ponokawan',
                'email' => 'cabang.ponokawan@lespintar.id',
                'nip' => 'NIP-CAB-001',
                'password' => 'cabang123',
                'desc' => 'Kelola operasional, siswa, guru & kas cabang',
            ],
            [
                'role' => 'guru',
                'title' => 'Pengajar / Guru (Bu Divani)',
                'email' => 'nip3101@lespintar.id',
                'nip' => 'NIP-3101',
                'password' => '1995-05-14',
                'desc' => 'Portal jadwal mengajar & scan presensi mandiri',
            ],
        ];

        return view('auth.login', compact('demoAccounts'));
    }

    /**
     * Proses otentikasi login pengguna via Email atau NIP / No Pegawai.
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ], [
            'identifier.required' => 'Email atau NIP/No Pegawai wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $identifier = trim($validated['identifier']);
        $password = $validated['password'];

        $user = User::where('email', $identifier)
            ->orWhere('no_pegawai', $identifier)
            ->first();

        // Fallback demo aliases for smooth evaluation
        if (! $user) {
            $normalized = strtolower($identifier);
            if (in_array($normalized, ['divani@lespintar.id', 'nip-pnk-001', 'divani', 'budivani', 'bu divani'], true)) {
                $user = User::where('email', 'nip3101@lespintar.id')
                    ->orWhere('no_pegawai', 'NIP-3101')
                    ->first();
            } elseif (in_array($normalized, ['admin@lespintar.id', 'nip-pst-001', 'admin'], true)) {
                $user = User::where('email', 'admin@lespintar.id')->first();
            } elseif (in_array($normalized, ['cabang.ponokawan@lespintar.id', 'nip-cab-001', 'cabang'], true)) {
                $user = User::where('email', 'cabang.ponokawan@lespintar.id')->first();
            }
        }

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => 'Email / NIP atau password yang Anda masukkan salah.',
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'identifier' => 'Akun ini sedang dinonaktifkan. Silakan hubungi Administrator.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        AuditService::log('Login Pengguna', "Pengguna {$user->name} ({$user->role}) berhasil masuk ke sistem.", $user);

        $redirectUrl = $this->resolvePostLoginUrl($request, $user);

        if ($user->role === 'guru') {
            return redirect($redirectUrl)->with('success', "Selamat datang, {$user->name}!");
        }

        return redirect($redirectUrl)->with('success', "Selamat datang di Sistem Informasi Manajemen Les, {$user->name}!");
    }

    /**
     * Resolve URL tujuan setelah login agar url.intended lintas role tidak memicu 403.
     */
    private function resolvePostLoginUrl(Request $request, User $user): string
    {
        $defaultUrl = $user->role === 'guru'
            ? route('jadwal.index')
            : route('admin.dashboard');

        $intendedUrl = $request->session()->pull('url.intended');
        if (! is_string($intendedUrl) || $intendedUrl === '') {
            return $defaultUrl;
        }

        $parsed = parse_url($intendedUrl);
        if ($parsed === false) {
            return $defaultUrl;
        }

        if (isset($parsed['host']) && strcasecmp($parsed['host'], $request->getHost()) !== 0) {
            return $defaultUrl;
        }

        $path = $parsed['path'] ?? '/';
        $query = isset($parsed['query']) ? '?'.$parsed['query'] : '';

        try {
            $route = Route::getRoutes()->match(Request::create($path.$query, 'GET'));
        } catch (Throwable) {
            return $defaultUrl;
        }

        foreach ($route->gatherMiddleware() as $middleware) {
            if (! is_string($middleware)) {
                continue;
            }

            $roleList = null;
            if (str_starts_with($middleware, 'role:')) {
                $roleList = substr($middleware, 5);
            } elseif (str_starts_with($middleware, RoleMiddleware::class.':')) {
                $roleList = substr($middleware, strlen(RoleMiddleware::class) + 1);
            }

            if ($roleList !== null) {
                $allowedRoles = explode(',', $roleList);
                if (! in_array($user->role, $allowedRoles, true)) {
                    return $defaultUrl;
                }
            }
        }

        return $intendedUrl;
    }

    /**
     * Proses keluar (logout).
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            AuditService::log('Logout Pengguna', "Pengguna {$user->name} keluar dari sistem.", $user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
