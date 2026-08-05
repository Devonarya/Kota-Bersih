@props(['size' => 'h-9 w-9'])

<img src="{{ asset('images/logo.png') }}" alt="Logo KotaBersih Berbudaya"
    {{ $attributes->merge(['class' => $size.' rounded-xl object-cover']) }}>
