<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Login' }} - Pesona NTT</title>

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
                            500: '#0F6E63',
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
            background: linear-gradient(135deg, #0F6E63 0%, #e55a2b 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.25);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 107, 53, 0.45);
        }
        .cinematic-card { border-radius: 40px; }
    </style>

    @livewireStyles
</head>
<body class="antialiased min-h-screen bg-slate-50 text-slate-800 relative flex flex-col justify-center" style="background-color: #fafaf9;">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_var(--tw-gradient-stops))] from-white via-slate-50 to-slate-100 pointer-events-none z-0"></div>
    <div class="relative z-10 w-full">
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
