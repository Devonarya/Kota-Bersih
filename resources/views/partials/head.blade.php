<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title ?? 'KotaBersih' }} — Manajemen Sistem Sampah</title>

<link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    // Font untuk desain baru (landing & daftar)
                    display: ['Fraunces', 'ui-serif', 'Georgia', 'serif'],
                    body: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    mono: ['Space Mono', 'ui-monospace', 'monospace'],
                },
                colors: {
                    // Palet desain baru
                    leaf: {
                        900: '#1E3A24',
                        700: '#2E5C34',
                        600: '#3F7A45',
                        100: '#E4EFDE',
                    },
                    clay: {
                        600: '#BD5B34',
                        100: '#F4E3D8',
                    },
                    gold: {
                        600: '#C6912E',
                        100: '#F3E6C6',
                    },
                    paper: '#F6F4EC',
                    line: '#DEDCCF',
                    ink: {
                        DEFAULT: '#23291F',
                        soft: '#5B6355',
                        faint: '#8A9082',
                    },
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

@stack('styles')
