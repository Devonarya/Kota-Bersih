<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Banjar;
use App\Models\Kabupaten;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register', [
            'pohonWilayah' => $this->pohonWilayah(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:warga,pengangkut'],

            // Tiap level divalidasi terhadap induknya, jadi kombinasi wilayah yang
            // tidak nyambung (mis. dikirim manual lewat DevTools) langsung ditolak.
            'kabupaten_id' => ['required', 'exists:kabupatens,id'],
            'kecamatan_id' => ['required', Rule::exists('kecamatans', 'id')->where('kabupaten_id', $request->input('kabupaten_id'))],
            'desa_id' => ['required', Rule::exists('desas', 'id')->where('kecamatan_id', $request->input('kecamatan_id'))],
            'banjar_id' => ['required', Rule::exists('banjars', 'id')->where('desa_id', $request->input('desa_id'))],

            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'ktp_number' => ['nullable', 'required_if:role,pengangkut', 'digits:16'],
            'banjar_logo' => ['nullable', 'image', 'max:2048'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'kabupaten_id.required' => 'Kabupaten wajib dipilih.',
            'kecamatan_id.required' => 'Kecamatan wajib dipilih.',
            'kecamatan_id.exists' => 'Kecamatan itu tidak ada di kabupaten yang dipilih.',
            'desa_id.required' => 'Desa wajib dipilih.',
            'desa_id.exists' => 'Desa itu tidak ada di kecamatan yang dipilih.',
            'banjar_id.required' => 'Banjar wajib dipilih.',
            'banjar_id.exists' => 'Banjar itu tidak ada di desa yang dipilih.',
            'ktp_number.required_if' => 'No. KTP wajib diisi untuk akun pengangkut.',
            'ktp_number.digits' => 'No. KTP harus 16 digit angka.',
            'banjar_logo.image' => 'Logo banjar harus berupa file gambar.',
            'banjar_logo.max' => 'Ukuran logo banjar maksimal 2 MB.',
        ]);

        $isWarga = $validated['role'] === 'warga';

        $user = User::create([
            'banjar_id' => $validated['banjar_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $isWarga ? ($validated['address'] ?? null) : null,
            'ktp_number' => $isWarga ? null : $validated['ktp_number'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        if ($request->hasFile('banjar_logo')) {
            $this->simpanLogoBanjar($request, (int) $validated['banjar_id']);
        }

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Seluruh pohon wilayah untuk dropdown bertingkat di form daftar.
     *
     * Sengaja dikirim sekaligus, bukan lewat AJAX per level: datanya masih satu
     * wilayah pilot, jadi jauh lebih ringan daripada menambah endpoint sendiri.
     * Kalau nanti datanya sudah se-provinsi, baru diganti pemanggilan bertahap.
     *
     * @return array<int, array<string, mixed>>
     */
    private function pohonWilayah(): array
    {
        return Kabupaten::with([
            'kecamatans' => fn ($query) => $query->orderBy('name'),
            'kecamatans.desas' => fn ($query) => $query->orderBy('name'),
            'kecamatans.desas.banjars' => fn ($query) => $query->orderBy('name'),
        ])
            ->orderBy('name')
            ->get()
            ->map(fn (Kabupaten $kabupaten) => [
                'id' => $kabupaten->id,
                'nama' => $kabupaten->name,
                'anak' => $kabupaten->kecamatans->map(fn ($kecamatan) => [
                    'id' => $kecamatan->id,
                    'nama' => $kecamatan->name,
                    'anak' => $kecamatan->desas->map(fn ($desa) => [
                        'id' => $desa->id,
                        'nama' => $desa->name,
                        'anak' => $desa->banjars->map(fn ($banjar) => [
                            'id' => $banjar->id,
                            'nama' => $banjar->name,
                        ])->all(),
                    ])->all(),
                ])->all(),
            ])
            ->all();
    }

    /**
     * Simpan logo banjar yang diunggah saat mendaftar (warga maupun pengangkut).
     *
     * Catatan: logo ini milik banjar, dipakai bersama semua anggotanya. Kalau banjar
     * yang sama sudah punya logo, logo lama diganti dan filenya dihapus supaya tidak
     * menumpuk di storage. Set $timpaLogoLama = false kalau logo pertama yang harus menang.
     */
    private function simpanLogoBanjar(Request $request, int $banjarId): void
    {
        $timpaLogoLama = true;

        $banjar = Banjar::find($banjarId);

        if (! $banjar || ($banjar->logo_path && ! $timpaLogoLama)) {
            return;
        }

        $logoLama = $banjar->logo_path;

        $banjar->update([
            'logo_path' => $request->file('banjar_logo')->store('banjar', 'public'),
        ]);

        if ($logoLama) {
            Storage::disk('public')->delete($logoLama);
        }
    }
}
