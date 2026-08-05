@extends('layouts.app')

@section('content')
    @php
        $user = auth()->user();
        $jenisLabels = ['organik' => 'Organik', 'plastik' => 'Plastik', 'kertas' => 'Kertas', 'b3' => 'B3'];
        $jenisColors = [
            'organik' => 'bg-brand-100 text-brand-700',
            'plastik' => 'bg-amber-100 text-amber-700',
            'kertas' => 'bg-blue-100 text-blue-700',
            'b3' => 'bg-red-100 text-red-700',
        ];
    @endphp

    <div class="flex flex-col items-start justify-between gap-4 rounded-2xl bg-white p-6 shadow-sm sm:flex-row sm:items-center">
        <div class="flex items-center gap-4">
            <x-avatar :user="$user" size="h-16 w-16 text-xl" />
            <div>
                <p class="text-xl font-semibold text-gray-800">{{ $user->name }}</p>
                <p class="text-sm text-gray-500">{{ $user->banjar->name ?? 'Belum ada banjar' }} &middot; No. Warga {{ $user->id }}</p>
            </div>
        </div>
        <div class="flex gap-8 sm:text-right">
            <div>
                <p class="text-2xl font-semibold text-brand-700">{{ $totalSetoran }}</p>
                <p class="text-sm text-gray-500">Total Pengambilan</p>
            </div>
        </div>
    </div>

    <div class="mt-8">
        <h1 class="text-2xl font-semibold text-gray-800">Riwayat Pengambilan Sampah</h1>
        <p class="text-sm text-gray-500">Catatan sampah yang sudah diambil dari rumahmu</p>
    </div>

    <form method="GET" action="{{ route('sampah.index') }}" class="mt-4 flex flex-wrap gap-3">
        <input type="month" name="month" value="{{ $month }}" onchange="this.form.submit()"
            class="rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">

        <select name="jenis" onchange="this.form.submit()"
            class="rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value="semua" @selected($jenis === 'semua')>Semua Jenis</option>
            @foreach ($jenisLabels as $value => $label)
                <option value="{{ $value }}" @selected($jenis === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    <div class="mt-4 overflow-x-auto rounded-2xl bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-6 py-3 font-medium">Hari / Tanggal</th>
                    <th class="px-6 py-3 font-medium">Jenis Sampah</th>
                    <th class="px-6 py-3 font-medium">Detail Lokasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($deposits as $deposit)
                    <tr>
                        <td class="px-6 py-4 text-gray-700">{{ $deposit->deposited_on->locale('id')->translatedFormat('l, d M Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($deposit->types as $tipe)
                                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $jenisColors[$tipe->jenis_sampah] ?? '' }}">
                                        {{ $jenisLabels[$tipe->jenis_sampah] ?? $tipe->jenis_sampah }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $deposit->detail_lokasi ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-gray-400">Belum ada pengambilan untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
