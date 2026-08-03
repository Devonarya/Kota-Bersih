<!DOCTYPE html>
<html lang="id" class="font-sans">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-gray-100">
    <nav class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-6 py-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <x-logo size="h-10 w-10" />
                <span class="font-display text-lg font-semibold leading-tight text-leaf-900">KotaBersih</span>
            </a>

            <div class="hidden items-center gap-8 text-sm text-gray-700 md:flex">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'font-semibold text-brand-700' : 'hover:text-brand-700' }}">Beranda</a>

                <a href="{{ route('banjar.index') }}" class="{{ request()->routeIs('banjar.*') ? 'font-semibold text-brand-700' : 'hover:text-brand-700' }}">Banjar</a>

                @if (auth()->user()->role === 'warga')
                    <a href="{{ route('pengambilan.index') }}" class="{{ request()->routeIs('pengambilan.*') ? 'font-semibold text-brand-700' : 'hover:text-brand-700' }}">Pengambilan</a>

                    <a href="{{ route('sampah.index') }}" class="{{ request()->routeIs('sampah.*') ? 'font-semibold text-brand-700' : 'hover:text-brand-700' }}">Riwayat</a>
                @endif

                <a href="{{ route('news.index') }}" class="{{ request()->routeIs('news.*') ? 'font-semibold text-brand-700' : 'hover:text-brand-700' }}">News</a>

                @if (auth()->user()->role === 'pengangkut')
                    <a href="{{ route('pengangkut.index') }}" class="{{ request()->routeIs('pengangkut.*') ? 'font-semibold text-brand-700' : 'hover:text-brand-700' }}">Pengangkut</a>
                @endif
            </div>

            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" type="button">
                    <x-avatar :name="auth()->user()->name" :src="auth()->user()->avatarUrl()" />
                </button>

                <div x-show="open" x-cloak class="absolute right-0 z-20 mt-2 w-52 rounded-xl border border-gray-100 bg-white p-2 shadow-lg">
                    <p class="truncate px-3 py-2 text-sm font-medium text-gray-800">{{ auth()->user()->name }}</p>
                    <a href="{{ route('profil.edit') }}" class="block rounded-lg px-3 py-2 text-sm text-gray-600 hover:bg-gray-100">Profil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm text-gray-600 hover:bg-gray-100">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-7xl px-6 py-8">
        @if (session('status'))
            <div class="mb-6 rounded-xl bg-brand-50 px-4 py-3 text-sm text-brand-800">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
