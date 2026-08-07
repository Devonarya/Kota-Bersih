@extends('layouts.app')

@php
    $user = auth()->user();
@endphp

@section('content')
    <div class="pb-1">
        <p class="mb-2 font-mono text-xs uppercase tracking-[0.08em] text-leaf-600">{{ $banjar->name ?? 'Beranda' }}</p>
        <h1 class="font-display text-[25px] font-semibold text-leaf-900">Selamat Datang, {{ $user->name }}</h1>
        <p class="mt-1.5 text-sm text-ink-soft">Kelola sampah di lingkungan banjar dengan mudah dan transparan.</p>
    </div>

    <div class="mt-5 rounded-[14px] bg-leaf-700 px-6 py-6 text-white sm:px-7">
        <h2 class="font-display text-lg font-semibold">Lingkungan Bersih, Banjar Sehat</h2>
        <p class="mt-1 max-w-lg text-[13.5px] leading-[1.6] text-leaf-100">
            KotaBersih membantu warga, penjadwalan pengangkutan, dan pemantauan setoran sampah dalam satu platform.
        </p>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-3.5 sm:grid-cols-3">
        <x-stat :value="$totalWarga" label="Total Warga" />
        <x-stat :value="$totalSetoranBulanIni" label="Setoran Bulan Ini" />
        <x-stat :value="$banjar->name ?? '—'" label="Banjar" />
    </div>

    <h2 class="mt-8 font-display text-lg font-semibold text-leaf-900">Apa yang Anda bisa lakukan?</h2>

    <div class="mt-4 flex gap-4 overflow-x-auto pb-1">
        @if ($user->role === 'warga')
            <x-card as="a" href="{{ route('pengambilan.index') }}"
                class="flex min-w-[260px] flex-1 items-start gap-4 p-6 transition hover:border-leaf-600">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-leaf-100 text-leaf-700">
                    @include('partials.icon', ['name' => 'pengambilan'])
                </span>
                <div>
                    <p class="font-semibold text-ink">Minta Pengambilan</p>
                    <p class="mt-1 text-sm text-ink-soft">Ajukan permintaan pengambilan sampah dari rumah Anda</p>
                </div>
            </x-card>

            <x-card as="a" href="{{ route('sampah.index') }}"
                class="flex min-w-[260px] flex-1 items-start gap-4 p-6 transition hover:border-leaf-600">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-leaf-100 text-leaf-700">
                    @include('partials.icon', ['name' => 'riwayat'])
                </span>
                <div>
                    <p class="font-semibold text-ink">Riwayat Setoran</p>
                    <p class="mt-1 text-sm text-ink-soft">Lihat riwayat setoran sampah dan statusnya</p>
                </div>
            </x-card>
        @else
            <x-card as="a" href="{{ route('pengangkut.index') }}"
                class="flex min-w-[260px] flex-1 items-start gap-4 p-6 transition hover:border-leaf-600">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-leaf-100 text-leaf-700">
                    @include('partials.icon', ['name' => 'pengangkut'])
                </span>
                <div>
                    <p class="font-semibold text-ink">Tugas Pengangkutan</p>
                    <p class="mt-1 text-sm text-ink-soft">Terima dan selesaikan permintaan pengambilan sampah warga</p>
                </div>
            </x-card>
        @endif

        <x-card as="a" href="{{ route('news.index') }}"
            class="flex min-w-[260px] flex-1 items-start gap-4 p-6 transition hover:border-leaf-600">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gold-100 text-gold-600">
                @include('partials.icon', ['name' => 'konten'])
            </span>
            <div>
                <p class="font-semibold text-ink">Berita & Info</p>
                <p class="mt-1 text-sm text-ink-soft">Informasi terbaru seputar kebersihan dan banjar</p>
            </div>
        </x-card>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-5 lg:grid-cols-2">

        {{-- Aktivitas Terbaru --}}
        <div>
            <h2 class="mb-3 font-display text-lg font-semibold text-leaf-900">Aktivitas Terbaru</h2>

            @if ($aktivitas->isEmpty())
                <x-card class="px-5 py-[60px] text-center text-sm text-ink-faint">Belum ada aktivitas.</x-card>
            @else
                <x-card class="divide-y divide-line">
                    @foreach ($aktivitas as $item)
                        <div class="flex items-center gap-3.5 px-5 py-4">
                            <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $item['kelas'] }}"></span>
                            <div class="min-w-0 flex-1">
                                <p class="text-[13.5px] font-semibold text-ink">{{ $item['judul'] }}</p>
                                @if ($item['detail'])
                                    <p class="mt-0.5 truncate text-xs text-ink-soft">{{ $item['detail'] }}</p>
                                @endif
                            </div>
                            <span class="shrink-0 whitespace-nowrap text-xs text-ink-faint">{{ $item['waktu'] }}</span>
                        </div>
                    @endforeach
                </x-card>
            @endif
        </div>

        {{-- Pengumuman Terbaru --}}
        <div>
            <h2 class="mb-3 font-display text-lg font-semibold text-leaf-900">Pengumuman Terbaru</h2>

            <div class="flex flex-col gap-2.5">
                @forelse ($pengumuman as $item)
                    @php
                        // Konten Quill berisi HTML; sambung tag blok jadi spasi dulu
                        // supaya "<p>A</p><p>B</p>" tidak nempel jadi "AB".
                        $ringkas = strip_tags(str_replace(['</p>', '<br>', '<br/>', '<br />'], ' ', $item->content));
                        $ringkas = trim(preg_replace('/\s+/', ' ', $ringkas));
                    @endphp
                    <x-card as="a" href="{{ route('news.show', $item) }}"
                        class="flex items-start gap-3.5 px-4 py-3.5 hover:border-leaf-600">
                        <div class="flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-[10px] bg-gold-100 text-gold-600">
                            <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" />
                                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-baseline justify-between gap-2">
                                <div class="truncate text-[13.5px] font-semibold leading-[1.4] text-ink">{{ $item->title }}</div>
                                <div class="shrink-0 font-mono text-[11px] text-ink-faint">
                                    {{ $item->published_at?->format('d M Y') ?? '—' }}
                                </div>
                            </div>
                            <p class="mt-1 line-clamp-2 text-xs leading-[1.5] text-ink-soft">{{ $ringkas }}</p>
                        </div>
                    </x-card>
                @empty
                    <x-card class="border-dashed px-4 py-6 text-center text-[13px] text-ink-faint">
                        Belum ada pengumuman.
                    </x-card>
                @endforelse
            </div>
        </div>
    </div>
@endsection
