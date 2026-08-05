@extends('layouts.app')

@section('content')
    @php
        $user = auth()->user();
        $jenisLabels = ['organik' => 'Organik', 'plastik' => 'Plastik', 'kertas' => 'Kertas', 'b3' => 'B3'];
    @endphp

    <div x-data="{ aksi: '', tolakOpen: false, terimaOpen: false, hari: 'hari_ini', tanggal: '', slot: 'pagi' }">

        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Kartu identitas pengangkut --}}
        <div class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-5">
                <x-avatar :user="$user" size="h-16 w-16 text-xl" rounded="rounded-xl" />
                <div>
                    <p class="text-xl font-semibold text-gray-800">{{ $user->name }}</p>
                    <p class="text-sm text-gray-500">Wilayah: {{ $user->banjar->name ?? 'Belum ada banjar' }}</p>
                </div>
            </div>
            <span class="self-start rounded-full bg-brand-100 px-4 py-1.5 text-sm font-medium text-brand-800 sm:self-auto">
                Aktif Bertugas
            </span>
        </div>

        <div class="mt-8">
            <h1 class="text-2xl font-semibold text-gray-800">Permintaan Angkut Masuk</h1>
            <p class="text-sm text-gray-500">Request Depo Sampah dari Warga</p>
        </div>

        <div class="mt-4 space-y-4">
            @forelse ($requests as $item)
                <div class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-start gap-4">
                        <x-avatar :user="$item->user" size="h-11 w-11 text-sm" />
                        <div>
                            <p class="text-xl font-semibold text-gray-800">{{ $item->user->name }}</p>
                            <p class="text-sm text-gray-500">Wilayah: {{ $item->banjar->name }}</p>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $item->types->pluck('jenis_sampah')->map(fn ($jenis) => $jenisLabels[$jenis] ?? $jenis)->implode(', ') }}
                                @if ($item->detail_lokasi)
                                    &middot; {{ $item->detail_lokasi }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex shrink-0 gap-3">
                        <button type="button"
                            @click="tolakOpen = true; aksi = @js(route('pengangkut.reject', $item))"
                            class="rounded-xl border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Tolak
                        </button>
                        <button type="button"
                            @click="terimaOpen = true; hari = 'hari_ini'; tanggal = ''; slot = 'pagi'; aksi = @js(route('pengangkut.accept', $item))"
                            class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
                            Terima dan Angkut
                        </button>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl bg-white p-10 text-center text-gray-400 shadow-sm">
                    Belum ada permintaan angkut yang masuk.
                </div>
            @endforelse
        </div>

        @if ($jadwal->isNotEmpty())
            <div class="mt-10">
                <h2 class="text-xl font-semibold text-gray-800">Jadwal Pengangkutan Kamu</h2>
                <p class="text-sm text-gray-500">Permintaan yang sudah kamu terima — tandai selesai setelah sampahnya diangkut</p>
            </div>

            <div class="mt-4 space-y-3">
                @foreach ($jadwal as $item)
                    <div class="flex flex-col gap-4 rounded-2xl bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-4">
                            <x-avatar :user="$item->user" size="h-10 w-10 text-sm" />
                            <div>
                                <p class="font-medium text-gray-800">{{ $item->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $item->banjar->name }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 sm:justify-end">
                            <div class="text-sm sm:text-right">
                                <p class="font-medium text-gray-700">
                                    {{ $item->scheduled_date?->locale('id')->translatedFormat('l, d M Y') }}
                                </p>
                                <p class="text-gray-500">
                                    {{ $timeSlots[$item->scheduled_time_slot] ?? $item->scheduled_time_slot }}
                                </p>
                            </div>

                            <form method="POST" action="{{ route('pengangkut.complete', $item) }}" class="shrink-0">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
                                    Selesai
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Modal: Tolak --}}
        <div x-show="tolakOpen" x-cloak class="fixed inset-0 z-30 flex items-center justify-center bg-black/40 px-4">
            <div @click.outside="tolakOpen = false" class="w-full max-w-lg rounded-3xl bg-white p-8 shadow-xl">
                <div class="h-12 w-12 rounded-full bg-red-100"></div>
                <h2 class="mt-5 text-xl font-semibold text-gray-800">Tolak Permintaan Angkut?</h2>
                <p class="mt-2 text-sm text-gray-500">
                    Warga akan diberi tahu bahwa permintaannya ditolak. Permintaan ini akan hilang dari daftar.
                </p>

                <form method="POST" :action="aksi" class="mt-8 flex justify-end gap-3">
                    @csrf
                    @method('PATCH')
                    <button type="button" @click="tolakOpen = false"
                        class="rounded-xl border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="rounded-xl bg-red-700 px-6 py-2.5 text-sm font-semibold text-white hover:bg-red-800">
                        Tolak Permintaan
                    </button>
                </form>
            </div>
        </div>

        {{-- Modal: Terima & jadwalkan --}}
        <div x-show="terimaOpen" x-cloak class="fixed inset-0 z-30 flex items-center justify-center bg-black/40 px-4">
            <div @click.outside="terimaOpen = false" class="w-full max-w-lg rounded-3xl bg-white p-8 shadow-xl">
                <div class="h-12 w-12 rounded-full bg-brand-50"></div>
                <h2 class="mt-5 text-xl font-semibold text-gray-800">Jadwalkan Pengangkutan</h2>
                <p class="mt-1 text-sm text-gray-500">Warga akan menerima notifikasi berisi jadwal yang kamu tentukan</p>

                <form method="POST" :action="aksi" class="mt-6">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="jadwal" :value="hari">

                    <p class="text-sm text-gray-600">Hari Pengangkutan</p>
                    <div class="mt-2 flex flex-wrap gap-3">
                        <template x-for="pilihan in [
                            { nilai: 'hari_ini', label: 'Hari Ini' },
                            { nilai: 'besok', label: 'Besok' },
                            { nilai: 'pilih', label: 'Pilih tanggal' }
                        ]" :key="pilihan.nilai">
                            <button type="button" @click="hari = pilihan.nilai" x-text="pilihan.label"
                                :class="hari === pilihan.nilai
                                    ? 'border-brand-500 bg-brand-50 text-brand-700'
                                    : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'"
                                class="rounded-xl border px-5 py-2.5 text-sm font-medium"></button>
                        </template>
                    </div>

                    <div x-show="hari === 'pilih'" x-cloak class="mt-3">
                        <input type="date" name="scheduled_date" x-model="tanggal" min="{{ now()->toDateString() }}"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm text-gray-700 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    </div>

                    <p class="mt-5 text-sm text-gray-600">Perkiraan Waktu</p>
                    <select name="scheduled_time_slot" x-model="slot"
                        class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm text-gray-700 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        @foreach ($timeSlots as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="terimaOpen = false"
                            class="rounded-xl border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                            class="rounded-xl bg-brand-800 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-900">
                            Terima dan Angkut
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection