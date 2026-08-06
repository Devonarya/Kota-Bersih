<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banjar;
use App\Models\Desa;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Level terdalam hierarki wilayah. Dipisah dari WilayahController karena banjar
 * punya logo, jumlah keluarga, dan deskripsi yang tidak dimiliki level di atasnya.
 */
class BanjarController extends Controller
{
    public function index(Desa $desa): View
    {
        $desa->load('kecamatan.kabupaten');

        return view('admin.banjar', [
            'desa' => $desa,
            'remah' => [
                ['label' => 'Wilayah', 'url' => route('admin.wilayah.index')],
                ['label' => $desa->kecamatan->kabupaten->name, 'url' => route('admin.wilayah.kabupaten', $desa->kecamatan->kabupaten)],
                ['label' => $desa->kecamatan->name, 'url' => route('admin.wilayah.kecamatan', $desa->kecamatan)],
            ],
            'banjars' => $desa->banjars()
                ->withCount(['users', 'wasteDeposits'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request, Desa $desa): RedirectResponse
    {
        $validated = $this->validasi($request);

        $desa->banjars()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'family_count' => $validated['family_count'],
            ...$request->hasFile('logo')
                ? ['logo_path' => $request->file('logo')->store('banjar', 'public')]
                : [],
        ]);

        return back()->with('status', "{$validated['name']} berhasil ditambahkan.");
    }

    public function update(Request $request, Banjar $banjar): RedirectResponse
    {
        $validated = $this->validasi($request, $banjar);

        $logoLama = $banjar->logo_path;

        $banjar->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'family_count' => $validated['family_count'],
            ...$request->hasFile('logo')
                ? ['logo_path' => $request->file('logo')->store('banjar', 'public')]
                : [],
        ]);

        // Logo lama baru dihapus setelah yang baru tersimpan, supaya kalau
        // penyimpanan gagal banjar tidak berakhir tanpa logo sama sekali.
        if ($request->hasFile('logo') && $logoLama) {
            Storage::disk('public')->delete($logoLama);
        }

        return back()->with('status', "{$banjar->name} berhasil diperbarui.");
    }

    public function destroy(Banjar $banjar): RedirectResponse
    {
        $banjar->loadCount(['users', 'wasteDeposits']);

        // Menghapus banjar akan ikut menghapus seluruh setoran miliknya (cascade)
        // dan melepas banjar dari anggotanya, jadi selama masih terpakai ditolak.
        if ($banjar->users_count > 0 || $banjar->waste_deposits_count > 0) {
            $penghalang = [];

            if ($banjar->users_count > 0) {
                $penghalang[] = "{$banjar->users_count} anggota";
            }

            if ($banjar->waste_deposits_count > 0) {
                $penghalang[] = "{$banjar->waste_deposits_count} riwayat setoran";
            }

            return back()->withErrors([
                'hapus' => "{$banjar->name} masih dipakai ".implode(' dan ', $penghalang)
                    .'. Pindahkan dulu sebelum banjar ini bisa dihapus.',
            ]);
        }

        $logo = $banjar->logo_path;
        $nama = $banjar->name;

        $banjar->delete();

        if ($logo) {
            Storage::disk('public')->delete($logo);
        }

        return back()->with('status', "{$nama} berhasil dihapus.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validasi(Request $request, ?Banjar $banjar = null): array
    {
        $unik = Rule::unique('banjars', 'name');

        return $request->validate([
            'name' => ['required', 'string', 'max:255', $banjar ? $unik->ignore($banjar) : $unik],
            'description' => ['nullable', 'string', 'max:1000'],
            'family_count' => ['required', 'integer', 'min:0'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.unique' => 'Sudah ada banjar dengan nama itu.',
            'family_count.min' => 'Jumlah keluarga tidak boleh negatif.',
            'logo.image' => 'Logo banjar harus berupa file gambar.',
            'logo.max' => 'Ukuran logo banjar maksimal 2 MB.',
        ]);
    }
}
