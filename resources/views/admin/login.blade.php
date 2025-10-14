@extends('layouts.app')

@section('title', 'Admin Login - Livekenceng')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 to-primary-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <!-- Logo and Title -->
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo.jpeg') }}" alt="Livekenceng Logo" class="w-20 h-20 rounded-2xl mx-auto mb-4 shadow-lg">
            <h2 class="text-3xl font-bold text-gray-900">Admin Login</h2>
            <p class="mt-2 text-gray-600">Livekenceng Dashboard & Reseller Login</p>
        </div>

        <!-- Login Form -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf

                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror"
                        placeholder="admin@livekenceng.com"
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <input 
                        id="password" 
                        type="password" 
                        name="password" 
                        required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all @error('password') border-red-500 @enderror"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center mb-6">
                    <input 
                        id="remember" 
                        type="checkbox" 
                        name="remember"
                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Remember me
                    </label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 rounded-lg transition-colors shadow-lg hover:shadow-xl"
                >
                    Sign In
                </button>
            </form>

            <!-- Back to Home -->
            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                    ← Back to Homepage
                </a>
            </div>
        </div>
    </div>
</div>
@endsection


