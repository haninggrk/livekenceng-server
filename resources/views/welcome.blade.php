@extends('layouts.app')

@section('content')
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Livekenceng Logo" class="w-10 h-10 rounded-lg">
                    <span class="ml-3 text-2xl font-bold text-gray-900">Livekenceng</span>
                </div>
                
                <!-- Navigation -->
                <nav class="hidden md:flex space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-primary-600 transition-colors">Fitur</a>
                    <a href="#pricing" class="text-gray-600 hover:text-primary-600 transition-colors">Harga</a>
                    <a href="#contact" class="text-gray-600 hover:text-primary-600 transition-colors">Kontak</a>
                    <a href="{{ route('download') }}" class="text-gray-600 hover:text-primary-600 transition-colors">Download</a>
                </nav>
                
                <!-- CTA Button (Primary: WhatsApp) -->
                <a href="https://api.whatsapp.com/send/?phone=6288989676008&text&type=phone_number&app_absent=0" target="_blank" rel="noopener" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Beli via WhatsApp
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-orange-50 via-primary-50 to-orange-100 py-20 lg:py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Content -->
                <div>
                    <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                        Software Automasi 
                        <span class="text-primary-500">Shopee</span> 
                        Indonesia
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        Tingkatkan penjualan Anda dengan automasi Shopee. 
                        Dapatkan RTMP Shopee secara instan, kelola multi akun, dan nikmati fitur live downloader canggih.
                    </p>
                    
                    <!-- CTA Buttons (Primary: WhatsApp) -->
                    <div class="flex flex-col sm:flex-row gap-4 mb-8">
                        <a href="https://api.whatsapp.com/send/?phone=6288989676008&text&type=phone_number&app_absent=0" target="_blank" rel="noopener" class="bg-primary-500 hover:bg-primary-600 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-colors text-center">
                            Beli / Trial via WhatsApp
                        </a>
                        <a href="#features" class="border-2 border-primary-500 text-primary-500 hover:bg-primary-50 px-8 py-4 rounded-lg font-semibold text-lg transition-colors text-center">
                            Lihat Fitur
                        </a>
                    </div>
                    
                    <!-- Social Proof -->
                    <div class="flex items-center space-x-2">
                        <div class="flex text-yellow-400">
                            <span>★★★★★</span>
                        </div>
                        <span class="text-gray-600">Dipakai oleh 1000+ seller Indonesia</span>
                    </div>
                </div>
                
                <!-- Hero Image -->
                <div class="relative">
                    <div class="bg-gradient-to-br from-primary-400 to-primary-600 rounded-2xl p-4 shadow-2xl">
                        <img src="{{ asset('images/livestreaming.jpg') }}" alt="Livekenceng Live Streaming Interface" class="w-full rounded-xl shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                    Fitur Canggih untuk Shopee
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Nikmati kemudahan dan kecepatan dalam mengelola Shopee 
                    dengan fitur-fitur automasi unggulan kami.
                </p>
            </div>
            
            <div class="grid lg:grid-cols-2 gap-12 items-center mb-16">
                <!-- Features Text -->
                <div>
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Feature 1 -->
                        <div class="bg-gradient-to-br from-orange-50 to-primary-50 rounded-2xl p-6 text-center">
                            <div class="w-12 h-12 bg-primary-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-3">Dapatkan RTMP</h3>
                            <p class="text-sm text-gray-600">
                                Dapatkan RTMP di semua tipe akun shopee bahkan yang belum terverifikasi. 
                                Tidak perlu menunggu atau konfirmasi manual.
                            </p>
                        </div>
                        
                        <!-- Feature 2 -->
                        <div class="bg-gradient-to-br from-orange-50 to-primary-50 rounded-2xl p-6 text-center">
                            <div class="w-12 h-12 bg-primary-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-3">Multi Account</h3>
                            <p class="text-sm text-gray-600">
                                Satu lisensi dapat digunakan untuk beberapa toko tanpa harus membeli lisensi tambahan. 
                                Kelola banyak toko dengan mudah.
                            </p>
                        </div>
                        
                        <!-- Feature 3 -->
                        <div class="bg-gradient-to-br from-orange-50 to-primary-50 rounded-2xl p-6 text-center md:col-span-2">
                            <div class="w-12 h-12 bg-primary-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-3">Live Downloader</h3>
                            <p class="text-sm text-gray-600">
                                Download rekaman live streaming Shopee dan TikTok otomatis untuk digunakan sebagai 
                                konten marketing atau arsip penjualan.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Feature Screenshot -->
                <div class="relative">
                    <div class="bg-gradient-to-br from-primary-400 to-primary-600 rounded-2xl p-4 shadow-2xl">
                        <img src="{{ asset('images/downloader.jpg') }}" alt="Livekenceng Downloader Interface" class="w-full rounded-xl shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-gradient-to-br from-orange-50 to-primary-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                    Pilih Paket Sesuai Kebutuhan Anda
                </h2>
                <p class="text-xl text-gray-600">
                    Mulai dengan trial gratis atau pilih paket berlangganan yang tepat untuk bisnis Shopee Anda
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- Free Trial -->
                <div class="bg-white rounded-2xl p-8 shadow-lg border-2 border-gray-200 flex flex-col">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Free Trial</h3>
                        <div class="text-4xl font-bold text-primary-500 mb-2">GRATIS</div>
                        <div class="text-gray-600">1 Hari</div>
                    </div>
                    
                    <ul class="space-y-4 mb-8 flex-grow">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Dapatkan RTMP Shopee</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">1 Akun Shopee</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Basic Support</span>
                        </li>
                    </ul>
                    
                    <a href="https://api.whatsapp.com/send/?phone=6288989676008&text&type=phone_number&app_absent=0" target="_blank" rel="noopener" class="w-full bg-gray-500 hover:bg-gray-600 text-white py-3 rounded-lg font-semibold transition-colors text-center block mt-auto">
                        Coba via WhatsApp
                    </a>
                </div>
                
                <!-- Weekly Plan -->
                <div class="bg-white rounded-2xl p-8 shadow-lg border-2 border-primary-500 relative flex flex-col">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="bg-primary-500 text-white px-4 py-1 rounded-full text-sm font-medium">Populer</span>
                    </div>
                    
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Paket Mingguan</h3>
                        <div class="text-4xl font-bold text-primary-500 mb-2">Rp 40.000</div>
                        <div class="text-gray-600">per minggu</div>
                    </div>
                    
                    <ul class="space-y-4 mb-8 flex-grow">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Dapatkan RTMP Shopee</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Multi Akun Shopee</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Live Downloader</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Priority Support</span>
                        </li>
                    </ul>
                    
                    <a href="https://api.whatsapp.com/send/?phone=6288989676008&text&type=phone_number&app_absent=0" target="_blank" rel="noopener" class="w-full bg-primary-500 hover:bg-primary-600 text-white py-3 rounded-lg font-semibold transition-colors text-center block mt-auto">
                        Beli via WhatsApp
                    </a>
                </div>
                
                <!-- Monthly Plan -->
                <div class="bg-white rounded-2xl p-8 shadow-lg border-2 border-gray-200 flex flex-col">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Paket Bulanan</h3>
                        <div class="mb-2">
                            <div class="text-lg text-gray-400 line-through mb-1">Rp 160.000</div>
                            <div class="text-4xl font-bold text-primary-500">Rp 139.000</div>
                            <div class="text-gray-600 text-sm">per bulan</div>
                        </div>
                        <div class="text-sm text-green-600 font-medium">Hemat 13%</div>
                    </div>
                    
                    <ul class="space-y-4 mb-8 flex-grow">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Dapatkan RTMP Shopee</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Multi Akun Shopee</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Live Downloader</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600">Priority Support</span>
                        </li>
                    </ul>
                    
                    <a href="https://api.whatsapp.com/send/?phone=6288989676008&text&type=phone_number&app_absent=0" target="_blank" rel="noopener" class="w-full bg-primary-500 hover:bg-primary-600 text-white py-3 rounded-lg font-semibold transition-colors text-center block mt-auto">
                        Beli via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section: Primary WhatsApp, Secondary Official Partner (Telegram) -->
    <section id="contact" class="py-20 bg-gradient-to-br from-primary-500 to-primary-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold mb-6">
                Siap Meningkatkan Penjualan dengan Shopee?
            </h2>
            <p class="text-xl mb-8 opacity-90">
                WhatsApp adalah channel utama untuk trial dan pembelian berlangganan. Jika tidak tersedia, gunakan Official Partner (Telegram).
            </p>

            <!-- Simplified: Single WhatsApp CTA -->
            <div class="mb-8"></div>

            <a href="https://api.whatsapp.com/send/?phone=6288989676008&text&type=phone_number&app_absent=0" target="_blank" rel="noopener" class="bg-white text-primary-600 hover:bg-gray-100 px-8 py-4 rounded-lg font-semibold text-lg transition-colors inline-block">
                Chat WhatsApp Sekarang
            </a>
        </div>
    </section>

    <!-- Official Community Section -->
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h3 class="text-2xl lg:text-3xl font-bold text-gray-900">Komunitas Resmi</h3>
                <p class="text-gray-600">Bergabung untuk tips, update, dan dukungan komunitas.</p>
            </div>
            <div class="grid sm:grid-cols-2 gap-6">
                <!-- WhatsApp Community -->
                <a href="https://chat.whatsapp.com/B6jzzUwZDNU1BiZECPec28?mode=wwt" target="_blank" rel="noopener" class="flex items-center justify-between p-5 rounded-2xl border border-gray-200 hover:border-primary-300 hover:bg-primary-50 transition-colors">
                    <div class="flex items-center">
                        <img src="/images/WhatsApp.webp" alt="WhatsApp" class="w-8 h-8 mr-3" />
                        <div>
                            <div class="text-lg font-semibold text-gray-900">WhatsApp Community</div>
                            <div class="text-sm text-gray-600">Bot Live Kenceng ⚡ Group</div>
                        </div>
                    </div>
                    <span class="text-primary-600 font-medium">Join</span>
                </a>
                <a href="https://t.me/+BzP5UnKPU6s4YTVl" target="_blank" rel="noopener" class="flex items-center justify-between p-5 rounded-2xl border border-gray-200 hover:border-primary-300 hover:bg-primary-50 transition-colors">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 mr-3 text-primary-500" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                        </svg>
                        <div>
                            <div class="text-lg font-semibold text-gray-900">Official Partner: Telegram</div>
                            <div class="text-sm text-gray-600">LIVE KENCENG | VIP CHANNEL</div>
                        </div>
                    </div>
                    <span class="text-primary-600 font-medium">Join</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <!-- Logo & Description -->
                <div class="md:col-span-2">
                    <div class="flex items-center mb-4">
                        <img src="{{ asset('images/logo.jpeg') }}" alt="Livekenceng Logo" class="w-10 h-10 rounded-lg">
                        <span class="ml-3 text-2xl font-bold">Livekenceng</span>
                    </div>
                    <p class="text-gray-400 mb-4 max-w-md">
                        Software terdepan untuk automasi Shopee di Indonesia. 
                        Tingkatkan penjualan Anda dengan teknologi automasi terbaik.
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Tautan Cepat</h3>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-gray-400 hover:text-white transition-colors">Fitur</a></li>
                        <li><a href="#pricing" class="text-gray-400 hover:text-white transition-colors">Harga</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-white transition-colors">Kontak</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Kontak</h3>
                    <ul class="space-y-2">
                        <li class="text-gray-400">Email: support@livekenceng.com</li>
                        <li class="text-gray-400">WhatsApp: <a class="underline hover:text-white" target="_blank" rel="noopener" href="https://api.whatsapp.com/send/?phone=6288989676008&text&type=phone_number&app_absent=0">+62 889-8967-6008</a></li>
                        <li class="text-gray-400">WA Community: <a class="underline hover:text-white" target="_blank" rel="noopener" href="https://chat.whatsapp.com/B6jzzUwZDNU1BiZECPec28?mode=wwt">Join Group</a></li>
                        <li class="text-gray-400">Official Partner: <a class="underline hover:text-white" target="_blank" rel="noopener" href="https://t.me/+BzP5UnKPU6s4YTVl">Telegram Community</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 Livekenceng. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @push('scripts')
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll effect to header
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.classList.add('shadow-lg');
            } else {
                header.classList.remove('shadow-lg');
            }
        });
    </script>
    @endpush
@endsection
