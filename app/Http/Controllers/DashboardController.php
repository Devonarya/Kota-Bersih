<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        return view('dashboard', [
            'totalWarga' => User::where('role', 'warga')->count(),
            'totalBeratLg' => (float) WasteDeposit::sum('berat_kg'),
        ]);
    }
}
