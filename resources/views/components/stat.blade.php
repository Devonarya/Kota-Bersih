{{-- Kotak angka ringkas untuk dashboard admin & landing. --}}
@props(['value', 'label'])

<x-card {{ $attributes->merge(['class' => 'px-5 py-[18px]']) }}>
    <div class="font-display text-[26px] text-leaf-900">{{ $value }}</div>
    <div class="mt-1 text-xs text-ink-soft">{{ $label }}</div>
</x-card>
