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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['warga', 'pengankut', 'admin'])->default('warga')->after('password');
            $table->foreignId('banjar_id')->nullable()->after('role')->constarined()->nullOnDelete();
            $table->string('phone')->nullable()->after('bajar_id');
            $table->string('address')->nullable()->after('phone');
            $table->string('avatar_path')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('banjar_id');
            $table->dropColumn(['role', 'phone', 'address', 'avatar_path']);
        });
    }
};
