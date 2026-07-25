<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('depo_sampah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('banjar_id')->constrained()->cascadeOnDelete();
            $table->enum('jenis_sampah', ['organik', 'anorganik', 'Kaca', 'b3']);
            $table->text(keterangan)->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'selesai'])->default('selesai');
            $table->foreignId('pengangku_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('scheduled_date')->nullable();
            $table->string('schedule_time_slot')->nullable();
            $table->date('deposisted_on');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depo_sampah');
    }
};
