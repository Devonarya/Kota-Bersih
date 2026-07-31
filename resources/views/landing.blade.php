@php
    $kategoriGaya = [
        'daur_ulang' => ['label' => 'Daur Ulang', 'thumb' => 'bg-leaf-100 text-leaf-700', 'tag' => 'bg-leaf-100 text-leaf-700'],
        'edukasi' => ['label' => 'Edukasi', 'thumb' => 'bg-clay-100 text-clay-600', 'tag' => 'bg-clay-100 text-clay-600'],
        'kegiatan' => ['label' => 'Kegiatan', 'thumb' => 'bg-leaf-100 text-leaf-700', 'tag' => 'bg-leaf-100 text-leaf-700'],
        'pengumuman' => ['label' => 'Pengumuman', 'thumb' => 'bg-gold-100 text-gold-600', 'tag' => 'bg-gold-100 text-gold-600'],
    ];
@endphp

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    @include('partials.head', ['title' => 'KotaBersih — Bersama Jaga Kebersihan Banjar'])
</head>
<body class="bg-paper font-body text-ink antialiased">

    {{-- ============================ NAV ============================ --}}
    <nav class="border-b border-line">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-6 px-8 py-5">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5">
                <x-logo size="h-9 w-9" />
                <span class="font-display text-lg font-semibold text-leaf-900">KotaBersih</span>
            </a>

            <div class="hidden gap-7 text-sm font-medium text-ink-soft md:flex">
                <a href="#tentang" class="hover:text-leaf-700">Tentang</a>
                <a href="#pengumuman" class="hover:text-leaf-700">Pengumuman</a>
                <a href="#berita" class="hover:text-leaf-700">Berita</a>
            </div>

            <div class="flex items-center gap-2.5">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center rounded-[10px] bg-leaf-700 px-4 py-2.5 text-[13px] font-semibold text-white hover:bg-leaf-900">
                        Ke Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center rounded-[10px] border-[1.5px] border-leaf-700 px-4 py-2 text-[13px] font-semibold text-leaf-700 hover:bg-leaf-100">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center rounded-[10px] bg-leaf-700 px-4 py-2.5 text-[13px] font-semibold text-white hover:bg-leaf-900">
                        Daftar Anggota
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="mx-auto max-w-5xl px-8">

        {{-- ============================ HERO ============================ --}}
        <div class="flex flex-wrap items-center gap-10 py-14">
            <div class="min-w-[300px] flex-1">
                <p class="mb-3 font-mono text-xs uppercase tracking-[0.08em] text-leaf-600">Warga &amp; pengangkut sampah</p>
                <h1 class="max-w-[480px] font-display text-4xl font-semibold leading-[1.25] text-leaf-900">
                    Sampah tercatat, kota terjaga bersih.
                </h1>
                <p class="mt-3.5 max-w-[440px] text-[15px] leading-[1.7] text-ink-soft">
                    KotaBersih menjembatani warga dan pengangkut sampah lewat struktur banjar yang sudah kamu kenal —
                    setor tercatat, tugas pengangkut terpantau, semua di satu tempat.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center rounded-[10px] bg-leaf-700 px-5 py-3 text-sm font-semibold text-white hover:bg-leaf-900">
                        Daftar Anggota
                    </a>
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center rounded-[10px] border-[1.5px] border-leaf-700 px-5 py-3 text-sm font-semibold text-leaf-700 hover:bg-leaf-100">
                        Masuk
                    </a>
                </div>
            </div>

            <div class="relative hidden h-[300px] w-[340px] shrink-0 overflow-hidden rounded-[20px] bg-leaf-700 lg:block">
                <div class="absolute right-[18px] top-[18px] h-[110px] w-[110px]"
                    style="background-image:radial-gradient(rgba(255,255,255,.18) 2px, transparent 2.5px);background-size:14px 14px;"></div>

                <p class="absolute left-[26px] top-6 max-w-[150px] text-[13px] leading-[1.5] text-white/90">
                    Setiap setoran tercatat rapi per banjar.
                </p>

                <div class="absolute bottom-[26px] left-[26px] w-[230px] -rotate-[4deg] rounded-[14px] bg-white p-4 shadow-[0_18px_34px_rgba(0,0,0,.22)]">
                    <div class="flex justify-between font-mono text-[10px] uppercase tracking-[0.05em] text-ink-faint">
                        <span>No. tiket</span>
                        <span>Hari ini</span>
                    </div>
                    <div class="mt-1 font-mono text-[13px] font-bold text-leaf-700">BJK-0842</div>
                    <span class="mt-2.5 inline-block rounded-[20px] bg-gold-100 px-2.5 py-1 text-[10px] font-semibold text-gold-600">
                        Menunggu pengangkut
                    </span>
                </div>
            </div>
        </div>

        {{-- ============================ TENTANG ============================ --}}
        <section id="tentang" class="scroll-mt-24 border-t border-line py-14">
            <div class="flex flex-wrap gap-12">
                <div class="min-w-[320px] max-w-[460px] flex-1">
                    <p class="mb-2.5 font-mono text-xs uppercase tracking-[0.08em] text-leaf-600">Tentang kami</p>
                    <h2 class="font-display text-[26px] font-semibold leading-[1.3] text-leaf-900">Kenapa KotaBersih?</h2>
                    <p class="mt-3.5 text-[14.5px] leading-[1.75] text-ink-soft">
                        KotaBersih adalah platform yang menjembatani warga dan pengangkut sampah, dibangun mengikuti
                        struktur banjar yang sudah dikenal di Bali. Warga mengajukan setoran, pengangkut memantau tugas
                        per banjar, dan pengurus banjar melihat semuanya dari satu tempat — tanpa catatan tercecer di
                        buku atau grup chat.
                    </p>
                    <p class="mt-3.5 text-[14.5px] leading-[1.75] text-ink-soft">
                        Daftar sekali sebagai warga atau pengangkut, pilih banjarmu, dan semua fitur otomatis mengikuti
                        wilayahmu.
                    </p>
                </div>

                <div class="grid min-w-[220px] flex-1 content-start grid-cols-2 gap-3.5">
                    <div class="rounded-[14px] border border-line bg-white px-5 py-[18px]">
                        <div class="font-display text-[26px] text-leaf-900">{{ $jumlahWarga }}</div>
                        <div class="mt-1 text-xs text-ink-soft">Warga terdaftar</div>
                    </div>
                    <div class="rounded-[14px] border border-line bg-white px-5 py-[18px]">
                        <div class="font-display text-[26px] text-leaf-900">{{ $setoranBulanIni }} kali</div>
                        <div class="mt-1 text-xs text-ink-soft">Setoran bulan ini</div>
                    </div>
                    <div class="rounded-[14px] border border-line bg-white px-5 py-[18px]">
                        <div class="font-display text-[26px] text-leaf-900">{{ $jumlahBanjar }}</div>
                        <div class="mt-1 text-xs text-ink-soft">Banjar tergabung</div>
                    </div>
                    <div class="rounded-[14px] border border-line bg-white px-5 py-[18px]">
                        <div class="font-display text-[26px] text-leaf-900">{{ $jumlahPengangkut > 0 ? 'Aktif' : 'Kosong' }}</div>
                        <div class="mt-1 text-xs text-ink-soft">Status pengangkut hari ini</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ==================== PENGUMUMAN & BERITA ==================== --}}
        <section id="pengumuman" class="scroll-mt-24 border-t border-line pb-[70px] pt-14">
            <p class="mb-2.5 font-mono text-xs uppercase tracking-[0.08em] text-leaf-600">Kabar banjar</p>
            <h2 class="font-display text-[26px] font-semibold text-leaf-900">Pengumuman &amp; berita terkini</h2>

            <div class="mt-7 grid gap-7 md:grid-cols-2">

                {{-- Kolom pengumuman --}}
                <div>
                    <div class="mb-3.5 flex items-center gap-2">
                        <span class="h-0.5 w-4 bg-leaf-700"></span>
                        <h3 class="font-mono text-xs font-bold uppercase tracking-[0.08em] text-leaf-900">Pengumuman</h3>
                    </div>

                    <div class="flex flex-col gap-2.5">
                        @forelse ($pengumuman as $item)
                            @php $gaya = $kategoriGaya[$item->category] @endphp
                            <article class="flex items-center gap-3.5 rounded-[14px] border border-line bg-white px-4 py-3.5">
                                <div class="flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-[10px] {{ $gaya['thumb'] }}">
                                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" />
                                        <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="mb-1.5 inline-block rounded-md px-2 py-[3px] text-[10px] font-semibold {{ $gaya['tag'] }}">
                                        {{ $gaya['label'] }}
                                    </span>
                                    <div class="text-[13.5px] font-semibold leading-[1.4] text-ink">{{ $item->title }}</div>
                                    <div class="mt-1 font-mono text-[11px] text-ink-faint">
                                        {{ $item->published_at?->format('d M Y') ?? '—' }}
                                    </div>
                                </div>
                            </article>
                        @empty
                            <p class="rounded-[14px] border border-dashed border-line px-4 py-6 text-center text-[13px] text-ink-faint">
                                Belum ada pengumuman.
                            </p>
                        @endforelse
                    </div>
                </div>

                {{-- Kolom berita --}}
                <div id="berita" class="scroll-mt-24">
                    <div class="mb-3.5 flex items-center gap-2">
                        <span class="h-0.5 w-4 bg-leaf-700"></span>
                        <h3 class="font-mono text-xs font-bold uppercase tracking-[0.08em] text-leaf-900">Berita terkini</h3>
                    </div>

                    <div class="flex flex-col gap-2.5">
                        @forelse ($berita as $item)
                            @php $gaya = $kategoriGaya[$item->category] @endphp
                            <article class="flex items-center gap-3.5 rounded-[14px] border border-line bg-white px-4 py-3.5">
                                <div class="flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-[10px] {{ $gaya['thumb'] }}">
                                    @if ($item->category === 'daur_ulang')
                                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M12 3C8 6 5 9 5 13a7 7 0 0 0 14 0c0-4-3-7-7-10Z" />
                                        </svg>
                                    @elseif ($item->category === 'kegiatan')
                                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="8" r="4" />
                                            <path d="M4 21c0-4 4-6 8-6s8 2 8 6" />
                                        </svg>
                                    @else
                                        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 4h16v16H4z" />
                                            <path d="M8 9h8M8 13h5" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <span class="mb-1.5 inline-block rounded-md px-2 py-[3px] text-[10px] font-semibold {{ $gaya['tag'] }}">
                                        {{ $gaya['label'] }}
                                    </span>
                                    <div class="text-[13.5px] font-semibold leading-[1.4] text-ink">{{ $item->title }}</div>
                                    <div class="mt-1 font-mono text-[11px] text-ink-faint">
                                        {{ $item->published_at?->format('d M Y') ?? '—' }}
                                    </div>
                                </div>
                            </article>
                        @empty
                            <p class="rounded-[14px] border border-dashed border-line px-4 py-6 text-center text-[13px] text-ink-faint">
                                Belum ada berita.
                            </p>
                        @endforelse
                    </div>
                </div>

            </div>
        </section>

    </div>

    {{-- ============================ FOOTER ============================ --}}
    <footer class="border-t border-line py-[26px]">
        <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-2.5 px-8">
            <span class="font-mono text-xs text-ink-faint">© {{ now()->year }} KotaBersih</span>
            <span class="text-xs text-ink-faint">Dibangun untuk warga dan pengangkut sampah, mulai dari banjar.</span>
        </div>
    </footer>

</body>
</html>
