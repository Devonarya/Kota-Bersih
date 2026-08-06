@extends('layouts.admin')

{{-- View generik untuk tiga level teratas wilayah (kabupaten, kecamatan, desa).
     Read-only: datanya ikut Kemendagri lewat `php artisan wilayah:impor`, jadi
     tidak ada tombol tambah/ubah/hapus di sini. Level banjar punya view sendiri
     karena di sanalah admin benar-benar mengelola data. --}}

@section('content')

    <div>
        @include('admin.partials.remah')

        <div class="pb-1">
            <p class="mb-2 font-mono text-xs uppercase tracking-[0.08em] text-leaf-600">{{ $labelLevel }}</p>
            <h1 class="font-display text-[25px] font-semibold text-leaf-900">{{ $judul }}</h1>
            <p class="mt-1.5 text-sm text-ink-soft">{{ $keterangan }}</p>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($items as $item)
                <x-card as="a" href="{{ $item['urlBuka'] }}" class="block p-5 transition hover:border-leaf-600">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-display text-lg font-semibold text-leaf-900">{{ $item['nama'] }}</p>
                            <p class="mt-1 text-[13px] text-ink-soft">
                                {{ $item['jumlahAnak'] }} {{ $labelAnak }}
                            </p>
                        </div>

                        @if ($item['kode'])
                            <span class="shrink-0 rounded-full bg-paper px-2.5 py-1 font-mono text-[10.5px] text-ink-faint">
                                {{ $item['kode'] }}
                            </span>
                        @endif
                    </div>

                    <span class="mt-4 flex items-center gap-1 text-[12.5px] font-semibold text-leaf-700">
                        Lihat {{ $labelAnak }}
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 18l6-6-6-6" />
                        </svg>
                    </span>
                </x-card>
            @empty
                <x-card class="px-5 py-[60px] text-center text-sm text-ink-faint sm:col-span-2 lg:col-span-3">
                    Belum ada {{ strtolower($labelLevel) }}.
                    <span class="mt-1 block font-mono text-xs">php artisan wilayah:impor</span>
                </x-card>
            @endforelse
        </div>
    </div>

@endsection
