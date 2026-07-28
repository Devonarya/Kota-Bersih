@extends('layouts.app')

@section('content')
    <div class="rounded-2x1 bg-white p-6 shadow-sm">
        <h1 class="text-x1 font-semibold text-gray-800">Apa itu Banjar?</h1>
        <p class ="mt-3 text-gray-600">
            Banjar adalah satuan komunitas terkecil dalam masyarakat adat Bali, kumpulan keluarga yang tinggal
            dalam satu wilayah dan mengurus kegiatan bersama, mulai dari upacara adat, gotong royong, sampai urusan
            lingkungan sehari-hari.
        </p>
    </div>

    <div class="mt-8">
        <h2 class="text-x1 font-semibold text-gray-800">Daftar Banjar</h2>
        <p class="text-sm text-gray-500">Banjar yang terdaftar di sistem</p>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($banjars as $banjar)
            <div class="overflow-hidden rounded 2x1 bg-white shadow-sm">
             <div class="flex h-40 item-center justify-center bg-brand-700">
                <span class="text-2x1 font-semibold text-white">Gambar</span>
             </div>
             <div class="p-5">
                @if ($banjar->desa)
                    <span class="inline-block rounded-full bg-brand-100 px-3 py-1 text-xs font-medium text-brand-700">{{ $banjar->desa }}</span>
                @endif
                <p class="mt-2 text-lg font-medium text-gray-800">{{ $banjar->name }}</p>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $banjar->family_count }} kerluagra terdaftar &middot; {{ $banjar->users_count }} warga aktif di sistem
                </p>
             </div>   
        </div>
        @endforeach
    </div>
@endsection