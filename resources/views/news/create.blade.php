@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-800">Tambah Berita</h1>
    <p class="text-sm text-gray-500">Tulis dan publikasikan berita baru</p>

    <div x-data="{ confirmOpen: false }" class="mt-6">
        <form id="news-form" method="POST" action="{{ route('news.store') }}" enctype="multipart/form-data" class="space-y-5 rounded-2xl bg-white p-6 shadow-sm">
            @csrf

            <div>
                <label class="mb-1 block text-sm text-gray-600">Judul Berita</label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Bank Sampah Desa Kembali Beroperasi" required
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm text-gray-600">Kategori</label>
                    <select name="category" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm text-gray-700 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="" disabled {{ old('category') ? '' : 'selected' }}>Pilih Kategori</option>
                        @foreach ($categories as $value => $label)
                            <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm text-gray-600">Tanggal</label>
                    <input type="date" name="published_at" value="{{ old('published_at', now()->toDateString()) }}" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm text-gray-700 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm text-gray-600">Gambar Sampul</label>
                <label class="flex h-32 cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 text-center hover:bg-gray-50">
                    <span class="text-sm font-medium text-gray-700">Klik untuk unggah gambar</span>
                    <span class="text-xs text-gray-400">PNG atau JPG, Maks. 2MB</span>
                    <input type="file" name="cover_image" accept="image/png,image/jpeg" class="hidden">
                </label>
                @error('cover_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm text-gray-600">Isi Berita</label>
                <div id="content-editor" class="rounded-xl border border-gray-300 bg-white text-sm [&_.ql-container]:min-h-[180px] [&_.ql-container]:rounded-b-xl [&_.ql-container]:border-0 [&_.ql-container]:font-sans [&_.ql-container]:text-sm [&_.ql-toolbar]:rounded-t-xl [&_.ql-toolbar]:border-0 [&_.ql-toolbar]:border-b [&_.ql-toolbar]:border-gray-300"></div>
                <input type="hidden" name="content" id="content-input" value="{{ old('content') }}">
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p id="content-error" class="mt-1 hidden text-sm text-red-600">Isi berita wajib diisi.</p>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('news.index') }}" class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" name="action" value="draft" class="rounded-xl border border-brand-600 px-5 py-2.5 text-sm font-semibold text-brand-700 hover:bg-brand-50">Simpan Draf</button>
                <button type="button" @click="confirmOpen = true" class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Publikasikan</button>
            </div>

            <div x-show="confirmOpen" x-cloak class="fixed inset-0 z-30 flex items-center justify-center bg-black/40 px-4">
                <div @click.outside="confirmOpen = false" class="w-full max-w-md rounded-3xl bg-white p-8 shadow-xl">
                    <div class="h-14 w-14 rounded-full bg-brand-100"></div>
                    <h2 class="mt-4 text-xl text-gray-800">Publikasikan Berita ini?</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Berita ini akan langsung tampil di halaman publik dan bisa dilihat semua pengunjung. Kamu masih bisa mengeditnya nanti.
                    </p>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="confirmOpen = false" class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                        <button type="submit" name="action" value="publish" class="rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-800">Ya, Publikasikan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

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
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link'],
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
