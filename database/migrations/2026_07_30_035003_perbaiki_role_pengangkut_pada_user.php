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
            $table->string('role_baru')->default('warga')->after('password');
        });

        DB::table('users')->update(['role_baru' => DB::raw('role')]);
        DB::table('users')->where('role_baru', 'pengankut')->update(['role_baru' => 'pengangkut']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('role_baru', 'role');
        });
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'pengangkut')->update(['role' => 'warga']);
    }
};