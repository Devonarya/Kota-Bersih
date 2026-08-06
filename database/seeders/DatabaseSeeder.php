<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Urutannya penting: wilayah (sampai banjar) dulu, lalu akun, baru setoran
     * yang menunjuk keduanya. Semua seeder aman dijalankan berulang kali.
     */
    public function run(): void
    {
        $this->call([
            WilayahSeeder::class,
            AdminUserSeeder::class,
            MemberSeeder::class,
            WasteDepositSeeder::class,
        ]);
    }
}
