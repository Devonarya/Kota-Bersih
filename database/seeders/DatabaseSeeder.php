<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Urutannya penting: banjar dulu, lalu akun, baru setoran yang menunjuk keduanya.
     * Semua seeder aman dijalankan berulang kali.
     */
    public function run(): void
    {
        $this->call([
            BanjarSeeder::class,
            AdminUserSeeder::class,
            MemberSeeder::class,
            WasteDepositSeeder::class,
        ]);
    }
}
