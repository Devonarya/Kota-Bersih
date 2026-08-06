<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banjar;
use App\Models\User;
use App\Models\WasteDeposit;
use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $rentang = (int) request('rentang', 14);
        $rentang = in_array($rentang, [7, 14, 30], true) ? $rentang : 14;

        return view('admin.dashboard', [
            'totalWarga' => User::where('role', 'warga')->count(),
            'totalPengangkut' => User::where('role', 'pengangkut')->count(),
            'setoranHariIni' => WasteDeposit::whereDate('deposited_on', today())->count(),
            'permintaanMenunggu' => WasteDeposit::where('status', 'pending')->count(),

            'permintaanTerbaru' => WasteDeposit::with(['user', 'banjar', 'types'])
                ->orderByDesc('id')
                ->limit(5)
                ->get(),

            'ringkasanBanjar' => Banjar::withCount([
                'users as jumlah_warga' => fn ($query) => $query->where('role', 'warga'),
                'wasteDeposits as setoran_bulan_ini' => fn ($query) => $query
                    ->whereYear('deposited_on', now()->year)
                    ->whereMonth('deposited_on', now()->month),
            ])->orderBy('name')->get(),

            'rentangTren' => $rentang,
            'trenPendaftar' => $this->trenPendaftar($rentang),
        ]);
    }

    /**
     * Jumlah akun baru per hari untuk $hari hari terakhir, disandingkan dengan
     * rentang hari yang sama satu bulan sebelumnya supaya bisa dibandingkan.
     *
     * @return array<int, array{label: string, labelLalu: string, bulanIni: int, bulanLalu: int}>
     */
    private function trenPendaftar(int $hari): array
    {
        $akhirIni = today();
        $mulaiIni = $akhirIni->copy()->subDays($hari - 1);

        // subMonthNoOverflow: 31 Mar mundur sebulan jadi 28/29 Feb, bukan lompat ke Mar.
        $akhirLalu = $akhirIni->copy()->subMonthNoOverflow();
        $mulaiLalu = $akhirLalu->copy()->subDays($hari - 1);

        $hitungIni = $this->pendaftarPerHari($mulaiIni, $akhirIni);
        $hitungLalu = $this->pendaftarPerHari($mulaiLalu, $akhirLalu);

        return collect(range(0, $hari - 1))
            ->map(function (int $i) use ($mulaiIni, $mulaiLalu, $hitungIni, $hitungLalu) {
                $tglIni = $mulaiIni->copy()->addDays($i);
                $tglLalu = $mulaiLalu->copy()->addDays($i);

                return [
                    'label' => $tglIni->locale('id')->translatedFormat('d M'),
                    'labelLalu' => $tglLalu->locale('id')->translatedFormat('d M'),
                    'bulanIni' => (int) ($hitungIni[$tglIni->format('Y-m-d')] ?? 0),
                    'bulanLalu' => (int) ($hitungLalu[$tglLalu->format('Y-m-d')] ?? 0),
                ];
            })
            ->all();
    }

    /**
     * Semua pendaftar dihitung lewat created_at, apa pun membership_status-nya.
     *
     * @return \Illuminate\Support\Collection<string, int>
     */
    private function pendaftarPerHari(CarbonInterface $mulai, CarbonInterface $akhir)
    {
        return User::selectRaw('date(created_at) as tanggal, count(*) as jumlah')
            ->whereBetween('created_at', [$mulai->copy()->startOfDay(), $akhir->copy()->endOfDay()])
            ->groupBy('tanggal')
            ->pluck('jumlah', 'tanggal');
    }
}
