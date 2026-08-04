{{-- Dipakai bersama halaman Warga dan Pengangkut; yang membedakan hanya teks & peran. --}}
@extends('layouts.admin')

@php
    $labelPeran = ['warga' => 'Warga', 'pengangkut' => 'Pengangkut Sampah'];

    $kelasKartu = 'rounded-[14px] border border-line bg-white';
@endphp

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
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-faint"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7" /><path d="m21 21-4.3-4.3" />
                </svg>
                <input type="search" name="cari" value="{{ $cari }}" placeholder="Cari nama..."
                    class="w-full rounded-[10px] border border-line bg-white py-2.5 pl-[38px] pr-3.5 text-sm text-ink
                           focus:border-leaf-600 focus:outline-none focus:ring-[3px] focus:ring-leaf-100">
            </form>
        </div>

        {{-- Daftar anggota --}}
        <div class="mt-[18px] flex flex-col gap-2.5">
            @forelse ($anggota as $orang)
                @php
                    $namaBanjar = $orang->banjar?->name ?? 'Tanpa banjar';
                    $logo = $orang->banjar?->logo_path;

                    $dataItem = [
                        'nama' => $orang->name,
                        'peran' => $labelPeran[$orang->role] ?? $orang->role,
                        'hp' => $orang->phone ?: '—',
                        'email' => $orang->email,
                        'banjar' => $namaBanjar,
                        'tanggal' => $orang->created_at->locale('id')->translatedFormat('d M Y'),
                        'isWarga' => $orang->role === 'warga',
                        'alamat' => $orang->address ?: '—',
                        'jangkauan' => [$namaBanjar],
                        'ktp' => $orang->ktp_number
                            ? substr($orang->ktp_number, 0, 4).str_repeat('•', 8).substr($orang->ktp_number, -4)
                            : '—',
                        'logoUrl' => $logo ? asset('storage/'.$logo) : null,
                        'logoNama' => $logo ? basename($logo) : 'Belum ada logo banjar',
                    ];
                @endphp

                <div class="{{ $kelasKartu }} flex flex-wrap items-center justify-between gap-4 px-[18px] py-4">
                    <div class="flex min-w-[220px] items-center gap-3.5">
                        @if ($orang->avatarUrl())
                            <img src="{{ $orang->avatarUrl() }}" alt="{{ $orang->name }}"
                                class="h-[42px] w-[42px] shrink-0 rounded-full object-cover">
                        @else
                            <span class="flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-full bg-gold-100 font-display text-sm font-semibold text-gold-600">
                                {{ $orang->initials() }}
                            </span>
                        @endif
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
                </div>
            @empty
                <div class="{{ $kelasKartu }} px-5 py-[60px] text-center text-sm text-ink-faint">
                    {{ $cari !== '' ? 'Tidak ada nama yang cocok dengan pencarian.' : $kosong }}
                </div>
            @endforelse
        </div>

        {{-- ====================== Modal: detail anggota ====================== --}}
        <div x-show="detailOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-leaf-900/45 p-6">
            <div @click.outside="detailOpen = false"
                class="max-h-[88vh] w-full max-w-[480px] overflow-y-auto rounded-2xl bg-white">
                <div class="flex items-center justify-between border-b border-line px-[22px] py-5">
                    <h3 class="font-display text-[17px] font-semibold text-leaf-900">Detail Anggota</h3>
                    <button type="button" @click="detailOpen = false"
                        class="h-[30px] w-[30px] rounded-full bg-paper text-base leading-none text-ink-soft">&times;</button>
                </div>

                <div class="px-[22px] py-5">
                    <div class="grid grid-cols-1 gap-x-4 gap-y-3.5 sm:grid-cols-2">
                        <div>
                            <div class="mb-1 text-[11px] uppercase tracking-[0.03em] text-ink-faint">Nama Lengkap</div>
                            <div class="text-sm" x-text="item.nama"></div>
                        </div>
                        <div>
                            <div class="mb-1 text-[11px] uppercase tracking-[0.03em] text-ink-faint">Peran</div>
                            <div class="text-sm" x-text="item.peran"></div>
                        </div>
                        <div>
                            <div class="mb-1 text-[11px] uppercase tracking-[0.03em] text-ink-faint">No. HP/WA</div>
                            <div class="text-sm" x-text="item.hp"></div>
                        </div>
                        <div>
                            <div class="mb-1 text-[11px] uppercase tracking-[0.03em] text-ink-faint">Email</div>
                            <div class="break-all text-sm" x-text="item.email"></div>
                        </div>
                        <div>
                            <div class="mb-1 text-[11px] uppercase tracking-[0.03em] text-ink-faint">Banjar</div>
                            <div class="text-sm" x-text="item.banjar"></div>
                        </div>
                        <div>
                            <div class="mb-1 text-[11px] uppercase tracking-[0.03em] text-ink-faint">Tanggal Daftar</div>
                            <div class="font-mono text-sm" x-text="item.tanggal"></div>
                        </div>

                        {{-- Bagian yang berbeda antara warga & pengangkut --}}
                        <div class="sm:col-span-2">
                            <template x-if="item.isWarga">
                                <div>
                                    <div class="mb-1 text-[11px] uppercase tracking-[0.03em] text-ink-faint">Alamat Detail</div>
                                    <div class="text-sm" x-text="item.alamat"></div>
                                </div>
                            </template>

                            <template x-if="!item.isWarga">
                                <div>
                                    <div class="mb-1 text-[11px] uppercase tracking-[0.03em] text-ink-faint">Banjar Jangkauan</div>
                                    <div class="flex flex-wrap gap-1.5">
                                        <template x-for="banjar in item.jangkauan" :key="banjar">
                                            <span class="rounded-full bg-leaf-100 px-2.5 py-1 text-xs font-semibold text-leaf-700"
                                                x-text="banjar"></span>
                                        </template>
                                    </div>

                                    <div class="mb-1 mt-3 text-[11px] uppercase tracking-[0.03em] text-ink-faint">No. KTP</div>
                                    <div class="font-mono text-sm" x-text="item.ktp"></div>

                                    <div class="mb-1 mt-3 text-[11px] uppercase tracking-[0.03em] text-ink-faint">Logo Banjar</div>
                                    <template x-if="item.logoUrl">
                                        <img :src="item.logoUrl" :alt="item.banjar"
                                            class="h-11 w-11 rounded-[10px] border border-line object-cover">
                                    </template>
                                    <template x-if="!item.logoUrl">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-[10px] bg-leaf-100 text-leaf-700">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M4 4h16v16H4z" /><path d="m4 16 4-4 4 4 6-6" />
                                            </svg>
                                        </div>
                                    </template>
                                    <div class="mt-1 text-xs text-ink-faint" x-text="item.logoNama"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="px-[22px] pb-[22px] pt-4">
                    <button type="button" @click="detailOpen = false"
                        class="w-full rounded-[10px] border border-line bg-paper py-3 text-sm font-semibold text-ink-soft">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>

@endsection
