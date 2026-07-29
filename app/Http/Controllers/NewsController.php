<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function create(): View
    {
        return view('news.create', [
            'categories' => self::CATEGORIES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
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

        $coverPath = $request->file('cover_image')?->store('news', 'public');

        $request->user()->news()->create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'content' => $validated['content'],
            'cover_image_path' => $coverPath,
            'status' => $validated['action'] === 'publish' ? 'published' : 'draft',
            'published_at' => $validated['published_at'],
        ]);

        $message = $validated['action'] === 'publish'
            ? 'Berita berhasil dipublikasikan.'
            : 'Berita berhasil disimpan sebagai draf.';

        return redirect()->route('news.index')->with('status', $message);
    }
}
