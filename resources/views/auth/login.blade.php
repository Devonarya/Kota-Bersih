@extends('layouts.guest')

@section('content')
    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus
                class= "w-full rounded-x1 border border-gray-300 px-4 py-3 text-sm placeholder:text-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
        </div>

        <div>
            <input type="password" name="password" placeholder="Password" required
                class="w-full rounded-x1 border border-gray-300 px-4 py-3 text-sm placeholder:text-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
        </div>

        <div class="text-right">
            <span class="cursor-not-allowed text-sm font-semibold text-blue-500 underline" title="Segara hadir">Lupa Password?</span>
        </div>

        <button type="submit" class="w-full rounded-x1 bg-brand-400 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand-500">
            Masuk Ke Dashboard
        </button>
        <p class="text-center text-sm text-gray-600">
            Belum punya akun? <a href="{{ route('register') }}" class= "font-semibold text0-blue-500 underline">Daftar disini.</a>
        </p>
    </form>
@endsection