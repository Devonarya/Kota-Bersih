<?php

namespace App\Http\Controllers;

use App\Models\WasteDeposit;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PengangkutController extends Controller
{
    /**
     * @var array<string, string>
     */
    public const TIME_SLOTS = [
        'pagi' => 'Pagi (07.00 - 10.00)',
        'siang' => 'Siang (10.00 - 14.00)',
        'sore' => 'Sore (14.00 - 17.00)',
    ];

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('pengangkut.index', [
            'requests' => WasteDeposit::with(['user', 'banjar'])
                ->where('status', 'pending')
                ->where('banjar_id', $user->banjar_id)
                ->orderBy('created_at')
                ->get(),
            'jadwal' => WasteDeposit::with(['user', 'banjar'])
                ->where('status', 'diterima')
                ->where('pengangkut_id', $user->id)
                ->orderBy('scheduled_date')
                ->get(),
            'timeSlots' => self::TIME_SLOTS,
        ]);
    }

    public function accept(Request $request, WasteDeposit $deposit): RedirectResponse
    {
        $this->pastikanBisaDiproses($request, $deposit);

        $validated = $request->validate([
            'jadwal' => ['required', 'in:hari_ini,besok,pilih'],
            'scheduled_date' => ['nullable', 'required_if:jadwal,pilih', 'date', 'after_or_equal:today'],
            'scheduled_time_slot' => ['required', Rule::in(array_keys(self::TIME_SLOTS))],
        ], [
            'scheduled_date.required_if' => 'Silakan pilih tanggal pengangkutan.',
            'scheduled_date.after_or_equal' => 'Tanggal pengangkutan tidak boleh di masa lalu.',
        ]);

        $this->prosesJikaMasihPending($deposit, [
            'status' => 'diterima',
            'pengangkut_id' => $request->user()->id,
            'scheduled_date' => $this->tanggalPengangkutan($validated),
            'scheduled_time_slot' => $validated['scheduled_time_slot'],
        ]);

        return redirect()->route('pengangkut.index')
            ->with('status', 'Permintaan diterima dan jadwal sudah dikirim ke warga.');
    }

    public function reject(Request $request, WasteDeposit $deposit): RedirectResponse
    {
        $this->pastikanBisaDiproses($request, $deposit);

        $this->prosesJikaMasihPending($deposit, [
            'status' => 'ditolak',
            'pengangkut_id' => $request->user()->id,
        ]);

        return redirect()->route('pengangkut.index')
            ->with('status', 'Permintaan angkut ditolak.');
    }

    public function complete(Request $request, WasteDeposit $deposit): RedirectResponse
    {
        abort_unless(
            $deposit->status === 'diterima' && $deposit->pengangkut_id === $request->user()->id,
            403
        );

        $this->prosesJikaStatus($deposit, 'diterima', [
            'status' => 'selesai',
            'deposited_on' => now()->toDateString(),
        ], 'Pengangkutan ini sudah ditandai selesai.');

        return redirect()->route('pengangkut.index')
            ->with('status', 'Pengangkutan ditandai selesai dan masuk ke riwayat warga.');
    }

    /**
     * Pengangkut hanya boleh memproses permintaan pending di
     * banjarnya sendiri.
     */
    protected function pastikanBisaDiproses(Request $request, WasteDeposit $deposit): void
    {
        abort_unless(
            $deposit->status === 'pending' && $deposit->banjar_id === $request->user()->banjar_id,
            403
        );
    }

    /**
     * Tulis perubahan hanya jika status di database masih 'pending', supaya
     * dua pengangkut tidak bisa memproses permintaan yang sama bersamaan.
     *
     * @param  array<string, mixed>  $attributes
     */
    protected function prosesJikaMasihPending(WasteDeposit $deposit, array $attributes): void
    {
        $this->prosesJikaStatus($deposit, 'pending', $attributes, 'Permintaan ini sudah diproses pengangkut lain.');
    }

    /**
     * Tulis perubahan hanya jika status di database masih sesuai yang diharapkan.
     *
     * @param  array<string, mixed>  $attributes
     */
    protected function prosesJikaStatus(WasteDeposit $deposit, string $dariStatus, array $attributes, string $pesanKonflik): void
    {
        $terproses = WasteDeposit::whereKey($deposit->getKey())
            ->where('status', $dariStatus)
            ->update($attributes);

        abort_if($terproses === 0, 409, $pesanKonflik);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    protected function tanggalPengangkutan(array $validated): string
    {
        return match ($validated['jadwal']) {
            'hari_ini' => now()->toDateString(),
            'besok' => now()->addDay()->toDateString(),
            default => $validated['scheduled_date'],
        };
    }
}