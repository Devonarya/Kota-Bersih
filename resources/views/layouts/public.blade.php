@php
    // Tautan bagian di landing. Kalau sedang berada di landing, biarkan jadi anchor
    // murni supaya scroll-smooth tetap jalan; dari halaman lain perlu URL penuh.
    $keLanding = request()->routeIs('landing') ? '' : route('landing');
@endphp

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    @include('partials.head', ['title' => $title ?? null])
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
                <a href="{{ $keLanding }}#tentang" class="hover:text-leaf-700">Tentang</a>
                <a href="{{ $keLanding }}#pengumuman" class="hover:text-leaf-700">Pengumuman</a>
                <a href="{{ $keLanding }}#berita" class="hover:text-leaf-700">Berita</a>
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

    @yield('content')

    {{-- ============================ FOOTER ============================ --}}
    <footer class="border-t border-line py-[26px]">
        <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-2.5 px-8">
            <span class="font-mono text-xs text-ink-faint">© {{ now()->year }} KotaBersih</span>
            <span class="text-xs text-ink-faint">Dibangun untuk warga dan pengangkut sampah, mulai dari banjar.</span>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
