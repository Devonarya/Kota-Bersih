@extends('layouts.auth')

@section('nav-hint')
    Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-leaf-700 hover:underline">Masuk</a>
@endsection

@section('content')
    {{-- x-data harus di elemen HTML biasa, bukan di tag komponen Blade: @js tidak
         ikut dikompilasi kalau ditaruh sebagai atribut <x-...>. --}}
    <div x-data="formDaftar(@js($pohonWilayah), @js([
        'role' => old('role', 'warga'),
        'kabupaten' => old('kabupaten_id', ''),
        'kecamatan' => old('kecamatan_id', ''),
        'desa' => old('desa_id', ''),
        'banjar' => old('banjar_id', ''),
    ]))">

        <div>
            <p class="mb-2.5 font-mono text-xs uppercase tracking-[0.08em] text-leaf-600">Buat akun</p>
            <h1 class="font-display text-[28px] font-semibold text-leaf-900">Daftar Anggota</h1>
            <p class="mt-2 max-w-[480px] text-sm leading-[1.6] text-ink-soft">
                Pilih mau daftar sebagai warga atau pengangkut sampah — data yang diminta menyesuaikan.
            </p>
        </div>

        {{-- Toggle peran --}}
        <div class="mt-6 flex gap-2 rounded-xl border border-line bg-white p-[5px]">
            <button type="button" @click="role = 'warga'"
                :class="role === 'warga' ? 'bg-leaf-700 text-white' : 'text-ink-soft hover:bg-leaf-100'"
                class="flex-1 rounded-lg py-2.5 text-sm font-semibold transition">
                Warga
            </button>
            <button type="button" @click="role = 'pengangkut'"
                :class="role === 'pengangkut' ? 'bg-leaf-700 text-white' : 'text-ink-soft hover:bg-leaf-100'"
                class="flex-1 rounded-lg py-2.5 text-sm font-semibold transition">
                Pengangkut Sampah
            </button>
        </div>

        <x-card as="form" method="POST" action="{{ route('register') }}" enctype="multipart/form-data"
            class="mt-[18px] overflow-hidden">
            @csrf
            <input type="hidden" name="role" :value="role">

            <div class="bg-leaf-700 px-6 py-3.5 font-mono text-xs font-bold uppercase tracking-[0.06em] text-white">
                Identitas <span x-text="labelPeran"></span>
            </div>

            <div class="px-6 pb-7 pt-6">

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-1.5 block text-xs font-semibold text-ink-soft">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Nama sesuai KTP" required
                            class="w-full rounded-[10px] border border-line bg-white px-3.5 py-3 text-sm placeholder:text-ink-faint focus:border-leaf-600 focus:outline-none focus:ring-[3px] focus:ring-leaf-100">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="mb-1.5 block text-xs font-semibold text-ink-soft">No. HP/WA</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xx-xxxx-xxxx"
                            class="w-full rounded-[10px] border border-line bg-white px-3.5 py-3 text-sm placeholder:text-ink-faint focus:border-leaf-600 focus:outline-none focus:ring-[3px] focus:ring-leaf-100">
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-1.5 block text-xs font-semibold text-ink-soft">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required
                            class="w-full rounded-[10px] border border-line bg-white px-3.5 py-3 text-sm placeholder:text-ink-faint focus:border-leaf-600 focus:outline-none focus:ring-[3px] focus:ring-leaf-100">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="password" class="mb-1.5 block text-xs font-semibold text-ink-soft">Kata Sandi</label>
                        <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" required
                            class="w-full rounded-[10px] border border-line bg-white px-3.5 py-3 text-sm placeholder:text-ink-faint focus:border-leaf-600 focus:outline-none focus:ring-[3px] focus:ring-leaf-100">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Lokasi bertingkat: tiap pilihan mereset level di bawahnya --}}
                @php
                    $kelasSelect = 'w-full rounded-[10px] border border-line bg-white px-3.5 py-3 text-sm text-ink
                                    focus:border-leaf-600 focus:outline-none focus:ring-[3px] focus:ring-leaf-100
                                    disabled:cursor-not-allowed disabled:bg-paper disabled:text-ink-faint';
                @endphp

                <div class="mt-5 border-t border-line pt-5">
                    <p class="mb-3 font-mono text-[11px] uppercase tracking-[0.06em] text-leaf-600">Lokasi</p>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="kabupaten_id" class="mb-1.5 block text-xs font-semibold text-ink-soft">Kabupaten</label>
                            <select id="kabupaten_id" name="kabupaten_id" required
                                x-model="kabupatenId" @change="gantiKabupaten()" class="{{ $kelasSelect }}">
                                <option value="" disabled>Pilih kabupaten</option>
                                <template x-for="w in pohon" :key="w.id">
                                    <option :value="w.id" x-text="w.nama"></option>
                                </template>
                            </select>
                            @error('kabupaten_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="kecamatan_id" class="mb-1.5 block text-xs font-semibold text-ink-soft">Kecamatan</label>
                            <select id="kecamatan_id" name="kecamatan_id" required
                                x-model="kecamatanId" @change="gantiKecamatan()"
                                :disabled="!kabupatenId" class="{{ $kelasSelect }}">
                                <option value="" disabled x-text="kabupatenId ? 'Pilih kecamatan' : 'Pilih kabupaten dulu'"></option>
                                <template x-for="w in daftarKecamatan" :key="w.id">
                                    <option :value="w.id" x-text="w.nama"></option>
                                </template>
                            </select>
                            @error('kecamatan_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="desa_id" class="mb-1.5 block text-xs font-semibold text-ink-soft">Desa</label>
                            <select id="desa_id" name="desa_id" required
                                x-model="desaId" @change="gantiDesa()"
                                :disabled="!kecamatanId" class="{{ $kelasSelect }}">
                                <option value="" disabled x-text="kecamatanId ? 'Pilih desa' : 'Pilih kecamatan dulu'"></option>
                                <template x-for="w in daftarDesa" :key="w.id">
                                    <option :value="w.id" x-text="w.nama"></option>
                                </template>
                            </select>
                            @error('desa_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="banjar_id" class="mb-1.5 block text-xs font-semibold text-ink-soft">
                                <span x-text="role === 'warga' ? 'Banjar' : 'Banjar Jangkauan'"></span>
                            </label>
                            <select id="banjar_id" name="banjar_id" required
                                x-model="banjarId" :disabled="!desaId" class="{{ $kelasSelect }}">
                                <option value="" disabled x-text="desaId ? 'Pilih banjar' : 'Pilih desa dulu'"></option>
                                <template x-for="w in daftarBanjar" :key="w.id">
                                    <option :value="w.id" x-text="w.nama"></option>
                                </template>
                            </select>
                            @error('banjar_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Khusus warga --}}
                <div x-show="role === 'warga'" x-cloak class="mt-4">
                    <label for="address" class="mb-1.5 block text-xs font-semibold text-ink-soft">Alamat Detail</label>
                    <textarea id="address" name="address" placeholder="Nama jalan, nomor rumah, patokan"
                        :disabled="role !== 'warga'"
                        class="min-h-[64px] w-full resize-y rounded-[10px] border border-line bg-white px-3.5 py-3 text-sm placeholder:text-ink-faint focus:border-leaf-600 focus:outline-none focus:ring-[3px] focus:ring-leaf-100">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Khusus pengangkut --}}
                <div x-show="role === 'pengangkut'" x-cloak class="mt-4">
                    <label for="ktp_number" class="mb-1.5 block text-xs font-semibold text-ink-soft">No. KTP</label>
                    <input type="text" id="ktp_number" name="ktp_number" value="{{ old('ktp_number') }}"
                        maxlength="16" inputmode="numeric" placeholder="16 digit sesuai KTP"
                        :disabled="role !== 'pengangkut'" :required="role === 'pengangkut'"
                        class="w-full rounded-[10px] border border-line bg-white px-3.5 py-3 text-sm placeholder:text-ink-faint focus:border-leaf-600 focus:outline-none focus:ring-[3px] focus:ring-leaf-100">
                    <p class="mt-1.5 text-[11.5px] text-ink-faint">
                        Dipakai pengurus banjar untuk memverifikasi pengangkut sebelum akun dipakai bertugas.
                    </p>
                    @error('ktp_number')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Berlaku untuk warga maupun pengangkut --}}
                <div class="mt-4" x-data="{ namaFile: '' }">
                    <span class="mb-1.5 block text-xs font-semibold text-ink-soft">Logo Banjar</span>

                    <label
                        @dragover.prevent="$el.classList.add('border-leaf-600','bg-leaf-100')"
                        @dragleave.prevent="$el.classList.remove('border-leaf-600','bg-leaf-100')"
                        @drop.prevent="
                            $el.classList.remove('border-leaf-600','bg-leaf-100');
                            $refs.logo.files = $event.dataTransfer.files;
                            namaFile = $refs.logo.files[0] ? $refs.logo.files[0].name : '';
                        "
                        class="block cursor-pointer rounded-xl border-[1.5px] border-dashed border-line px-5 py-8 text-center transition hover:border-leaf-600 hover:bg-leaf-100">

                        <svg class="mx-auto h-[26px] w-[26px] text-ink-faint" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 18a4.6 4.4 0 0 1-1.5-8.86A6 6 0 0 1 17.2 7.2 5 5 0 0 1 17 17H7Z" />
                            <path d="M12 12v9M9 15l3-3 3 3" />
                        </svg>

                        <p class="mt-2.5 text-[13px] text-ink-soft">Drag atau drop untuk memilih gambar</p>
                        <p class="mt-2 text-[12.5px] font-semibold text-leaf-700" x-text="namaFile"></p>

                        <input type="file" name="banjar_logo" x-ref="logo" accept="image/*" class="hidden"
                            @change="namaFile = $event.target.files[0] ? $event.target.files[0].name : ''">
                    </label>

                    <p class="mt-1.5 text-[11.5px] text-ink-faint">
                        Opsional. Format gambar, maksimal 2 MB. Logo ini dipakai bersama oleh semua anggota banjar
                        yang dipilih di atas.
                    </p>

                    @error('banjar_logo')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @error('role')
                    <p class="mt-3 text-xs text-red-600">{{ $message }}</p>
                @enderror

                <button type="submit"
                    class="mt-6 w-full rounded-[10px] bg-leaf-700 py-3.5 text-sm font-semibold text-white transition hover:bg-leaf-900">
                    Buat Akun <span x-text="labelPeran"></span>
                </button>

                <p class="mt-3 text-center text-[11.5px] text-ink-faint">
                    Data lain bisa dilengkapi nanti lewat menu Profil.
                </p>

            </div>
        </x-card>

        <p class="mt-6 text-center text-[13px] text-ink-soft">
            Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-leaf-700 hover:underline">Masuk di sini</a>
        </p>

    </div>
@endsection

@push('scripts')
<script>
    /**
     * Pohon wilayah dikirim utuh dari server, jadi pergantian dropdown tidak
     * perlu menunggu request. Tiap level yang diganti mengosongkan level di
     * bawahnya supaya tidak tersisa kombinasi yang tidak nyambung.
     */
    function formDaftar(pohon, lama) {
        return {
            pohon,
            role: lama.role,
            kabupatenId: lama.kabupaten,
            kecamatanId: lama.kecamatan,
            desaId: lama.desa,
            banjarId: lama.banjar,

            get labelPeran() {
                return this.role === 'warga' ? 'Warga' : 'Pengangkut Sampah';
            },
            get daftarKecamatan() {
                return this.pohon.find((w) => w.id == this.kabupatenId)?.anak ?? [];
            },
            get daftarDesa() {
                return this.daftarKecamatan.find((w) => w.id == this.kecamatanId)?.anak ?? [];
            },
            get daftarBanjar() {
                return this.daftarDesa.find((w) => w.id == this.desaId)?.anak ?? [];
            },

            gantiKabupaten() {
                this.kecamatanId = '';
                this.desaId = '';
                this.banjarId = '';
            },
            gantiKecamatan() {
                this.desaId = '';
                this.banjarId = '';
            },
            gantiDesa() {
                this.banjarId = '';
            },
        };
    }
</script>
@endpush
