<?php

namespace App\Http\Controllers;

use App\Models\WasteDeposit;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class WasteDepositController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $month = $request->string('month')->toString() ?: now()->format('Y-m');
        $jenis = $request->string('jenis')->toString() ?: 'semua';

        $period = Carbon::createFromFormat('Y-m', $month)->startOfMonth();

        $deposits = $user->wasteDeposits()
            ->with('types')
            ->whereBetween('deposited_on', [$period->copy()->startOfMonth(), $period->copy()->endOfMonth()])
            ->when($jenis !== 'semua', fn ($query) => $query
                ->whereHas('types', fn ($tipe) => $tipe->where('jenis_sampah', $jenis)))
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
            ->with(['banjar', 'types'])
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
            'jenis_sampah' => ['required', 'array', 'min:1'],
            'jenis_sampah.*' => ['distinct', Rule::in(['organik', 'plastik', 'kertas', 'b3'])],
            'scheduled_time_slot' => ['required', 'in:pagi,siang,sore'],
            'detail_lokasi' => ['nullable', 'string', 'max:500'],
        ], [
            'jenis_sampah.required' => 'Pilih minimal satu jenis sampah.',
            'jenis_sampah.min' => 'Pilih minimal satu jenis sampah.',
        ]);

        $deposit = $user->wasteDeposits()->create([
            'banjar_id' => $user->banjar_id,
            'detail_lokasi' => $validated['detail_lokasi'] ?? null,
            'status' => 'pending',
            'scheduled_date' => now()->toDateString(),
            'scheduled_time_slot' => $validated['scheduled_time_slot'],
            'deposited_on' => now()->toDateString(),
        ]);

        $deposit->types()->createMany(
            collect($validated['jenis_sampah'])->map(fn (string $jenis) => ['jenis_sampah' => $jenis])->all()
        );

        return redirect()->route('pengambilan.index')
            ->with('status', 'Permintaan pengambilan berhasil diajukan.');
    }
}
