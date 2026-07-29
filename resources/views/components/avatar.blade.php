@props(['name', 'size' => 'h-10 w-10 text-base', 'src' => null])

@php
    $initials = collect(preg_split('/\s+/', trim($name)))
        ->filter()
        ->take(2)
        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
        ->implode('') ?: '?';
@endphp

@if ($src)
    <img src="{{ $src }}" alt="{{ $name }}" {{ $attributes->merge(['class' => "$size inline-block rounded-full object-cover shrink-0"]) }}>
@else
    <span {{ $attributes->merge(['class' => "$size inline-flex items-center justify-center rounded-full bg-brand-200 text-brand-800 font-semibold shrink-0"]) }}>
        {{ $initials }}
    </span>
@endif
