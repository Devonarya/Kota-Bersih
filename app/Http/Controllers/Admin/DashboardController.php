<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banjar;
use App\Models\User;
use App\Models\WasteDeposit;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalWarga' => User::where('role', 'warga')->count(),
            'totalPengangkut' => User::where('role', 'pengangkut')->count(),
            'setoranHariIni' => WasteDeposit::whereDate('deposited_on', today())->count(),
            'permintaanMenunggu' => WasteDeposit::where('status', 'pending')->count(),

            'permintaanTerbaru' => WasteDeposit::with(['user', 'banjar'])
                ->orderByDesc('id')
                ->limit(5)
                ->get(),

            'ringkasanBanjar' => Banjar::withCount([
                'users as jumlah_warga' => fn ($query) => $query->where('role', 'warga'),
                'wasteDeposits as setoran_bulan_ini' => fn ($query) => $query
                    ->whereYear('deposited_on', now()->year)
                    ->whereMonth('deposited_on', now()->month),
            ])->orderBy('name')->get(),
        ]);
    }
}
