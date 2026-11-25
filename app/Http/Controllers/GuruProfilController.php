<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Services\AuditService;
use App\Services\CabangContextService;
use App\Services\ImageOptimizerService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class GuruProfilController extends Controller
{
    /**
     * Tampilkan halaman profil dan edit data guru.
     */
    public function index(Request $request, CabangContextService $contextService): View
    {
        $context = $contextService->getContext($request);
        $user = Auth::user();

        // Cari relasi guru dengan fallback NIP / Email
        $guru = $user->guru ?? Guru::where('user_id', $user->id)
            ->orWhere('no_pegawai', $user->no_pegawai)
            ->orWhere('email', $user->email)
            ->first();

        $currentCabang = $context['currentCabang'];
        $isGuru = true;

        return view('guru.profil', compact('user', 'guru', 'currentCabang', 'context', 'isGuru'));
    }

    /**
     * Simpan pembaruan data profil guru (nama, nomor WA angka, alamat).
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $guru = $user->guru ?? Guru::where('user_id', $user->id)
            ->orWhere('no_pegawai', $user->no_pegawai)
            ->orWhere('email', $user->email)
            ->first();

        // Sanitasi input nomor telepon agar murni angka
        if ($request->filled('no_telp')) {
            $request->merge(['no_telp' => preg_replace('/[^0-9]/', '', (string) $request->input('no_telp'))]);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string|regex:/^[0-9]+$/|min:8|max:20',
            'alamat' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'hapus_foto' => 'nullable|boolean',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'no_telp.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'no_telp.regex' => 'Nomor WhatsApp / HP hanya boleh berupa angka.',
            'no_telp.min' => 'Nomor WhatsApp / HP minimal 8 digit angka.',
            'no_telp.max' => 'Nomor WhatsApp / HP maksimal 20 digit angka.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $fotoPath = $user->foto ?? $guru?->foto;

        if ($request->boolean('hapus_foto')) {
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = null;
        } elseif ($request->hasFile('foto')) {
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = ImageOptimizerService::optimizeAndStore($request->file('foto'), 'profil');
        }

        $user->update([
            'name' => $validated['nama'],
            'foto' => $fotoPath,
        ]);

        if ($guru) {
            $guru->update([
                'nama' => $validated['nama'],
                'no_telp' => $validated['no_telp'],
                'alamat' => $validated['alamat'] ?? $guru->alamat,
                'foto' => $fotoPath,
            ]);
        }

        AuditService::log(
            'Update Profil Guru Mandiri',
            "Pengajar {$user->name} memperbarui data profil secara mandiri.",
            $user
        );

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Proses ubah password akun guru.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed|different:current_password',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.different' => 'Password baru harus berbeda dengan password lama Anda.',
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password saat ini yang Anda masukkan salah.',
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        AuditService::log(
            'Ganti Password Guru Mandiri',
            "Pengajar {$user->name} berhasil mengubah password akun.",
            $user
        );

        return back()->with('success', 'Password Anda berhasil diperbarui. Silakan gunakan password baru untuk login berikutnya.');
    }
}
