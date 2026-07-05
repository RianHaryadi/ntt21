<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title') - Pesona NTT</title>
    <meta name="description" content="AI Travel Assistant - Explore NTT Hidden Gems.">

    <!-- Google Fonts: Montserrat + Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        montserrat: ['Montserrat', 'sans-serif'],
                    },
                    colors: {
                        ocean: {
                            950: '#000f1f',
                            900: '#001a33', // Deep Ocean Blue
                            800: '#002b5e',
                            700: '#004080',
                            600: '#0059b3',
                            500: '#0073e6',
                        },
                        sunset: {
                            500: '#0F6E63', // Sunset Orange
                            600: '#e55a2b',
                            400: '#ff8559',
                        },
                        light: '#f8f9fa',
                    },
                    boxShadow: {
                        'soft': '0 10px 40px -10px rgba(0,0,0,0.08)',
                        'hover': '0 20px 40px -10px rgba(0,0,0,0.15)',
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        html, body {
            height: 100%;
            scroll-behavior: smooth;
            background-color: #000f1f; /* Premium dark background */
            color: #f3f4f6;
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
        }
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #000f1f;
        }
        ::-webkit-scrollbar-thumb {
            background: #0F6E63;
            border-radius: 3px;
        }
    </style>
    @livewireStyles
</head>
<body class="antialiased overflow-x-hidden">

    <!-- Glowing Top Accent -->
    <div class="fixed top-0 left-0 w-full h-[3px] bg-gradient-to-r from-laut via-petrol to-ocean-500 z-50 shadow-[0_0_15px_#0F6E63]"></div>

    <!-- Main Container -->
    <div class="min-h-screen flex flex-col justify-between">
        <!-- Content -->
        <main class="flex-1 flex flex-col">
            @yield('content')
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
