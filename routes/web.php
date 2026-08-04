<?php

use App\Http\Controllers\Admin\BanjarController as AdminBanjarController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MemberRequestController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BanjarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PengangkutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WasteDepositController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

// Detail berita bisa dibaca tanpa login supaya kartu di landing bisa dibuka tamu.
// Dibatasi angka agar tidak menyerobot /news/create yang ada di grup auth.
Route::get('/news/{news}', [NewsController::class, 'show'])->whereNumber('news')->name('news.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/permintaan', [MemberRequestController::class, 'index'])->name('permintaan.index');
        Route::patch('/permintaan/{user}/setujui', [MemberRequestController::class, 'approve'])->name('permintaan.approve');
        Route::patch('/permintaan/{user}/tolak', [MemberRequestController::class, 'reject'])->name('permintaan.reject');

        Route::get('/warga', [MemberController::class, 'warga'])->name('warga.index');
        Route::get('/pengangkut', [MemberController::class, 'pengangkut'])->name('pengangkut.index');

        Route::get('/berita', [AdminNewsController::class, 'index'])->name('berita.index');
        Route::patch('/berita/{news}/turunkan', [AdminNewsController::class, 'demote'])->name('berita.demote');
        Route::delete('/berita/{news}', [AdminNewsController::class, 'destroy'])->name('berita.destroy');

        Route::get('/banjar', [AdminBanjarController::class, 'index'])->name('banjar.index');
        Route::patch('/banjar/{banjar}', [AdminBanjarController::class, 'update'])->name('banjar.update');
        Route::delete('/banjar/{banjar}', [AdminBanjarController::class, 'destroy'])->name('banjar.destroy');
    });

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profil.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profil.update');
    Route::post('/profil/foto', [ProfileController::class, 'updateAvatar'])->name('profil.avatar');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profil.password');

    Route::middleware('role:warga')->group(function () {
        Route::get('/pengambilan', [WasteDepositController::class, 'pengambilan'])->name('pengambilan.index');
        Route::post('/pengambilan', [WasteDepositController::class, 'store'])->name('pengambilan.store');
        Route::get('/sampah', [WasteDepositController::class, 'index'])->name('sampah.index');
    });

    Route::middleware('role:pengangkut')->group(function () {
        Route::get('/pengangkut', [PengangkutController::class, 'index'])->name('pengangkut.index');
        Route::patch('/pengangkut/{deposit}/terima', [PengangkutController::class, 'accept'])->name('pengangkut.accept');
        Route::patch('/pengangkut/{deposit}/tolak', [PengangkutController::class, 'reject'])->name('pengangkut.reject');
        Route::patch('/pengangkut/{deposit}/selesai', [PengangkutController::class, 'complete'])->name('pengangkut.complete');
    });

    Route::get('/banjar', [BanjarController::class, 'index'])->name('banjar.index');

    Route::get('/news', [NewsController::class, 'index'])->name('news.index');

    Route::middleware('role:warga,pengangkut')->group(function () {
        Route::get('/news/saya', [NewsController::class, 'mine'])->name('news.mine');
        Route::get('/news/create', [NewsController::class, 'create'])->name('news.create');
        Route::post('/news', [NewsController::class, 'store'])->name('news.store');
        Route::delete('/news/{news}', [NewsController::class, 'destroy'])->name('news.destroy');
    });

    // Mengubah tulisan boleh oleh penulisnya sendiri maupun admin, jadi di luar
    // grup peran di atas — pengecekannya ada di controller.
    Route::get('/news/{news}/edit', [NewsController::class, 'edit'])->whereNumber('news')->name('news.edit');
    Route::patch('/news/{news}', [NewsController::class, 'update'])->whereNumber('news')->name('news.update');
});
