@extends('layouts.app')

@section('content')
    @php
        $badgeColors = [
            'daur_ulang' => 'bg-brand-100 text-brand-700',
            'edukasi' => 'bg-emerald-100 text-emerald-700',
            'kegiatan' => 'bg-blue-100 text-blue-700',
            'pengumuman' => 'bg-amber-100 text-amber-700',
        ];
    @endphp

    {{-- Menulis berita sekarang dilakukan dari halaman Tulisan Saya. --}}
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">Berita Terkini</h1>
        <p class="text-sm text-gray-500">Informasi dan edukasi seputar pengeloaan sampah</p>
    </div>

    @if (session('status'))
        <div class="mt-4 rounded-xl bg-brand-50 px-4 py-3 text-sm text-brand-800">{{ session('status') }}</div>
    @endif

    @if ($featured)
        <a href="{{ route('news.show', $featured) }}"
            class="mt-6 grid grid-cols-1 overflow-hidden rounded-2xl bg-white shadow-sm ring-brand-600 hover:ring-2 lg:grid-cols-2">
            @if ($featured->cover_image_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($featured->cover_image_path) }}" alt="" class="h-64 w-full object-cover lg:h-full">
            @else
                <div class="flex h-64 items-center justify-center bg-brand-700 lg:h-full">
                    <span class="text-2xl font-semibold text-white">Gambar</span>
                </div>
            @endif

            <div class="p-6">
                <span class="inline-block rounded-full px-3 py-1 text-xs font-medium {{ $badgeColors[$featured->category] }}">{{ $categories[$featured->category] }}</span>
                <h2 class="mt-3 text-2xl font-semibold text-gray-800">{{ $featured->title }}</h2>
                <p class="mt-3 text-gray-600">{{ \Illuminate\Support\Str::limit(strip_tags($featured->content), 320) }}</p>

                <div class="mt-6 flex items-center gap-3">
                    <x-avatar :name="$featured->author->name" :src="$featured->author->avatarUrl()" size="h-9 w-9 text-sm" />
                    <span class="text-gray-700">{{ $featured->author->name }}</span>
                </div>
            </div>
        </a>
    @else
        <div class="mt-6 rounded-2xl bg-white p-10 text-center text-gray-400 shadow-sm">Belum ada berita yang dipublikasikan.</div>
    @endif

    <div class="mt-8 flex flex-wrap gap-2">
        <a href="{{ route('news.index') }}"
            class="rounded-full px-4 py-2 text-sm font-medium {{ $category === 'semua' ? 'bg-brand-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100' }}">
            Semua
        </a>
        @foreach ($categories as $value => $label)
            <a href="{{ route('news.index', ['category' => $value]) }}"
                class="rounded-full px-4 py-2 text-sm font-medium {{ $category === $value ? 'bg-brand-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($others as $article)
            <a href="{{ route('news.show', $article) }}"
                class="overflow-hidden rounded-2xl bg-white shadow-sm ring-brand-600 hover:ring-2">
                @if ($article->cover_image_path)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($article->cover_image_path) }}" alt="" class="h-40 w-full object-cover">
                @else
                    <div class="flex h-40 items-center justify-center bg-brand-700">
                        <span class="text-xl font-semibold text-white">Gambar</span>
                    </div>
                @endif

                <div class="p-5">
                    <span class="inline-block rounded-full px-3 py-1 text-xs font-medium {{ $badgeColors[$article->category] }}">{{ $categories[$article->category] }}</span>
                    <p class="mt-2 font-medium text-gray-800">{{ $article->title }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ \Illuminate\Support\Str::limit(strip_tags($article->content), 90) }}</p>
                </div>
            </a>
        @empty
            @if ($featured)
                <p class="text-gray-400 sm:col-span-2 lg:col-span-3">Tidak ada berita lain untuk kategori ini.</p>
            @endif
        @endforelse
    </div>
@endsection
