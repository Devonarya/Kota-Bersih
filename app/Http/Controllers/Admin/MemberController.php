<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Daftar anggota aktif per peran.
 *
 * Berbeda dengan MemberRequestController yang mengurus alur persetujuan,
 * halaman ini hanya memajang anggota yang sudah disetujui — tanpa filter,
 * tanpa tag status, dan tanpa aksi setujui/tolak.
 */
class MemberController extends Controller
{
    public function warga(Request $request): View
    {
        return $this->daftar($request, 'warga', [
            'judul' => 'Warga',
            'deskripsi' => 'Daftar warga yang keanggotaannya sudah disetujui.',
            'kosong' => 'Belum ada warga terdaftar.',
        ]);
    }

    public function pengangkut(Request $request): View
    {
        return $this->daftar($request, 'pengangkut', [
            'judul' => 'Pengangkut',
            'deskripsi' => 'Daftar pengangkut sampah yang keanggotaannya sudah disetujui.',
            'kosong' => 'Belum ada pengangkut terdaftar.',
        ]);
    }

    /**
     * @param  array{judul: string, deskripsi: string, kosong: string}  $teks
     */
    private function daftar(Request $request, string $peran, array $teks): View
    {
        $cari = trim($request->string('cari')->toString());

        $anggota = User::with('banjar')
            ->where('role', $peran)
            ->where('membership_status', 'disetujui')
            ->when($cari !== '', fn ($query) => $query->where('name', 'like', '%'.$cari.'%'))
            ->orderBy('name')
            ->get();

        return view('admin.anggota', [
            'anggota' => $anggota,
            'peran' => $peran,
            'cari' => $cari,
            ...$teks,
        ]);
    }
}
