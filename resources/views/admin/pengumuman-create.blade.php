@extends('layouts.admin')

@section('content')
    <div class="pb-1">
        <p class="mb-2 font-mono text-xs uppercase tracking-[0.08em] text-leaf-600">Konten</p>
        <h1 class="font-display text-[25px] font-semibold text-leaf-900">Buat Pengumuman</h1>
        <p class="mt-1.5 text-sm text-ink-soft">Tulis pengumuman resmi, simpan dulu sebagai draf atau langsung terbitkan.</p>
    </div>

    @include('news.partials.form', [
        'news' => null,
        'kategoriTetap' => 'pengumuman',
        'aksiSimpan' => route('admin.berita.store'),
    ])
@endsection
