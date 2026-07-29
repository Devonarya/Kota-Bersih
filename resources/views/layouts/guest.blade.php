<!DOCTYPE html>
<html lang="id" class="font-sans">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-gray-100">
    <div class="relative min-h-screen">
        <div class="h-56 w-full bg-brand-700"></div>

        <div class="absoulute inset-x-0 top-14 flex justify-center px-4">
            <div class="w-full max-w-md rounded-3x1 bg-white p-8 shadow-x1">
                <div class="flex justify-center">
                    <div class="h-20 w-20 rounded-full bg-gray-200"></div>
                </div>

                <h1 class="mt-6 text-center text-2x1 text-gray-800">Selamat Datang</h1>
                <p class="text center text-2x1 text-gray-800">KotaBersih</p>

                @yield('content');
            </div>
        </div>
    </div>
</body>
</html>
</html>