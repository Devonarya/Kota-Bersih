<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Wilayah bertingkat: kabupaten > kecamatan > desa > banjar.
     *
     * Sebelumnya desa cuma kolom string bebas di tabel banjars, jadi kabupaten
     * dan kecamatan tidak tercatat sama sekali. Tabel banjars sengaja tidak
     * dibuat ulang supaya id-nya tetap dan foreign key dari users maupun
     * waste_deposits tidak perlu ikut disentuh.
     */
    public function up(): void
    {
        Schema::create('kabupatens', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('kecamatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kabupaten_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            // Nama kecamatan hanya perlu unik di dalam kabupatennya sendiri.
            $table->unique(['kabupaten_id', 'name']);
        });

        Schema::create('desas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->unique(['kecamatan_id', 'name']);
        });

        // Nullable di level DB (wajibnya diurus validasi). Mengubah kolom jadi
        // NOT NULL di SQLite butuh membangun ulang tabel, tidak sepadan.
        Schema::table('banjars', function (Blueprint $table) {
            $table->foreignId('desa_id')->nullable()->after('name')->constrained()->nullOnDelete();
        });

        $this->pindahkanDesaLama();

        Schema::table('banjars', function (Blueprint $table) {
            $table->dropColumn('desa');
        });
    }

    public function down(): void
    {
        Schema::table('banjars', function (Blueprint $table) {
            $table->string('desa')->nullable()->after('name');
        });

        // Kembalikan nama desa ke kolom string supaya rollback tidak kehilangan data.
        foreach (DB::table('desas')->get() as $desa) {
            DB::table('banjars')->where('desa_id', $desa->id)->update(['desa' => $desa->name]);
        }

        Schema::table('banjars', function (Blueprint $table) {
            $table->dropForeign(['desa_id']);
            $table->dropColumn('desa_id');
        });

        Schema::dropIfExists('desas');
        Schema::dropIfExists('kecamatans');
        Schema::dropIfExists('kabupatens');
    }

    /**
     * Naikkan setiap nilai unik kolom string `desa` jadi baris tabel desas.
     *
     * Semua data awal berasal dari satu wilayah pilot, jadi kabupaten dan
     * kecamatannya di-hardcode. Banjar yang kolom desa-nya kosong dibiarkan
     * tanpa desa_id, biar admin yang menempatkan lewat halaman wilayah.
     */
    private function pindahkanDesaLama(): void
    {
        $namaDesa = DB::table('banjars')
            ->whereNotNull('desa')
            ->where('desa', '!=', '')
            ->distinct()
            ->pluck('desa');

        if ($namaDesa->isEmpty()) {
            return;
        }

        $sekarang = now();

        $kabupatenId = DB::table('kabupatens')->insertGetId([
            'name' => 'Jembrana',
            'created_at' => $sekarang,
            'updated_at' => $sekarang,
        ]);

        $kecamatanId = DB::table('kecamatans')->insertGetId([
            'kabupaten_id' => $kabupatenId,
            'name' => 'Jembrana',
            'created_at' => $sekarang,
            'updated_at' => $sekarang,
        ]);

        foreach ($namaDesa as $nama) {
            $desaId = DB::table('desas')->insertGetId([
                'kecamatan_id' => $kecamatanId,
                'name' => $nama,
                'created_at' => $sekarang,
                'updated_at' => $sekarang,
            ]);

            DB::table('banjars')->where('desa', $nama)->update(['desa_id' => $desaId]);
        }
    }
};
