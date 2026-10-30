<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Setting;
use App\Models\TarifSpp;
use App\Services\CabangContextService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PengaturanController extends Controller
{
    /**
     * Halaman Master Data, Tarif SPP & Kelola Cabang.
     */
    public function index(Request $request, CabangContextService $contextService): View
    {
        $context = $contextService->getContext($request);

        $cabangs = Cabang::withCount(['siswa' => fn ($q) => $q->where('status', 'aktif')])
            ->withCount('guru')
            ->get();

        $tarifSpps = TarifSpp::all();
        $biayaPendaftaran = (float) Setting::get('biaya_pendaftaran', '100000');

        return view('admin.pengaturan.index', compact(
            'cabangs',
            'tarifSpps',
            'biayaPendaftaran',
            'context'
        ));
    }

    /**
     * Perbarui Master Tarif SPP & Biaya Pendaftaran.
     */
    public function updateTarif(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tarif' => 'required|array',
            'tarif.*' => 'numeric|min:0',
            'biaya_pendaftaran' => 'required|numeric|min:0',
        ]);

        foreach ($validated['tarif'] as $jenjang => $nominal) {
            TarifSpp::where('jenjang', $jenjang)->update(['nominal' => $nominal]);
        }

        Setting::set('biaya_pendaftaran', (string) $validated['biaya_pendaftaran']);

        return back()->with('success', 'Master tarif SPP dan biaya pendaftaran berhasil diperbarui.');
    }

    /**
     * Tambah Cabang Baru.
     */
    public function storeCabang(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'kode' => 'required|string|max:20|unique:cabang,kode',
            'alamat' => 'nullable|string',
        ]);

        Cabang::create([
            'id' => (string) Str::uuid(),
            'nama' => $validated['nama'],
            'kode' => strtoupper($validated['kode']),
            'alamat' => $validated['alamat'] ?? '',
            'status' => 'aktif',
        ]);

        return back()->with('success', "Cabang {$validated['nama']} berhasil ditambahkan.");
    }
}

