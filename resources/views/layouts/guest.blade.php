<!DOCTYPE html>
<html lang="id" class="font-sans">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-gray-100">
    <div class="relative min-h-screen">
        <div class="h-56 w-full bg-brand-700"></div>

        <div class="absolute inset-x-0 top-14 flex justify-center px-4">
            <div class="w-full max-w-md rounded-3xl bg-white p-8 shadow-xl">
                <div class="flex justify-center">
                    <div class="h-20 w-20 rounded-full bg-gray-200"></div>
                </div>

                <h1 class="mt-6 text-center text-2xl text-gray-800">Selamat Datang</h1>
                <p class="text-center text-2xl text-gray-800">Kota Bersih</p>

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
