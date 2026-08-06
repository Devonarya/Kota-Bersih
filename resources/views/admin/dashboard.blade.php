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
        <x-stat :value="$totalWarga" label="Warga terdaftar" />
        <x-stat :value="$totalPengangkut" label="Pengangkut terdaftar" />
        <x-stat :value="$setoranHariIni.' kali'" label="Setoran hari ini" />
        <x-stat :value="$permintaanMenunggu" label="Permintaan menunggu" />
    </div>

    {{-- Tren pendaftar akun baru --}}
    @php
        // Geometri SVG. Nilai ini harus sama dengan yang dipakai chartPendaftar()
        // di bawah, karena label sumbu-X dirender di sini (server-side) sementara
        // garisnya digambar Alpine.
        $svgW = 800;
        $svgPadL = 8;
        $svgPadR = 8;

        $titikX = fn (int $i, int $n) => $n <= 1
            ? $svgPadL
            : $svgPadL + ($i / ($n - 1)) * ($svgW - $svgPadL - $svgPadR);

        $jumlahTitik = count($trenPendaftar);
        $jumlahLabel = min($jumlahTitik, 7);
        $indeksLabel = $jumlahLabel <= 1
            ? [0]
            : collect(range(0, $jumlahLabel - 1))
                ->map(fn ($k) => (int) round($k * ($jumlahTitik - 1) / ($jumlahLabel - 1)))
                ->unique()
                ->values()
                ->all();

        $totalIni = collect($trenPendaftar)->sum('bulanIni');
        $totalLalu = collect($trenPendaftar)->sum('bulanLalu');
        $selisihPersen = $totalLalu > 0 ? (int) round((($totalIni - $totalLalu) / $totalLalu) * 100) : null;
    @endphp

    <x-card as="section" class="mt-4 px-[22px] py-5">
        {{-- x-data sengaja di <div> biasa, bukan di <x-card>: @js tidak ikut
             dikompilasi kalau ditaruh sebagai atribut tag komponen Blade. --}}
        <div x-data="chartPendaftar(@js($trenPendaftar))">
            <div class="mb-4 flex flex-wrap items-start justify-between gap-4">
                <div class="flex flex-wrap items-center gap-6">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-full bg-leaf-700"></span>
                            <span class="text-xs text-ink-soft">{{ $rentangTren }} hari terakhir</span>
                        </div>
                        <div class="mt-0.5 flex items-center gap-2">
                            <span class="font-display text-2xl text-leaf-900">{{ $totalIni }}</span>
                            @if ($selisihPersen !== null)
                                <span class="rounded-full px-1.5 py-0.5 text-[10.5px] font-semibold {{ $selisihPersen >= 0
                                    ? 'bg-leaf-100 text-leaf-700'
                                    : 'bg-clay-100 text-clay-600' }}">
                                    {{ $selisihPersen >= 0 ? '+' : '' }}{{ $selisihPersen }}%
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-full bg-gold-600"></span>
                            <span class="text-xs text-ink-soft">Periode sama bulan lalu</span>
                        </div>
                        <div class="mt-0.5 font-display text-2xl text-leaf-900">{{ $totalLalu }}</div>
                    </div>
                </div>

                <div class="flex gap-1 rounded-[10px] border border-line p-1">
                    @foreach ([7 => '7 Hari', 14 => '14 Hari', 30 => '30 Hari'] as $nilai => $teks)
                        <a href="{{ route('admin.dashboard', ['rentang' => $nilai]) }}"
                            class="rounded-[7px] px-2.5 py-1 text-[11.5px] font-semibold {{ $rentangTren === $nilai
                                ? 'bg-leaf-700 text-white'
                                : 'text-ink-soft hover:bg-paper' }}">
                            {{ $teks }}
                        </a>
                    @endforeach
                </div>
            </div>

            <h3 class="mb-3 font-display text-[15.5px] font-semibold text-leaf-900">Pendaftar Akun Baru</h3>

            <div class="relative">
                <svg viewBox="0 0 800 240" class="block h-auto w-full" aria-hidden="true">
                    {{-- Garis grid ditulis literal: <template x-for> tidak jalan di
                         dalam <svg> (template ikut namespace SVG, .content undefined). --}}
                    @foreach ([214, 147.34, 80.68, 12] as $garisY)
                        <line x1="8" x2="792" y1="{{ $garisY }}" y2="{{ $garisY }}" stroke="#DEDCCF" stroke-width="1" />
                    @endforeach

                    <polyline :points="linePoints('bulanLalu')" fill="none" stroke="#C6912E" stroke-width="2"
                        stroke-linejoin="round" stroke-linecap="round" />
                    <polyline :points="linePoints('bulanIni')" fill="none" stroke="#2E5C34" stroke-width="2"
                        stroke-linejoin="round" stroke-linecap="round" />

                    <circle :cx="x(data.length - 1)" :cy="y(data[data.length - 1].bulanLalu)" r="4" fill="#C6912E" stroke="#fff" stroke-width="2" />
                    <circle :cx="x(data.length - 1)" :cy="y(data[data.length - 1].bulanIni)" r="4" fill="#2E5C34" stroke="#fff" stroke-width="2" />

                    <line x-show="hover !== null" :x1="x(hover ?? 0)" :x2="x(hover ?? 0)" :y1="padT" :y2="h - padB"
                        stroke="#8A9082" stroke-width="1" stroke-dasharray="3 3" />
                    <circle x-show="hover !== null" :cx="x(hover ?? 0)" :cy="y(hoverPoint?.bulanLalu ?? 0)" r="4" fill="#C6912E" stroke="#fff" stroke-width="2" />
                    <circle x-show="hover !== null" :cx="x(hover ?? 0)" :cy="y(hoverPoint?.bulanIni ?? 0)" r="4" fill="#2E5C34" stroke="#fff" stroke-width="2" />

                    <rect x="8" y="12" width="784" height="202" fill="transparent"
                        style="cursor: crosshair;" @mousemove="onMove($event)" @mouseleave="onLeave()" />
                </svg>

                <div x-show="hover !== null" x-cloak
                    class="pointer-events-none absolute top-0 z-10 -translate-x-1/2 whitespace-nowrap rounded-[10px] border border-line bg-white px-3 py-2 text-[11.5px] shadow-[0_10px_24px_rgba(0,0,0,.14)]"
                    :style="`left: ${(x(hover ?? 0) / w) * 100}%`">
                    <div class="flex items-center gap-1.5">
                        <span class="h-[2px] w-3 bg-leaf-700"></span>
                        <span class="font-semibold text-ink" x-text="hoverPoint?.bulanIni"></span>
                        <span class="text-ink-faint">akun</span>
                        <span class="font-mono text-[10px] text-ink-faint" x-text="hoverPoint?.label"></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="h-[2px] w-3 bg-gold-600"></span>
                        <span class="font-semibold text-ink" x-text="hoverPoint?.bulanLalu"></span>
                        <span class="text-ink-faint">akun</span>
                        <span class="font-mono text-[10px] text-ink-faint" x-text="hoverPoint?.labelLalu"></span>
                    </div>
                </div>

                <div class="relative mt-1.5 h-4">
                    @foreach ($indeksLabel as $i)
                        <span class="absolute font-mono text-[10px] text-ink-faint"
                            style="left: {{ ($titikX($i, $jumlahTitik) / $svgW) * 100 }}%; transform: translateX({{ $i === 0 ? '0%' : ($i === $jumlahTitik - 1 ? '-100%' : '-50%') }})">
                            {{ $trenPendaftar[$i]['label'] }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </x-card>

    <div class="mt-4 grid gap-4 lg:grid-cols-[1.1fr_1fr]">

        {{-- Permintaan terbaru --}}
        <x-card as="section" class="px-[22px] py-5">
            <div class="mb-3.5 flex items-center justify-between">
                <h3 class="font-display text-[15.5px] font-semibold text-leaf-900">Permintaan Terbaru</h3>
                <span class="cursor-not-allowed text-[12.5px] font-semibold text-ink-faint" title="Belum tersedia">Lihat semua →</span>
            </div>

            @forelse ($permintaanTerbaru as $permintaan)
                @php $gaya = $gayaStatus[$permintaan->status] ?? ['label' => $permintaan->status, 'kelas' => 'bg-gold-100 text-gold-600'] @endphp
                <div class="flex items-center justify-between gap-2.5 border-b border-line py-2.5 last:border-b-0 last:pb-0">
                    <div class="flex min-w-0 items-center gap-[11px]">
                        <x-avatar :user="$permintaan->user" size="h-8 w-8 text-[11.5px]" />
                        <div class="min-w-0">
                            <div class="truncate text-[13.5px] font-semibold">{{ $permintaan->user?->name ?? 'Pengguna dihapus' }}</div>
                            <div class="mt-0.5 truncate text-[11.5px] text-ink-soft">
                                {{ $permintaan->types->pluck('jenis_sampah')->map(fn ($jenis) => ucfirst($jenis))->implode(', ') }} · {{ $permintaan->banjar?->name ?? 'Tanpa banjar' }}
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
        </x-card>

        {{-- Ringkasan per banjar --}}
        <x-card as="section" class="px-[22px] py-5">
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
        </x-card>

    </div>

@endsection

@push('scripts')
<script>
    function chartPendaftar(data) {
        return {
            data,
            hover: null,
            w: 800,
            h: 240,
            padL: 8,
            padR: 8,
            padT: 12,
            padB: 26,
            get plotW() { return this.w - this.padL - this.padR; },
            get plotH() { return this.h - this.padT - this.padB; },
            get maxVal() {
                const nilai = this.data.flatMap((d) => [d.bulanIni, d.bulanLalu]);
                return Math.max(1, ...nilai) * 1.15;
            },
            get hoverPoint() {
                return this.hover === null ? null : this.data[this.hover];
            },
            x(i) {
                return this.data.length === 1
                    ? this.padL
                    : this.padL + (i / (this.data.length - 1)) * this.plotW;
            },
            y(v) {
                return this.padT + this.plotH - (v / this.maxVal) * this.plotH;
            },
            linePoints(key) {
                return this.data.map((d, i) => `${this.x(i)},${this.y(d[key])}`).join(' ');
            },
            onMove(e) {
                const rect = e.currentTarget.getBoundingClientRect();
                const ratio = (e.clientX - rect.left) / rect.width;
                const i = Math.round(ratio * (this.data.length - 1));
                this.hover = Math.min(this.data.length - 1, Math.max(0, i));
            },
            onLeave() {
                this.hover = null;
            },
        };
    }
</script>
@endpush
