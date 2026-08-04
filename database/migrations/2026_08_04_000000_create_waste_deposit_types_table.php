<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Satu permintaan pengambilan boleh berisi beberapa jenis sampah sekaligus,
     * jadi jenisnya dipecah ke tabel sendiri alih-alih satu kolom enum di
     * waste_deposits.
     */
    public function up(): void
    {
        Schema::create('waste_deposit_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waste_deposit_id')->constrained()->cascadeOnDelete();
            $table->enum('jenis_sampah', ['organik', 'plastik', 'kertas', 'b3']);
            $table->timestamps();

            $table->unique(['waste_deposit_id', 'jenis_sampah']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_deposit_types');
    }
};
