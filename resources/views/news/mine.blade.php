@extends('layouts.app')

@php
    $gayaStatus = [
        'published' => ['label' => 'Terbit', 'kelas' => 'bg-leaf-100 text-leaf-700'],
        'draft' => ['label' => 'Draf', 'kelas' => 'bg-gold-100 text-gold-600'],
    ];
@endphp

@section('content')

    <div x-data="{ hapusOpen: false, item: {} }">

        <div class="flex flex-wrap items-start justify-between gap-4 pb-1">
            <div>
                <p class="mb-2 font-mono text-xs uppercase tracking-[0.08em] text-leaf-600">Konten</p>
                <h1 class="font-display text-[25px] font-semibold text-leaf-900">Tulisan Saya</h1>
                <p class="mt-1.5 text-sm text-ink-soft">Semua berita yang kamu tulis, termasuk yang masih draf.</p>
            </div>

            <a href="{{ route('news.create') }}"
                class="rounded-[10px] bg-leaf-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-leaf-900">
                Tambah Berita
            </a>
        </div>

        @if ($errors->any())
            <div class="mt-5 rounded-[14px] border border-clay-600 bg-clay-100 px-4 py-3 text-sm text-clay-600">
                @foreach ($errors->all() as $pesan)
                    <p @class(['mt-1' => ! $loop->first])>{{ $pesan }}</p>
                @endforeach
            </div>
        @endif

        <div class="mt-6 flex flex-col gap-2.5">
            @forelse ($tulisan as $item)
                @php
                    $gaya = $gayaStatus[$item->status] ?? $gayaStatus['draft'];
                @endphp

                <div class="flex flex-wrap items-center justify-between gap-4 rounded-[14px] border border-line bg-white px-[18px] py-4">
                    <div class="flex min-w-[240px] flex-1 items-center gap-3.5">
                        @if ($item->cover_image_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($item->cover_image_path) }}" alt=""
                                class="h-[52px] w-[68px] shrink-0 rounded-[10px] border border-line object-cover">
                        @else
                            <div class="flex h-[52px] w-[68px] shrink-0 items-center justify-center rounded-[10px] bg-leaf-100 text-leaf-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16v16H4z" /><path d="m4 16 4-4 4 4 6-6" />
                                </svg>
                            </div>
                        @endif

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-md px-2 py-[3px] text-[10.5px] font-bold {{ $gaya['kelas'] }}">
                                    {{ $gaya['label'] }}
                                </span>
                                <span class="text-[11.5px] text-ink-faint">{{ $categories[$item->category] }}</span>
                            </div>
                            <div class="mt-1 text-[14.5px] font-semibold leading-snug">{{ $item->title }}</div>
                            <div class="mt-0.5 font-mono text-[11.5px] text-ink-faint">
                                {{ $item->published_at?->locale('id')->translatedFormat('d M Y') ?? '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                        @if ($item->status === 'published')
                            <a href="{{ route('news.show', $item) }}"
                                class="rounded-[9px] border border-line bg-white px-3.5 py-2.5 text-[13px] font-semibold text-leaf-700 hover:bg-paper">
                                Lihat
                            </a>
                        @endif

                        <a href="{{ route('news.edit', $item) }}" title="Ubah berita"
                            class="flex h-9 w-9 items-center justify-center rounded-[9px] border border-line text-ink-soft hover:bg-paper hover:text-leaf-700">
                            <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20h9" /><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                            </svg>
                        </a>

                        <button type="button" title="Hapus berita"
                            @click="item = @js(['judul' => $item->title, 'aksi' => route('news.destroy', $item)]); hapusOpen = true"
                            class="flex h-9 w-9 items-center justify-center rounded-[9px] border border-line text-ink-soft hover:bg-clay-100 hover:text-clay-600">
                            <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6" /><path d="M10 11v6M14 11v6" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="rounded-[14px] border border-line bg-white px-5 py-[60px] text-center">
                    <p class="text-sm text-ink-faint">Kamu belum menulis berita apa pun.</p>
                    <a href="{{ route('news.create') }}"
                        class="mt-4 inline-block rounded-[10px] bg-leaf-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-leaf-900">
                        Tulis Berita Pertama
                    </a>
                </div>
            @endforelse
        </div>

        {{-- ====================== Modal: hapus tulisan ====================== --}}
        <div x-show="hapusOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-leaf-900/45 p-6">
            <div @click.outside="hapusOpen = false" class="w-full max-w-[480px] rounded-2xl bg-white">
                <div class="flex items-center justify-between border-b border-line px-[22px] py-5">
                    <h3 class="font-display text-[17px] font-semibold text-leaf-900">Hapus Berita</h3>
                    <button type="button" @click="hapusOpen = false"
                        class="h-[30px] w-[30px] rounded-full bg-paper text-base leading-none text-ink-soft">&times;</button>
                </div>

                <form method="POST" :action="item.aksi">
                    @csrf
                    @method('DELETE')

                    <div class="px-[22px] py-5">
                        <p class="text-[13.5px] leading-relaxed text-ink-soft">
                            Hapus <strong class="text-ink" x-text="item.judul"></strong>?
                        </p>
                        <p class="mt-3 text-xs text-ink-faint">Naskahnya ikut hilang dan tidak bisa dikembalikan.</p>
                    </div>

                    <div class="flex gap-2.5 px-[22px] pb-[22px] pt-1">
                        <button type="button" @click="hapusOpen = false"
                            class="flex-1 rounded-[10px] border border-line bg-paper py-3 text-sm font-semibold text-ink-soft">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 rounded-[10px] bg-clay-600 py-3 text-sm font-semibold text-white hover:opacity-90">
                            Hapus Berita
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection
