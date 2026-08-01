@extends('layouts.admin')

@php
    $gayaStatus = [
        'pending' => ['label' => 'Menunggu', 'kelas' => 'bg-gold-100 text-gold-600'],
        'diterima' => ['label' => 'Disetujui', 'kelas' => 'bg-leaf-100 text-leaf-700'],
        'ditolak' => ['label' => 'Ditolak', 'kelas' => 'bg-clay-100 text-clay-600'],
        'selesai' => ['label' => 'Selesai', 'kelas' => 'bg-leaf-100 text-leaf-700'],
    ];
@endphp

@section('content')

    {{-- Judul halaman --}}
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="mb-2 font-mono text-xs uppercase tracking-[0.08em] text-leaf-600">Selamat datang kembali</p>
            <h1 class="font-display text-[25px] font-semibold text-leaf-900">Dashboard</h1>
            <p class="mt-1.5 text-sm text-ink-soft">Ringkasan aktivitas KotaBersih hari ini.</p>
        </div>
        <div class="mt-1 whitespace-nowrap font-mono text-[12.5px] text-ink-faint">
            {{ now()->locale('id')->translatedFormat('l, d F Y') }}
        </div>
    </div>

    {{-- Kartu statistik --}}
    <div class="mt-[26px] grid grid-cols-2 gap-3.5 lg:grid-cols-4">
        <div class="rounded-[14px] border border-line bg-white px-5 py-[18px]">
            <div class="font-display text-[27px] text-leaf-900">{{ $totalWarga }}</div>
            <div class="mt-1 text-xs text-ink-soft">Warga terdaftar</div>
        </div>
        <div class="rounded-[14px] border border-line bg-white px-5 py-[18px]">
            <div class="font-display text-[27px] text-leaf-900">{{ $totalPengangkut }}</div>
            <div class="mt-1 text-xs text-ink-soft">Pengangkut terdaftar</div>
        </div>
        <div class="rounded-[14px] border border-line bg-white px-5 py-[18px]">
            <div class="font-display text-[27px] text-leaf-900">{{ $setoranHariIni }} kali</div>
            <div class="mt-1 text-xs text-ink-soft">Setoran hari ini</div>
        </div>
        <div class="rounded-[14px] border border-line bg-white px-5 py-[18px]">
            <div class="font-display text-[27px] text-leaf-900">{{ $permintaanMenunggu }}</div>
            <div class="mt-1 text-xs text-ink-soft">Permintaan menunggu</div>
        </div>
    </div>

    <div class="mt-4 grid gap-4 lg:grid-cols-[1.1fr_1fr]">

        {{-- Permintaan terbaru --}}
        <section class="rounded-[14px] border border-line bg-white px-[22px] py-5">
            <div class="mb-3.5 flex items-center justify-between">
                <h3 class="font-display text-[15.5px] font-semibold text-leaf-900">Permintaan Terbaru</h3>
                <span class="cursor-not-allowed text-[12.5px] font-semibold text-ink-faint" title="Belum tersedia">Lihat semua →</span>
            </div>

            @forelse ($permintaanTerbaru as $permintaan)
                @php $gaya = $gayaStatus[$permintaan->status] ?? ['label' => $permintaan->status, 'kelas' => 'bg-gold-100 text-gold-600'] @endphp
                <div class="flex items-center justify-between gap-2.5 border-b border-line py-2.5 last:border-b-0 last:pb-0">
                    <div class="flex min-w-0 items-center gap-[11px]">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gold-100 font-display text-[11.5px] font-semibold text-gold-600">
                            {{ $permintaan->user?->initials() ?? '?' }}
                        </span>
                        <div class="min-w-0">
                            <div class="truncate text-[13.5px] font-semibold">{{ $permintaan->user?->name ?? 'Pengguna dihapus' }}</div>
                            <div class="mt-0.5 truncate text-[11.5px] text-ink-soft">
                                {{ ucfirst($permintaan->jenis_sampah) }} · {{ $permintaan->banjar?->name ?? 'Tanpa banjar' }}
                            </div>
                        </div>
                    </div>
                    <span class="whitespace-nowrap rounded-md px-2.5 py-1 text-[10.5px] font-bold {{ $gaya['kelas'] }}">
                        {{ $gaya['label'] }}
                    </span>
                </div>
            @empty
                <p class="py-6 text-center text-[13px] text-ink-faint">Belum ada permintaan setoran.</p>
            @endforelse
        </section>

        {{-- Ringkasan per banjar --}}
        <section class="rounded-[14px] border border-line bg-white px-[22px] py-5">
            <div class="mb-3.5 flex items-center justify-between">
                <h3 class="font-display text-[15.5px] font-semibold text-leaf-900">Ringkasan per Banjar</h3>
                <span class="cursor-not-allowed text-[12.5px] font-semibold text-ink-faint" title="Belum tersedia">Kelola →</span>
            </div>

            @forelse ($ringkasanBanjar as $banjar)
                <div class="flex items-center justify-between gap-3 border-b border-line py-[11px] last:border-b-0 last:pb-0">
                    <div class="min-w-0 truncate text-[13.5px] font-semibold">{{ $banjar->name }}</div>
                    <div class="flex shrink-0 gap-4 text-right">
                        <div>
                            <span class="block font-mono text-[13px] font-bold text-leaf-900">{{ $banjar->jumlah_warga }}</span>
                            <small class="text-[10px] text-ink-faint">warga</small>
                        </div>
                        <div>
                            <span class="block font-mono text-[13px] font-bold text-leaf-900">{{ $banjar->setoran_bulan_ini }}×</span>
                            <small class="text-[10px] text-ink-faint">setor bulan ini</small>
                        </div>
                    </div>
                </div>
            @empty
                <p class="py-6 text-center text-[13px] text-ink-faint">Belum ada banjar terdaftar.</p>
            @endforelse
        </section>

    </div>

@endsection
