{{-- Kotak putih standar untuk daftar & panel. Kelas tambahan (flex, padding, dll)
     cukup dioper lewat atribut class biasa.

     Pakai :as untuk mengubah taginya kalau kotaknya sekaligus form/tautan/section,
     mis. <x-card as="form" method="POST" action="...">. --}}
@props(['as' => 'div'])

<{{ $as }} {{ $attributes->merge(['class' => 'rounded-[14px] border border-line bg-white']) }}>
    {{ $slot }}
</{{ $as }}>
