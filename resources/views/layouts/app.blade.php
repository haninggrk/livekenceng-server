<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Livekenceng - Software Automasi Shopee Indonesia. Dapatkan RTMP Shopee secara instan, multi akun, dan live downloader untuk meningkatkan penjualan Anda.">
    <meta name="keywords" content="shopee livestreaming, RTMP shopee, live streaming shopee, shopee automation, indonesia">
    <meta name="author" content="Livekenceng">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Livekenceng - Software Automasi Shopee Indonesia">
    <meta property="og:description" content="Software terdepan untuk automasi Shopee di Indonesia. Dapatkan RTMP Shopee secara instan, kelola multi akun, dan gunakan live downloader.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://livekenceng.com">
    <meta property="og:image" content="https://livekenceng.com/og-image.jpg">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Livekenceng - Software Automasi Shopee">
    <meta name="twitter:description" content="Software terdepan untuk automasi Shopee di Indonesia">
    <meta name="twitter:image" content="https://livekenceng.com/twitter-image.jpg">
    
    <title>@yield('title', 'Livekenceng - Software Automasi Shopee Indonesia')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="/images/logo.jpeg">
    <link rel="shortcut icon" type="image/jpeg" href="/images/logo.jpeg">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com" onload="configureTailwind()"></script>
    
    <!-- Custom Tailwind Config -->
    <script>
        function configureTailwind() {
            if (typeof tailwind !== 'undefined') {
                tailwind.config = {
                    theme: {
                        extend: {
                            colors: {
                                primary: {
                                    50: '#fff7ed',
                                    100: '#ffedd5',
                                    200: '#fed7aa',
                                    300: '#fdba74',
                                    400: '#fb923c',
                                    500: '#f97316',
                                    600: '#ea580c',
                                    700: '#c2410c',
                                    800: '#9a3412',
                                    900: '#7c2d12',
                                }
                            },
                            fontFamily: {
                                'sans': ['Inter', 'system-ui', 'sans-serif'],
                            }
                        }
                    }
                };
            }
        }
    </script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="font-sans bg-white text-gray-900">
    @yield('content')
    
    <!-- Floating WhatsApp Button (Global) -->
    <a href="https://api.whatsapp.com/send/?phone=6288989676008&text&type=phone_number&app_absent=0" target="_blank" rel="noopener" aria-label="Chat WhatsApp"
       class="fixed z-[60] right-4 bottom-4 md:right-6 md:bottom-6 group">
        <span class="relative inline-flex items-center justify-center transition transform group-hover:scale-105">
            <img src="/images/WhatsApp.webp" alt="WhatsApp" class="w-20 h-20" />
        </span>
        <span class="absolute right-20 -top-1 hidden md:inline-block bg-gray-900 text-white text-sm px-3 py-1.5 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
            Chat WhatsApp
        </span>
    </a>

    @stack('scripts')
</body>
</html>


