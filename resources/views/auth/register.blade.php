@extends('layouts.guest')

@section('content')
    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <select name="banjar_id" required
            class="w-full rounded-x1 border border-gray-300 px-4 py-3 text-sm text-gray-700 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            <option value="" disabled {{ old('banjar_id') ? '' : 'selected' }}>Pilih Banjar</option>
            @foreach($banjars as $banjar)
                <option value="{{ $bajar->id }}" @selected(old('banjar_id') == $banjar -> id)>{{ $banjar->name }}</option>
            @endforeach
            </select>
            @error('banjar_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required
                class="w-full rounded-x1 border border-gray-300 px-4 py-3 text-sm placeholder:text-gray-500 focus:border-brand-500 focus:outline-none focus:ring-brand-500">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama" required
                class="w-full rounded-x1 border border-gray-300 px-4 py-3 text-sm placeholder:text-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <input type="password" name="password" placeholder="Password" required
                class="w-full rounded-x1 border border-gray-300 px-4 py-3 text-sm placeholder:text-gray-500 focus:border-brand-500 focus outline-none focus:ring-1 focus:ring-brand-500">
            @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <input type="password" name="password" placeholder="Password" required
                class="w-full rounded-x1 border border-gray-300 px-4 py-3 text-sm placeholder:text-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full rounded-x1 bg-brand-400 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand-500">
            Masuk Ke Dashboard
        </button>

        <p class="text-center text-sm text-gray-600">
            Data lain seperti nomor telepon dan alamat bisa dilengkapi di menu profile
        </p>

        <p class="text-center text-sm text-gray-600">
            sudah punya akun masuk disini? <a href="{{ route('login') }}" class="font-semibold text-blue-500 underline">Masuk disini</a>
        </p>
    </form>
@endsection