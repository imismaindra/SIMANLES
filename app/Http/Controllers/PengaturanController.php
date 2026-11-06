<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Setting;
use App\Models\TarifSpp;
use App\Services\AuditService;
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
        $currentCabang = $context['currentCabang'];

        return view('admin.pengaturan.index', compact(
            'cabangs',
            'tarifSpps',
            'biayaPendaftaran',
            'currentCabang',
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

        AuditService::log(
            'Update Tarif SPP & Pendaftaran',
            'Pimpinan Pusat memperbarui daftar nominal tarif SPP dan biaya pendaftaran (Rp '.number_format($validated['biaya_pendaftaran'], 0, ',', '.').').'
        );

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

        $cabang = Cabang::create([
            'id' => (string) Str::uuid(),
            'nama' => $validated['nama'],
            'kode' => strtoupper($validated['kode']),
            'alamat' => $validated['alamat'] ?? '',
            'status' => 'aktif',
        ]);

        AuditService::log(
            'Tambah Cabang Baru',
            "Membuka cabang baru {$cabang->nama} (Kode: {$cabang->kode})."
        );

        return back()->with('success', "Cabang {$validated['nama']} berhasil ditambahkan.");
    }

    /**
     * Perbarui data rincian cabang.
     */
    public function updateCabang(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'alamat' => 'nullable|string',
            'status' => 'nullable|in:aktif,nonaktif',
        ]);

        $cabang = Cabang::findOrFail($id);
        $cabang->update([
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'] ?? $cabang->alamat,
            'status' => $validated['status'] ?? $cabang->status,
        ]);

        AuditService::log(
            'Update Data Cabang',
            "Memperbarui data cabang {$cabang->nama} (Status: {$cabang->status})."
        );

        return back()->with('success', "Data cabang {$cabang->nama} berhasil diperbarui.");
    }

    /**
     * Toggle status aktif/nonaktif cabang.
     */
    public function toggleCabangStatus(string $id): RedirectResponse
    {
        $cabang = Cabang::findOrFail($id);
        $newStatus = $cabang->status === 'aktif' ? 'nonaktif' : 'aktif';
        $cabang->update(['status' => $newStatus]);

        AuditService::log(
            'Ubah Status Cabang',
            "Mengubah status cabang {$cabang->nama} menjadi {$newStatus}."
        );

        return back()->with('success', "Status cabang {$cabang->nama} diubah menjadi {$newStatus}.");
    }
}
