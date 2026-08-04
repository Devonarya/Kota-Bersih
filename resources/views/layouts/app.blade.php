@php
    $user = auth()->user();

    // Menu datar tanpa pengelompokan. 'peran' membatasi item ke peran tertentu,
    // 'aktif' boleh berisi beberapa nama route yang menyalakan item yang sama.
    $menu = [
        ['label' => 'Beranda', 'icon' => 'dashboard', 'route' => 'dashboard', 'aktif' => 'dashboard'],
        ['label' => 'Banjar', 'icon' => 'banjar', 'route' => 'banjar.index', 'aktif' => 'banjar.*'],
        ['label' => 'Pengambilan', 'icon' => 'pengambilan', 'route' => 'pengambilan.index', 'aktif' => 'pengambilan.*', 'peran' => 'warga'],
        ['label' => 'Riwayat', 'icon' => 'riwayat', 'route' => 'sampah.index', 'aktif' => 'sampah.*', 'peran' => 'warga'],
        ['label' => 'News', 'icon' => 'konten', 'route' => 'news.index', 'aktif' => ['news.index', 'news.show']],
        ['label' => 'Tulisan Saya', 'icon' => 'tulisan', 'route' => 'news.mine',
            'aktif' => ['news.mine', 'news.create', 'news.edit'], 'peran' => ['warga', 'pengangkut']],
        ['label' => 'Pengangkut', 'icon' => 'pengangkut', 'route' => 'pengangkut.index', 'aktif' => 'pengangkut.*', 'peran' => 'pengangkut'],
    ];

    $labelPeran = ['warga' => 'Warga', 'pengangkut' => 'Pengangkut', 'admin' => 'Admin'][$user->role] ?? 'Anggota';

    $navItem = 'flex items-center gap-[11px] rounded-[9px] px-2.5 py-2.5 text-[13.5px] font-medium';
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
</head>
<body class="bg-paper font-body text-ink antialiased">

    <div class="flex min-h-screen flex-col md:flex-row">

        {{-- ============================ SIDEBAR ============================ --}}
        <aside class="flex w-full shrink-0 flex-row items-center gap-2 overflow-x-auto border-b border-line bg-white px-4 py-3.5
                      md:sticky md:top-0 md:h-screen md:w-[252px] md:flex-col md:items-stretch md:gap-0 md:overflow-y-auto md:border-b-0 md:border-r md:px-4 md:py-[22px]">

            <a href="{{ route('dashboard') }}" class="flex shrink-0 items-center gap-2.5 pr-3.5 md:px-2 md:pb-[22px] md:pr-2">
                <x-logo size="h-[34px] w-[34px]" />
                <div>
                    <div class="font-display text-[17px] font-semibold leading-tight text-leaf-900">KotaBersih</div>
                    <div class="font-mono text-[9.5px] uppercase tracking-[0.06em] text-leaf-600">{{ $labelPeran }}</div>
                </div>
            </a>

            <div class="flex items-center gap-1 md:block">
                @foreach ($menu as $item)
                    @continue(isset($item['peran']) && ! in_array($user->role, (array) $item['peran'], true))

                    <a href="{{ route($item['route']) }}"
                        class="{{ $navItem }} mb-0.5 {{ request()->routeIs(...(array) $item['aktif'])
                            ? 'bg-leaf-100 font-semibold text-leaf-700'
                            : 'text-ink-soft hover:bg-paper' }}">
                        @include('partials.icon', ['name' => $item['icon']])
                        <span class="hidden md:inline">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>

            {{-- Akun + logout --}}
            <div class="ml-auto flex shrink-0 items-center gap-2.5 md:ml-0 md:mt-auto md:border-t md:border-line md:pl-2.5 md:pt-3.5">
                @if ($user->avatarUrl())
                    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}"
                        class="h-[34px] w-[34px] shrink-0 rounded-full object-cover">
                @else
                    <span class="flex h-[34px] w-[34px] shrink-0 items-center justify-center rounded-full bg-gold-100 text-xs font-bold text-gold-600">
                        {{ $user->initials() }}
                    </span>
                @endif

                <div>
                    <a href="{{ route('profil.edit') }}"
                        class="text-[13px] font-semibold text-ink hover:text-leaf-700 hover:underline">
                        {{ $user->name }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-[11.5px] text-ink-faint hover:text-clay-600 hover:underline">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- ============================= MAIN ============================= --}}
        <main class="min-w-0 flex-1 px-[18px] pb-[50px] pt-6 md:px-9 md:pb-[60px] md:pt-8">
            @if (session('status'))
                <div class="mb-6 rounded-[14px] border border-line bg-leaf-100 px-4 py-3 text-sm text-leaf-700">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>

    </div>

    @stack('scripts')
</body>
</html>
