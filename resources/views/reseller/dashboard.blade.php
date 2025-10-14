@extends('layouts.app')

@section('title', 'Reseller Dashboard - Livekenceng')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Livekenceng Logo" class="w-10 h-10 rounded-lg">
                    <span class="ml-3 text-2xl font-bold text-gray-900">Livekenceng Reseller</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">{{ $reseller->name }}</span>
                    <form method="POST" action="{{ route('reseller.logout') }}">
                        @csrf
                        <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Current Balance</p>
                        <p class="text-3xl font-bold mt-2">Rp {{ number_format($reseller->balance, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-xl p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Discount Rate</p>
                        <p class="text-3xl font-bold mt-2">{{ $reseller->discount_percentage }}%</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-xl p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-primary-400 to-primary-600 rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primary-100 text-sm font-medium">Total Licenses</p>
                        <p class="text-3xl font-bold mt-2">{{ $licenses->count() }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-xl p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Generate License Section -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Generate License Keys</h2>
            
            <!-- Pricing Table -->
            <div class="grid grid-cols-5 gap-4 mb-6">
                <div class="bg-gradient-to-br from-orange-50 to-primary-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-gray-600 mb-2">1 Day</p>
                    <p class="text-xs text-gray-500 line-through">Rp 10,000</p>
                    <p class="text-lg font-bold text-primary-600" data-duration="1" data-base-price="10000">Rp {{ number_format(10000 * (100 - $reseller->discount_percentage) / 100, 0, ',', '.') }}</p>
                </div>
                <div class="bg-gradient-to-br from-orange-50 to-primary-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-gray-600 mb-2">3 Days</p>
                    <p class="text-xs text-gray-500 line-through">Rp 25,000</p>
                    <p class="text-lg font-bold text-primary-600" data-duration="3" data-base-price="25000">Rp {{ number_format(25000 * (100 - $reseller->discount_percentage) / 100, 0, ',', '.') }}</p>
                </div>
                <div class="bg-gradient-to-br from-orange-50 to-primary-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-gray-600 mb-2">7 Days</p>
                    <p class="text-xs text-gray-500 line-through">Rp 40,000</p>
                    <p class="text-lg font-bold text-primary-600" data-duration="7" data-base-price="40000">Rp {{ number_format(40000 * (100 - $reseller->discount_percentage) / 100, 0, ',', '.') }}</p>
                </div>
                <div class="bg-gradient-to-br from-orange-50 to-primary-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-gray-600 mb-2">14 Days</p>
                    <p class="text-xs text-gray-500 line-through">Rp 70,000</p>
                    <p class="text-lg font-bold text-primary-600" data-duration="14" data-base-price="70000">Rp {{ number_format(70000 * (100 - $reseller->discount_percentage) / 100, 0, ',', '.') }}</p>
                </div>
                <div class="bg-gradient-to-br from-orange-50 to-primary-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-gray-600 mb-2">30 Days</p>
                    <p class="text-xs text-gray-500 line-through">Rp 139,000</p>
                    <p class="text-lg font-bold text-primary-600" data-duration="30" data-base-price="139000">Rp {{ number_format(139000 * (100 - $reseller->discount_percentage) / 100, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Generate Form -->
            <form id="generateForm" class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Duration</label>
                    <select id="duration" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="1">1 Day</option>
                        <option value="3">3 Days</option>
                        <option value="7">7 Days</option>
                        <option value="14">14 Days</option>
                        <option value="30">30 Days</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                    <input type="number" id="quantity" min="1" max="100" value="1" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-primary-500 hover:bg-primary-600 text-white py-2 rounded-lg font-semibold transition-colors">
                        Generate
                    </button>
                </div>
            </form>
        </div>

        <!-- License Keys Table -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Your License Keys</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">License Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Used By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="licensesTableBody" class="bg-white divide-y divide-gray-200">
                        @foreach($licenses as $license)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $license->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $license->duration_days }} days</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($license->price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($license->is_used)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Used</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $license->member?->email ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $license->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="copyLicense('{{ $license->code }}')" class="text-primary-600 hover:text-primary-900">Copy</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Copy license key
    function copyLicense(code) {
        navigator.clipboard.writeText(code).then(() => {
            alert('License key copied to clipboard!');
        });
    }

    // Generate license
    document.getElementById('generateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const data = {
            duration_days: document.getElementById('duration').value,
            quantity: document.getElementById('quantity').value,
        };
        
        if (!confirm(`Generate ${data.quantity} license(s) for ${data.duration_days} day(s)?`)) {
            return;
        }
        
        fetch('/reseller/licenses/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(`Success! ${data.licenses.length} license key(s) generated.\nTotal cost: Rp ${data.total_cost.toLocaleString('id-ID')}\nRemaining balance: Rp ${data.remaining_balance.toLocaleString('id-ID')}`);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            alert('Error generating license: ' + err.message);
        });
    });
</script>
@endpush
@endsection


