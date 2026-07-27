<?php

namespace Database\Seeders;

use App\Models\Banjar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        collect([
            ['name' => 'Banjar Tegal Sari', 'desa' => 'Desa Dauhwaru', 'family_count' => 142],
            ['name' => 'Banjar Pande Mas', 'desa' => 'Desa Dauhwaru', 'family_count' => 98],
            ['name' => 'Banjar Kertha Wangi', 'desa' => 'Desa Dauhwaru', 'family_count' => 76],
        ])->each(fn(array $banjar) => Banjar::create($banjar));
    }
}
