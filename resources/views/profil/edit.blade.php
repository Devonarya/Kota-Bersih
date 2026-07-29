@extends('layouts.app')

@section('content')
    @php
        $latestDeposit = $user->wasteDeposits()->latest()->first();
        $roleLabels = ['warga' => 'Warga', 'pengangkut' => 'Pengangkut', 'admin' => 'Admin'];
    @endphp

    <h1 class="text-2xl font-semibold text-gray-800">Profil Pengguna</h1>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-2xl bg-white p-6 text-center shadow-sm">
            <div class="flex justify-center">
                <x-avatar :name="$user->name" :src="$user->avatarUrl()" size="h-24 w-24 text-3xl" />
            </div>
            <p class="mt-4 text-lg font-semibold text-gray-800">{{ $user->name }}</p>
            <p class="text-brand-600">{{ $roleLabels[$user->role] ?? $user->role }}</p>

            <hr class="my-4 border-gray-100">

            <div class="space-y-2 text-left text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Bergabung</span>
                    <span class="text-gray-700">{{ $user->created_at->locale('id')->translatedFormat('M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Setoran</span>
                    <span class="text-gray-700">{{ $user->wasteDeposits()->count() }} Kali</span>
                </div>
            </div>

            <button type="button" disabled title="Segera hadir"
                class="mt-6 w-full cursor-not-allowed rounded-xl bg-gray-200 px-4 py-2 text-sm font-medium text-gray-500">
                Ubah Foto
            </button>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="font-semibold text-gray-800">Informasi Pribadi</h2>

            <form method="POST" action="{{ route('profil.update') }}" class="mt-4 space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="mb-1 block text-sm text-gray-600">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm text-gray-600">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm text-gray-600">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="mb-1 block text-sm text-gray-600">Alamat</label>
                    <input type="text" name="address" value="{{ old('address', $user->address) }}"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <button type="submit" class="rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-800">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-gray-800">Keamanan Akun</h2>
            <p class="mt-1 text-sm text-gray-500">Ubah kata sandi secara berkala untuk menjaga keamanan akun</p>

            <form method="POST" action="{{ route('profil.password') }}" class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-1 block text-sm text-gray-600">Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm text-gray-600">Kata Sandi Baru</label>
                    <input type="password" name="password" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="rounded-xl bg-gray-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">
                    Ubah Kata Sandi
                </button>
            </form>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="font-semibold text-gray-800">Aktifitas Terakhir</h2>

            <div class="mt-4 space-y-3 text-sm">
                @if ($latestDeposit)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Setoran Sampah</span>
                        <span class="text-gray-400">{{ $latestDeposit->created_at->diffForHumans() }}</span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-600">Update Profil</span>
                    <span class="text-gray-400">{{ $user->updated_at->diffForHumans() }}</span>
                </div>
                @if (! $latestDeposit)
                    <p class="text-gray-400">Belum ada aktivitas setoran.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
