<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Buat satu akun admin supaya dashboard admin bisa diakses.
     *
     * Aman dijalankan berkali-kali: kalau emailnya sudah ada, tidak dibuat ulang.
     * Ganti passwordnya lewat menu Profil setelah login pertama.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@kotabersih.id'],
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
                // Admin tidak lewat alur persetujuan, jadi langsung ditandai disetujui
                // supaya tidak ikut terhitung sebagai pendaftar yang menunggu.
                'membership_status' => 'disetujui',
                'reviewed_at' => now(),
            ],
        );

        $this->command?->info($admin->wasRecentlyCreated
            ? 'Akun admin dibuat: admin@kotabersih.id / admin12345'
            : 'Akun admin sudah ada, dilewati: admin@kotabersih.id');
    }
}
