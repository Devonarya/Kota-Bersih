{{-- Tamu membaca dengan rangka publik, anggota yang sudah login tetap di dalam sidebar. --}}
@extends(auth()->check() ? 'layouts.app' : 'layouts.public')

@php
    $kategoriGaya = [
        'daur_ulang' => ['thumb' => 'bg-leaf-100 text-leaf-700', 'tag' => 'bg-leaf-100 text-leaf-700'],
        'edukasi' => ['thumb' => 'bg-clay-100 text-clay-600', 'tag' => 'bg-clay-100 text-clay-600'],
        'kegiatan' => ['thumb' => 'bg-leaf-100 text-leaf-700', 'tag' => 'bg-leaf-100 text-leaf-700'],
        'pengumuman' => ['thumb' => 'bg-gold-100 text-gold-600', 'tag' => 'bg-gold-100 text-gold-600'],
    ];

    $ikonKategori = [
        'daur_ulang' => '<path d="M12 3C8 6 5 9 5 13a7 7 0 0 0 14 0c0-4-3-7-7-10Z"/>',
        'kegiatan' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>',
    ];
    $ikonBawaan = '<path d="M4 4h16v16H4z"/><path d="M8 9h8M8 13h5"/>';

    // Anggota kembali ke daftar berita miliknya; tamu kembali ke bagian berita di landing.
    $tautanKembali = auth()->check() ? route('news.index') : route('landing').'#berita';
@endphp

@section('content')

    <article class="mx-auto max-w-[700px] px-1 pb-5 pt-2 md:pt-11">

        <a href="{{ $tautanKembali }}"
            class="mb-[26px] inline-flex items-center gap-1.5 text-[13px] font-semibold text-ink-soft hover:text-leaf-700">
            <svg class="h-[15px] w-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7" />
            </svg>
            Kembali ke Berita
        </a>

        <div>
            <span class="inline-block rounded-md px-2.5 py-1 text-[10.5px] font-semibold {{ $kategoriGaya[$news->category]['tag'] }}">
                {{ $categories[$news->category] }}
            </span>

            @if ($news->status !== 'published')
                <span class="ml-1.5 inline-block rounded-md bg-ink/10 px-2.5 py-1 text-[10.5px] font-semibold text-ink-soft">
                    Draf
                </span>
            @endif
        </div>

        <h1 class="mt-3.5 font-display text-[25px] font-semibold leading-[1.32] text-leaf-900 sm:text-[30px]">
            {{ $news->title }}
        </h1>

        <div class="mt-[22px] flex items-center gap-[11px]">
            <x-avatar :user="$news->author" size="h-[34px] w-[34px] text-xs" />

            <div>
                <div class="text-[13.5px] font-semibold text-ink">{{ $news->author->name }}</div>
                <div class="mt-0.5 font-mono text-[11.5px] text-ink-faint">
                    {{ $news->published_at?->locale('id')->translatedFormat('j M Y') ?? 'Belum terbit' }}
                </div>
            </div>
        </div>

        <div class="my-[26px] h-px bg-line md:mb-7"></div>

        @if ($news->cover_image_path)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($news->cover_image_path) }}" alt=""
                class="mb-7 w-full rounded-[14px] border border-line object-cover">
        @endif

        {{-- Isi disaring dulu lewat HTMLPurifier: lihat profil 'berita' di config/purifier.php --}}
        <div class="text-[15px] leading-[1.85] text-ink-soft
                    [&_a]:font-medium [&_a]:text-leaf-700 [&_a]:underline
                    [&_blockquote]:border-l-[3px] [&_blockquote]:border-line [&_blockquote]:pl-4 [&_blockquote]:italic
                    [&_h2]:mb-2.5 [&_h2]:mt-7 [&_h2]:font-display [&_h2]:text-xl [&_h2]:font-semibold [&_h2]:text-leaf-900
                    [&_h3]:mb-2 [&_h3]:mt-6 [&_h3]:font-display [&_h3]:text-lg [&_h3]:font-semibold [&_h3]:text-leaf-900
                    [&_img]:my-4 [&_img]:max-w-full [&_img]:rounded-[10px]
                    [&_li]:mb-1.5 [&_ol]:mb-[18px] [&_ol]:list-decimal [&_ol]:pl-5
                    [&_p]:mb-[18px] [&_p:last-child]:mb-0
                    [&_ul]:mb-[18px] [&_ul]:list-disc [&_ul]:pl-5">
            {!! clean($news->content, 'berita') !!}
        </div>
    </article>

    {{-- ========================= BERITA LAINNYA ========================= --}}
    @if ($lainnya->isNotEmpty())
        <div class="mx-auto mt-4 max-w-[700px] border-t border-line px-1 pb-[70px] pt-9">
            <div class="mb-4 flex items-center gap-2">
                <span class="h-0.5 w-4 bg-leaf-700"></span>
                <h2 class="font-mono text-xs font-bold uppercase tracking-[0.08em] text-leaf-900">Berita lainnya</h2>
            </div>

            <div class="flex flex-col gap-2.5">
                @foreach ($lainnya as $item)
                    <x-card as="a" href="{{ route('news.show', $item) }}"
                        class="flex items-center gap-3.5 px-4 py-3.5 hover:border-leaf-600">
                        <div class="flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-[10px] {{ $kategoriGaya[$item->category]['thumb'] }}">
                            <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                {!! $ikonKategori[$item->category] ?? $ikonBawaan !!}
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <span class="mb-1.5 inline-block rounded-md px-2 py-[3px] text-[10px] font-semibold {{ $kategoriGaya[$item->category]['tag'] }}">
                                {{ $categories[$item->category] }}
                            </span>
                            <div class="text-[13.5px] font-semibold leading-[1.4] text-ink">{{ $item->title }}</div>
                            <div class="mt-1 font-mono text-[11px] text-ink-faint">
                                {{ $item->published_at?->locale('id')->translatedFormat('j M Y') ?? '—' }}
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>
        </div>
    @endif
@endsection
