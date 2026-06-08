<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Login' }} - Wonderful NTT</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
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
                            900: '#001a33',
                            800: '#002b5e',
                            700: '#004080',
                            600: '#0059b3',
                            500: '#0073e6',
                        },
                        sunset: {
                            500: '#ff6b35',
                            600: '#e55a2b',
                            400: '#ff8559',
                        },
                    },
                    boxShadow: {
                        'soft': '0 10px 40px -10px rgba(0,0,0,0.08)',
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        html, body { height: 100%; font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4 { font-family: 'Montserrat', sans-serif; }
        .btn-primary {
            background-color: #ff6b35;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #e55a2b;
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(255,107,53,0.3);
        }
        .cinematic-card { border-radius: 40px; }
    </style>

    @livewireStyles
</head>
<body class="antialiased min-h-screen overflow-hidden">
    {{ $slot }}
    @livewireScripts
</body>
</html>
