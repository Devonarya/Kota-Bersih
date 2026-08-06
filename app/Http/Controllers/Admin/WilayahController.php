<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use Illuminate\Contracts\View\View;

/**
 * Penelusuran tiga level teratas wilayah: kabupaten, kecamatan, desa.
 *
 * Sengaja read-only. Ketiganya berasal dari data resmi Kemendagri yang diisi
 * lewat `php artisan wilayah:impor`, jadi admin tidak boleh menambah, mengubah,
 * atau menghapusnya dari sini — perubahan manual akan tertimpa saat impor ulang.
 * Yang bisa dikelola admin cuma banjar (lihat BanjarController), karena banjar
 * satuan adat dan memang tidak ada di data pemerintah.
 */
class WilayahController extends Controller
{
    public function kabupatenIndex(): View
    {
        return view('admin.wilayah', [
            'judul' => 'Wilayah',
            'keterangan' => 'Data kabupaten, kecamatan, dan desa mengikuti Kemendagri. Banjar dikelola di level terdalam.',
            'labelLevel' => 'Kabupaten',
            'labelAnak' => 'kecamatan',
            'remah' => [],
            'items' => Kabupaten::withCount('kecamatans')
                ->orderBy('name')
                ->get()
                ->map(fn (Kabupaten $item) => [
                    'nama' => $item->name,
                    'kode' => $item->code,
                    'jumlahAnak' => $item->kecamatans_count,
                    'urlBuka' => route('admin.wilayah.kabupaten', $item),
                ])
                ->all(),
        ]);
    }

    public function kecamatanIndex(Kabupaten $kabupaten): View
    {
        return view('admin.wilayah', [
            'judul' => $kabupaten->name,
            'keterangan' => 'Daftar kecamatan di kabupaten ini.',
            'labelLevel' => 'Kecamatan',
            'labelAnak' => 'desa',
            'remah' => [['label' => 'Wilayah', 'url' => route('admin.wilayah.index')]],
            'items' => $kabupaten->kecamatans()
                ->withCount('desas')
                ->orderBy('name')
                ->get()
                ->map(fn (Kecamatan $item) => [
                    'nama' => $item->name,
                    'kode' => $item->code,
                    'jumlahAnak' => $item->desas_count,
                    'urlBuka' => route('admin.wilayah.kecamatan', $item),
                ])
                ->all(),
        ]);
    }

    public function desaIndex(Kecamatan $kecamatan): View
    {
        $kecamatan->load('kabupaten');

        return view('admin.wilayah', [
            'judul' => $kecamatan->name,
            'keterangan' => 'Daftar desa di kecamatan ini.',
            'labelLevel' => 'Desa',
            'labelAnak' => 'banjar',
            'remah' => [
                ['label' => 'Wilayah', 'url' => route('admin.wilayah.index')],
                ['label' => $kecamatan->kabupaten->name, 'url' => route('admin.wilayah.kabupaten', $kecamatan->kabupaten)],
            ],
            'items' => $kecamatan->desas()
                ->withCount('banjars')
                ->orderBy('name')
                ->get()
                ->map(fn (Desa $item) => [
                    'nama' => $item->name,
                    'kode' => $item->code,
                    'jumlahAnak' => $item->banjars_count,
                    'urlBuka' => route('admin.wilayah.desa', $item),
                ])
                ->all(),
        ]);
    }
}
