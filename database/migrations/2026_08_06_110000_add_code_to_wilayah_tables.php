<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kode wilayah resmi Kemendagri, mis. 51.01 (Jembrana), 51.01.05 (Kec.
     * Jembrana), 51.01.05.1005 (Dauhwaru).
     *
     * Dipakai sebagai kunci pencocokan oleh command wilayah:impor supaya impor
     * ulang memperbarui baris yang sama, bukan menumpuk duplikat. Nullable
     * karena banjar tidak punya kode resmi dan data lama boleh belum berkode.
     */
    public function up(): void
    {
        foreach (['kabupatens', 'kecamatans', 'desas'] as $tabel) {
            Schema::table($tabel, function (Blueprint $table) {
                $table->string('code', 20)->nullable()->unique()->after('id');
            });
        }
    }

    public function down(): void
    {
        foreach (['kabupatens', 'kecamatans', 'desas'] as $tabel) {
            Schema::table($tabel, function (Blueprint $table) use ($tabel) {
                // Index unique dibuang eksplisit; SQLite tidak ikut menghapusnya
                // otomatis saat kolomnya di-drop.
                $table->dropUnique($tabel.'_code_unique');
                $table->dropColumn('code');
            });
        }
    }
};
