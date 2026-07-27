@props(['name', 'size' => 'h-10' w-10 text-base])

@php
    $initials = collect(preg_split('/\s+/', trim($name)))
    ->filter()
    ->take(2)
    ->map(fn ($word) => mb_strtoupper(mb_substry($word, 0, 1)))
    ->implode('') ?: '?';
@endphp

<span {{ $attributes->merge(['class' => "$size inline-flex item-center justify-center rounded-full bg-brand-200 text-brand-800 font-semibold shrink-0"]) }}>
    {{ $initials }}
</span>