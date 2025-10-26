@extends('layouts.app')

@section('title', 'Download - Livekenceng')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Livekenceng Logo" class="w-12 h-12 rounded-lg">
                    <div class="ml-4">
                        <h1 class="text-3xl font-bold text-gray-900">Livekenceng</h1>
                        <p class="text-gray-600">Download Center</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">Home</a>
                    <a href="{{ route('download') }}" class="text-primary-600 font-semibold">Download</a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Latest Version Section -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <div class="text-center mb-8">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Latest Version</h2>
                @if($latestUpdate)
                    <div class="inline-flex items-center bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Version {{ $latestUpdate->version }}
                    </div>
                    <p class="text-gray-600 mb-6">{{ $latestUpdate->notes }}</p>
                    <p class="text-sm text-gray-500 mb-8">Released on {{ $latestUpdate->pub_date->format('F j, Y') }}</p>
                    
                    <div class="flex justify-center space-x-4">
                        @if(in_array('windows', $latestUpdate->platforms ?? []))
                            <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                </svg>
                                Download for Windows
                            </a>
                        @endif
                        @if(in_array('macos', $latestUpdate->platforms ?? []))
                            <a href="#" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-3 rounded-lg font-semibold transition-colors flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Download for macOS
                            </a>
                        @endif
                    </div>
                @else
                    <div class="text-gray-500">
                        <p class="text-xl mb-4">No software updates available</p>
                        <p>Please check back later for new releases.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Previous Versions Section -->
        @if($previousUpdates->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">Previous Versions</h3>
            <p class="text-gray-600 mb-6">Download older versions of the software if needed.</p>
            
            <div class="space-y-4">
                @foreach($previousUpdates as $update)
                <div class="border border-gray-200 rounded-lg">
                    <button class="w-full px-6 py-4 text-left focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-inset" onclick="toggleAccordion('version-{{ $update->id }}')">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-gray-400 transform transition-transform duration-200" id="icon-version-{{ $update->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">Version {{ $update->version }}</h4>
                                    <p class="text-sm text-gray-500">Released {{ $update->pub_date->format('F j, Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if(in_array('windows', $update->platforms ?? []))
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Windows</span>
                                @endif
                                @if(in_array('macos', $update->platforms ?? []))
                                    <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">macOS</span>
                                @endif
                            </div>
                        </div>
                    </button>
                    
                    <div id="version-{{ $update->id }}" class="hidden px-6 pb-4">
                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-gray-700 mb-4">{{ $update->notes }}</p>
                            <div class="flex space-x-3">
                                @if(in_array('windows', $update->platforms ?? []))
                                    <a href="#" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-medium transition-colors text-sm">
                                        Download Windows
                                    </a>
                                @endif
                                @if(in_array('macos', $update->platforms ?? []))
                                    <a href="#" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded font-medium transition-colors text-sm">
                                        Download macOS
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

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
</script>
@endpush
@endsection
