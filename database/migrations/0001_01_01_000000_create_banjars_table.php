<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Banjar dibuat paling awal karena users, waste_deposits, dan news
     * semuanya menyimpan foreign key ke tabel ini.
     */
    public function up(): void
    {
        Schema::create('banjars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('desa')->nullable();
            $table->text('description')->nullable();

            // Logo dipakai bersama seluruh anggota banjar, bukan milik satu pendaftar.
            $table->string('logo_path')->nullable();

            $table->unsignedInteger('family_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banjars');
    }
};
