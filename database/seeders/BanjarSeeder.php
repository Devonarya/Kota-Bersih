<?php

namespace Database\Seeders;

use App\Models\Banjar;
use Illuminate\Database\Seeder;

class BanjarSeeder extends Seeder
{
    /**
     * Tiga banjar yang dipakai seluruh data dummy. Dijalankan paling awal
     * karena user dan setoran menunjuk ke sini.
     */
    public function run(): void
    {
        collect([
            ['name' => 'Banjar Tegal Sari', 'desa' => 'Desa Dauhwaru', 'family_count' => 142],
            ['name' => 'Banjar Pande Mas', 'desa' => 'Desa Dauhwaru', 'family_count' => 98],
            ['name' => 'Banjar Kertha Wangi', 'desa' => 'Desa Dauhwaru', 'family_count' => 76],
        ])->each(fn (array $banjar) => Banjar::firstOrCreate(['name' => $banjar['name']], $banjar));
    }
}
