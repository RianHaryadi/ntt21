<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Itinerary Rekomendasi Ara - Wonderful NTT</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

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
                        ocean: { 950:'#000f1f', 900:'#001a33', 800:'#002b5e', 700:'#004080', 600:'#0059b3', 500:'#0073e6' },
                        sunset: { 500:'#ff6b35', 600:'#e55a2b', 400:'#ff8559' },
                    },
                    boxShadow: { soft: '0 10px 40px -10px rgba(0,0,0,0.08)' }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        html, body { font-family: 'Inter', sans-serif; background: #f8f9fa; }
        h1,h2,h3,h4,h5 { font-family: 'Montserrat', sans-serif; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #ff6b35; border-radius: 3px; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
    @livewireStyles
</head>
<body class="antialiased">
    <livewire:travel-recommendation :token="$token" />
    @livewireScripts
</body>
</html>
