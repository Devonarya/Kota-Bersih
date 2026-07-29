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

    <div x-data="{ depoOpen: false }">
    <div class="flex flex-col items-start justify-between gap-4 rounded-2xl bg-white p-6 shadow-sm sm:flex-row sm:items-center">
        <div class="flex items-center gap-4">
            <x-avatar :name="$user->name" size="h-16 w-16 text-xl" />
            <div>
                <p class="text-xl font-semibold text-gray-800">{{ $user->name }}</p>
                <p class="text-sm text-gray-500">{{ $user->banjar->name ?? 'Belum ada banjar' }} &middot; No. Warga {{ $user->id }}</p>
            </div>
        </div>
        <div class="flex gap-8 sm:text-right">
            <div>
                <p class="text-2xl font-semibold text-brand-700">{{ $totalSetoran }}</p>
                <p class="text-sm text-gray-500">Total Setoran</p>
            </div>
            <div>
                <p class="text-2xl font-semibold text-brand-700">{{ number_format($kgBulanIni, 1) }} <span class="text-sm font-normal">kg</span></p>
                <p class="text-sm text-gray-500">Bulan Ini</p>
            </div>
        </div>
    </div>

    <div class="mt-8 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Riwayat Buang Sampah</h1>
            <p class="text-sm text-gray-500">Catatan sampah yang sudah kamu setorkan</p>
        </div>
        <button type="button" @click="depoOpen = true"
            class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
            Catat Buang Sampah
        </button>
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

    <div class="mt-4 overflow-hidden rounded-2xl bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-500">
                <tr>
                    <th class="px-6 py-3 font-medium">Hari / Tanggal</th>
                    <th class="px-6 py-3 font-medium">Jenis Sampah</th>
                    <th class="px-6 py-3 font-medium">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($deposits as $deposit)
                    <tr>
                        <td class="px-6 py-4 text-gray-700">{{ $deposit->deposited_on->locale('id')->translatedFormat('l, d M Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $jenisColors[$deposit->jenis_sampah] }}">
                                {{ $jenisLabels[$deposit->jenis_sampah] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $deposit->keterangan ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-gray-400">Belum ada setoran untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div x-show="depoOpen" x-cloak
        class="fixed inset-0 z-30 flex items-center justify-center bg-black/40 px-4">
        <div @click.outside="depoOpen = false" class="w-full max-w-md rounded-3xl bg-white p-8 shadow-xl">
            <div class="h-14 w-14 rounded-full bg-brand-100"></div>
            <h2 class="mt-4 text-xl text-gray-800">Catat Buang Sampah</h2>
            <p class="mt-1 text-sm text-gray-500">Catat sampah yang baru saja kamu setorkan.</p>

            <form method="POST" action="{{ route('sampah.store') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label class="mb-1 block text-sm text-gray-600">Jenis Sampah</label>
                    <select name="jenis_sampah" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm text-gray-700 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        @foreach ($jenisLabels as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm text-gray-600">Keterangan</label>
                    <textarea name="keterangan" rows="3" placeholder="Contoh: Sisa dapur & sayuran"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="depoOpen = false" class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection
