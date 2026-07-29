<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BanjarController extends Controller
{
    public function index(): View
    {
        return view('banjar.index', [
            'bajars' => Banjar::withCount('users')->orderBy('name')->get(),
        ]);
    }
}
