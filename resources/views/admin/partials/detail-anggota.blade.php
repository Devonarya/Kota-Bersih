{{-- Isi modal detail anggota. Dipakai halaman Warga/Pengangkut dan Permintaan —
     keduanya menampilkan orang yang sama, hanya tombol di bawahnya yang berbeda.
     Sumber datanya objek Alpine `item` yang diisi tombol "Lihat Detail". --}}
<div class="grid grid-cols-1 gap-x-4 gap-y-3.5 sm:grid-cols-2">
    <x-field label="Nama Lengkap" text="item.nama" />
    <x-field label="Peran" text="item.peran" />
    <x-field label="No. HP/WA" text="item.hp" />
    <x-field label="Email" text="item.email" value-class="break-all text-sm" />
    <x-field label="Banjar" text="item.banjar" />
    <x-field label="Tanggal Daftar" text="item.tanggal" value-class="font-mono text-sm" />

    {{-- Bagian yang berbeda antara warga & pengangkut --}}
    <div class="sm:col-span-2">
        <template x-if="item.isWarga">
            <x-field label="Alamat Detail" text="item.alamat" />
        </template>

        <template x-if="!item.isWarga">
            <div>
                <x-field label="Banjar Jangkauan">
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="banjar in item.jangkauan" :key="banjar">
                            <span class="rounded-full bg-leaf-100 px-2.5 py-1 text-xs font-semibold text-leaf-700"
                                x-text="banjar"></span>
                        </template>
                    </div>
                </x-field>

                <x-field label="No. KTP" text="item.ktp" value-class="font-mono text-sm" class="mt-3" />
            </div>
        </template>

        {{-- Logo milik banjar, bukan peran, jadi tampil untuk warga maupun pengangkut --}}
        <x-field label="Logo Banjar" class="mt-3">
            <template x-if="item.logoUrl">
                <img :src="item.logoUrl" :alt="item.banjar"
                    class="h-11 w-11 rounded-[10px] border border-line object-cover">
            </template>
            <template x-if="!item.logoUrl">
                <div class="flex h-11 w-11 items-center justify-center rounded-[10px] bg-leaf-100 text-leaf-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16v16H4z" /><path d="m4 16 4-4 4 4 6-6" />
                    </svg>
                </div>
            </template>
            <div class="mt-1 text-xs text-ink-faint" x-text="item.logoNama"></div>
        </x-field>
    </div>
</div>
