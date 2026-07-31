<?php

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

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profil.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profil.update');
    Route::post('/profil/foto', [ProfileController::class, 'updateAvatar'])->name('profil.avatar');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profil.password');

    Route::middleware('role:warga')->group(function () {
        Route::get('/sampah', [WasteDepositController::class, 'index'])->name('sampah.index');
        Route::post('/sampah', [WasteDepositController::class, 'store'])->name('sampah.store');
    });

    Route::middleware('role:pengangkut')->group(function () {
        Route::get('/pengangkut', [PengangkutController::class, 'index'])->name('pengangkut.index');
        Route::patch('/pengangkut/{deposit}/terima', [PengangkutController::class, 'accept'])->name('pengangkut.accept');
        Route::patch('/pengangkut/{deposit}/tolak', [PengangkutController::class, 'reject'])->name('pengangkut.reject');
    });

    Route::get('/banjar', [BanjarController::class, 'index'])->name('banjar.index');

    Route::get('/news', [NewsController::class, 'index'])->name('news.index');

    Route::middleware('role:warga,pengangkut')->group(function () {
        Route::get('/news/create', [NewsController::class, 'create'])->name('news.create');
        Route::post('/news', [NewsController::class, 'store'])->name('news.store');
    });
});
