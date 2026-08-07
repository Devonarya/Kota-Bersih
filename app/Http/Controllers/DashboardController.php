<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\User;
use App\Models\WasteDeposit;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Label aktivitas per status setoran. Tidak ada tabel log aktivitas, jadi
     * ini diturunkan dari WasteDeposit — updated_at dipakai sebagai perkiraan
     * waktu, bukan pencatatan waktu transisi status yang sesungguhnya.
     */
    private const AKTIVITAS_GAYA = [
        'pending' => ['judul' => 'Permintaan pengambilan dibuat', 'kelas' => 'bg-gold-100 text-gold-600'],
        'diterima' => ['judul' => 'Permintaan diterima pengangkut', 'kelas' => 'bg-leaf-100 text-leaf-700'],
        'selesai' => ['judul' => 'Pengambilan sampah selesai', 'kelas' => 'bg-leaf-100 text-leaf-700'],
        'ditolak' => ['judul' => 'Permintaan ditolak', 'kelas' => 'bg-clay-100 text-clay-600'],
    ];

    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $depositAktivitas = WasteDeposit::query()
            ->when($user->role === 'pengangkut', fn ($query) => $query->where('pengangkut_id', $user->id))
            ->when($user->role !== 'pengangkut', fn ($query) => $query->where('user_id', $user->id))
            ->with(['pengangkut', 'types'])
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get()
            ->map(fn (WasteDeposit $deposit) => [
                'judul' => self::AKTIVITAS_GAYA[$deposit->status]['judul'] ?? $deposit->status,
                'kelas' => self::AKTIVITAS_GAYA[$deposit->status]['kelas'] ?? 'bg-gray-100 text-gray-600',
                'detail' => match ($deposit->status) {
                    'diterima' => trim(($deposit->scheduled_date?->locale('id')->translatedFormat('d M Y') ?? '').' · '.($deposit->scheduled_time_slot ?? ''), ' ·'),
                    'selesai' => $deposit->pengangkut ? 'Oleh: '.$deposit->pengangkut->name : null,
                    default => $deposit->types->pluck('jenis_sampah')->implode(', ') ?: null,
                },
                'waktu' => $deposit->updated_at,
            ]);

        // Tidak ada tabel log aktivitas, jadi tulisan yang diterbitkan warga/pengangkut
        // sendiri juga diturunkan dari News — updated_at dipakai sebagai perkiraan
        // waktu, sama seperti pada WasteDeposit di atas.
        $newsAktivitas = $user->news()
            ->where('status', 'published')
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get()
            ->map(fn (News $news) => [
                'judul' => 'Tulisan diterbitkan',
                'kelas' => 'bg-leaf-100 text-leaf-700',
                'detail' => $news->title,
                'waktu' => $news->updated_at,
            ]);

        $aktivitas = $depositAktivitas->concat($newsAktivitas)
            ->sortByDesc('waktu')
            ->take(3)
            ->map(fn (array $item) => [...$item, 'waktu' => $item['waktu']->locale('id')->diffForHumans()])
            ->values();

        return view('dashboard', [
            'banjar' => $user->banjar,
            'totalWarga' => User::where('role', 'warga')->count(),
            'totalSetoranBulanIni' => $user->banjar_id
                ? WasteDeposit::where('banjar_id', $user->banjar_id)
                    ->whereYear('deposited_on', now()->year)
                    ->whereMonth('deposited_on', now()->month)
                    ->count()
                : 0,
            'aktivitas' => $aktivitas,
            'pengumuman' => News::where('status', 'published')
                ->where('category', 'pengumuman')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit(3)
                ->get(),
        ]);
    }
}
