<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WasteDeposit;
use Illuminate\Database\Seeder;

class WasteDepositSeeder extends Seeder
{
    /**
     * Setoran dummy untuk warga yang sudah disetujui. Sebagian sengaja dibiarkan
     * berstatus pending/diterima supaya halaman Pengangkut ada yang bisa diproses,
     * dan sebagian bertanggal hari ini supaya angka di dashboard admin tidak nol.
     */
    public function run(): void
    {
        $warga = User::where('role', 'warga')
            ->where('membership_status', 'disetujui')
            ->get();

        $pengangkut = User::where('role', 'pengangkut')
            ->where('membership_status', 'disetujui')
            ->get()
            ->keyBy('banjar_id');

        if ($warga->isEmpty()) {
            return;
        }

        $contoh = [
            ['jenis' => 'organik', 'berat' => 4.5, 'status' => 'selesai', 'hari' => 0],
            ['jenis' => 'plastik', 'berat' => 2.25, 'status' => 'selesai', 'hari' => 0],
            ['jenis' => 'kertas', 'berat' => 3.0, 'status' => 'diterima', 'hari' => 1],
            ['jenis' => 'b3', 'berat' => 0.75, 'status' => 'pending', 'hari' => 2],
            ['jenis' => 'organik', 'berat' => 5.2, 'status' => 'selesai', 'hari' => 6],
            ['jenis' => 'plastik', 'berat' => 1.8, 'status' => 'ditolak', 'hari' => 9],
        ];

        // Contoh di lapis luar supaya setoran terbaru tersebar ke beberapa warga,
        // bukan menumpuk pada satu orang di daftar "Permintaan Terbaru" dashboard.
        foreach ($contoh as $urutan => $data) {
            foreach ($warga as $index => $pemilik) {
                // Digeser per warga supaya tanggalnya tidak menumpuk di hari yang sama.
                $tanggal = now()->subDays($data['hari'] + $index)->startOfDay();

                // Hanya setoran yang sudah diproses yang punya pengangkut.
                $penanggungJawab = in_array($data['status'], ['diterima', 'selesai'], true)
                    ? $pengangkut->get($pemilik->banjar_id)
                    : null;

                WasteDeposit::firstOrCreate([
                    'user_id' => $pemilik->id,
                    'jenis_sampah' => $data['jenis'],
                    'deposited_on' => $tanggal->toDateString(),
                ], [
                    'banjar_id' => $pemilik->banjar_id,
                    'berat_kg' => $data['berat'],
                    'status' => $data['status'],
                    'pengangkut_id' => $penanggungJawab?->id,
                    'scheduled_date' => $tanggal->toDateString(),
                    'scheduled_time_slot' => $urutan % 2 === 0 ? '08:00 - 10:00' : '13:00 - 15:00',
                    'keterangan' => null,
                ]);
            }
        }
    }
}
