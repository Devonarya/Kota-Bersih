<?php

namespace App\Console\Commands;

use App\Models\Desa;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Mengisi kabupaten/kecamatan/desa dari data resmi Kemendagri lewat wilayah.id
 * (API JSON statis tanpa API key, sumbernya repo cahyadsn/wilayah, lisensi MIT).
 *
 * Datanya diimpor ke tabel sendiri, bukan dipanggil saat form dibuka, karena
 * banjars.desa_id itu foreign key yang butuh baris nyata di database kita dan
 * form pendaftaran tidak boleh ikut mati kalau layanan itu sedang bermasalah.
 *
 * Banjar tidak ikut diimpor: banjar satuan adat, tidak ada di data pemerintah
 * mana pun. Itu tetap diisi admin lewat halaman Wilayah.
 *
 * Induk tiap baris tidak perlu disimpan terpisah karena sudah tersirat di
 * kodenya: 51.01.05.1005 (desa) -> 51.01.05 (kecamatan) -> 51.01 (kabupaten).
 */
class ImporWilayah extends Command
{
    protected $signature = 'wilayah:impor
        {--provinsi=51 : Kode provinsi Kemendagri (51 = Bali)}
        {--dari= : Impor dari berkas JSON hasil database/data/unduh-wilayah.py, bukan dari jaringan}
        {--max-kecamatan= : Batas jumlah kecamatan per kabupaten}
        {--max-desa= : Batas jumlah desa per kecamatan}';

    protected $description = 'Impor kabupaten, kecamatan, dan desa dari wilayah.id (data Kemendagri)';

    private const BASIS = 'https://wilayah.id/api';

    public function handle(): int
    {
        try {
            $data = $this->option('dari')
                ? $this->dariBerkas((string) $this->option('dari'))
                : $this->dariJaringan((string) $this->option('provinsi'));
        } catch (ConnectionException $e) {
            $this->error('Gagal menghubungi wilayah.id: '.$e->getMessage());
            $this->line('Kalau ini galat TLS, PHP di mesin ini belum punya CA bundle (curl.cainfo kosong).');
            $this->line('Alternatifnya unduh dulu lalu impor dari berkas:');
            $this->line('  python database/data/unduh-wilayah.py 51 database/data/wilayah-bali.json');
            $this->line('  php artisan wilayah:impor --dari=database/data/wilayah-bali.json');

            return self::FAILURE;
        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return $this->simpan($data);
    }

    /**
     * @param  array{kabupaten: array<int, array<string, string>>, kecamatan: array<int, array<string, string>>, desa: array<int, array<string, string>>}  $data
     */
    private function simpan(array $data): int
    {
        $maxKecamatan = $this->batas('max-kecamatan');
        $maxDesa = $this->batas('max-desa');

        $idKabupaten = [];
        foreach ($data['kabupaten'] as $baris) {
            $idKabupaten[$baris['code']] = Kabupaten::updateOrCreate(
                ['code' => $baris['code']],
                ['name' => $baris['name']],
            )->id;
        }

        $idKecamatan = [];
        $terpakaiKecamatan = [];
        foreach ($data['kecamatan'] as $baris) {
            $kodeInduk = $this->kodeInduk($baris['code']);

            if (! isset($idKabupaten[$kodeInduk])) {
                continue;
            }

            $terpakaiKecamatan[$kodeInduk] = ($terpakaiKecamatan[$kodeInduk] ?? 0) + 1;

            if ($maxKecamatan !== null && $terpakaiKecamatan[$kodeInduk] > $maxKecamatan) {
                continue;
            }

            $idKecamatan[$baris['code']] = Kecamatan::updateOrCreate(
                ['code' => $baris['code']],
                ['kabupaten_id' => $idKabupaten[$kodeInduk], 'name' => $baris['name']],
            )->id;
        }

        $terpakaiDesa = [];
        $jumlahDesa = 0;
        foreach ($data['desa'] as $baris) {
            $kodeInduk = $this->kodeInduk($baris['code']);

            if (! isset($idKecamatan[$kodeInduk])) {
                continue;
            }

            $terpakaiDesa[$kodeInduk] = ($terpakaiDesa[$kodeInduk] ?? 0) + 1;

            if ($maxDesa !== null && $terpakaiDesa[$kodeInduk] > $maxDesa) {
                continue;
            }

            Desa::updateOrCreate(
                ['code' => $baris['code']],
                ['kecamatan_id' => $idKecamatan[$kodeInduk], 'name' => $baris['name']],
            );
            $jumlahDesa++;
        }

        $this->info(sprintf(
            'Selesai: %d kabupaten, %d kecamatan, %d desa.',
            count($idKabupaten),
            count($idKecamatan),
            $jumlahDesa,
        ));

        return self::SUCCESS;
    }

    /**
     * @return array{kabupaten: array<int, array<string, string>>, kecamatan: array<int, array<string, string>>, desa: array<int, array<string, string>>}
     */
    private function dariBerkas(string $jalur): array
    {
        if (! is_file($jalur)) {
            throw new \RuntimeException("Berkas tidak ditemukan: {$jalur}");
        }

        $isi = json_decode((string) file_get_contents($jalur), true);

        if (! is_array($isi) || ! isset($isi['kabupaten'], $isi['kecamatan'], $isi['desa'])) {
            throw new \RuntimeException("Isi {$jalur} tidak sesuai format unduh-wilayah.py.");
        }

        $this->info("Mengimpor dari berkas {$jalur} …");

        return $isi;
    }

    /**
     * @return array{kabupaten: array<int, array<string, string>>, kecamatan: array<int, array<string, string>>, desa: array<int, array<string, string>>}
     */
    private function dariJaringan(string $provinsi): array
    {
        $this->info("Mengimpor wilayah provinsi {$provinsi} dari wilayah.id …");

        $data = ['kabupaten' => $this->ambil("regencies/{$provinsi}"), 'kecamatan' => [], 'desa' => []];

        if ($data['kabupaten'] === []) {
            throw new \RuntimeException("Tidak ada kabupaten untuk kode provinsi {$provinsi}.");
        }

        foreach ($data['kabupaten'] as $kabupaten) {
            $kecamatans = $this->ambil("districts/{$kabupaten['code']}");
            $data['kecamatan'] = [...$data['kecamatan'], ...$kecamatans];

            foreach ($kecamatans as $kecamatan) {
                $data['desa'] = [...$data['desa'], ...$this->ambil("villages/{$kecamatan['code']}")];
            }

            $this->line("  {$kabupaten['name']} selesai.");
        }

        return $data;
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function ambil(string $jalur): array
    {
        $response = Http::timeout(20)->retry(3, 500)->get(self::BASIS."/{$jalur}.json");

        // Kecamatan/desa yang kosong dijawab 404, itu bukan kegagalan impor.
        if ($response->status() === 404) {
            return [];
        }

        $response->throw();

        return $response->json('data', []);
    }

    /**
     * 51.01.05.1005 -> 51.01.05, dan 51.01.05 -> 51.01.
     */
    private function kodeInduk(string $kode): string
    {
        $bagian = explode('.', $kode);
        array_pop($bagian);

        return implode('.', $bagian);
    }

    private function batas(string $opsi): ?int
    {
        $nilai = $this->option($opsi);

        return $nilai === null || $nilai === '' ? null : max(1, (int) $nilai);
    }
}
