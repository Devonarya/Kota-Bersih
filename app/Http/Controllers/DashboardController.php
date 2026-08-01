<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WasteDeposit;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $banjar = $request->user()->banjar;

        return view('dashboard', [
            'totalWarga' => User::where('role', 'warga')->count(),
            'totalSetoranBanjar' => $banjar ? WasteDeposit::where('banjar_id', $banjar->id)->count() : 0,
            'banjar' => $banjar,
        ]);
    }
}
