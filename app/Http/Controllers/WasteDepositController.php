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

    /**
     * Halaman pengambilan: kalau ada permintaan yang masih berjalan tampilkan
     * status tiketnya, kalau tidak ada tampilkan form pengajuan.
     */
    public function pengambilan(Request $request): View
    {
        $aktif = $request->user()->wasteDeposits()
            ->whereIn('status', ['pending', 'diterima'])
            ->with('banjar')
            ->orderByDesc('id')
            ->first();

        return view('pengambilan.index', [
            'aktif' => $aktif,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->banjar_id) {
            return redirect()->route('pengambilan.index')
                ->with('status', 'Lengkapi banjar di profil dulu sebelum mengajukan pengambilan.');
        }

        $sudahAda = $user->wasteDeposits()
            ->whereIn('status', ['pending', 'diterima'])
            ->exists();

        if ($sudahAda) {
            return redirect()->route('pengambilan.index')
                ->with('status', 'Masih ada permintaan yang berjalan. Tunggu sampai selesai dulu.');
        }

        $validated = $request->validate([
            'jenis_sampah' => ['required', 'in:organik,plastik,kertas,b3'],
            'scheduled_time_slot' => ['required', 'in:pagi,siang,sore'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $user->wasteDeposits()->create([
            'banjar_id' => $user->banjar_id,
            'jenis_sampah' => $validated['jenis_sampah'],
            'keterangan' => $validated['keterangan'] ?? null,
            'status' => 'pending',
            'scheduled_date' => now()->toDateString(),
            'scheduled_time_slot' => $validated['scheduled_time_slot'],
            'deposited_on' => now()->toDateString(),
        ]);

        return redirect()->route('pengambilan.index')
            ->with('status', 'Permintaan pengambilan berhasil diajukan.');
    }
}
