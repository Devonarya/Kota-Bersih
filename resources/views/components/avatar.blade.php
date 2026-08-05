{{-- Foto profil, jatuh balik ke inisial kalau pengguna belum mengunggah apa pun.
     $user boleh null (mis. pemiliknya sudah dihapus) — inisialnya jadi '?'. --}}
@props(['user' => null, 'size' => 'h-10 w-10 text-base', 'rounded' => 'rounded-full'])

@if ($user?->avatarUrl())
    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}"
        {{ $attributes->merge(['class' => "$size $rounded shrink-0 object-cover"]) }}>
@else
    <span {{ $attributes->merge(['class' => "$size $rounded shrink-0 inline-flex items-center justify-center bg-gold-100 font-display font-semibold text-gold-600"]) }}>
        {{ $user?->initials() ?? '?' }}
    </span>
@endif
