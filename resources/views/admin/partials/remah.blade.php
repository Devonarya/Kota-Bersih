{{-- Remah jejak hierarki wilayah. $remah berisi [['label' => ..., 'url' => ...], ...]
     berurutan dari level teratas; halaman yang sedang dibuka tidak ikut di dalamnya
     karena sudah tampil sebagai judul. --}}
@if (! empty($remah))
    <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-[12.5px] text-ink-soft">
        @foreach ($remah as $titik)
            <a href="{{ $titik['url'] }}" class="rounded-[7px] px-1.5 py-0.5 hover:bg-paper hover:text-leaf-700">
                {{ $titik['label'] }}
            </a>
            <span class="text-ink-faint">/</span>
        @endforeach
    </nav>
@endif
