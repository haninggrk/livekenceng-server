@extends('layouts.app')

@section('title', 'Admin Dashboard - Livekenceng')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Livekenceng Logo" class="w-10 h-10 rounded-lg">
                    <span class="ml-3 text-2xl font-bold text-gray-900">Livekenceng Admin</span>
                </div>
                
            <div class="flex items-center space-x-4">
                    <span class="text-gray-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
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
            <div class="bg-gradient-to-br from-primary-400 to-primary-600 rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primary-100 text-sm font-medium">Total Members</p>
                        <p class="text-3xl font-bold mt-2" id="totalMembers">{{ $members->count() }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-xl p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Active Members</p>
                        <p class="text-3xl font-bold mt-2" id="activeMembers">{{ $members->filter->isActive()->count() }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-xl p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Licenses</p>
                        <p class="text-3xl font-bold mt-2" id="totalLicenses">{{ $licenseKeys->count() }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-xl p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-2xl shadow-sm">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button onclick="switchTab('members')" id="tab-members" class="tab-button active px-6 py-4 text-sm font-medium border-b-2 border-primary-500 text-primary-600">
                        Members Management
                    </button>
                    <button onclick="switchTab('licenses')" id="tab-licenses" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        License Keys
                    </button>
                    <button onclick="switchTab('resellers')" id="tab-resellers" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Resellers
                    </button>
                    <button onclick="switchTab('pricing')" id="tab-pricing" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        License Pricing
                    </button>
                    <a href="{{ route('admin.updates.index') }}" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Software Updates
                    </a>
                </nav>
            </div>

            <!-- Members Tab -->
            <div id="content-members" class="tab-content p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Member Management</h2>
                    <button onclick="openAddMemberModal()" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        + Add Member
                    </button>
                </div>

                <!-- Search and Filter Controls -->
                <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" id="memberSearch" placeholder="Search by email or telegram..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        
                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select id="memberStatusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>
                        
                        <!-- Sort By -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                            <select id="memberSortBy" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="created_at_desc">Newest First</option>
                                <option value="created_at_asc">Oldest First</option>
                                <option value="expiry_date_desc">Expiry Date (Latest)</option>
                                <option value="expiry_date_asc">Expiry Date (Earliest)</option>
                                <option value="email_asc">Email (A-Z)</option>
                                <option value="email_desc">Email (Z-A)</option>
                            </select>
                        </div>
                        
                        <!-- Clear Filters -->
                        <div class="flex items-end">
                            <button onclick="clearMemberFilters()" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>

            <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Machine ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telegram</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="membersTableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Members will be loaded dynamically via JavaScript -->
                    </tbody>
                    </table>
                </div>
            </div>

            <!-- Licenses Tab -->
            <div id="content-licenses" class="tab-content p-6 hidden">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">License Key Management</h2>
                    <button onclick="openGenerateLicenseModal()" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        + Generate License
                                </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">License Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Used By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generated By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="licensesTableBody" class="bg-white divide-y divide-gray-200">
                            @foreach($licenseKeys as $license)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $license->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $license->duration_days }} days</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($license->is_used)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Used</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $license->member?->email ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($license->creator)
                                        {{ $license->creator->name ?? $license->creator->email }}
                                    @elseif($license->reseller)
                                        {{ $license->reseller->name }} (Reseller)
                                    @else
                                        System
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $license->created_at->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="copyLicense('{{ $license->code }}')" class="text-primary-600 hover:text-primary-900 mr-3">Copy</button>
                                    @if(!$license->is_used)
                                        <button onclick="deleteLicense({{ $license->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                                    @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

            <!-- Resellers Tab -->
            <div id="content-resellers" class="tab-content p-6 hidden">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Reseller Management</h2>
                    <button onclick="openAddResellerModal()" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        + Add Reseller
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance Spent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount %</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Licenses</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="resellersTableBody" class="bg-white divide-y divide-gray-200">
                            @foreach($resellers as $reseller)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $reseller->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $reseller->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($reseller->balance, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($reseller->balance_spent ?? 0, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($reseller->profit ?? 0, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $reseller->discount_percentage }}%</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $reseller->licenseKeys->count() }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="editReseller({{ $reseller->id }})" class="text-primary-600 hover:text-primary-900 mr-3">Edit</button>
                                    <button onclick="addBalanceModal({{ $reseller->id }})" class="text-green-600 hover:text-green-900 mr-3">Add Balance</button>
                                    <button onclick="deleteReseller({{ $reseller->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pricing/Plans Tab -->
            <div id="content-pricing" class="tab-content p-6 hidden">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">License Plans</h2>
                    <button onclick="openAddPlanModal()" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        + Add Plan
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price (Rp)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="plansTableBody">
                            @foreach($plans as $plan)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $plan->name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $plan->duration_days }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($plan->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($plan->is_active)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="editPlan({{ $plan->id }})" class="text-primary-600 hover:text-primary-900 mr-3">Edit</button>
                                    <button onclick="deletePlan({{ $plan->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Add/Edit Member Modal -->
<div id="memberModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full mx-4">
        <h3 id="memberModalTitle" class="text-2xl font-bold text-gray-900 mb-6">Add Member</h3>
        <form id="memberForm">
            <input type="hidden" id="memberId">
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" id="memberEmail" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                <input type="password" id="memberPassword" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password (when editing)</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Machine ID</label>
                <input type="text" id="memberMachineId" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Telegram Username</label>
                <input type="text" id="memberTelegramUsername" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="@username">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Expiry Date</label>
                <input type="datetime-local" id="memberExpiryDate" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-primary-500 hover:bg-primary-600 text-white py-2 rounded-lg font-semibold transition-colors">
                    Save
                </button>
                <button type="button" onclick="closeMemberModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 rounded-lg font-semibold transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Generate License Modal -->
<div id="licenseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold text-gray-900 mb-6">Generate License Keys</h3>
        <form id="licenseForm">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Plan</label>
                <select id="licensePlanId" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    @foreach($plans as $plan)
                    <option value="{{ $plan->id }}">{{ $plan->name ?? ($plan->duration_days . ' Day') }} - Rp {{ number_format($plan->price, 0, ',', '.') }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                <input type="number" id="licenseQuantity" min="1" max="100" value="1" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-primary-500 hover:bg-primary-600 text-white py-2 rounded-lg font-semibold transition-colors">
                    Generate
                </button>
                <button type="button" onclick="closeLicenseModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 rounded-lg font-semibold transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Reseller Modal -->
<div id="resellerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full mx-4">
        <h3 id="resellerModalTitle" class="text-2xl font-bold text-gray-900 mb-6">Add Reseller</h3>
        <form id="resellerForm">
            <input type="hidden" id="resellerId">
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                <input type="text" id="resellerName" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" id="resellerEmail" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                <input type="password" id="resellerPassword" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password (when editing)</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Balance (Rp)</label>
                <input type="number" id="resellerBalance" min="0" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Discount Percentage (%)</label>
                <input type="number" id="resellerDiscount" min="0" max="100" step="0.01" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-primary-500 hover:bg-primary-600 text-white py-2 rounded-lg font-semibold transition-colors">
                    Save
                </button>
                <button type="button" onclick="closeResellerModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 rounded-lg font-semibold transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Balance Modal -->
<div id="balanceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold text-gray-900 mb-6">Add Balance</h3>
        <form id="balanceForm">
            <input type="hidden" id="balanceResellerId">
            
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (Rp)</label>
                <input type="number" id="balanceAmount" min="0" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-primary-500 hover:bg-primary-600 text-white py-2 rounded-lg font-semibold transition-colors">
                    Add Balance
                </button>
                <button type="button" onclick="closeBalanceModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 rounded-lg font-semibold transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Plan Modal -->
<div id="planModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full mx-4">
        <h3 id="planModalTitle" class="text-2xl font-bold text-gray-900 mb-6">Add Plan</h3>
        <form id="planForm">
            <input type="hidden" id="planId">

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Name (optional)</label>
                <input type="text" id="planName" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Duration (days)</label>
                <input type="number" id="planDays" min="1" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Price (Rp)</label>
                <input type="number" id="planPrice" min="0" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Active</label>
                <select id="planActive" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-primary-500 hover:bg-primary-600 text-white py-2 rounded-lg font-semibold transition-colors">
                    Save
                </button>
                <button type="button" onclick="closePlanModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 rounded-lg font-semibold transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>


@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Tab switching
    function switchTab(tab) {
        // Hide all contents
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        // Remove active from all buttons
        document.querySelectorAll('.tab-button').forEach(el => {
            el.classList.remove('active', 'border-primary-500', 'text-primary-600');
            el.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Show selected content
        document.getElementById('content-' + tab).classList.remove('hidden');
        // Add active to selected button
        const btn = document.getElementById('tab-' + tab);
        btn.classList.add('active', 'border-primary-500', 'text-primary-600');
        btn.classList.remove('border-transparent', 'text-gray-500');

        // Persist selected tab so reloads stay on the same tab
        try { localStorage.setItem('adminActiveTab', tab); } catch (e) {}
    }

    // Member Management
    function loadMembers() {
        const search = document.getElementById('memberSearch').value;
        const status = document.getElementById('memberStatusFilter').value;
        const sortBy = document.getElementById('memberSortBy').value;
        
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (status) params.append('status', status);
        if (sortBy) params.append('sort_by', sortBy);
        
        fetch(`/admin/members?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderMembersTable(data.members);
                }
            })
            .catch(error => {
                console.error('Error loading members:', error);
            });
    }

    function renderMembersTable(members) {
        const tbody = document.getElementById('membersTableBody');
        tbody.innerHTML = '';
        
        members.forEach(member => {
            const row = document.createElement('tr');
            const isActive = member.expiry_date ? new Date(member.expiry_date) > new Date() : true;
            const statusBadge = isActive 
                ? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>'
                : '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Expired</span>';
            
            const expiryDate = member.expiry_date 
                ? new Date(member.expiry_date).toLocaleString('en-CA', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                }).replace(',', '')
                : '-';
            
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${member.email}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${member.machine_id || '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${member.telegram_username || '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${expiryDate}</td>
                <td class="px-6 py-4 whitespace-nowrap">${statusBadge}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="/admin/members/${member.id}/edit" class="text-primary-600 hover:text-primary-900 mr-3">Edit</a>
                    <button onclick="deleteMember(${member.id})" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function clearMemberFilters() {
        document.getElementById('memberSearch').value = '';
        document.getElementById('memberStatusFilter').value = '';
        document.getElementById('memberSortBy').value = 'created_at_desc';
        loadMembers();
    }

    // Debounce function for search input
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function openAddMemberModal() {
        document.getElementById('memberModalTitle').textContent = 'Add Member';
        document.getElementById('memberForm').reset();
        document.getElementById('memberId').value = '';
        document.getElementById('memberPassword').required = true;
        document.getElementById('memberModal').classList.remove('hidden');
        document.getElementById('memberModal').classList.add('flex');
    }

    function closeMemberModal() {
        document.getElementById('memberModal').classList.add('hidden');
        document.getElementById('memberModal').classList.remove('flex');
    }

    function editMember(id) {
        fetch(`/admin/members/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const member = data.member;
                    document.getElementById('memberModalTitle').textContent = 'Edit Member';
                    document.getElementById('memberId').value = member.id;
                    document.getElementById('memberEmail').value = member.email;
                    document.getElementById('memberMachineId').value = member.machine_id || '';
                    document.getElementById('memberTelegramUsername').value = member.telegram_username || '';
                    document.getElementById('memberExpiryDate').value = member.expiry_date ? member.expiry_date.slice(0, 16) : '';
                    document.getElementById('memberPassword').required = false;
                    document.getElementById('memberModal').classList.remove('hidden');
                    document.getElementById('memberModal').classList.add('flex');
                }
            });
    }

    function deleteMember(id) {
        if (!confirm('Are you sure you want to delete this member?')) return;
        
        fetch(`/admin/members/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                loadMembers();
            } else {
                showToast(data.message, 'error');
            }
        });
    }

    document.getElementById('memberForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = document.getElementById('memberId').value;
        const url = id ? `/admin/members/${id}` : '/admin/members';
        const method = id ? 'PUT' : 'POST';
        
        const data = {
            email: document.getElementById('memberEmail').value,
            machine_id: document.getElementById('memberMachineId').value,
            telegram_username: document.getElementById('memberTelegramUsername').value,
            expiry_date: document.getElementById('memberExpiryDate').value,
        };
        
        const password = document.getElementById('memberPassword').value;
        if (password) {
            data.password = password;
        }
        
        fetch(url, {
            method: method,
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
                location.reload();
            } else if (data.errors) {
                alert(Object.values(data.errors).flat().join('\n'));
            }
        });
    });

    // License Management
    function openGenerateLicenseModal() {
        document.getElementById('licenseForm').reset();
        document.getElementById('licenseModal').classList.remove('hidden');
        document.getElementById('licenseModal').classList.add('flex');
    }

    function closeLicenseModal() {
        document.getElementById('licenseModal').classList.add('hidden');
        document.getElementById('licenseModal').classList.remove('flex');
    }

    function copyLicense(code) {
        navigator.clipboard.writeText(code).then(() => {
            alert('License key copied to clipboard!');
        });
    }

    function deleteLicense(id) {
        if (!confirm('Are you sure you want to delete this license key?')) return;
        
        fetch(`/admin/licenses/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }

    document.getElementById('licenseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
        const data = {
            plan_id: document.getElementById('licensePlanId').value,
            quantity: document.getElementById('licenseQuantity').value,
        };
        
        fetch('/admin/licenses/generate', {
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
                location.reload();
            }
        });
    });

    // Reseller Management
    function openAddResellerModal() {
        document.getElementById('resellerModalTitle').textContent = 'Add Reseller';
        document.getElementById('resellerForm').reset();
        document.getElementById('resellerId').value = '';
        document.getElementById('resellerPassword').required = true;
        document.getElementById('resellerModal').classList.remove('hidden');
        document.getElementById('resellerModal').classList.add('flex');
    }

    function closeResellerModal() {
        document.getElementById('resellerModal').classList.add('hidden');
        document.getElementById('resellerModal').classList.remove('flex');
    }

    function editReseller(id) {
        fetch(`/admin/resellers/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const reseller = data.reseller;
                    document.getElementById('resellerModalTitle').textContent = 'Edit Reseller';
                    document.getElementById('resellerId').value = reseller.id;
                    document.getElementById('resellerName').value = reseller.name;
                    document.getElementById('resellerEmail').value = reseller.email;
                    document.getElementById('resellerBalance').value = reseller.balance;
                    document.getElementById('resellerDiscount').value = reseller.discount_percentage;
                    document.getElementById('resellerPassword').required = false;
                    document.getElementById('resellerModal').classList.remove('hidden');
                    document.getElementById('resellerModal').classList.add('flex');
                }
            });
    }

    function deleteReseller(id) {
        if (!confirm('Are you sure you want to delete this reseller?')) return;
        
        fetch(`/admin/resellers/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }

    function addBalanceModal(id) {
        document.getElementById('balanceResellerId').value = id;
        document.getElementById('balanceForm').reset();
        document.getElementById('balanceModal').classList.remove('hidden');
        document.getElementById('balanceModal').classList.add('flex');
    }

    function closeBalanceModal() {
        document.getElementById('balanceModal').classList.add('hidden');
        document.getElementById('balanceModal').classList.remove('flex');
    }

    document.getElementById('resellerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = document.getElementById('resellerId').value;
        const url = id ? `/admin/resellers/${id}` : '/admin/resellers';
        const method = id ? 'PUT' : 'POST';
        
        const data = {
            name: document.getElementById('resellerName').value,
            email: document.getElementById('resellerEmail').value,
            balance: document.getElementById('resellerBalance').value,
            discount_percentage: document.getElementById('resellerDiscount').value,
        };
        
        const password = document.getElementById('resellerPassword').value;
        if (password) {
            data.password = password;
        }
        
        fetch(url, {
            method: method,
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
                location.reload();
            } else if (data.errors) {
                alert(Object.values(data.errors).flat().join('\n'));
            }
        });
    });

    document.getElementById('balanceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = document.getElementById('balanceResellerId').value;
        const amount = document.getElementById('balanceAmount').value;
        
        fetch(`/admin/resellers/${id}/add-balance`, {
                    method: 'POST',
                    headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ amount: amount })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(`Balance added successfully! New balance: Rp ${data.new_balance.toLocaleString('id-ID')}`);
                location.reload();
            }
        });
    });

    // Pricing Management
    function openAddPlanModal() {
        document.getElementById('planModalTitle').textContent = 'Add Plan';
        document.getElementById('planForm').reset();
        document.getElementById('planId').value = '';
        document.getElementById('planActive').value = '1';
        document.getElementById('planModal').classList.remove('hidden');
        document.getElementById('planModal').classList.add('flex');
    }

    function closePlanModal() {
        document.getElementById('planModal').classList.add('hidden');
        document.getElementById('planModal').classList.remove('flex');
    }

    function editPlan(id) {
        fetch(`/admin/plans/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const plan = data.plan;
                    document.getElementById('planModalTitle').textContent = 'Edit Plan';
                    document.getElementById('planId').value = plan.id;
                    document.getElementById('planName').value = plan.name || '';
                    document.getElementById('planDays').value = plan.duration_days;
                    document.getElementById('planPrice').value = plan.price;
                    document.getElementById('planActive').value = plan.is_active ? '1' : '0';
                    document.getElementById('planModal').classList.remove('hidden');
                    document.getElementById('planModal').classList.add('flex');
                }
            });
    }

    function deletePlan(id) {
        if (!confirm('Delete this plan?')) return;
        fetch(`/admin/plans/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }

    document.getElementById('planForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('planId').value;
        const url = id ? `/admin/plans/${id}` : '/admin/plans';
        const method = id ? 'PUT' : 'POST';
        const data = {
            name: document.getElementById('planName').value,
            duration_days: document.getElementById('planDays').value,
            price: document.getElementById('planPrice').value,
            is_active: document.getElementById('planActive').value,
        };
        fetch(url, {
            method: method,
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
                location.reload();
            } else if (data.errors) {
                alert(Object.values(data.errors).flat().join('\n'));
            }
        });
    });

    // Restore the last active tab on page load
    document.addEventListener('DOMContentLoaded', function () {
        try {
            const saved = localStorage.getItem('adminActiveTab');
            if (saved && document.getElementById('tab-' + saved)) {
                switchTab(saved);
            }
        } catch (e) {}
        
        // Load members on page load
        loadMembers();
        
        // Add event listeners for member filters
        document.getElementById('memberSearch').addEventListener('input', debounce(loadMembers, 300));
        document.getElementById('memberStatusFilter').addEventListener('change', loadMembers);
        document.getElementById('memberSortBy').addEventListener('change', loadMembers);
    });

    </script>
@endpush
@endsection


