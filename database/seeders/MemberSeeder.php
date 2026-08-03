<?php

namespace Database\Seeders;

use App\Models\Banjar;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    /**
     * Warga & pengangkut dummy dengan status persetujuan yang beragam, supaya
     * halaman Permintaan Anggota punya isi di ketiga tab sekaligus.
     *
     * Semua akun memakai kata sandi yang sama: password123
     */
    public const PASSWORD = 'password123';

    public function run(): void
    {
        $banjar = Banjar::pluck('id', 'name');

        $anggota = [
            // Sudah disetujui — ini yang bisa login dan dipakai fitur setoran.
            [
                'name' => 'Wayan Suradnya', 'email' => 'wayan.suradnya@example.test',
                'role' => 'warga', 'banjar' => 'Banjar Kertha Wangi',
                'phone' => '081234567890', 'address' => 'Jl. Merta Sari No. 14, dekat Pura Dalem',
                'membership_status' => 'disetujui', 'daftar' => 40,
            ],
            [
                'name' => 'Kadek Ayu Lestari', 'email' => 'kadek.ayu@example.test',
                'role' => 'warga', 'banjar' => 'Banjar Pande Mas',
                'phone' => '083811229900', 'address' => 'Jl. Kelod Kauh No. 9',
                'membership_status' => 'disetujui', 'daftar' => 38,
            ],
            [
                'name' => 'Ketut Darma', 'email' => 'ketut.darma@example.test',
                'role' => 'pengangkut', 'banjar' => 'Banjar Kertha Wangi',
                'phone' => '082144337789', 'ktp_number' => '5171019876540003',
                'membership_status' => 'disetujui', 'daftar' => 35,
            ],
            [
                'name' => 'I Ketut Sudana', 'email' => 'ketut.sudana@example.test',
                'role' => 'pengangkut', 'banjar' => 'Banjar Tegal Sari',
                'phone' => '081999887766', 'ktp_number' => '5171011122330004',
                'membership_status' => 'disetujui', 'daftar' => 33,
            ],

            // Masih menunggu — inilah yang muncul dengan tombol Setujui / Tolak.
            [
                'name' => 'Made Ariani', 'email' => 'made.ariani@example.test',
                'role' => 'pengangkut', 'banjar' => 'Banjar Pande Mas',
                'phone' => '081322114456', 'ktp_number' => '5171012345670002',
                'membership_status' => 'menunggu', 'daftar' => 5,
            ],
            [
                'name' => 'Nyoman Sujana', 'email' => 'nyoman.sujana@example.test',
                'role' => 'warga', 'banjar' => 'Banjar Tegal Sari',
                'phone' => '085799881122', 'address' => 'Jl. Tegal Sari Gg. III No. 7',
                'membership_status' => 'menunggu', 'daftar' => 4,
            ],
            [
                'name' => 'Gede Mahendra', 'email' => 'gede.mahendra@example.test',
                'role' => 'warga', 'banjar' => 'Banjar Kertha Wangi',
                'phone' => '087812340098', 'address' => 'Jl. Kertha Wangi No. 21',
                'membership_status' => 'menunggu', 'daftar' => 2,
            ],

            // Pernah ditolak — dipakai mengecek tampilan catatan alasan.
            [
                'name' => 'Putu Wirawan', 'email' => 'putu.wirawan@example.test',
                'role' => 'warga', 'banjar' => 'Banjar Pande Mas',
                'phone' => '081955663344', 'address' => 'Jl. Sudirman No. 2',
                'membership_status' => 'ditolak', 'daftar' => 12,
                'review_note' => 'Alamat berada di luar wilayah Banjar Pande Mas.',
            ],
        ];

        foreach ($anggota as $data) {
            $didaftarkan = now()->subDays($data['daftar']);
            $sudahDitinjau = $data['membership_status'] !== 'menunggu';

            $user = User::firstOrCreate(['email' => $data['email']], [
                'name' => $data['name'],
                'password' => Hash::make(self::PASSWORD),
                'role' => $data['role'],
                'banjar_id' => $banjar[$data['banjar']] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'ktp_number' => $data['ktp_number'] ?? null,
                'membership_status' => $data['membership_status'],
                'review_note' => $data['review_note'] ?? null,
                // Ditinjau beberapa hari setelah mendaftar, bukan di hari yang sama.
                'reviewed_at' => $sudahDitinjau ? $didaftarkan->copy()->addDay() : null,
            ]);

            // created_at menentukan tanggal daftar yang tampil di daftar permintaan.
            if ($user->wasRecentlyCreated) {
                $user->forceFill(['created_at' => $didaftarkan])->save();
            }
        }
    }
}
