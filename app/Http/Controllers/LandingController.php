<?php

namespace App\Http\Controllers;

use App\Models\Banjar;
use App\Models\News;
use App\Models\User;
use App\Models\WasteDeposit;
use Illuminate\Contracts\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $pengumuman = News::where('status', 'published')
            ->where('category', 'pengumuman')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        $berita = News::where('status', 'published')
            ->where('category', '!=', 'pengumuman')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        return view('landing', [
            'title' => 'KotaBersih — Bersama Jaga Kebersihan Banjar',
            'pengumuman' => $pengumuman,
            'berita' => $berita,
            'jumlahWarga' => User::where('role', 'warga')->count(),
            'setoranBulanIni' => WasteDeposit::whereYear('deposited_on', now()->year)
                ->whereMonth('deposited_on', now()->month)
                ->count(),
            'jumlahBanjar' => Banjar::count(),
            'jumlahPengangkut' => User::where('role', 'pengangkut')->count(),
        ]);
    }
}
