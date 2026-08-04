<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banjar;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BanjarController extends Controller
{
    public function index(): View
    {
        return view('admin.banjar', [
            'banjars' => Banjar::withCount(['users', 'wasteDeposits'])->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Banjar $banjar): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('banjars', 'name')->ignore($banjar)],
            'desa' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'family_count' => ['required', 'integer', 'min:0'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.unique' => 'Sudah ada banjar dengan nama itu.',
            'family_count.min' => 'Jumlah keluarga tidak boleh negatif.',
            'logo.image' => 'Logo banjar harus berupa file gambar.',
            'logo.max' => 'Ukuran logo banjar maksimal 2 MB.',
        ]);

        $logoLama = $banjar->logo_path;

        $banjar->update([
            'name' => $validated['name'],
            'desa' => $validated['desa'] ?? null,
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
}
