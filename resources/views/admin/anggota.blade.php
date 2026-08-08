{{-- Dipakai bersama halaman Warga dan Pengangkut; yang membedakan hanya teks & peran. --}}
@extends('layouts.admin')

@section('content')

    <div x-data="{ detailOpen: false, item: {} }">

        {{-- Judul halaman --}}
        <div class="pb-1">
            <p class="mb-2 font-mono text-xs uppercase tracking-[0.08em] text-leaf-600">Keanggotaan</p>
            <h1 class="font-display text-[25px] font-semibold text-leaf-900">{{ $judul }}</h1>
            <p class="mt-1.5 text-sm text-ink-soft">{{ $deskripsi }}</p>
        </div>

        {{-- Pencarian --}}
        <div class="mt-7">
            <form method="GET" class="relative max-w-md">
                <label for="cari-anggota" class="sr-only">Cari nama</label>
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-faint"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7" /><path d="m21 21-4.3-4.3" />
                </svg>
                <input type="search" id="cari-anggota" name="cari" value="{{ $cari }}" placeholder="Cari nama..."
                    class="w-full rounded-[10px] border border-line bg-white py-2.5 pl-[38px] pr-3.5 text-sm text-ink
                           focus:border-leaf-600 focus:outline-none focus:ring-[3px] focus:ring-leaf-100">
            </form>
        </div>

        {{-- Daftar anggota --}}
        <div class="mt-[18px] flex flex-col gap-2.5">
            @forelse ($anggota as $orang)
                @php
                    $namaBanjar = $orang->banjar?->name ?? 'Tanpa banjar';
                    $dataItem = $orang->detailPayload();
                @endphp

                <x-card class="flex flex-wrap items-center justify-between gap-4 px-[18px] py-4">
                    <div class="flex min-w-[220px] items-center gap-3.5">
                        <x-avatar :user="$orang" size="h-[42px] w-[42px] text-sm" />
                        <div class="min-w-0">
                            <div class="text-[14.5px] font-semibold">{{ $orang->name }}</div>
                            {{-- Peran tidak ditulis lagi: seluruh halaman ini sudah satu peran. --}}
                            <div class="mt-0.5 text-[12.5px] text-ink-soft">
                                {{ $namaBanjar }} ·
                                <span class="font-mono">{{ $orang->created_at->locale('id')->translatedFormat('d M Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <button type="button" @click="item = @js($dataItem); detailOpen = true"
                        class="rounded-[9px] border border-line bg-white px-3.5 py-2.5 text-[13px] font-semibold text-leaf-700 hover:bg-paper">
                        Lihat Detail
                    </button>
                </x-card>
            @empty
                <x-card class="px-5 py-[60px] text-center text-sm text-ink-faint">
                    {{ $cari !== '' ? 'Tidak ada nama yang cocok dengan pencarian.' : $kosong }}
                </x-card>
            @endforelse
        </div>

        {{-- ====================== Modal: detail anggota ====================== --}}
        <x-modal state="detailOpen" title="Detail Anggota" :scrollable="true">
            <div class="px-[22px] py-5">
                @include('admin.partials.detail-anggota')
            </div>

            <div class="px-[22px] pb-[22px] pt-4">
                <button type="button" @click="detailOpen = false"
                    class="w-full rounded-[10px] border border-line bg-paper py-3 text-sm font-semibold text-ink-soft">
                    Tutup
                </button>
            </div>
        </x-modal>

    </div>

@endsection
