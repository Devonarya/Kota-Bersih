<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('membership_status', 20)->default('menunggu')->after('role');
            $table->string('review_note')->nullable()->after('membership_status');
            $table->timestamp('reviewed_at')->nullable()->after('review_note');
        });

        // Akun yang sudah ada sebelum fitur persetujuan dianggap sudah disetujui,
        // supaya tidak tiba-tiba menumpuk di daftar permintaan admin.
        DB::table('users')->update([
            'membership_status' => 'disetujui',
            'reviewed_at' => DB::raw('created_at'),
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['membership_status', 'review_note', 'reviewed_at']);
        });
    }
};
