<?php

namespace Database\Seeders;

use App\Models\Banjar;
use App\Models\Kabupaten;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    /**
     * Wilayah pilot beserta tiga banjar yang dipakai seluruh data dummy.
     *
     * Kabupaten/kecamatan/desa di sini memakai kode Kemendagri asli, jadi kalau
     * nanti `php artisan wilayah:impor` dijalankan, barisnya dikenali lewat kode
     * dan ikut diperbarui — bukan diduplikasi. Seeder sengaja tidak memanggil
     * jaringan supaya migrate:fresh --seed tetap jalan offline.
     *
     * Banjar tidak ada di data Kemendagri (satuan adat), jadi memang cuma bisa
     * datang dari sini atau dari input admin.
     */
    public function run(): void
    {
        $kabupaten = Kabupaten::updateOrCreate(
            ['code' => '51.01'],
            ['name' => 'Kabupaten Jembrana'],
        );

        $kecamatan = $kabupaten->kecamatans()->updateOrCreate(
            ['code' => '51.01.05'],
            ['name' => 'Jembrana'],
        );

        $desa = $kecamatan->desas()->updateOrCreate(
            ['code' => '51.01.05.1005'],
            ['name' => 'Dauhwaru'],
        );

        collect([
            ['name' => 'Banjar Tegal Sari', 'family_count' => 142],
            ['name' => 'Banjar Pande Mas', 'family_count' => 98],
            ['name' => 'Banjar Kertha Wangi', 'family_count' => 76],
        ])->each(fn (array $banjar) => Banjar::firstOrCreate(
            ['name' => $banjar['name']],
            [...$banjar, 'desa_id' => $desa->id],
        ));
    }
}
