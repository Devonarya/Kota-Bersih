<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NewsController as PublicNewsController;
use App\Models\News;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
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
