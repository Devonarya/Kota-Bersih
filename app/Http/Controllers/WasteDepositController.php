<?php

namespace App\Http\Controllers;

use App\Models\WasteDeposit;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WasteDepositController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $month = $request->string('month')->toString() ?: now()->format('Y-m');
        $jenis = $request->string('jenis')->toString() ?: 'semua';

        $period = Carbon::createFromFormat('Y-m', $month)->startOfMonth();

        $deposits = $user->wasteDeposits()
            ->whereBetween('deposited_on', [$period->copy()->startOfMonth(), $period->copy()->endOfMonth()])
            ->when($jenis !== 'semua', fn ($query) => $query->where('jenis_sampah', $jenis))
            ->orderByDesc('deposited_on')
            ->get();

        return view('sampah.index', [
            'deposits' => $deposits,
            'month' => $month,
            'jenis' => $jenis,
            'totalSetoran' => $user->wasteDeposits()->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis_sampah' => ['required', 'in:organik,plastik,kertas,b3'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'berat_kg' => ['nullable', 'numeric', 'min:0'],
        ]);

        $request->user()->wasteDeposits()->create([
            'banjar_id' => $request->user()->banjar_id,
            'jenis_sampah' => $validated['jenis_sampah'],
            'keterangan' => $validated['keterangan'] ?? null,
            'berat_kg' => $validated['berat_kg'] ?? null,
            'status' => 'pending',
            'deposited_on' => now()->toDateString(),
        ]);

        return redirect()->route('sampah.index')->with('status', 'Setoran sampah berhasil dicatat.');
    }
}
