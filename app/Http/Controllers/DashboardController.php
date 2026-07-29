<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WasteDeposit;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        return view('dashboard', [
            'totalWarga' => User::where('role', 'warga')->count(),
            'totalBeratKg' => (float) WasteDeposit::sum('berat_kg'),
        ]);
    }
}
