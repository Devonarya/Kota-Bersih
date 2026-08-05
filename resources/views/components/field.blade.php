{{-- Sepasang label kecil + nilainya, dipakai di dalam modal detail.
     Isi nilainya lewat :text (ekspresi Alpine, mis. text="item.nama") atau lewat
     slot kalau butuh markup sendiri. --}}
@props(['label', 'text' => null, 'valueClass' => 'text-sm'])

<div {{ $attributes }}>
    <div class="mb-1 text-[11px] uppercase tracking-[0.03em] text-ink-faint">{{ $label }}</div>

    @isset($text)
        <div class="{{ $valueClass }}" x-text="{{ $text }}"></div>
    @else
        {{ $slot }}
    @endisset
</div>
