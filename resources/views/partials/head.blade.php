<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title ?? 'KotaBersih' }} — Manajemen Sistem Sampah</title>

<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                },
                colors: {
                    brand: {
                        50: '#eef7ee',
                        100: '#d7ecd8',
                        200: '#b0d9b2',
                        300: '#82c085',
                        400: '#7cb87f',
                        500: '#4d9450',
                        600: '#357638',
                        700: '#2f7d32',
                        800: '#275f2a',
                        900: '#204d24',
                    },
                },
            },
        },
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>

<style>
    [x-cloak] { display: none !important; }
</style>
