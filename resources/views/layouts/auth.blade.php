<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
</head>
<body class="bg-paper font-body text-ink antialiased">

    <nav class="border-b border-line">
        <div class="mx-auto flex max-w-[760px] items-center justify-between gap-4 px-8 py-5">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5">
                <x-logo size="h-9 w-9" />
                <span class="font-display text-lg font-semibold text-leaf-900">KotaBersih</span>
            </a>

            <div class="text-[13px] text-ink-soft">
                @yield('nav-hint')
            </div>
        </div>
    </nav>

    <div class="mx-auto max-w-[680px] px-8 pb-20 pt-12">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
