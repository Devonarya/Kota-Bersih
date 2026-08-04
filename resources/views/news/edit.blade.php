{{-- Admin mengubah tulisan orang lain dari rangka admin, penulis dari rangka anggota. --}}
@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.app')

@section('content')
    <div class="pb-1">
        <p class="mb-2 font-mono text-xs uppercase tracking-[0.08em] text-leaf-600">
            {{ auth()->user()->role === 'admin' ? 'Pengumuman & Berita' : 'Tulisan Saya' }}
        </p>
        <h1 class="font-display text-[25px] font-semibold text-leaf-900">Ubah Berita</h1>
        <p class="mt-1.5 text-sm text-ink-soft">
            @if (auth()->id() !== $news->user_id)
                Tulisan milik {{ $news->author->name }}.
            @elseif ($news->status === 'published')
                Berita ini sedang tayang. Perubahan langsung terlihat pembaca setelah disimpan.
            @else
                Masih berstatus draf dan belum bisa dibaca orang lain.
            @endif
        </p>
    </div>

    @include('news.partials.form')
@endsection
