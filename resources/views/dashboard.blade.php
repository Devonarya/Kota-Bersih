@extends('layouts.app')

@section('content')
    <div class="rounded-3xl bg-brand-700 p-8 text-white">
        <p class="text-brand-100">Selamat Datang, {{ auth()->user()->name }}</p>
        <h1 class="mt-1 text-3xl font-semibold">Manajemen Sistem Sampah</h1>
        <p class="mt-3 max-w-2xl text-brand-100">
            Website untuk mengelola sampah di lingkungan banjar: dari pendataan warga, penjadwalan pengangkutan,
            sampai pemantauan setoran sampah semua di satu tempat.
        </p>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Total Warga</p>
            <p class="mt-2 text-3xl font-semibold text-gray-800">{{ $totalWarga }}</p>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Setoran di Lokasi Banjar Warga</p>
            <p class="mt-2 text-3xl font-semibold text-gray-800">{{ $totalSetoranBanjar }} <span class="text-base font-normal text-gray-500">setoran</span></p>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Banjar</p>
            <p class="mt-2 text-3xl font-semibold text-gray-800">{{ $banjar->name ?? '—' }}</p>
        </div>
    </div>

    <h2 class="mt-8 text-lg text-gray-800">Apa yang anda bisa lakukan?</h2>

    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="flex items-start gap-4 rounded-2xl bg-white p-6 opacity-50 shadow-sm" title="Segera hadir">
            <span class="h-14 w-14 shrink-0 rounded-xl bg-gray-100"></span>
            <div>
                <p class="font-medium text-gray-800">Kelola Banjar</p>
                <p class="mt-1 text-sm text-gray-500">Kelola data banjar dan pantau kegiatan tiap wilayah</p>
            </div>
        </div>

        @if (auth()->user()->role === 'warga')
            <a href="{{ route('sampah.index') }}" class="flex items-start gap-4 rounded-2xl bg-white p-6 shadow-sm transition hover:shadow-md">
                <span class="h-14 w-14 shrink-0 rounded-xl bg-brand-100"></span>
                <div>
                    <p class="font-medium text-gray-800">Warga</p>
                    <p class="mt-1 text-sm text-gray-500">Mendepot sampah dan bisa menginput jenis sampah dan keterangannya</p>
                </div>
            </a>
        @else
            <div class="flex items-start gap-4 rounded-2xl bg-white p-6 opacity-50 shadow-sm" title="Segera hadir">
                <span class="h-14 w-14 shrink-0 rounded-xl bg-gray-100"></span>
                <div>
                    <p class="font-medium text-gray-800">Warga</p>
                    <p class="mt-1 text-sm text-gray-500">Mendepot sampah dan bisa menginput jenis sampah dan keterangannya</p>
                </div>
            </div>
        @endif

        <a href="{{ route('news.index') }}" class="flex items-start gap-4 rounded-2xl bg-white p-6 shadow-sm transition hover:shadow-md">
            <span class="h-14 w-14 shrink-0 rounded-xl bg-brand-100"></span>
            <div>
                <p class="font-medium text-gray-800">News</p>
                <p class="mt-1 text-sm text-gray-500">Berita seputar sampah terkini, dan anda bisa menambahkan berita anda sendiri</p>
            </div>
        </a>

        <div class="flex items-start gap-4 rounded-2xl bg-white p-6 opacity-50 shadow-sm" title="Segera hadir">
            <span class="h-14 w-14 shrink-0 rounded-xl bg-gray-100"></span>
            <div>
                <p class="font-medium text-gray-800">Pengangkut</p>
                <p class="mt-1 text-sm text-gray-500">Jadwal dan rute petugas pengangkut sampah dari tiap banjar</p>
            </div>
        </div>
    </div>
@endsection
