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
     *
     * Jenis sampah berupa daftar (array) supaya sekalian jadi contoh permintaan
     * dengan lebih dari satu jenis sekaligus.
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
            [
                'jenis' => ['organik', 'plastik'],
                'berat' => 4.5,
                'status' => 'selesai',
                'hari' => 0,
                'lokasi' => 'Tempat sampah ada di samping pagar rumah.',
            ],
            [
                'jenis' => ['kertas'],
                'berat' => 3.0,
                'status' => 'diterima',
                'hari' => 1,
                'lokasi' => null,
            ],
            [
                'jenis' => ['b3'],
                'berat' => 0.75,
                'status' => 'pending',
                'hari' => 2,
                'lokasi' => 'Kardus kecil, dititip di teras depan.',
            ],
            [
                'jenis' => ['organik'],
                'berat' => 5.2,
                'status' => 'selesai',
                'hari' => 6,
                'lokasi' => null,
            ],
            [
                'jenis' => ['plastik', 'kertas'],
                'berat' => 1.8,
                'status' => 'ditolak',
                'hari' => 9,
                'lokasi' => null,
            ],
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

                $deposit = WasteDeposit::firstOrCreate([
                    'user_id' => $pemilik->id,
                    'deposited_on' => $tanggal->toDateString(),
                ], [
                    'banjar_id' => $pemilik->banjar_id,
                    'detail_lokasi' => $data['lokasi'],
                    'berat_kg' => $data['berat'],
                    'status' => $data['status'],
                    'pengangkut_id' => $penanggungJawab?->id,
                    'scheduled_date' => $tanggal->toDateString(),
                    'scheduled_time_slot' => $urutan % 2 === 0 ? '08:00 - 10:00' : '13:00 - 15:00',
                ]);

                if ($deposit->wasRecentlyCreated) {
                    $deposit->types()->createMany(
                        collect($data['jenis'])->map(fn (string $jenis) => ['jenis_sampah' => $jenis])->all()
                    );
                }
            }
        }
    }
}
