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
        Schema::create('waste_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('banjar_id')->constrained()->cascadeOnDelete();
            $table->text('detail_lokasi')->nullable();
            $table->decimal('berat_kg', 8, 2)->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'selesai'])->default('selesai');
            $table->foreignId('pengangkut_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('scheduled_date')->nullable();
            $table->string('scheduled_time_slot')->nullable();
            $table->date('deposited_on');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_deposits');
    }
};
