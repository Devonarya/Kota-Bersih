<?php

namespace App\Http\Controllers;

use App\Models\Banjar;
use Illuminate\Contracts\View\View;

class BanjarController extends Controller
{
    public function index(): View
    {
        return view('banjar.index', [
            'banjars' => Banjar::withCount('users')->orderBy('name')->get(),
        ]);
    }
}
