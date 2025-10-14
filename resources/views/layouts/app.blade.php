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
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Config -->
    <script>
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
    
    @stack('scripts')
</body>
</html>


