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
    
    <!-- Floating WhatsApp Button (Global) -->
    <a href="https://api.whatsapp.com/send/?phone=6288989676008&text&type=phone_number&app_absent=0" target="_blank" rel="noopener" aria-label="Chat WhatsApp"
       class="fixed z-[60] right-4 bottom-4 md:right-6 md:bottom-6 group">
        <span class="relative inline-flex items-center justify-center w-14 h-14 rounded-full shadow-lg transition transform group-hover:scale-105"
              style="background: #25D366;">
            <!-- Simple WhatsApp icon -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="w-7 h-7 text-white">
                <path fill="currentColor" d="M19.11 17.34c-.27-.14-1.6-.79-1.85-.88c-.25-.09-.43-.14-.61.14c-.18.27-.7.88-.86 1.06c-.16.18-.31.2-.57.07c-.27-.14-1.09-.4-2.08-1.28c-.78-.7-1.31-1.56-1.46-1.82c-.15-.27-.02-.41.11-.54c.12-.12.26-.3.39-.45c.13-.15.17-.27.26-.45c.08-.18.05-.33-.02-.47c-.07-.13-.6-1.45-.83-1.99c-.22-.53-.44-.46-.6-.46h-.5c-.18 0-.45.07-.69.32c-.24.25-.92.93-.92 2.26c0 1.33.95 2.61 1.08 2.8c.13.18 1.86 2.83 4.52 3.95c.64.27 1.13.43 1.52.54c.64.2 1.22.18 1.68.11c.51-.08 1.56-.63 1.77-1.23c.22-.61.22-1.12.15-1.22c-.06-.1-.24-.18-.51-.31z"/>
                <path fill="currentColor" d="M16 3C9.38 3 4 8.38 4 15c0 2.56.83 4.93 2.24 6.86L5 29l7.36-1.94C13.7 27.7 14.83 28 16 28c6.62 0 12-5.38 12-12S22.62 3 16 3m0 22c-1.17 0-2.3-.3-3.3-.86l-.24-.14l-4.48 1.19l1.2-4.36l-.15-.23C7.3 19.51 7 18.33 7 17c0-4.97 4.03-9 9-9s9 4.03 9 9s-4.03 9-9 9"/>
            </svg>
        </span>
        <span class="absolute right-20 -top-1 hidden md:inline-block bg-gray-900 text-white text-sm px-3 py-1.5 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
            Chat WhatsApp
        </span>
    </a>

    @stack('scripts')
</body>
</html>


