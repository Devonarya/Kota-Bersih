@extends('layouts.admin')

@php
    $labelPeran = ['warga' => 'Warga', 'pengangkut' => 'Pengangkut Sampah'];

    $gayaStatus = [
        'menunggu' => ['label' => 'Menunggu', 'kelas' => 'bg-gold-100 text-gold-600'],
        'disetujui' => ['label' => 'Disetujui', 'kelas' => 'bg-leaf-100 text-leaf-700'],
        'ditolak' => ['label' => 'Ditolak', 'kelas' => 'bg-clay-100 text-clay-600'],
    ];

    $tabStatus = ['semua' => 'Semua', 'menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'];
    $chipPeran = ['semua' => 'Semua Peran', 'warga' => 'Warga', 'pengangkut' => 'Pengangkut'];
@endphp

@section('content')

    <div x-data="{ detailOpen: false, tolakOpen: false, setujuiOpen: false, item: {} }">

        {{-- Judul halaman --}}
        <div class="pb-1">
            <p class="mb-2 font-mono text-xs uppercase tracking-[0.08em] text-leaf-600">Keanggotaan</p>
            <h1 class="font-display text-[25px] font-semibold text-leaf-900">Permintaan Anggota</h1>
            <p class="mt-1.5 text-sm text-ink-soft">Tinjau dan setujui pendaftaran warga &amp; pengangkut baru.</p>
        </div>

        {{-- Pencarian & filter --}}
        <div class="mt-7 flex flex-wrap items-center gap-3">
            <form method="GET" class="relative min-w-[200px] flex-1">
                <input type="hidden" name="status" value="{{ $filterStatus }}">
                <input type="hidden" name="peran" value="{{ $filterPeran }}">
                <label for="cari-permintaan" class="sr-only">Cari nama</label>
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-faint"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7" /><path d="m21 21-4.3-4.3" />
                </svg>
                <input type="search" id="cari-permintaan" name="cari" value="{{ $cari }}" placeholder="Cari nama..."
                    class="w-full rounded-[10px] border border-line bg-white py-2.5 pl-[38px] pr-3.5 text-sm text-ink
                           focus:border-leaf-600 focus:outline-none focus:ring-[3px] focus:ring-leaf-100">
            </form>

            <div class="flex gap-1.5 rounded-[10px] border border-line bg-white p-1">
                @foreach ($tabStatus as $nilai => $label)
                    <a href="{{ request()->fullUrlWithQuery(['status' => $nilai]) }}"
                        class="whitespace-nowrap rounded-[7px] px-3.5 py-2 text-[13px] font-semibold {{ $filterStatus === $nilai
                            ? 'bg-leaf-700 text-white'
                            : 'text-ink-soft hover:bg-paper' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            @foreach ($chipPeran as $nilai => $label)
                <a href="{{ request()->fullUrlWithQuery(['peran' => $nilai]) }}"
                    class="whitespace-nowrap rounded-full border px-[15px] py-2 text-[13px] font-semibold {{ $filterPeran === $nilai
                        ? 'border-leaf-100 bg-leaf-100 text-leaf-700'
                        : 'border-line bg-white text-ink-soft hover:bg-paper' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Daftar permintaan --}}
        <div class="mt-[18px] flex flex-col gap-2.5">
            @forelse ($permintaan as $anggota)
                @php
                    $gaya = $gayaStatus[$anggota->membership_status] ?? $gayaStatus['menunggu'];
                    $namaBanjar = $anggota->banjar?->name ?? 'Tanpa banjar';
                    $logo = $anggota->banjar?->logo_path;

                    $dataItem = [
                        'nama' => $anggota->name,
                        'peran' => $labelPeran[$anggota->role] ?? $anggota->role,
                        'hp' => $anggota->phone ?: '—',
                        'email' => $anggota->email,
                        'banjar' => $namaBanjar,
                        'tanggal' => $anggota->created_at->locale('id')->translatedFormat('d M Y'),
                        'isWarga' => $anggota->role === 'warga',
                        'alamat' => $anggota->address ?: '—',
                        'jangkauan' => [$namaBanjar],
                        'ktp' => $anggota->ktp_number
                            ? substr($anggota->ktp_number, 0, 4).str_repeat('•', 8).substr($anggota->ktp_number, -4)
                            : '—',
                        'logoUrl' => $logo ? asset('storage/'.$logo) : null,
                        'logoNama' => $logo ? basename($logo) : 'Belum ada logo banjar',
                        'menunggu' => $anggota->membership_status === 'menunggu',
                        'aksiSetujui' => route('admin.permintaan.approve', $anggota),
                        'aksiTolak' => route('admin.permintaan.reject', $anggota),
                    ];
                @endphp

                <x-card class="flex flex-wrap items-center justify-between gap-4 px-[18px] py-4">
                    <div class="flex min-w-[220px] items-center gap-3.5">
                        <x-avatar :user="$anggota" size="h-[42px] w-[42px] text-sm" />
                        <div class="min-w-0">
                            <div class="text-[14.5px] font-semibold">{{ $anggota->name }}</div>
                            <div class="mt-0.5 text-[12.5px] text-ink-soft">
                                {{ $labelPeran[$anggota->role] ?? $anggota->role }} · {{ $namaBanjar }} ·
                                <span class="font-mono">{{ $anggota->created_at->locale('id')->translatedFormat('d M Y') }}</span>
                            </div>
                            @if ($anggota->review_note)
                                <div class="mt-0.5 text-xs text-ink-faint">{{ $anggota->review_note }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="whitespace-nowrap rounded-md px-2.5 py-[5px] text-[11px] font-bold {{ $gaya['kelas'] }}">
                            {{ $gaya['label'] }}
                        </span>

                        <button type="button" @click="item = @js($dataItem); detailOpen = true"
                            class="rounded-[9px] border border-line bg-white px-3.5 py-2.5 text-[13px] font-semibold text-leaf-700 hover:bg-paper">
                            Lihat Detail
                        </button>

                        @if ($anggota->membership_status === 'menunggu')
                            <button type="button" @click="item = @js($dataItem); tolakOpen = true"
                                class="rounded-[9px] border border-clay-600 bg-white px-3.5 py-2.5 text-[13px] font-semibold text-clay-600 hover:bg-clay-100">
                                Tolak
                            </button>
                            <button type="button" @click="item = @js($dataItem); setujuiOpen = true"
                                class="rounded-[9px] bg-leaf-700 px-3.5 py-2.5 text-[13px] font-semibold text-white hover:bg-leaf-900">
                                Setujui
                            </button>
                        @endif
                    </div>
                </x-card>
            @empty
                <x-card class="px-5 py-[60px] text-center text-sm text-ink-faint">
                    Tidak ada permintaan yang cocok.
                </x-card>
            @endforelse
        </div>

        {{-- ====================== Modal: detail permintaan ====================== --}}
        <x-modal state="detailOpen" title="Detail Permintaan" :scrollable="true">
            <div class="px-[22px] py-5">
                @include('admin.partials.detail-anggota')
            </div>

            <div class="flex gap-2.5 px-[22px] pb-[22px] pt-4">
                <button type="button" @click="detailOpen = false"
                    class="flex-1 rounded-[10px] border border-line bg-paper py-3 text-sm font-semibold text-ink-soft">
                    Tutup
                </button>
                <template x-if="item.menunggu">
                    <button type="button" @click="detailOpen = false; tolakOpen = true"
                        class="flex-1 rounded-[10px] bg-clay-600 py-3 text-sm font-semibold text-white">
                        Tolak
                    </button>
                </template>
                <template x-if="item.menunggu">
                    <button type="button" @click="detailOpen = false; setujuiOpen = true"
                        class="flex-1 rounded-[10px] bg-leaf-700 py-3 text-sm font-semibold text-white">
                        Setujui
                    </button>
                </template>
            </div>
        </x-modal>

        {{-- ====================== Modal: setujui ====================== --}}
        <x-modal state="setujuiOpen" title="Setujui Pendaftaran">
            <form method="POST" :action="item.aksiSetujui">
                @csrf
                @method('PATCH')

                <div class="px-[22px] py-5">
                    <p class="text-[13.5px] leading-relaxed text-ink-soft">
                        Pendaftaran <strong class="text-ink" x-text="item.nama"></strong>
                        sebagai <span x-text="item.peran"></span> di <span x-text="item.banjar"></span>
                        akan ditandai disetujui.
                    </p>
                    <p class="mt-3 text-xs text-ink-faint">
                        Pemberitahuan email belum aktif — sampaikan hasilnya ke pemohon secara manual.
                    </p>
                </div>

                <div class="flex gap-2.5 px-[22px] pb-[22px] pt-1">
                    <button type="button" @click="setujuiOpen = false"
                        class="flex-1 rounded-[10px] border border-line bg-paper py-3 text-sm font-semibold text-ink-soft">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 rounded-[10px] bg-leaf-700 py-3 text-sm font-semibold text-white hover:bg-leaf-900">
                        Setujui
                    </button>
                </div>
            </form>
        </x-modal>

        {{-- ====================== Modal: tolak ====================== --}}
        <x-modal state="tolakOpen" title="Tolak Pendaftaran">
            <form method="POST" :action="item.aksiTolak">
                @csrf
                @method('PATCH')

                <div class="px-[22px] py-5">
                    <p class="mb-3 text-[13.5px] text-ink-soft">
                        Alasan ini disimpan dan ditampilkan pada daftar permintaan.
                    </p>
                    <label for="review-note" class="sr-only">Alasan penolakan</label>
                    <textarea name="review_note" id="review-note" maxlength="255"
                        placeholder="Contoh: alamat di luar wilayah banjar, data KTP tidak terbaca, dll."
                        class="min-h-[80px] w-full resize-y rounded-[10px] border border-line px-3.5 py-3 text-sm
                               focus:border-clay-600 focus:outline-none"></textarea>
                </div>

                <div class="flex gap-2.5 px-[22px] pb-[22px] pt-1">
                    <button type="button" @click="tolakOpen = false"
                        class="flex-1 rounded-[10px] border border-line bg-paper py-3 text-sm font-semibold text-ink-soft">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 rounded-[10px] bg-clay-600 py-3 text-sm font-semibold text-white hover:opacity-90">
                        Tolak Pendaftaran
                    </button>
                </div>
            </form>
        </x-modal>

    </div>

@endsection
