{{-- Sampul kecil untuk baris daftar berita (halaman admin & Tulisan Saya).
     Tanpa sampul, tampilkan placeholder ikon gambar. --}}
@props(['news'])

@if ($news->cover_image_path)
    <img src="{{ \Illuminate\Support\Facades\Storage::url($news->cover_image_path) }}" alt=""
        class="h-[52px] w-[68px] shrink-0 rounded-[10px] border border-line object-cover">
@else
    <div class="flex h-[52px] w-[68px] shrink-0 items-center justify-center rounded-[10px] bg-leaf-100 text-leaf-700">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 4h16v16H4z" /><path d="m4 16 4-4 4 4 6-6" />
        </svg>
    </div>
@endif
