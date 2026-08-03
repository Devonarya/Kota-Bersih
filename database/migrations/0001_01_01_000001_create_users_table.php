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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Sengaja string, bukan enum: mengubah daftar nilai enum di SQLite
            // butuh membangun ulang tabel. Nilai yang dipakai: warga, pengangkut, admin.
            $table->string('role')->default('warga');

            // Status persetujuan pendaftaran oleh admin: menunggu, disetujui, ditolak.
            // review_note menyimpan alasan penolakan yang ditulis admin.
            $table->string('membership_status', 20)->default('menunggu');
            $table->string('review_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->foreignId('banjar_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();

            // Hanya diisi pengangkut; warga tidak diminta nomor KTP.
            $table->string('ktp_number', 16)->nullable();

            $table->string('avatar_path')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
