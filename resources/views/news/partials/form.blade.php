{{--
    Form berita, dipakai halaman tambah maupun ubah — juga dipakai admin untuk
    membuat pengumuman lewat $kategoriTetap & $aksiSimpan.

    Variabel yang diharapkan:
    - $news          News|null  — null berarti tulisan baru
    - $categories    array
    - $kategoriTetap string|null — kalau diisi, dropdown kategori diganti tampilan terkunci
    - $aksiSimpan     string|null — tujuan submit waktu $news masih null, bawaannya route('news.store')
--}}
@php
    $news ??= null;
    $kategoriTetap ??= null;
    $aksiSimpan ??= route('news.store');
    $sudahTerbit = $news?->status === 'published';

    // Nilai lama menang supaya isian tidak hilang saat validasi gagal.
    $nilai = fn (string $field, $bawaan = null) => old($field, $news?->{$field} ?? $bawaan);

    $kelasField = 'w-full rounded-[10px] border border-line bg-white px-3.5 py-2.5 text-sm text-ink
                   focus:border-leaf-600 focus:outline-none focus:ring-[3px] focus:ring-leaf-100';
    $kelasLabel = 'mb-1.5 block text-[12.5px] font-semibold text-ink';
@endphp

<div x-data="{ konfirmasiOpen: false }" class="mt-6 max-w-[760px]">
    <x-card as="form" id="news-form" method="POST" enctype="multipart/form-data"
        action="{{ $news ? route('news.update', $news) : $aksiSimpan }}"
        class="space-y-5 p-6">
        @csrf
        @if ($news)
            @method('PATCH')
        @endif

        <div>
            <label for="title" class="{{ $kelasLabel }}">Judul Berita</label>
            <input id="title" type="text" name="title" required maxlength="255"
                value="{{ $nilai('title') }}"
                placeholder="Contoh: Bank Sampah Desa Kembali Beroperasi" class="{{ $kelasField }}">
            @error('title')
                <p class="mt-1 text-xs text-clay-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                @if ($kategoriTetap)
                    <span class="{{ $kelasLabel }}">Kategori</span>
                    <div class="{{ $kelasField }} bg-paper text-ink-soft">{{ $categories[$kategoriTetap] ?? $kategoriTetap }}</div>
                    <input type="hidden" name="category" value="{{ $kategoriTetap }}">
                @else
                    <label for="category" class="{{ $kelasLabel }}">Kategori</label>
                    <select id="category" name="category" required class="{{ $kelasField }}">
                        <option value="" disabled @selected(! $nilai('category'))>Pilih Kategori</option>
                        @foreach ($categories as $value => $label)
                            <option value="{{ $value }}" @selected($nilai('category') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                @endif
                @error('category')
                    <p class="mt-1 text-xs text-clay-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="published_at" class="{{ $kelasLabel }}">Tanggal</label>
                <input id="published_at" type="date" name="published_at" required
                    value="{{ old('published_at', $news?->published_at?->toDateString() ?? now()->toDateString()) }}"
                    class="{{ $kelasField }}">
                @error('published_at')
                    <p class="mt-1 text-xs text-clay-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <span class="{{ $kelasLabel }}">Gambar Sampul</span>

            @if ($news?->cover_image_path)
                <div class="mb-2.5 flex items-center gap-3">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($news->cover_image_path) }}" alt=""
                        class="h-16 w-24 rounded-[10px] border border-line object-cover">
                    <span class="text-xs text-ink-faint">Sampul sekarang. Unggah baru untuk menggantinya.</span>
                </div>
            @endif

            <label class="flex h-28 cursor-pointer flex-col items-center justify-center rounded-[10px] border border-dashed border-line text-center hover:bg-paper">
                <span class="text-[13px] font-semibold text-ink">Klik untuk unggah gambar</span>
                <span class="mt-0.5 text-xs text-ink-faint">PNG atau JPG, maksimal 2 MB</span>
                <input type="file" name="cover_image" accept="image/png,image/jpeg" class="hidden">
            </label>
            @error('cover_image')
                <p class="mt-1 text-xs text-clay-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label id="content-label" class="{{ $kelasLabel }}">Isi Berita</label>
            <div id="content-editor" aria-labelledby="content-label"
                class="rounded-[10px] border border-line bg-white text-sm
                       [&_.ql-container]:min-h-[180px] [&_.ql-container]:rounded-b-[10px] [&_.ql-container]:border-0 [&_.ql-container]:font-body [&_.ql-container]:text-sm
                       [&_.ql-toolbar]:rounded-t-[10px] [&_.ql-toolbar]:border-0 [&_.ql-toolbar]:border-b [&_.ql-toolbar]:border-line"></div>
            <input type="hidden" name="content" id="content-input" value="{{ $nilai('content') }}">
            @error('content')
                <p class="mt-1 text-xs text-clay-600">{{ $message }}</p>
            @enderror
            <p id="content-error" class="mt-1 hidden text-xs text-clay-600">Isi berita wajib diisi.</p>
        </div>

        <div class="flex flex-wrap justify-end gap-2.5 pt-1">
            <a href="{{ auth()->user()->role === 'admin' ? route('admin.berita.index') : route('news.mine') }}"
                class="rounded-[10px] border border-line bg-paper px-5 py-2.5 text-sm font-semibold text-ink-soft hover:bg-white">
                Batal
            </a>

            <button type="submit" name="action" value="draft"
                class="rounded-[10px] border border-leaf-700 px-5 py-2.5 text-sm font-semibold text-leaf-700 hover:bg-leaf-100">
                {{ $sudahTerbit ? 'Jadikan Draf' : 'Simpan Draf' }}
            </button>

            @if ($sudahTerbit)
                {{-- Sudah tayang, tidak perlu konfirmasi lagi. --}}
                <button type="submit" name="action" value="publish"
                    class="rounded-[10px] bg-leaf-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-leaf-900">
                    Simpan Perubahan
                </button>
            @else
                <button type="button" @click="konfirmasiOpen = true"
                    class="rounded-[10px] bg-leaf-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-leaf-900">
                    Publikasikan
                </button>
            @endif
        </div>

        {{-- Konfirmasi sebelum tulisan pertama kali tayang ke publik --}}
        <div x-show="konfirmasiOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-leaf-900/45 p-6">
            <div @click.outside="konfirmasiOpen = false" class="w-full max-w-[480px] rounded-2xl bg-white p-7">
                <h2 class="font-display text-xl font-semibold text-leaf-900">Publikasikan berita ini?</h2>
                <p class="mt-2 text-[13.5px] leading-relaxed text-ink-soft">
                    Berita akan tampil di halaman publik dan bisa dibaca siapa saja, termasuk pengunjung
                    yang belum punya akun. Kamu masih bisa mengubahnya lagi nanti lewat Tulisan Saya.
                </p>

                <div class="mt-6 flex justify-end gap-2.5">
                    <button type="button" @click="konfirmasiOpen = false"
                        class="rounded-[10px] border border-line bg-paper px-5 py-2.5 text-sm font-semibold text-ink-soft">
                        Batal
                    </button>
                    <button type="submit" name="action" value="publish"
                        class="rounded-[10px] bg-leaf-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-leaf-900">
                        Ya, Publikasikan
                    </button>
                </div>
            </div>
        </div>
    </x-card>
</div>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.js"></script>
    <script>
        const quill = new Quill('#content-editor', {
            theme: 'snow',
            placeholder: 'Tulis isi berita di sini...',
            modules: {
                toolbar: [
                    [{ header: [2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    ['blockquote'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link', 'image'],
                    ['clean'],
                ],
            },
        });

        const contentInput = document.getElementById('content-input');
        if (contentInput.value) {
            quill.clipboard.dangerouslyPasteHTML(contentInput.value);
        }

        document.getElementById('news-form').addEventListener('submit', (event) => {
            const isEmpty = quill.getText().trim().length === 0;
            contentInput.value = isEmpty ? '' : quill.root.innerHTML;

            const errorEl = document.getElementById('content-error');
            if (isEmpty) {
                event.preventDefault();
                errorEl.classList.remove('hidden');
            } else {
                errorEl.classList.add('hidden');
            }
        });
    </script>
@endpush
