<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NewsController as PublicNewsController;
use App\Models\News;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Penertiban tulisan oleh admin.
 *
 * Aksi utamanya menurunkan tulisan jadi draf, bukan menghapus: naskahnya
 * kembali ke Tulisan Saya milik penulisnya sehingga masih bisa diperbaiki.
 * Hapus permanen tetap ada sebagai langkah terakhir.
 */
class NewsController extends Controller
{
    public function index(): View
    {
        return view('admin.berita', [
            'tulisan' => News::with('author')
                ->orderByRaw("CASE WHEN status = 'published' THEN 0 ELSE 1 END")
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->get(),
            'categories' => PublicNewsController::CATEGORIES,
        ]);
    }

    public function create(): View
    {
        return view('admin.pengumuman-create', [
            'categories' => PublicNewsController::CATEGORIES,
        ]);
    }

    /**
     * Kategorinya selalu dipaksa 'pengumuman' di sini, apa pun yang dikirim
     * form — form-nya memang menguncinya lewat input tersembunyi, tapi
     * nilainya tetap tidak dipercaya begitu saja dari sisi klien.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = (new PublicNewsController)->validasi($request);

        $request->user()->news()->create([
            'title' => $validated['title'],
            'category' => 'pengumuman',
            'content' => $validated['content'],
            'cover_image_path' => $request->file('cover_image')?->store('news', 'public'),
            'status' => $validated['action'] === 'publish' ? 'published' : 'draft',
            'published_at' => $validated['published_at'],
        ]);

        return redirect()->route('admin.berita.index')->with('status', $validated['action'] === 'publish'
            ? 'Pengumuman berhasil dipublikasikan.'
            : 'Pengumuman berhasil disimpan sebagai draf.');
    }

    public function demote(News $news): RedirectResponse
    {
        if ($news->status !== 'published') {
            return back()->withErrors(['berita' => "\"{$news->title}\" memang sudah berstatus draf."]);
        }

        $news->update(['status' => 'draft']);

        return back()->with('status',
            "\"{$news->title}\" diturunkan jadi draf dan kembali ke Tulisan Saya milik {$news->author->name}.");
    }

    public function destroy(News $news): RedirectResponse
    {
        $sampul = $news->cover_image_path;
        $judul = $news->title;

        $news->delete();

        if ($sampul) {
            Storage::disk('public')->delete($sampul);
        }

        return back()->with('status', "\"{$judul}\" dihapus permanen.");
    }
}
