@extends('layouts.app')

@php
    $user = auth()->user();

    $jenisOpsi = [
        'organik' => ['label' => 'Organik', 'ikon' => '<path d="M12 3C8 6 5 9 5 13a7 7 0 0 0 14 0c0-4-3-7-7-10Z"/>'],
        'plastik' => ['label' => 'Plastik', 'ikon' => '<path d="M21 8l-9-5-9 5 9 5 9-5Z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/>'],
        'kertas' => ['label' => 'Kertas', 'ikon' => '<path d="M4 4h16v16H4z"/><path d="M8 9h8M8 13h5"/>'],
        'b3' => ['label' => 'Residu/B3', 'ikon' => '<path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><path d="M12 9v4"/><path d="M12 17h.01"/>'],
    ];

    $waktuOpsi = [
        'pagi' => ['label' => 'Pagi', 'jam' => '08.00–11.00'],
        'siang' => ['label' => 'Siang', 'jam' => '11.00–14.00'],
        'sore' => ['label' => 'Sore', 'jam' => '14.00–17.00'],
    ];

    $jenisWarna = [
        'organik' => 'bg-leaf-100 text-leaf-700',
        'plastik' => 'bg-clay-100 text-clay-600',
        'kertas' => 'bg-clay-100 text-clay-600',
        'b3' => 'bg-gold-100 text-gold-600',
    ];

    $inputKelas = 'w-full rounded-[10px] border border-line bg-white px-3.5 py-3 text-sm text-ink placeholder:text-ink-faint focus:border-leaf-600 focus:outline-none focus:ring-[3px] focus:ring-leaf-100';
@endphp

@section('content')
    <div class="mx-auto max-w-[640px] font-body">

        <div class="pb-1 pt-2">
            <p class="mb-2 font-mono text-xs uppercase tracking-[0.08em] text-leaf-600">
                Warga · {{ $user->banjar->name ?? 'Belum ada banjar' }}
            </p>
            <h1 class="font-display text-2xl font-semibold text-leaf-900">Pengambilan Sampah</h1>
            <p class="mt-1.5 text-[13.5px] leading-[1.6] text-ink-soft">
                Ajukan permintaan pengambilan sampah dari rumahmu. Pengangkut aktif di banjarmu akan mendapat notifikasi.
            </p>
        </div>

        @if ($aktif)

            {{-- ================= ADA PERMINTAAN AKTIF ================= --}}
            @php
                $selesaiDiterima = $aktif->status === 'diterima';
                $pill = $selesaiDiterima
                    ? ['label' => 'Diterima Pengangkut', 'kelas' => 'bg-leaf-100 text-leaf-700']
                    : ['label' => 'Menunggu Pengangkut', 'kelas' => 'bg-gold-100 text-gold-600'];
            @endphp

            <x-card class="mt-[18px] px-6 py-[26px]">

                <div class="flex flex-wrap items-start justify-between gap-2.5">
                    <div>
                        <div class="text-[11px] uppercase tracking-[0.04em] text-ink-faint">No. tiket</div>
                        <div class="mt-0.5 font-mono text-xl font-bold text-leaf-900">{{ $aktif->ticketCode() }}</div>
                    </div>
                    <span class="whitespace-nowrap rounded-[20px] px-3.5 py-1.5 text-xs font-bold {{ $pill['kelas'] }}">
                        {{ $pill['label'] }}
                    </span>
                </div>

                {{-- Stepper --}}
                @php
                    $langkah = [
                        ['label' => 'Diajukan', 'kondisi' => 'done'],
                        ['label' => 'Menunggu Pengangkut', 'kondisi' => $selesaiDiterima ? 'done' : 'current'],
                        ['label' => 'Selesai', 'kondisi' => $selesaiDiterima ? 'current' : 'pending'],
                    ];
                @endphp

                <div class="mb-6 mt-[26px] flex items-start">
                    @foreach ($langkah as $i => $step)
                        <div class="relative flex flex-1 flex-col items-center">
                            @if ($i > 0)
                                <div class="absolute left-[-50%] top-[13px] z-0 h-0.5 w-full {{ $step['kondisi'] === 'done' ? 'bg-leaf-700' : 'bg-line' }}"></div>
                            @endif

                            <div class="z-10 flex h-[26px] w-[26px] items-center justify-center rounded-full text-[11px] font-bold text-white
                                {{ $step['kondisi'] === 'pending' ? 'bg-line' : 'bg-leaf-700' }}
                                {{ $step['kondisi'] === 'current' ? 'ring-4 ring-leaf-100' : '' }}">
                                {{ $step['kondisi'] === 'done' ? '✓' : $i + 1 }}
                            </div>

                            <div class="mt-2 max-w-[90px] text-center text-[11px] leading-[1.4]
                                {{ $step['kondisi'] === 'pending' ? 'text-ink-faint' : 'font-semibold text-ink' }}">
                                {{ $step['label'] }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <p class="mb-5 rounded-[10px] bg-paper px-3.5 py-3 text-[12.5px] leading-[1.6] text-ink-soft">
                    Pengangkut aktif di {{ $aktif->banjar->name ?? 'banjarmu' }} sudah dapat notifikasi.
                    Begitu diambil, tiket ini otomatis pindah ke Riwayat.
                </p>

                <div class="mb-1.5 grid grid-cols-1 gap-x-3.5 gap-y-4 sm:grid-cols-2">
                    <x-field label="Jenis Sampah" class="sm:col-span-2">
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($aktif->types as $tipe)
                                <span class="inline-block rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $jenisWarna[$tipe->jenis_sampah] ?? '' }}">
                                    {{ $jenisOpsi[$tipe->jenis_sampah]['label'] ?? $tipe->jenis_sampah }}
                                </span>
                            @endforeach
                        </div>
                    </x-field>

                    <x-field label="Waktu Diinginkan">
                        <div class="text-[13.5px] text-ink">
                            @if ($aktif->scheduled_time_slot && isset($waktuOpsi[$aktif->scheduled_time_slot]))
                                {{ $waktuOpsi[$aktif->scheduled_time_slot]['label'] }} ·
                                {{ $waktuOpsi[$aktif->scheduled_time_slot]['jam'] }}
                            @else
                                Belum ditentukan
                            @endif
                        </div>
                    </x-field>

                    <x-field label="Diajukan">
                        <div class="font-mono text-[13.5px] text-ink">
                            {{ $aktif->created_at->locale('id')->translatedFormat('d M Y · H.i') }}
                        </div>
                    </x-field>

                    <x-field label="Lokasi" class="sm:col-span-2">
                        <div class="text-[13.5px] text-ink">{{ $user->address ?: 'Alamat belum diisi di profil' }}</div>
                    </x-field>

                    @if ($aktif->detail_lokasi)
                        <x-field label="Detail Lokasi" class="sm:col-span-2">
                            <div class="text-[13.5px] text-ink">{{ $aktif->detail_lokasi }}</div>
                        </x-field>
                    @endif
                </div>

            </x-card>

        @else

            {{-- ================= BELUM ADA PERMINTAAN ================= --}}
            @php
                // Dirakit di sini, bukan langsung di atribut: @js() tidak ikut
                // dikompilasi kalau ditulis di dalam atribut komponen Blade.
                $stateAwal = json_encode([
                    'jenis' => array_values((array) old('jenis_sampah', ['organik'])),
                    'waktu' => old('scheduled_time_slot', 'pagi'),
                ]);
            @endphp

            <x-card as="form" method="POST" action="{{ route('pengambilan.store') }}"
                x-data="{{ $stateAwal }}"
                class="mt-[18px] px-6 pb-[26px] pt-6">
                @csrf

                <div class="mb-5">
                    <label class="mb-2.5 block text-[12.5px] font-semibold text-ink-soft">
                        Jenis Sampah <span class="font-normal text-ink-faint">(bisa pilih lebih dari satu)</span>
                    </label>

                    <div class="flex flex-wrap gap-2.5">
                        @foreach ($jenisOpsi as $nilai => $opsi)
                            <button type="button"
                                @click="jenis.includes('{{ $nilai }}') ? jenis = jenis.filter(j => j !== '{{ $nilai }}') : jenis.push('{{ $nilai }}')"
                                :class="jenis.includes('{{ $nilai }}')
                                    ? 'bg-leaf-100 border-leaf-100 text-leaf-700'
                                    : 'bg-white border-line text-ink-soft hover:border-leaf-600'"
                                class="flex items-center gap-2 rounded-xl border px-4 py-2.5 text-[13.5px] font-semibold transition">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $opsi['ikon'] !!}</svg>
                                {{ $opsi['label'] }}
                            </button>
                        @endforeach
                    </div>

                    <template x-for="nilai in jenis" :key="nilai">
                        <input type="hidden" name="jenis_sampah[]" :value="nilai">
                    </template>

                    @error('jenis_sampah')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label class="mb-2.5 block text-[12.5px] font-semibold text-ink-soft">Lokasi Pengambilan</label>

                    <div class="flex items-center gap-3 rounded-xl border border-line bg-paper px-3.5 py-3">
                        <svg class="h-[18px] w-[18px] shrink-0 text-leaf-700" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <div class="min-w-0">
                            <div class="text-[13.5px] font-semibold">{{ $user->address ?: 'Alamat belum diisi' }}</div>
                            <div class="mt-0.5 text-xs text-ink-soft">{{ $user->banjar->name ?? 'Belum ada banjar' }}</div>
                        </div>
                        <a href="{{ route('profil.edit') }}"
                            class="ml-auto whitespace-nowrap text-xs font-semibold text-leaf-700 hover:underline">Ubah</a>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="mb-2.5 block text-[12.5px] font-semibold text-ink-soft">Waktu Diinginkan</label>

                    <div class="flex flex-wrap gap-2">
                        @foreach ($waktuOpsi as $nilai => $opsi)
                            <button type="button" @click="waktu = '{{ $nilai }}'"
                                :class="waktu === '{{ $nilai }}'
                                    ? 'bg-leaf-700 border-leaf-700 text-white'
                                    : 'bg-white border-line text-ink-soft hover:border-leaf-600'"
                                class="min-w-[104px] flex-1 rounded-[10px] border px-2.5 py-2.5 text-center text-[13px] font-semibold leading-[1.5] transition">
                                {{ $opsi['label'] }}
                                <small class="mt-0.5 block text-[11px] font-normal"
                                    :class="waktu === '{{ $nilai }}' ? 'text-white/75' : 'text-ink-faint'">{{ $opsi['jam'] }}</small>
                            </button>
                        @endforeach
                    </div>

                    <input type="hidden" name="scheduled_time_slot" :value="waktu">

                    @error('scheduled_time_slot')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="detail_lokasi" class="mb-2.5 block text-[12.5px] font-semibold text-ink-soft">
                        Detail Lokasi (opsional)
                    </label>
                    <textarea id="detail_lokasi" name="detail_lokasi" rows="3"
                        placeholder="Contoh: Tempat sampah ada di samping pagar rumah"
                        class="{{ $inputKelas }} min-h-[70px] resize-y">{{ old('detail_lokasi') }}</textarea>
                    @error('detail_lokasi')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="mt-6 w-full rounded-[10px] bg-leaf-700 py-3.5 text-[14.5px] font-semibold text-white transition hover:bg-leaf-900">
                    Ajukan Pengambilan
                </button>
            </x-card>

        @endif

    </div>
@endsection
