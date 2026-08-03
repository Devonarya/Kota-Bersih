<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MemberRequestController extends Controller
{
    /** Status yang boleh dipakai sebagai filter, di luar 'semua'. */
    private const STATUS = ['menunggu', 'disetujui', 'ditolak'];

    /** Peran yang muncul sebagai permintaan anggota — admin tidak ikut didaftar. */
    private const PERAN = ['warga', 'pengangkut'];

    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $peran = $request->string('peran')->toString();
        $cari = trim($request->string('cari')->toString());

        $status = in_array($status, self::STATUS, true) ? $status : 'semua';
        $peran = in_array($peran, self::PERAN, true) ? $peran : 'semua';

        $permintaan = User::with('banjar')
            ->whereIn('role', self::PERAN)
            ->when($status !== 'semua', fn ($query) => $query->where('membership_status', $status))
            ->when($peran !== 'semua', fn ($query) => $query->where('role', $peran))
            ->when($cari !== '', fn ($query) => $query->where('name', 'like', '%'.$cari.'%'))
            ->orderByRaw("CASE WHEN membership_status = 'menunggu' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->get();

        return view('admin.permintaan', [
            'permintaan' => $permintaan,
            'filterStatus' => $status,
            'filterPeran' => $peran,
            'cari' => $cari,
        ]);
    }

    public function approve(User $user): RedirectResponse
    {
        $this->pastikanPermintaan($user);

        $user->update([
            'membership_status' => 'disetujui',
            'review_note' => null,
            'reviewed_at' => now(),
        ]);

        return back()->with('status', "Pendaftaran {$user->name} disetujui.");
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        $this->pastikanPermintaan($user);

        $validated = $request->validate([
            'review_note' => ['nullable', 'string', 'max:255'],
        ], [
            'review_note.max' => 'Alasan penolakan maksimal 255 karakter.',
        ]);

        $user->update([
            'membership_status' => 'ditolak',
            'review_note' => $validated['review_note'] ?: 'Ditolak tanpa alasan spesifik',
            'reviewed_at' => now(),
        ]);

        return back()->with('status', "Pendaftaran {$user->name} ditolak.");
    }

    /**
     * Akun admin bukan permintaan anggota, jadi tidak boleh disetujui/ditolak
     * lewat halaman ini walaupun id-nya ditebak dari URL.
     */
    private function pastikanPermintaan(User $user): void
    {
        abort_unless(in_array($user->role, self::PERAN, true), 404);
    }
}
