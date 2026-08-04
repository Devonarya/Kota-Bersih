@extends('layouts.admin')

@php
    $kelasField = 'w-full rounded-[10px] border border-line bg-white px-3.5 py-2.5 text-sm text-ink
                   focus:border-leaf-600 focus:outline-none focus:ring-[3px] focus:ring-leaf-100';
    $kelasLabel = 'mb-1.5 block text-[12.5px] font-semibold text-ink';
@endphp

@section('content')

    <div x-data="{ editOpen: false, hapusOpen: false, item: {} }">

        {{-- Judul halaman --}}
        <div class="pb-1">
            <p class="mb-2 font-mono text-xs uppercase tracking-[0.08em] text-leaf-600">Operasional</p>
            <h1 class="font-display text-[25px] font-semibold text-leaf-900">Banjar</h1>
            <p class="mt-1.5 text-sm text-ink-soft">Kelola data banjar yang terdaftar di sistem.</p>
        </div>

        {{-- Modal tertutup begitu halaman dimuat ulang, jadi galat validasinya
             ditampilkan di sini supaya tidak hilang tanpa jejak. --}}
        @if ($errors->any())
            <div class="mt-5 rounded-[14px] border border-clay-600 bg-clay-100 px-4 py-3 text-sm text-clay-600">
                @foreach ($errors->all() as $pesan)
                    <p @class(['mt-1' => ! $loop->first])>{{ $pesan }}</p>
                @endforeach
            </div>
        @endif

        {{-- Daftar banjar --}}
        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($banjars as $banjar)
                @php
                    $bisaDihapus = $banjar->users_count === 0 && $banjar->waste_deposits_count === 0;

                    $dataItem = [
                        'nama' => $banjar->name,
                        'desa' => $banjar->desa,
                        'deskripsi' => $banjar->description,
                        'keluarga' => $banjar->family_count,
                        'anggota' => $banjar->users_count,
                        'setoran' => $banjar->waste_deposits_count,
                        'bisaDihapus' => $bisaDihapus,
                        'aksiUbah' => route('admin.banjar.update', $banjar),
                        'aksiHapus' => route('admin.banjar.destroy', $banjar),
                    ];
                @endphp

                <div class="overflow-hidden rounded-[14px] border border-line bg-white">
                    <div class="flex h-40 items-center justify-center bg-leaf-700">
                        @if ($banjar->logoUrl())
                            <img src="{{ $banjar->logoUrl() }}" alt="Logo {{ $banjar->name }}"
                                class="h-full w-full object-cover">
                        @else
                            <span class="font-display text-2xl font-semibold text-white/70">Gambar</span>
                        @endif
                    </div>

                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                @if ($banjar->desa)
                                    <span class="inline-block rounded-full bg-leaf-100 px-3 py-1 text-xs font-medium text-leaf-700">
                                        {{ $banjar->desa }}
                                    </span>
                                @endif
                                <p class="mt-2 font-display text-lg font-semibold text-leaf-900">{{ $banjar->name }}</p>
                            </div>

                            {{-- Aksi: ubah & hapus --}}
                            <div class="flex shrink-0 gap-1.5">
                                <button type="button" title="Ubah banjar"
                                    @click="item = @js($dataItem); editOpen = true"
                                    class="flex h-9 w-9 items-center justify-center rounded-[9px] border border-line text-ink-soft hover:bg-paper hover:text-leaf-700">
                                    <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9" />
                                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                    </svg>
                                </button>

                                <button type="button"
                                    title="{{ $bisaDihapus ? 'Hapus banjar' : 'Masih dipakai, tidak bisa dihapus' }}"
                                    @click="item = @js($dataItem); hapusOpen = true"
                                    class="flex h-9 w-9 items-center justify-center rounded-[9px] border text-ink-soft
                                        {{ $bisaDihapus
                                            ? 'border-line hover:bg-clay-100 hover:text-clay-600'
                                            : 'border-line opacity-40' }}">
                                    <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6" />
                                        <path d="M10 11v6M14 11v6" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <p class="mt-1.5 text-[13px] text-ink-soft">
                            {{ $banjar->family_count }} keluarga terdaftar ·
                            {{ $banjar->users_count }} anggota aktif di sistem
                        </p>
                        <p class="mt-0.5 font-mono text-[11px] text-ink-faint">
                            {{ $banjar->waste_deposits_count }} riwayat setoran
                        </p>
                    </div>
                </div>
            @empty
                <div class="rounded-[14px] border border-line bg-white px-5 py-[60px] text-center text-sm text-ink-faint sm:col-span-2 lg:col-span-3">
                    Belum ada banjar terdaftar.
                </div>
            @endforelse
        </div>

        {{-- ====================== Modal: ubah banjar ====================== --}}
        <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-leaf-900/45 p-6">
            <div @click.outside="editOpen = false"
                class="max-h-[88vh] w-full max-w-[480px] overflow-y-auto rounded-2xl bg-white">
                <div class="flex items-center justify-between border-b border-line px-[22px] py-5">
                    <h3 class="font-display text-[17px] font-semibold text-leaf-900">Ubah Banjar</h3>
                    <button type="button" @click="editOpen = false"
                        class="h-[30px] w-[30px] rounded-full bg-paper text-base leading-none text-ink-soft">&times;</button>
                </div>

                <form method="POST" :action="item.aksiUbah" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-4 px-[22px] py-5">
                        <div>
                            <label for="name" class="{{ $kelasLabel }}">Nama Banjar</label>
                            <input id="name" type="text" name="name" required maxlength="255"
                                :value="item.nama" class="{{ $kelasField }}">
                            @error('name')
                                <p class="mt-1 text-xs text-clay-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="desa" class="{{ $kelasLabel }}">Desa</label>
                            <input id="desa" type="text" name="desa" maxlength="255"
                                :value="item.desa" placeholder="Contoh: Desa Dauhwaru" class="{{ $kelasField }}">
                            @error('desa')
                                <p class="mt-1 text-xs text-clay-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="family_count" class="{{ $kelasLabel }}">Jumlah Keluarga</label>
                            <input id="family_count" type="number" name="family_count" required min="0"
                                :value="item.keluarga" class="{{ $kelasField }}">
                            @error('family_count')
                                <p class="mt-1 text-xs text-clay-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="{{ $kelasLabel }}">Deskripsi</label>
                            <textarea id="description" name="description" maxlength="1000" rows="3"
                                :value="item.deskripsi" placeholder="Keterangan singkat tentang banjar ini."
                                class="{{ $kelasField }} resize-y"></textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-clay-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="logo" class="{{ $kelasLabel }}">Ganti Logo</label>
                            <input id="logo" type="file" name="logo" accept="image/*"
                                class="w-full text-sm text-ink-soft file:mr-3 file:rounded-[9px] file:border-0
                                       file:bg-leaf-100 file:px-3.5 file:py-2 file:text-[13px] file:font-semibold file:text-leaf-700">
                            <p class="mt-1 text-xs text-ink-faint">Biarkan kosong kalau logonya tidak diganti. Maksimal 2 MB.</p>
                            @error('logo')
                                <p class="mt-1 text-xs text-clay-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex gap-2.5 px-[22px] pb-[22px] pt-1">
                        <button type="button" @click="editOpen = false"
                            class="flex-1 rounded-[10px] border border-line bg-paper py-3 text-sm font-semibold text-ink-soft">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 rounded-[10px] bg-leaf-700 py-3 text-sm font-semibold text-white hover:bg-leaf-900">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ====================== Modal: hapus banjar ====================== --}}
        <div x-show="hapusOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-leaf-900/45 p-6">
            <div @click.outside="hapusOpen = false" class="w-full max-w-[480px] rounded-2xl bg-white">
                <div class="flex items-center justify-between border-b border-line px-[22px] py-5">
                    <h3 class="font-display text-[17px] font-semibold text-leaf-900">Hapus Banjar</h3>
                    <button type="button" @click="hapusOpen = false"
                        class="h-[30px] w-[30px] rounded-full bg-paper text-base leading-none text-ink-soft">&times;</button>
                </div>

                {{-- Masih dipakai: hanya penjelasan, tanpa tombol hapus --}}
                <template x-if="!item.bisaDihapus">
                    <div>
                        <div class="px-[22px] py-5">
                            <p class="text-[13.5px] leading-relaxed text-ink-soft">
                                <strong class="text-ink" x-text="item.nama"></strong> belum bisa dihapus karena masih dipakai:
                            </p>
                            <ul class="mt-3 space-y-1.5 text-[13.5px] text-ink-soft">
                                <template x-if="item.anggota > 0">
                                    <li>· <span x-text="item.anggota"></span> anggota terdaftar di banjar ini</li>
                                </template>
                                <template x-if="item.setoran > 0">
                                    <li>· <span x-text="item.setoran"></span> riwayat setoran akan ikut terhapus</li>
                                </template>
                            </ul>
                            <p class="mt-3 text-xs text-ink-faint">
                                Pindahkan anggotanya ke banjar lain lebih dulu, baru banjar ini bisa dihapus.
                            </p>
                        </div>

                        <div class="px-[22px] pb-[22px] pt-1">
                            <button type="button" @click="hapusOpen = false"
                                class="w-full rounded-[10px] border border-line bg-paper py-3 text-sm font-semibold text-ink-soft">
                                Tutup
                            </button>
                        </div>
                    </div>
                </template>

                {{-- Kosong: boleh dihapus --}}
                <template x-if="item.bisaDihapus">
                    <form method="POST" :action="item.aksiHapus">
                        @csrf
                        @method('DELETE')

                        <div class="px-[22px] py-5">
                            <p class="text-[13.5px] leading-relaxed text-ink-soft">
                                Hapus banjar <strong class="text-ink" x-text="item.nama"></strong>?
                                Banjar ini belum punya anggota maupun riwayat setoran.
                            </p>
                            <p class="mt-3 text-xs text-ink-faint">Tindakan ini tidak bisa dibatalkan.</p>
                        </div>

                        <div class="flex gap-2.5 px-[22px] pb-[22px] pt-1">
                            <button type="button" @click="hapusOpen = false"
                                class="flex-1 rounded-[10px] border border-line bg-paper py-3 text-sm font-semibold text-ink-soft">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 rounded-[10px] bg-clay-600 py-3 text-sm font-semibold text-white hover:opacity-90">
                                Hapus Banjar
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>

    </div>

@endsection
