@extends('layout.app')

@section('content')
    <div class="rounded-3x1 bg-brand-700 p-8 text-white">
        <p class="text-brand-100">Selamat Datang, {{ auth()->user()->name }}</p>
        <h1 class="mt-1 text-3x1 font-semibold">Manajemen Sistem Sampah</h1>
        <p class="mt-3 max-2-2x1 text-brand-100">
            Website untuk mengelola sampah di lingkungan banjar: dari pendataan warga, penjadwalan pengangkutan,
            sampai pemantaian setoran sampah semua di satu tempat.
        </p>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2x1 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Total Warga</p>
            <p class="mt-2 text-3x1 font-semibold text-gray-800">{{ totalWarga }}</p>
        </div>
        <div class="rounded-2x1 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-600">Sampah Terkumpul</p>
            <p class="mt-2 text-3x1 font-semibold text-gray-800">{{ number_format($totalBeratKg / 100, 1) }}</p><span class="text-base font-normal text-gray-500">ton</span>
        </div>
        <div class="rounded-2x1 bg-white p-6 shadow-sm">
            <p class="text-sm text-gray-500">Jadwal Hari ini</p>
            <p class="mt-2 text-3x1 font-semibold text-gray-400">— <span class="text-base font-normal text-gray-500">Segara hadir</span></p>
        </div>
    </div>

    <h2 class="mt-8 text-lg text-gray-800">Apa yang anda bisa lakukan?</h2>

    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="flex item-start gap-4 rounded-2x1 bg-white p-6 opacity-50 shadow-sm" title="Segara hadir">
            <span class="h-14 w-14 shrink-0 rounded-x1 bg-gray-100"></span>
            <div>
                <p class="font-medium text-gray-800">Kelola Banjar</p>
                <p class="mt-1 text-sm text-gray-500">Kelola data banjar dan pantau kegiatan tiap wilayah</p>
            </div>
        </div>

        @if (auth()->user()->role === 'warga')
            <a href="{{ route('sampah.index') }}" class="flex items-start gap-4 rounded-2x1 bg-white p-6 shadow-sm transistion hover:shadow-md">
                <span class="h-14 w-14 shrink-0 rounded-x1 bg-brand-100"></span>
                <div>
                    <p class="font-medium text-gray-800">Warga</p>
                    <p class="mt-1 text-sm text-gray-500">Mendepot sampah dan bisa menginput jenis sampah dan keteranganya</p>
                </div>
            </a>
        @else
            <div class="flex item-start gap-4 rounded-2x1 bg-white p-6 opacity-50 shadow-sm" title="Segera hadir">
                <span class="h-14 w-14 shrink-0 rounded-x1 bg-gray-100"></span>
                <div>
                    <p class="font-medium text-gray-800">Warga</p>
                    <p class="mt-1 text-sm text-gray-500">Mendepot sampah dan bisa menginput jenis sampah dan keterangannya</p>
                </div>
            </div>
        @endif

        <a href="{{ route('news.index') }}" class="flex item-start gap-4 rounded-2x1 bg-white p-6 shadow-sm transisiton hover:shadow-md">
            <span class="h-14 w-14 shrink-0 rounded-x1 bg-brand-100"></span>
            <div>
                <p class="font-medium text-gray-800">News</p>
                <p class="mt-1 text-sm text-gray-500">Berita seputar sampah terkini, dan anda bisa menambahkan berita anda sendiri</p>
            </div>
        </a>

        <div class="flex item-start gap-4 rounded-2x1 bg-white p-6 opacity-50 shadow-sm" title="Segara hadir">
            <span class="h-14 w-14 shrink-0 rounded-x1 bg-gray-100"></span>
            <div>
                <p class="font-medium text-gray-800">Pengangkutan</p>
                <p class="mt-1 text-sm text-gray-500">Jadwal dan rute petugas pengangkut sampah dari tiap banjar</p>
            </div>
        </div>
    </div>
@endsection