<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class NewsController extends Controller
{
    /**
     * @var array<string, string>
     */
    public const CATEGORIES = [
        'daur_ulang' => 'Daur Ulang',
        'edukasi' => 'Edukasi',
        'kegiatan' => 'Kegiatan',
        'pengumuman' => 'Pengumuman',
    ];

    public function index(Request $request): View
    {
        $category = $request->string('category')->toString() ?: 'semua';

        $featured = News::where('status', 'published')
            ->with('author')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->first();

        $others = News::where('status', 'published')
            ->with('author')
            ->when($featured, fn ($query) => $query->whereKeyNot($featured->id))
            ->when($category !== 'semua', fn ($query) => $query->where('category', $category))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();

        return view('news.index', [
            'featured' => $featured,
            'others' => $others,
            'category' => $category,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function show(News $news): View
    {
        // Draf hanya boleh dilihat penulisnya sendiri atau admin.
        if ($news->status !== 'published') {
            $user = request()->user();

            abort_unless($user && ($user->id === $news->user_id || $user->role === 'admin'), 404);
        }

        $lainnya = News::where('status', 'published')
            ->with('author')
            ->whereKeyNot($news->id)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        return view('news.show', [
            'title' => $news->title,
            'news' => $news->load('author'),
            'lainnya' => $lainnya,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function create(): View
    {
        return view('news.create', [
            'categories' => self::CATEGORIES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validasi($request);

        $request->user()->news()->create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'content' => $validated['content'],
            'cover_image_path' => $request->file('cover_image')?->store('news', 'public'),
            'status' => $validated['action'] === 'publish' ? 'published' : 'draft',
            'published_at' => $validated['published_at'],
        ]);

        return redirect()->route('news.mine')->with('status', $validated['action'] === 'publish'
            ? 'Berita berhasil dipublikasikan.'
            : 'Berita berhasil disimpan sebagai draf.');
    }

    /**
     * Daftar tulisan milik pengguna sendiri, termasuk yang masih draf.
     */
    public function mine(Request $request): View
    {
        return view('news.mine', [
            'tulisan' => $request->user()->news()
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->get(),
            'categories' => self::CATEGORIES,
        ]);
    }

    public function edit(News $news): View
    {
        $this->pastikanBolehKelola($news);

        return view('news.edit', [
            'news' => $news,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function update(Request $request, News $news): RedirectResponse
    {
        $this->pastikanBolehKelola($news);

        $validated = $this->validasi($request);

        $sampulLama = $news->cover_image_path;
        $gantiSampul = $request->hasFile('cover_image');

        $news->update([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'content' => $validated['content'],
            'status' => $validated['action'] === 'publish' ? 'published' : 'draft',
            'published_at' => $validated['published_at'],
            ...$gantiSampul
                ? ['cover_image_path' => $request->file('cover_image')->store('news', 'public')]
                : [],
        ]);

        if ($gantiSampul && $sampulLama) {
            Storage::disk('public')->delete($sampulLama);
        }

        // Admin mengelola dari halaman sendiri, penulis dari Tulisan Saya.
        $tujuan = $request->user()->role === 'admin' ? 'admin.berita.index' : 'news.mine';

        return redirect()->route($tujuan)->with('status', $validated['action'] === 'publish'
            ? "\"{$news->title}\" berhasil diperbarui dan tayang."
            : "\"{$news->title}\" disimpan sebagai draf.");
    }

    /**
     * Hapus permanen. Hanya penulisnya sendiri — admin memakai halaman admin
     * yang menurunkan tulisan jadi draf lebih dulu.
     */
    public function destroy(Request $request, News $news): RedirectResponse
    {
        abort_unless($request->user()->id === $news->user_id, 403);

        $sampul = $news->cover_image_path;
        $judul = $news->title;

        $news->delete();

        if ($sampul) {
            Storage::disk('public')->delete($sampul);
        }

        return back()->with('status', "\"{$judul}\" berhasil dihapus.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validasi(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(array_keys(self::CATEGORIES))],
            'published_at' => ['required', 'date'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'content' => ['required', 'string', function ($attribute, $value, $fail) {
                if (trim(strip_tags($value)) === '') {
                    $fail('Isi berita wajib diisi.');
                }
            }],
            'action' => ['required', 'in:draft,publish'],
        ]);
    }

    /**
     * Tulisan hanya boleh diubah penulisnya sendiri atau admin.
     */
    private function pastikanBolehKelola(News $news): void
    {
        $user = request()->user();

        abort_unless($user->id === $news->user_id || $user->role === 'admin', 403);
    }
}
