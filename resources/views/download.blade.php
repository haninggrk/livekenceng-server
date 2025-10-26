@extends('layouts.app')

@section('title', 'Download - Livekenceng')

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
                    <a href="{{ route('home') }}#features" class="text-gray-600 hover:text-primary-600 transition-colors">Fitur</a>
                    <a href="{{ route('home') }}#pricing" class="text-gray-600 hover:text-primary-600 transition-colors">Harga</a>
                    <a href="{{ route('home') }}#contact" class="text-gray-600 hover:text-primary-600 transition-colors">Kontak</a>
                    <a href="{{ route('download') }}" class="text-primary-600 font-semibold">Download</a>
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
            <div class="text-center">
                <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                    Download <span class="text-primary-500">Livekenceng</span>
                </h1>
                <p class="text-xl text-gray-600 mb-8 leading-relaxed max-w-3xl mx-auto">
                    Dapatkan software automasi Shopee terbaru dengan fitur-fitur canggih. 
                    Pilih versi yang sesuai dengan kebutuhan Anda.
                </p>
            </div>
        </div>
    </section>

    <!-- Download Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Latest Version Section -->
            <div class="bg-gradient-to-br from-orange-50 to-primary-50 rounded-2xl p-8 mb-12">
                <div class="text-center mb-8">
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Versi Terbaru</h2>
                    @if($latestUpdate)
                        <div class="inline-flex items-center bg-green-100 text-green-800 px-6 py-3 rounded-full text-lg font-semibold mb-6">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Version {{ $latestUpdate->version }}
                        </div>
                        <div class="text-lg text-gray-700 mb-6 max-w-2xl mx-auto">{!! nl2br(e($latestUpdate->notes)) !!}</div>
                        <p class="text-sm text-gray-500 mb-8">Dirilis pada {{ $latestUpdate->pub_date->format('d F Y') }}</p>
                        
                        <div class="flex flex-col sm:flex-row justify-center gap-4">
                            @foreach($latestUpdate->platforms as $platformName => $platformData)
                                @if(isset($platformData['url']))
                                    @php
                                        $filePath = str_replace(asset('storage/'), '', $platformData['url']);
                                        $fullPath = storage_path('app/public/' . $filePath);
                                        $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
                                        $fileSizeFormatted = $fileSize > 0 ? number_format($fileSize / 1024 / 1024, 1) . ' MB' : 'Unknown size';
                                    @endphp
                                    <a href="{{ $platformData['url'] }}" class="bg-primary-500 hover:bg-primary-600 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-colors flex items-center justify-center">
                                        <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                        </svg>
                                        <div class="text-left">
                                            <div>Download untuk {{ ucfirst($platformName) }}</div>
                                            <div class="text-sm opacity-90">{{ $fileSizeFormatted }}</div>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-gray-500">
                            <p class="text-2xl mb-4">Tidak ada update software tersedia</p>
                            <p class="text-lg">Silakan kembali lagi nanti untuk rilis terbaru.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Previous Versions Section -->
            @if($previousUpdates->count() > 0)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8">
                <div class="text-center mb-8">
                    <h3 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4">Versi Sebelumnya</h3>
                    <p class="text-lg text-gray-600">Download versi lama software jika diperlukan.</p>
                </div>
                
                <div class="space-y-4">
                    @foreach($previousUpdates as $update)
                    <div class="border border-gray-200 rounded-lg hover:border-primary-300 transition-colors">
                        <button class="w-full px-6 py-4 text-left focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-inset" onclick="toggleAccordion('version-{{ $update->id }}')">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 text-gray-400 transform transition-transform duration-200" id="icon-version-{{ $update->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">Version {{ $update->version }}</h4>
                                        <p class="text-sm text-gray-500">Dirilis {{ $update->pub_date->format('d F Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if(in_array('windows', $update->platforms ?? []))
                                        <span class="bg-primary-100 text-primary-800 text-xs px-3 py-1 rounded-full font-medium">Windows</span>
                                    @endif
                                    @if(in_array('macos', $update->platforms ?? []))
                                        <span class="bg-gray-100 text-gray-800 text-xs px-3 py-1 rounded-full font-medium">macOS</span>
                                    @endif
                                </div>
                            </div>
                        </button>
                        
                        <div id="version-{{ $update->id }}" class="hidden px-6 pb-4">
                            <div class="border-t border-gray-200 pt-4">
                                <div class="text-gray-700 mb-4">{!! nl2br(e($update->notes)) !!}</div>
                                <div class="flex flex-col sm:flex-row gap-3">
                                    @foreach($update->platforms as $platformName => $platformData)
                                        @if(isset($platformData['url']))
                                            @php
                                                $filePath = str_replace(asset('storage/'), '', $platformData['url']);
                                                $fullPath = storage_path('app/public/' . $filePath);
                                                $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
                                                $fileSizeFormatted = $fileSize > 0 ? number_format($fileSize / 1024 / 1024, 1) . ' MB' : 'Unknown size';
                                            @endphp
                                            <a href="{{ $platformData['url'] }}" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-3 rounded-lg font-medium transition-colors text-center">
                                                <div>Download {{ ucfirst($platformName) }}</div>
                                                <div class="text-xs opacity-90">{{ $fileSizeFormatted }}</div>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-br from-primary-500 to-primary-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold mb-6">
                Butuh Bantuan dengan Download?
            </h2>
            <p class="text-xl mb-8 opacity-90">
                Tim support kami siap membantu Anda dengan proses download dan instalasi software.
            </p>
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
                        <li><a href="{{ route('home') }}#features" class="text-gray-400 hover:text-white transition-colors">Fitur</a></li>
                        <li><a href="{{ route('home') }}#pricing" class="text-gray-400 hover:text-white transition-colors">Harga</a></li>
                        <li><a href="{{ route('home') }}#contact" class="text-gray-400 hover:text-white transition-colors">Kontak</a></li>
                        <li><a href="{{ route('download') }}" class="text-gray-400 hover:text-white transition-colors">Download</a></li>
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
        function toggleAccordion(id) {
            const content = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

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
