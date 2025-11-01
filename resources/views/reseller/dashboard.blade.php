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

        <!-- Apps Tabs -->
        <div class="bg-white rounded-2xl shadow-sm">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px overflow-x-auto">
                    @foreach($apps as $app)
                    <button onclick="switchTab('{{ $app->identifier }}')" id="tab-{{ $app->identifier }}" class="tab-button px-6 py-4 text-sm font-medium border-b-2 {{ $loop->first ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap">
                        {{ $app->display_name }}
                    </button>
                    @endforeach
                </nav>
            </div>

            @foreach($apps as $app)
            <div id="content-{{ $app->identifier }}" class="tab-content p-6 {{ !$loop->first ? 'hidden' : '' }}">
                <!-- Plans Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6" id="plansGrid-{{ $app->identifier }}">
                    <p class="text-gray-600">Loading plans...</p>
                </div>

                <!-- Generate Form -->
                <form id="generateForm-{{ $app->identifier }}" class="grid grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Plan</label>
                        <select id="plan_id-{{ $app->identifier }}" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="">Loading...</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                        <input type="number" id="quantity-{{ $app->identifier }}" min="1" max="100" value="1" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-primary-500 hover:bg-primary-600 text-white py-2 rounded-lg font-semibold transition-colors">
                            Generate
                        </button>
                    </div>
                </form>

                <!-- Licenses Table -->
                <h3 class="text-xl font-bold text-gray-900 mb-4">License Keys</h3>
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
                        <tbody id="licensesTableBody-{{ $app->identifier }}" class="bg-white divide-y divide-gray-200">
                            <!-- Loading... -->
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const apps = @json($apps);

    // Tab switching
    function switchTab(appIdentifier) {
        // Update button states
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('border-primary-500', 'text-primary-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });
        document.getElementById('tab-' + appIdentifier).classList.remove('border-transparent', 'text-gray-500');
        document.getElementById('tab-' + appIdentifier).classList.add('border-primary-500', 'text-primary-600');

        // Update content visibility
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        document.getElementById('content-' + appIdentifier).classList.remove('hidden');

        // Load data for this tab
        loadDataForApp(appIdentifier);
    }

    // Load pricing and licenses for an app
    function loadDataForApp(appIdentifier) {
        const app = apps.find(a => a.identifier === appIdentifier);
        if (!app) return;

        // Load pricing
        fetch('/reseller/pricing?app_id=' + app.id)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderPlans(app.identifier, data.pricing);
                }
            });

        // Load licenses
        loadLicensesForApp(appIdentifier);
    }

    // Render plans for an app
    function renderPlans(appIdentifier, pricing) {
        const grid = document.getElementById('plansGrid-' + appIdentifier);
        const select = document.getElementById('plan_id-' + appIdentifier);
        
        grid.innerHTML = '';
        pricing.forEach(plan => {
            const planCard = document.createElement('div');
            planCard.className = 'bg-gradient-to-br from-orange-50 to-primary-50 rounded-xl p-4 text-center';
            planCard.innerHTML = `
                <p class="text-sm text-gray-600 mb-2">${plan.name || plan.duration_days + ' Day'}</p>
                <p class="text-xs text-gray-500 line-through">Rp ${plan.base_price.toLocaleString('id-ID')}</p>
                <p class="text-lg font-bold text-primary-600">Rp ${plan.final_price.toLocaleString('id-ID')}</p>
            `;
            grid.appendChild(planCard);
        });

        select.innerHTML = '';
        pricing.forEach(plan => {
            const option = document.createElement('option');
            option.value = plan.plan_id;
            option.textContent = `${plan.name || plan.duration_days + ' Day'} - Rp ${plan.base_price.toLocaleString('id-ID')}`;
            select.appendChild(option);
        });
    }

    // Load licenses for an app
    function loadLicensesForApp(appIdentifier) {
        fetch('/reseller/licenses')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const app = apps.find(a => a.identifier === appIdentifier);
                    const appLicenses = data.licenses.filter(license => {
                        if (!license.app_id && appIdentifier === 'livekenceng') return true;
                        return license.app && license.app.id === app.id;
                    });
                    renderLicenses(appIdentifier, appLicenses);
                }
            });
    }

    // Render licenses table
    function renderLicenses(appIdentifier, licenses) {
        const tbody = document.getElementById('licensesTableBody-' + appIdentifier);
        tbody.innerHTML = '';
        
        if (licenses.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No licenses yet</td></tr>';
            return;
        }

        licenses.forEach(license => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">${license.code}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${license.duration_days} days</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp ${parseFloat(license.price).toLocaleString('id-ID')}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${license.is_used 
                        ? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Used</span>'
                        : '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Available</span>'
                    }
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${license.member?.email || '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(license.created_at).toLocaleString('id-ID')}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button onclick="copyLicense('${license.code}')" class="text-primary-600 hover:text-primary-900">Copy</button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    // Copy license key
    function copyLicense(code) {
        navigator.clipboard.writeText(code).then(() => {
            alert('License key copied to clipboard!');
        });
    }

    // Set up form submissions for each app
    document.addEventListener('DOMContentLoaded', function() {
        apps.forEach(app => {
            const form = document.getElementById('generateForm-' + app.identifier);
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const data = {
                        plan_id: document.getElementById('plan_id-' + app.identifier).value,
                        quantity: document.getElementById('quantity-' + app.identifier).value,
                    };
                    
                    if (!confirm(`Generate ${data.quantity} license(s)?`)) {
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
            }
        });

        // Load first tab's data
        if (apps.length > 0) {
            loadDataForApp(apps[0].identifier);
        }
    });
</script>
@endpush
@endsection


