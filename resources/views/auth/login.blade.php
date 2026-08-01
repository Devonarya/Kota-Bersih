@extends('layouts.auth')

@section('nav-hint')
    Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-leaf-700 hover:underline">Daftar</a>
@endsection

@section('content')
    <div class="mx-auto max-w-[440px]">
        <div class="rounded-[14px] border border-line bg-white px-[30px] pb-[30px] pt-[34px] text-center">

            <x-logo size="h-[52px] w-[52px]" class="mx-auto mb-[18px]" />

            <p class="mb-2 font-mono text-xs uppercase tracking-[0.08em] text-leaf-600">Masuk</p>
            <h1 class="font-display text-[23px] font-semibold text-leaf-900">Selamat datang kembali</h1>
            <p class="mx-auto mb-[26px] mt-2 text-[13.5px] leading-[1.6] text-ink-soft">
                Masuk untuk mulai setor sampah dan pantau riwayatmu di banjar.
            </p>

            <form method="POST" action="{{ route('login') }}" class="text-left" x-data="{ lihat: false }">
                @csrf

                <div class="mb-4">
                    <label for="email" class="mb-1.5 block text-xs font-semibold text-ink-soft">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        placeholder="nama@email.com" required autofocus
                        class="w-full rounded-[10px] border border-line bg-white px-3.5 py-3 text-sm placeholder:text-ink-faint focus:border-leaf-600 focus:outline-none focus:ring-[3px] focus:ring-leaf-100">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="mb-1.5 block text-xs font-semibold text-ink-soft">Kata Sandi</label>

                    <div class="relative">
                        <input :type="lihat ? 'text' : 'password'" id="password" name="password"
                            placeholder="Masukkan kata sandi" required
                            class="w-full rounded-[10px] border border-line bg-white py-3 pl-3.5 pr-11 text-sm placeholder:text-ink-faint focus:border-leaf-600 focus:outline-none focus:ring-[3px] focus:ring-leaf-100">

                        <button type="button" @click="lihat = ! lihat"
                            :aria-label="lihat ? 'Sembunyikan kata sandi' : 'Lihat kata sandi'"
                            class="absolute right-2.5 top-1/2 flex -translate-y-1/2 p-1 text-ink-faint hover:text-ink-soft">

                            <svg x-show="! lihat" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>

                            <svg x-show="lihat" x-cloak class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.6 21.6 0 0 1 5.06-5.94M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 7 11 7a21.6 21.6 0 0 1-2.87 3.94M14.12 14.12a3 3 0 1 1-4.24-4.24" />
                                <path d="M1 1l22 22" />
                            </svg>
                        </button>
                    </div>

                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- Di bawah field password, bukan di atas --}}
                    <div class="mt-2 text-right">
                        <span class="cursor-not-allowed text-xs font-semibold text-ink-faint" title="Segera hadir">
                            Lupa kata sandi?
                        </span>
                    </div>
                </div>

                <button type="submit"
                    class="mt-1.5 w-full rounded-[10px] bg-leaf-700 py-3.5 text-sm font-semibold text-white transition hover:bg-leaf-900">
                    Masuk
                </button>
            </form>

            <p class="mt-[22px] text-center text-[13px] text-ink-soft">
                Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-leaf-700 hover:underline">Daftar di sini</a>
            </p>

        </div>
    </div>
@endsection
