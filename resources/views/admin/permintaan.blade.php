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

    $kelasKartu = 'rounded-[14px] border border-line bg-white';
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
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-faint"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7" /><path d="m21 21-4.3-4.3" />
                </svg>
                <input type="search" name="cari" value="{{ $cari }}" placeholder="Cari nama..."
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

                <div class="{{ $kelasKartu }} flex flex-wrap items-center justify-between gap-4 px-[18px] py-4">
                    <div class="flex min-w-[220px] items-center gap-3.5">
                        @if ($anggota->avatarUrl())
                            <img src="{{ $anggota->avatarUrl() }}" alt="{{ $anggota->name }}"
                                class="h-[42px] w-[42px] shrink-0 rounded-full object-cover">
                        @else
                            <span class="flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-full bg-gold-100 font-display text-sm font-semibold text-gold-600">
                                {{ $anggota->initials() }}
                            </span>
                        @endif
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
                </div>
            @empty
                <div class="{{ $kelasKartu }} px-5 py-[60px] text-center text-sm text-ink-faint">
                    Tidak ada permintaan yang cocok.
                </div>
            @endforelse
        </div>

        {{-- ====================== Modal: detail permintaan ====================== --}}
        <div x-show="detailOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-leaf-900/45 p-6">
            <div @click.outside="detailOpen = false"
                class="max-h-[88vh] w-full max-w-[480px] overflow-y-auto rounded-2xl bg-white">
                <div class="flex items-center justify-between border-b border-line px-[22px] py-5">
                    <h3 class="font-display text-[17px] font-semibold text-leaf-900">Detail Permintaan</h3>
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
            </div>
        </div>

        {{-- ====================== Modal: setujui ====================== --}}
        <div x-show="setujuiOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-leaf-900/45 p-6">
            <div @click.outside="setujuiOpen = false" class="w-full max-w-[480px] rounded-2xl bg-white">
                <div class="flex items-center justify-between border-b border-line px-[22px] py-5">
                    <h3 class="font-display text-[17px] font-semibold text-leaf-900">Setujui Pendaftaran</h3>
                    <button type="button" @click="setujuiOpen = false"
                        class="h-[30px] w-[30px] rounded-full bg-paper text-base leading-none text-ink-soft">&times;</button>
                </div>

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
            </div>
        </div>

        {{-- ====================== Modal: tolak ====================== --}}
        <div x-show="tolakOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-leaf-900/45 p-6">
            <div @click.outside="tolakOpen = false" class="w-full max-w-[480px] rounded-2xl bg-white">
                <div class="flex items-center justify-between border-b border-line px-[22px] py-5">
                    <h3 class="font-display text-[17px] font-semibold text-leaf-900">Tolak Pendaftaran</h3>
                    <button type="button" @click="tolakOpen = false"
                        class="h-[30px] w-[30px] rounded-full bg-paper text-base leading-none text-ink-soft">&times;</button>
                </div>

                <form method="POST" :action="item.aksiTolak">
                    @csrf
                    @method('PATCH')

                    <div class="px-[22px] py-5">
                        <p class="mb-3 text-[13.5px] text-ink-soft">
                            Alasan ini disimpan dan ditampilkan pada daftar permintaan.
                        </p>
                        <textarea name="review_note" maxlength="255"
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
            </div>
        </div>

    </div>

@endsection
