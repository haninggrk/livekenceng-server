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
                    <button onclick="switchTab('apps')" id="tab-apps" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Apps
                    </button>
                    <button onclick="switchTab('niches')" id="tab-niches" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Niches & Product Sets
                    </button>
                    <button onclick="switchTab('expired-subscriptions')" id="tab-expired-subscriptions" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Expired Subscriptions
                    </button>
                    <button onclick="switchTab('active-livestream')" id="tab-active-livestream" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Active Livestream
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
                        
                        <!-- Sort By -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                            <select id="memberSortBy" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="created_at_desc">Newest First</option>
                                <option value="created_at_asc">Oldest First</option>
                                <option value="email_asc">Email (A-Z)</option>
                                <option value="email_desc">Email (Z-A)</option>
                            </select>
                        </div>
                        
                        <!-- Per Page -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Per Page</label>
                            <select id="memberPerPage" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telegram</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subscriptions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="membersTableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Members will be loaded dynamically via JavaScript -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Controls -->
                <div id="membersPagination" class="mt-4 flex items-center justify-between">
                    <!-- Pagination info and controls will be rendered here -->
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

                <div id="licensesContent" class="space-y-8">
                    <p class="text-gray-600">Loading licenses...</p>
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
                <div id="plansContent" class="space-y-8">
                    <p class="text-gray-600">Loading plans...</p>
                </div>
            </div>

            <!-- Apps Tab -->
            <div id="content-apps" class="tab-content p-6 hidden">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Apps Management</h2>
                    <button onclick="openAddAppModal()" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        + Add App
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Display Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Identifier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subscriptions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">License Keys</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plans</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="appsTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Apps will be loaded dynamically via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Niches & Product Sets Tab -->
            <div id="content-niches" class="tab-content p-6 hidden">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Niches & Product Sets Management</h2>
                    
                    <!-- Member Selector -->
                    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Member</label>
                        <div class="flex gap-3">
                            <input type="text" id="nicheMemberSearch" placeholder="Search by email..." 
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <button onclick="loadNichesForMember()" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                Load
                    </button>
                        </div>
                        <div id="selectedMemberInfo" class="mt-3 text-sm text-gray-600 hidden"></div>
                    </div>
                </div>

                <!-- Niches Section -->
                <div id="nichesSection" class="hidden">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-gray-900">Niches</h3>
                        <button onclick="openAddNicheModal()" class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            + Add Niche
                        </button>
                        </div>

                    <div id="nichesList" class="space-y-4 mb-8">
                        <!-- Niches will be loaded here -->
                    </div>
                </div>

                <!-- Empty State -->
                <div id="nichesEmptyState" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Select a member to view niches</h3>
                    <p class="mt-1 text-sm text-gray-500">Search for a member by email to get started.</p>
                </div>
            </div>

            <!-- Expired Subscriptions Tab -->
            <div id="content-expired-subscriptions" class="tab-content p-6 hidden">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Expired Subscriptions</h2>
                        <p class="text-gray-600 mt-1">Review expired members, edit their machine IDs, and follow up easily.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button onclick="loadExpiredSubscriptions()" class="bg-primary-100 text-primary-700 hover:bg-primary-200 px-5 py-2 rounded-lg font-medium transition-colors">
                            🔄 Refresh List
                        </button>
                        <button onclick="openBulkMachineIdModal()" class="bg-primary-500 hover:bg-primary-600 text-white px-5 py-2 rounded-lg font-medium transition-colors">
                            ✏️ Bulk Edit Machine IDs
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" id="expiredSearch" placeholder="Search by email, Telegram username, or app name..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-2">Showing <span id="expiredCount">0</span> expired subscriptions.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">App</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expired Since</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Machine ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="expiredSubscriptionsBody" class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    Click "Refresh List" to load expired subscriptions.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Active Livestream Tab -->
            <div id="content-active-livestream" class="tab-content p-6 hidden">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Active Livestreams</h2>
                    <p class="text-gray-600 mb-4">Showing all active livestreams from all members with active Shopee accounts.</p>
                    <button onclick="loadActiveLivestreams()" class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        🔄 Refresh List
                    </button>
                </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Live Link</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GMV</th>
                                        </tr>
                                    </thead>
                        <tbody id="activeLivestreamsTableBody" class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    Click "Refresh List" to load active livestreams
                                </td>
                            </tr>
                                    </tbody>
                                </table>
                </div>
            </div>

                            </div>
                        </div>
                    </div>

<!-- Add/Edit Niche Modal -->
<div id="nicheModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full mx-4">
        <h3 id="nicheModalTitle" class="text-2xl font-bold text-gray-900 mb-6">Add Niche</h3>
        <form id="nicheForm">
            <input type="hidden" id="nicheId">
            <input type="hidden" id="nicheMemberId">
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                <input type="text" id="nicheName" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                <textarea id="nicheDescription" rows="3" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                                    </div>

            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-primary-500 hover:bg-primary-600 text-white py-2 rounded-lg font-semibold transition-colors">
                    Save
                </button>
                <button type="button" onclick="closeNicheModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 rounded-lg font-semibold transition-colors">
                    Cancel
                </button>
                                    </div>
        </form>
                                </div>
                                </div>

<!-- Add/Edit Product Set Modal -->
<div id="productSetModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full mx-4">
        <h3 id="productSetModalTitle" class="text-2xl font-bold text-gray-900 mb-6">Add Product Set</h3>
        <form id="productSetForm">
            <input type="hidden" id="productSetId">
            <input type="hidden" id="productSetMemberId">
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                <input type="text" id="productSetName" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Niche</label>
                <select id="productSetNicheId" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">No Niche</option>
                </select>
                        </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                <textarea id="productSetDescription" rows="3" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-primary-500 hover:bg-primary-600 text-white py-2 rounded-lg font-semibold transition-colors">
                    Save
                </button>
                <button type="button" onclick="closeProductSetModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 rounded-lg font-semibold transition-colors">
                    Cancel
                </button>
            </div>
        </form>
                    </div>
                </div>

<!-- Add Items to Product Set Modal -->
<div id="productSetItemsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h3 id="productSetItemsModalTitle" class="text-2xl font-bold text-gray-900 mb-6">Add Items</h3>
        <form id="productSetItemsForm">
            <input type="hidden" id="itemsProductSetId">
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Product URLs (one per line)</label>
                <textarea id="productUrls" rows="10" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent font-mono text-sm" placeholder="https://shopee.co.id/product/123/456&#10;https://shopee.co.id/product/789/012"></textarea>
                <p class="text-xs text-gray-500 mt-1">Enter one URL per line. Max 100 items per product set.</p>
                    </div>

            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-primary-500 hover:bg-primary-600 text-white py-2 rounded-lg font-semibold transition-colors">
                    Add Items
                        </button>
                <button type="button" onclick="closeProductSetItemsModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 rounded-lg font-semibold transition-colors">
                    Cancel
                </button>
                        </div>
        </form>
                    </div>
                </div>

<!-- Add/Edit App Modal -->
<div id="appModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full mx-4">
        <h3 id="appModalTitle" class="text-2xl font-bold text-gray-900 mb-6">Add App</h3>
        <form id="appForm">
            <input type="hidden" id="appId">
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                <input type="text" id="appName" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="e.g., ShopeeApp">
                <p class="text-xs text-gray-500 mt-1">Unique internal name</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Display Name</label>
                <input type="text" id="appDisplayName" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="e.g., Shopee Automation App">
        </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Identifier</label>
                <input type="text" id="appIdentifier" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="e.g., shopee-app">
                <p class="text-xs text-gray-500 mt-1">Used in API calls (app_identifier)</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                <textarea id="appDescription" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent" rows="3" placeholder="Optional description"></textarea>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" id="appIsActive" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" checked>
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-primary-500 hover:bg-primary-600 text-white py-2 rounded-lg font-semibold transition-colors">
                    Save
                </button>
                <button type="button" onclick="closeAppModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 rounded-lg font-semibold transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Machine ID Modal -->
<div id="bulkMachineIdModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-900">Bulk Edit Machine IDs</h3>
            <button onclick="closeBulkMachineIdModal()" class="text-gray-500 hover:text-gray-700 text-sm">✕ Close</button>
        </div>
        <p class="text-sm text-gray-600 mb-6">
            Only expired subscriptions currently loaded in the table are shown here. Update the machine IDs you need and click Save.
        </p>
        <form id="bulkMachineIdForm">
            <div id="bulkMachineIdEmptyState" class="text-center py-8 text-gray-500 hidden">
                No expired subscriptions available. Refresh the list first.
            </div>
            <div id="bulkMachineIdTableWrapper" class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">App</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Machine ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Machine ID</th>
                                    </tr>
                                </thead>
                    <tbody id="bulkMachineIdTableBody" class="bg-white divide-y divide-gray-200"></tbody>
                            </table>
                        </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:gap-4 mt-6">
                <button type="submit" class="flex-1 bg-primary-500 hover:bg-primary-600 text-white py-3 rounded-lg font-semibold transition-colors">
                    Save Changes
                </button>
                <button type="button" onclick="closeBulkMachineIdModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-3 rounded-lg font-semibold transition-colors">
                    Cancel
                </button>
                    </div>
        </form>
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
                <label class="block text-sm font-semibold text-gray-700 mb-2">Telegram Username</label>
                <input type="text" id="memberTelegramUsername" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="@username">
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
                <label class="block text-sm font-semibold text-gray-700 mb-2">App</label>
                <select id="licenseAppId" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Livekenceng (Default)</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Leave default for Livekenceng app, or select other app</p>
            </div>

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
            <input type="hidden" id="planAppId">

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">App</label>
                <select id="planAppSelect" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <!-- Will be populated dynamically -->
                </select>
                <p class="text-xs text-gray-500 mt-1">Select which app this plan belongs to</p>
            </div>

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
    let expiredSubscriptions = [];
    let filteredExpiredSubscriptions = [];

    function escapeHtml(value) {
        if (value === null || value === undefined) {
            return '';
        }
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatDateTime(value) {
        if (!value) {
            return '-';
        }
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) {
            return value;
        }
        return date.toLocaleString('id-ID', {
            timeZone: 'Asia/Jakarta',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    }

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
        
        // Load data for specific tabs
        if (tab === 'apps') {
            loadApps();
        } else if (tab === 'pricing') {
            loadAllPlansGrouped();
        } else if (tab === 'licenses') {
            loadAllLicensesGrouped();
        } else if (tab === 'members') {
            // Members are loaded on page load, so only reload if needed
        } else if (tab === 'niches') {
            // Reset niches tab
            document.getElementById('nicheMemberSearch').value = '';
            document.getElementById('selectedMemberInfo').classList.add('hidden');
            document.getElementById('nichesSection').classList.add('hidden');
            document.getElementById('nichesEmptyState').classList.remove('hidden');
        } else if (tab === 'expired-subscriptions') {
            loadExpiredSubscriptions();
        } else if (tab === 'active-livestream') {
            // Load active livestreams when tab is opened
            loadActiveLivestreams();
        }
    }

    function loadActiveLivestreams() {
        const tbody = document.getElementById('activeLivestreamsTableBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                    ⏳ Loading active livestreams...
                </td>
            </tr>
        `;

        fetch('/admin/livestreams/active')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderActiveLivestreams(data.livestreams);
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-red-600">
                                Error loading livestreams
                            </td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading livestreams:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-red-600">
                            Error loading livestreams
                        </td>
                    </tr>
                `;
            });
    }

    function renderActiveLivestreams(livestreams) {
        const tbody = document.getElementById('activeLivestreamsTableBody');
        tbody.innerHTML = '';

        if (livestreams.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                        No active livestreams found
                    </td>
                </tr>
            `;
            return;
        }

        livestreams.forEach((item, index) => {
            const row = document.createElement('tr');
            const liveUrl = `http://live.shopee.co.id/share?from=live&session=${item.session_id}`;
            const gmvFormatted = item.gmv ? new Intl.NumberFormat('id-ID').format(item.gmv) : '0';
            
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${index + 1}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${item.member_email}</div>
                    <div class="text-sm text-gray-500">${item.account_name}</div>
                </td>
                <td class="px-6 py-4">
                    <a href="${liveUrl}" target="_blank" class="text-sm text-primary-600 hover:text-primary-900 font-medium underline">
                        🔴 Session ${item.session_id}
                    </a>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                    Rp ${gmvFormatted}
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    // Expired subscriptions
    function loadExpiredSubscriptions() {
        const tbody = document.getElementById('expiredSubscriptionsBody');
        if (!tbody) return;

        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                    ⏳ Loading expired subscriptions...
                </td>
            </tr>
        `;

        fetch('/admin/subscriptions/expired')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    expiredSubscriptions = data.subscriptions || [];
                    document.getElementById('expiredCount').textContent = data.total || expiredSubscriptions.length;
                    renderExpiredSubscriptions();
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-red-600">
                                ${data.message || 'Failed to load expired subscriptions'}
                            </td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading expired subscriptions:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-red-600">
                            Error loading expired subscriptions
                        </td>
                    </tr>
                `;
            });
    }

    function renderExpiredSubscriptions() {
        const tbody = document.getElementById('expiredSubscriptionsBody');
        if (!tbody) return;

        const searchInput = document.getElementById('expiredSearch');
        const keyword = searchInput ? searchInput.value.trim().toLowerCase() : '';

        filteredExpiredSubscriptions = expiredSubscriptions.filter(sub => {
            if (!keyword) return true;
            const email = sub.member?.email?.toLowerCase() || '';
            const telegram = sub.member?.telegram_username?.toLowerCase() || '';
            const appName = sub.app?.display_name?.toLowerCase() || '';
            const appIdentifier = sub.app?.identifier?.toLowerCase() || '';
            const machineId = sub.machine_id?.toLowerCase() || '';
            return email.includes(keyword) ||
                telegram.includes(keyword) ||
                appName.includes(keyword) ||
                appIdentifier.includes(keyword) ||
                machineId.includes(keyword);
        });

        const summary = document.getElementById('expiredCount');
        if (summary) {
            summary.textContent = filteredExpiredSubscriptions.length;
        }

        if (filteredExpiredSubscriptions.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        No expired subscriptions match your search.
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = '';

        filteredExpiredSubscriptions.forEach(sub => {
            const row = document.createElement('tr');
            const subscriptionId = Number(sub.id);
            const email = sub.member?.email || 'Unknown';
            const telegram = sub.member?.telegram_username;
            const appLabel = sub.app
                ? `${sub.app.display_name} (${sub.app.identifier})`
                : 'Livekenceng (Legacy)';
            const expiryDisplay = formatDateTime(sub.expiry_date);
            const expiredDays = typeof sub.expired_days === 'number' ? sub.expired_days : null;
            const expiredText = expiredDays !== null ? `${expiredDays} day${expiredDays === 1 ? '' : 's'} ago` : '-';
            const machineId = sub.machine_id;
            const machineIdDisplay = machineId
                ? `<code class="text-xs bg-gray-100 px-2 py-1 rounded">${escapeHtml(machineId)}</code>`
                : '<span class="text-xs text-gray-500">Not set</span>';

            row.innerHTML = `
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">${escapeHtml(email)}</div>
                    ${telegram ? `<div class="text-xs text-gray-500">@${escapeHtml(telegram)}</div>` : ''}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${escapeHtml(appLabel)}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    <div>${expiryDisplay}</div>
                    <div class="text-xs text-gray-500">${expiredText}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900">
                    ${machineIdDisplay}
                </td>
                <td class="px-6 py-4 text-sm font-medium space-x-3 whitespace-nowrap">
                    <button onclick="editSubscriptionMachineId(${subscriptionId})" class="text-primary-600 hover:text-primary-900">
                        Edit Machine ID
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function editSubscriptionMachineId(subscriptionId) {
        const target = expiredSubscriptions.find(sub => Number(sub.id) === Number(subscriptionId));
        const currentValue = target?.machine_id || '';
        const newValue = prompt('Enter new machine ID (leave empty to clear):', currentValue);
        if (newValue === null) {
            return;
        }

        fetch(`/admin/subscriptions/${subscriptionId}/machine-id`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                machine_id: newValue.trim() ? newValue.trim() : null,
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                if (target) {
                    target.machine_id = data.machine_id;
                }
                renderExpiredSubscriptions();
            } else {
                showToast(data.message || 'Failed to update machine ID', 'error');
            }
        })
        .catch(error => {
            console.error('Error updating machine ID:', error);
            showToast('Failed to update machine ID', 'error');
        });
    }

    function openBulkMachineIdModal() {
        if (!expiredSubscriptions.length) {
            loadExpiredSubscriptions();
            showToast('Loading expired subscriptions first...', 'success');
            return;
        }

        populateBulkMachineIdTable();
        document.getElementById('bulkMachineIdModal').classList.remove('hidden');
        document.getElementById('bulkMachineIdModal').classList.add('flex');
    }

    function closeBulkMachineIdModal() {
        document.getElementById('bulkMachineIdModal').classList.add('hidden');
        document.getElementById('bulkMachineIdModal').classList.remove('flex');
    }

    function populateBulkMachineIdTable() {
        const tbody = document.getElementById('bulkMachineIdTableBody');
        const emptyState = document.getElementById('bulkMachineIdEmptyState');
        const wrapper = document.getElementById('bulkMachineIdTableWrapper');

        if (!filteredExpiredSubscriptions.length) {
            emptyState.classList.remove('hidden');
            wrapper.classList.add('hidden');
            tbody.innerHTML = '';
            return;
        }

        emptyState.classList.add('hidden');
        wrapper.classList.remove('hidden');
        tbody.innerHTML = '';

        filteredExpiredSubscriptions.forEach(sub => {
            const row = document.createElement('tr');
            row.dataset.subscriptionId = sub.id;
            const email = sub.member?.email || 'Unknown';
            const appLabel = sub.app
                ? `${sub.app.display_name} (${sub.app.identifier})`
                : 'Livekenceng (Legacy)';
            const currentMachineId = sub.machine_id || '';

            row.innerHTML = `
                <td class="px-4 py-3">
                    <div class="text-sm font-medium text-gray-900">${escapeHtml(email)}</div>
                    ${sub.member?.telegram_username ? `<div class="text-xs text-gray-500">@${escapeHtml(sub.member.telegram_username)}</div>` : ''}
                </td>
                <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(appLabel)}</td>
                <td class="px-4 py-3 text-sm">
                    ${currentMachineId
                        ? `<code class="text-xs bg-gray-100 px-2 py-1 rounded break-all">${escapeHtml(currentMachineId)}</code>`
                        : '<span class="text-xs text-gray-500">Not set</span>'}
                </td>
                <td class="px-4 py-3">
                    <input type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
                        value="${escapeHtml(currentMachineId)}"
                        data-original="${escapeHtml(currentMachineId)}"
                        placeholder="New machine ID">
                </td>
            `;

            tbody.appendChild(row);
        });
    }

    // Niches & Product Sets Management
    let currentMemberId = null;
    let currentNiches = [];

    function loadNichesForMember() {
        const email = document.getElementById('nicheMemberSearch').value.trim();
        if (!email) {
            showToast('Please enter member email', 'error');
            return;
        }

        // First, find member by email
        fetch(`/admin/members?search=${encodeURIComponent(email)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.members.length > 0) {
                    const member = data.members[0];
                    if (member.email.toLowerCase() !== email.toLowerCase()) {
                        showToast('Member not found', 'error');
                        return;
                    }
                    currentMemberId = member.id;
                    
                    // Update UI
                    document.getElementById('selectedMemberInfo').innerHTML = 
                        `Selected: <strong>${member.email}</strong>`;
                    document.getElementById('selectedMemberInfo').classList.remove('hidden');
                    
                    // Load niches
                    fetch(`/admin/members/${member.id}/niches`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                currentNiches = data.niches;
                                window.productSetsWithoutNiche = data.product_sets_without_niche || [];
                                renderNiches();
                                document.getElementById('nichesSection').classList.remove('hidden');
                                document.getElementById('nichesEmptyState').classList.add('hidden');
                            }
                        })
                        .catch(error => {
                            console.error('Error loading niches:', error);
                            showToast('Error loading niches', 'error');
                        });
                } else {
                    showToast('Member not found', 'error');
                }
            })
            .catch(error => {
                console.error('Error searching member:', error);
                showToast('Error searching member', 'error');
            });
    }

    function renderNiches() {
        const container = document.getElementById('nichesList');
        container.innerHTML = '';

        let hasContent = currentNiches.length > 0;

        // Render product sets without niche first
        if (window.productSetsWithoutNiche && window.productSetsWithoutNiche.length > 0) {
            hasContent = true;
            const noNicheCard = document.createElement('div');
            noNicheCard.className = 'bg-white rounded-lg border border-gray-200 p-6 mb-4';
            
            const productSetsHtml = window.productSetsWithoutNiche.map(ps => `
                <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="font-semibold text-gray-900">${ps.name || 'Unnamed Set'}</h4>
                            ${ps.description ? `<p class="text-sm text-gray-600 mt-1">${ps.description}</p>` : ''}
                            <p class="text-xs text-gray-500 mt-2">${ps.items ? ps.items.length : 0} items</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="openAddItemsModal(${ps.id})" class="text-primary-600 hover:text-primary-900 text-sm font-medium">+ Items</button>
                            <button onclick="editProductSet(${ps.id})" class="text-primary-600 hover:text-primary-900 text-sm font-medium">Edit</button>
                            <button onclick="deleteProductSet(${ps.id})" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                            <a href="/admin/product-sets/${ps.id}/export" class="text-green-600 hover:text-green-900 text-sm font-medium">Export CSV</a>
                        </div>
                    </div>
                    ${ps.items && ps.items.length > 0 ? `
                        <div class="mt-3 max-h-48 overflow-y-auto">
                            <table class="min-w-full text-xs">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-2 text-left">URL</th>
                                        <th class="px-3 py-2 text-left">Shop ID</th>
                                        <th class="px-3 py-2 text-left">Item ID</th>
                                        <th class="px-3 py-2 text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${ps.items.map(item => `
                                        <tr class="border-t">
                                            <td class="px-3 py-2 font-mono text-xs">${item.url.substring(0, 50)}...</td>
                                            <td class="px-3 py-2">${item.shop_id}</td>
                                            <td class="px-3 py-2">${item.item_id}</td>
                                            <td class="px-3 py-2">
                                                <button onclick="deleteProductSetItem(${ps.id}, ${item.id})" class="text-red-600 hover:text-red-900">Remove</button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    ` : '<p class="text-sm text-gray-500 mt-2">No items</p>'}
                </div>
            `).join('');

            noNicheCard.innerHTML = `
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">No Niche</h3>
                        <p class="text-xs text-gray-500 mt-2">Product sets without niche assignment</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="openAddProductSetModal(null)" class="text-primary-600 hover:text-primary-900 text-sm font-medium">+ Product Set</button>
                    </div>
                </div>
                <div class="mt-4">
                    ${productSetsHtml || '<p class="text-sm text-gray-500">No product sets</p>'}
                </div>
            `;
            container.appendChild(noNicheCard);
        }

        if (!hasContent) {
            container.innerHTML = '<p class="text-gray-500 text-center py-8">No niches found. Create one to get started.</p>';
            return;
        }

        currentNiches.forEach(niche => {
            const nicheCard = document.createElement('div');
            nicheCard.className = 'bg-white rounded-lg border border-gray-200 p-6';
            
            const productSetsHtml = niche.product_sets && niche.product_sets.length > 0
                ? niche.product_sets.map(ps => `
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-semibold text-gray-900">${ps.name || 'Unnamed Set'}</h4>
                                ${ps.description ? `<p class="text-sm text-gray-600 mt-1">${ps.description}</p>` : ''}
                                <p class="text-xs text-gray-500 mt-2">${ps.items ? ps.items.length : 0} items</p>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="openAddItemsModal(${ps.id})" class="text-primary-600 hover:text-primary-900 text-sm font-medium">+ Items</button>
                                <button onclick="editProductSet(${ps.id})" class="text-primary-600 hover:text-primary-900 text-sm font-medium">Edit</button>
                                <button onclick="deleteProductSet(${ps.id})" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                                <a href="/admin/product-sets/${ps.id}/export" class="text-green-600 hover:text-green-900 text-sm font-medium">Export CSV</a>
                            </div>
                        </div>
                        ${ps.items && ps.items.length > 0 ? `
                            <div class="mt-3 max-h-48 overflow-y-auto">
                                <table class="min-w-full text-xs">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-3 py-2 text-left">URL</th>
                                            <th class="px-3 py-2 text-left">Shop ID</th>
                                            <th class="px-3 py-2 text-left">Item ID</th>
                                            <th class="px-3 py-2 text-left">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${ps.items.map(item => `
                                            <tr class="border-t">
                                                <td class="px-3 py-2 font-mono text-xs">${item.url.substring(0, 50)}...</td>
                                                <td class="px-3 py-2">${item.shop_id}</td>
                                                <td class="px-3 py-2">${item.item_id}</td>
                                                <td class="px-3 py-2">
                                                    <button onclick="deleteProductSetItem(${ps.id}, ${item.id})" class="text-red-600 hover:text-red-900">Remove</button>
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        ` : '<p class="text-sm text-gray-500 mt-2">No items</p>'}
                    </div>
                `).join('')
                : '<p class="text-sm text-gray-500 mt-4">No product sets</p>';

            nicheCard.innerHTML = `
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">${niche.name}</h3>
                        ${niche.description ? `<p class="text-sm text-gray-600 mt-1">${niche.description}</p>` : ''}
                        <p class="text-xs text-gray-500 mt-2">${niche.product_sets ? niche.product_sets.length : 0} product sets</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="openAddProductSetModal(${niche.id})" class="text-primary-600 hover:text-primary-900 text-sm font-medium">+ Product Set</button>
                        <button onclick="editNiche(${niche.id})" class="text-primary-600 hover:text-primary-900 text-sm font-medium">Edit</button>
                        <button onclick="deleteNiche(${niche.id})" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                        <a href="/admin/niches/${niche.id}/export" class="text-green-600 hover:text-green-900 text-sm font-medium">Export CSV</a>
                    </div>
                </div>
                <div class="mt-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Product Sets:</h4>
                    ${productSetsHtml}
                </div>
            `;
            container.appendChild(nicheCard);
        });
    }

    function openAddNicheModal() {
        if (!currentMemberId) {
            showToast('Please select a member first', 'error');
            return;
        }
        document.getElementById('nicheModalTitle').textContent = 'Add Niche';
        document.getElementById('nicheForm').reset();
        document.getElementById('nicheId').value = '';
        document.getElementById('nicheMemberId').value = currentMemberId;
        document.getElementById('nicheModal').classList.remove('hidden');
        document.getElementById('nicheModal').classList.add('flex');
    }

    function closeNicheModal() {
        document.getElementById('nicheModal').classList.add('hidden');
        document.getElementById('nicheModal').classList.remove('flex');
    }

    function editNiche(id) {
        const niche = currentNiches.find(n => n.id == id);
        if (!niche) return;

        document.getElementById('nicheModalTitle').textContent = 'Edit Niche';
        document.getElementById('nicheId').value = niche.id;
        document.getElementById('nicheMemberId').value = niche.member_id;
        document.getElementById('nicheName').value = niche.name;
        document.getElementById('nicheDescription').value = niche.description || '';
        document.getElementById('nicheModal').classList.remove('hidden');
        document.getElementById('nicheModal').classList.add('flex');
    }

    function deleteNiche(id) {
        if (!confirm('Are you sure you want to delete this niche? All product sets in this niche will be removed from the niche.')) return;

        fetch(`/admin/niches/${id}`, {
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
                loadNichesForMember();
            } else {
                showToast(data.message, 'error');
            }
        });
    }

    function openAddProductSetModal(nicheId = null) {
        if (!currentMemberId) {
            showToast('Please select a member first', 'error');
            return;
        }
        document.getElementById('productSetModalTitle').textContent = 'Add Product Set';
        document.getElementById('productSetForm').reset();
        document.getElementById('productSetId').value = '';
        document.getElementById('productSetMemberId').value = currentMemberId;
        
        // Load niches for dropdown
        const nicheSelect = document.getElementById('productSetNicheId');
        nicheSelect.innerHTML = '<option value="">No Niche</option>';
        currentNiches.forEach(niche => {
            const option = document.createElement('option');
            option.value = niche.id;
            option.textContent = niche.name;
            if (nicheId && niche.id == nicheId) {
                option.selected = true;
            }
            nicheSelect.appendChild(option);
        });
        
        document.getElementById('productSetModal').classList.remove('hidden');
        document.getElementById('productSetModal').classList.add('flex');
    }

    function closeProductSetModal() {
        document.getElementById('productSetModal').classList.add('hidden');
        document.getElementById('productSetModal').classList.remove('flex');
    }

    function editProductSet(id) {
        // Find product set
        let productSet = null;
        
        // Check in niches
        for (const niche of currentNiches) {
            if (niche.product_sets) {
                productSet = niche.product_sets.find(ps => ps.id == id);
                if (productSet) break;
            }
        }
        
        // Check in product sets without niche
        if (!productSet && window.productSetsWithoutNiche) {
            productSet = window.productSetsWithoutNiche.find(ps => ps.id == id);
        }
        
        if (!productSet) return;

        document.getElementById('productSetModalTitle').textContent = 'Edit Product Set';
        document.getElementById('productSetId').value = productSet.id;
        document.getElementById('productSetMemberId').value = productSet.member_id;
        document.getElementById('productSetName').value = productSet.name;
        document.getElementById('productSetDescription').value = productSet.description || '';
        
        // Load niches for dropdown
        const nicheSelect = document.getElementById('productSetNicheId');
        nicheSelect.innerHTML = '<option value="">No Niche</option>';
        currentNiches.forEach(niche => {
            const option = document.createElement('option');
            option.value = niche.id;
            option.textContent = niche.name;
            if (productSet.niche_id && niche.id == productSet.niche_id) {
                option.selected = true;
            }
            nicheSelect.appendChild(option);
        });
        
        document.getElementById('productSetModal').classList.remove('hidden');
        document.getElementById('productSetModal').classList.add('flex');
    }

    function deleteProductSet(id) {
        if (!confirm('Are you sure you want to delete this product set? All items will be deleted.')) return;

        fetch(`/admin/product-sets/${id}`, {
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
                loadNichesForMember();
            } else {
                showToast(data.message, 'error');
            }
        });
    }

    function openAddItemsModal(productSetId) {
        document.getElementById('productSetItemsModalTitle').textContent = 'Add Items to Product Set';
        document.getElementById('itemsProductSetId').value = productSetId;
        document.getElementById('productUrls').value = '';
        document.getElementById('productSetItemsModal').classList.remove('hidden');
        document.getElementById('productSetItemsModal').classList.add('flex');
    }

    function closeProductSetItemsModal() {
        document.getElementById('productSetItemsModal').classList.add('hidden');
        document.getElementById('productSetItemsModal').classList.remove('flex');
    }

    function deleteProductSetItem(productSetId, itemId) {
        if (!confirm('Are you sure you want to remove this item?')) return;

        fetch(`/admin/product-sets/${productSetId}/items/${itemId}`, {
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
                loadNichesForMember();
            } else {
                showToast(data.message, 'error');
            }
        });
    }

    // Form submissions
    document.getElementById('nicheForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('nicheId').value;
        const url = id ? `/admin/niches/${id}` : '/admin/niches';
        const method = id ? 'PUT' : 'POST';
        const data = {
            member_id: document.getElementById('nicheMemberId').value,
            name: document.getElementById('nicheName').value,
            description: document.getElementById('nicheDescription').value,
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
                showToast(data.message, 'success');
                closeNicheModal();
                loadNichesForMember();
            } else {
                showToast(data.message || 'Error saving niche', 'error');
            }
        });
    });

    document.getElementById('productSetForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('productSetId').value;
        const url = id ? `/admin/product-sets/${id}` : '/admin/product-sets';
        const method = id ? 'PUT' : 'POST';
        const data = {
            member_id: document.getElementById('productSetMemberId').value,
            niche_id: document.getElementById('productSetNicheId').value || null,
            name: document.getElementById('productSetName').value,
            description: document.getElementById('productSetDescription').value,
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
                showToast(data.message, 'success');
                closeProductSetModal();
                loadNichesForMember();
            } else {
                showToast(data.message || 'Error saving product set', 'error');
            }
        });
    });

    document.getElementById('productSetItemsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const productSetId = document.getElementById('itemsProductSetId').value;
        const urls = document.getElementById('productUrls').value
            .split('\n')
            .map(line => line.trim())
            .filter(line => line.length > 0 && line.includes('shopee.co.id/product/'));
        
        if (urls.length === 0) {
            showToast('Please enter at least one valid Shopee product URL', 'error');
            return;
        }

        const items = urls.map(url => ({ url }));
        
        fetch(`/admin/product-sets/${productSetId}/items`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ items })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(`Items processed: ${data.added} added, ${data.skipped} skipped`, 'success');
                closeProductSetItemsModal();
                loadNichesForMember();
            } else {
                showToast(data.message || 'Error adding items', 'error');
            }
        });
    });

    // Member Management
    let allMembers = []; // Store all members for pagination
    let currentMemberPage = 1;

    function loadMembers() {
        const search = document.getElementById('memberSearch').value;
        const sortBy = document.getElementById('memberSortBy').value;
        
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (sortBy) params.append('sort_by', sortBy);
        
        fetch(`/admin/members?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    allMembers = data.members;
                    currentMemberPage = 1;
                    renderMembersTable();
                }
            })
            .catch(error => {
                console.error('Error loading members:', error);
            });
    }

    function renderMembersTable() {
        const tbody = document.getElementById('membersTableBody');
        tbody.innerHTML = '';
        
        const perPage = parseInt(document.getElementById('memberPerPage').value) || 10;
        const start = (currentMemberPage - 1) * perPage;
        const end = start + perPage;
        const displayedMembers = allMembers.slice(start, end);
        
        displayedMembers.forEach(member => {
            const row = document.createElement('tr');
            
            // Count active subscriptions
            const activeSubs = member.subscriptions ? member.subscriptions.filter(sub => {
                if (!sub.expiry_date) return false;
                return new Date(sub.expiry_date) > new Date();
            }).length : 0;
            
            const totalSubs = member.subscriptions ? member.subscriptions.length : 0;
            const subsBadge = totalSubs > 0 
                ? `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">${activeSubs}/${totalSubs} Active</span>`
                : '<span class="text-gray-400 text-xs">No subscriptions</span>';
            
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${member.email}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${member.telegram_username || '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap">${subsBadge}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="/admin/members/${member.id}/edit" class="text-primary-600 hover:text-primary-900 mr-3">Edit</a>
                    <button onclick="deleteMember(${member.id})" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
            `;
            tbody.appendChild(row);
        });
        
        renderMemberPagination();
    }

    function renderMemberPagination() {
        const pagination = document.getElementById('membersPagination');
        const perPage = parseInt(document.getElementById('memberPerPage').value) || 10;
        const totalPages = Math.ceil(allMembers.length / perPage);
        
        if (allMembers.length === 0) {
            pagination.innerHTML = '';
            return;
        }
        
        const start = (currentMemberPage - 1) * perPage + 1;
        const end = Math.min(currentMemberPage * perPage, allMembers.length);
        
        pagination.innerHTML = `
            <div class="text-sm text-gray-700">
                Showing ${start} to ${end} of ${allMembers.length} members
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="changeMemberPage(${currentMemberPage - 1})" 
                        ${currentMemberPage === 1 ? 'disabled' : ''}
                        class="px-3 py-1 border border-gray-300 rounded-lg ${currentMemberPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                    ← Previous
                </button>
                <span class="text-sm text-gray-700">Page ${currentMemberPage} of ${totalPages}</span>
                <button onclick="changeMemberPage(${currentMemberPage + 1})" 
                        ${currentMemberPage === totalPages ? 'disabled' : ''}
                        class="px-3 py-1 border border-gray-300 rounded-lg ${currentMemberPage === totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                    Next →
                </button>
            </div>
        `;
    }

    function changeMemberPage(page) {
        const perPage = parseInt(document.getElementById('memberPerPage').value) || 10;
        const totalPages = Math.ceil(allMembers.length / perPage);
        
        if (page >= 1 && page <= totalPages) {
            currentMemberPage = page;
            renderMembersTable();
        }
    }

    function clearMemberFilters() {
        document.getElementById('memberSearch').value = '';
        document.getElementById('memberSortBy').value = 'created_at_desc';
        document.getElementById('memberPerPage').value = '10';
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
                    document.getElementById('memberTelegramUsername').value = member.telegram_username || '';
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
            telegram_username: document.getElementById('memberTelegramUsername').value,
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
                showToast(data.message, 'success');
                loadMembers();
                closeMemberModal();
            } else if (data.errors) {
                showToast(Object.values(data.errors).flat().join('\n'), 'error');
            }
        });
    });

    // License Management
    function openGenerateLicenseModal() {
        document.getElementById('licenseForm').reset();
        // Load apps into the dropdown
        loadAppsForLicenseModal();
        document.getElementById('licenseModal').classList.remove('hidden');
        document.getElementById('licenseModal').classList.add('flex');
    }

    function loadAppsForLicenseModal() {
        fetch('/admin/apps')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const appSelect = document.getElementById('licenseAppId');
                    // Keep the first option (No App - Legacy)
                    const firstOption = appSelect.options[0];
                    appSelect.innerHTML = '';
                    appSelect.appendChild(firstOption);
                    
                    // Add apps to dropdown (excluding livekenceng as it's the default legacy app)
                    data.apps.forEach(app => {
                        // Skip livekenceng - it's the default for legacy licenses
                        if (app.identifier !== 'livekenceng') {
                            const option = document.createElement('option');
                            option.value = app.id;
                            option.textContent = `${app.display_name} (${app.identifier})`;
                            appSelect.appendChild(option);
                        }
                    });
                    
                    // Load plans for default app
                    loadPlansForApp(null);
                }
            })
            .catch(error => {
                console.error('Error loading apps:', error);
            });
    }

    function loadPlansForApp(appId) {
        fetch('/admin/plans' + (appId ? `?app_id=${appId}` : ''))
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const planSelect = document.getElementById('licensePlanId');
                    planSelect.innerHTML = '';
                    
                    data.plans.forEach(plan => {
                        const option = document.createElement('option');
                        option.value = plan.id;
                        option.textContent = `${plan.name || plan.duration_days + ' Day'} - Rp ${parseFloat(plan.price).toLocaleString('id-ID')}`;
                        planSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading plans:', error);
            });
    }

    function loadAllPlansGrouped() {
        Promise.all([
            fetch('/admin/apps').then(res => res.json()),
            fetch('/admin/plans').then(res => res.json())
        ])
        .then(([appsData, plansData]) => {
            if (appsData.success && plansData.success) {
                renderPlansGroupedByApp(appsData.apps, plansData.plans);
            }
        })
        .catch(error => {
            console.error('Error loading plans:', error);
        });
    }

    function renderPlansGroupedByApp(apps, plans) {
        const content = document.getElementById('plansContent');
        content.innerHTML = '';
        
        // Group plans by app_id
        const plansByApp = {};
        plans.forEach(plan => {
            const appId = plan.app_id || 'legacy';
            if (!plansByApp[appId]) {
                plansByApp[appId] = [];
            }
            plansByApp[appId].push(plan);
        });
        
        // Render each app's plans
        Object.keys(plansByApp).forEach(appId => {
            const appPlans = plansByApp[appId];
            let appName = 'Livekenceng (Legacy)';
            let appDisplayName = 'Livekenceng (Legacy)';
            
            if (appId !== 'legacy') {
                const app = apps.find(a => a.id == appId);
                if (app) {
                    appName = app.name;
                    appDisplayName = app.display_name;
                }
            }
            
            const section = document.createElement('div');
            section.className = 'bg-white rounded-lg border border-gray-200 overflow-hidden';
            section.innerHTML = `
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-white">${appDisplayName}</h3>
                        <button onclick="openAddPlanModalForApp(${appId === 'legacy' ? 'null' : appId})" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            + Add Plan
                        </button>
                    </div>
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
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${appPlans.map(plan => `
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${plan.name || '-'}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${plan.duration_days}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${parseFloat(plan.price).toLocaleString('id-ID')}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        ${plan.is_active 
                                            ? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>'
                                            : '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>'
                                        }
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="editPlan(${plan.id})" class="text-primary-600 hover:text-primary-900 mr-3">Edit</button>
                                        <button onclick="deletePlan(${plan.id})" class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
            content.appendChild(section);
        });
    }

    function loadAllLicensesGrouped() {
        Promise.all([
            fetch('/admin/apps').then(res => res.json()),
            fetch('/admin/licenses').then(res => res.json())
        ])
        .then(([appsData, licensesData]) => {
            if (appsData.success && licensesData.success) {
                renderLicensesGroupedByApp(appsData.apps, licensesData.licenses);
            }
        })
        .catch(error => {
            console.error('Error loading licenses:', error);
        });
    }

    function renderLicensesGroupedByApp(apps, licenses) {
        const content = document.getElementById('licensesContent');
        content.innerHTML = '';
        
        // Group licenses by app_id
        const licensesByApp = {};
        licenses.forEach(license => {
            const appId = license.app_id || 'legacy';
            if (!licensesByApp[appId]) {
                licensesByApp[appId] = [];
            }
            licensesByApp[appId].push(license);
        });
        
        // Render each app's licenses
        Object.keys(licensesByApp).forEach(appId => {
            const appLicenses = licensesByApp[appId];
            let appName = 'Livekenceng (Legacy)';
            let appDisplayName = 'Livekenceng (Legacy)';
            
            if (appId !== 'legacy') {
                const app = apps.find(a => a.id == appId);
                if (app) {
                    appName = app.name;
                    appDisplayName = app.display_name;
                }
            }
            
            const section = document.createElement('div');
            section.className = 'bg-white rounded-lg border border-gray-200 overflow-hidden';
            section.innerHTML = `
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">${appDisplayName}</h3>
                </div>
                <div id="licensesTableWrapper-${appId}" class="overflow-x-auto">
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
                        <tbody class="bg-white divide-y divide-gray-200" id="licensesTableBody-${appId}">
                            ${appLicenses.slice(0, 5).map(license => `
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">${license.code}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${license.duration_days} days</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        ${license.is_used 
                                            ? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Used</span>'
                                            : '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Available</span>'
                                        }
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${license.member?.email || '-'}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        ${license.creator 
                                            ? (license.creator.name || license.creator.email)
                                            : license.reseller 
                                                ? license.reseller.name + ' (Reseller)'
                                                : 'System'
                                        }
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(license.created_at).toISOString().split('T')[0]}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="copyLicense('${license.code}')" class="text-primary-600 hover:text-primary-900 mr-3">Copy</button>
                                        ${!license.is_used 
                                            ? `<button onclick="deleteLicense(${license.id})" class="text-red-600 hover:text-red-900">Delete</button>`
                                            : ''
                                        }
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                ${appLicenses.length > 5 ? `
                    <div class="px-6 py-4 bg-gray-50 text-center">
                        <p class="text-sm text-gray-600">Showing 5 of ${appLicenses.length} licenses</p>
                        <button onclick="showAllLicenses('${appId}', ${JSON.stringify(appLicenses).replace(/"/g, '&quot;')})" class="mt-2 text-primary-600 hover:text-primary-900 text-sm font-medium">Show All</button>
                    </div>
                ` : ''}
            `;
            content.appendChild(section);
        });
    }

    function showAllLicenses(appId, licenses) {
        const tbody = document.getElementById('licensesTableBody-' + appId);
        const wrapper = document.getElementById('licensesTableWrapper-' + appId);
        const nextSibling = wrapper.nextElementSibling;
        
        if (nextSibling && nextSibling.classList.contains('px-6')) {
            nextSibling.remove();
        }
        
        tbody.innerHTML = licenses.map(license => `
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">${license.code}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${license.duration_days} days</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${license.is_used 
                        ? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Used</span>'
                        : '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Available</span>'
                    }
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${license.member?.email || '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${license.creator 
                        ? (license.creator.name || license.creator.email)
                        : license.reseller 
                            ? license.reseller.name + ' (Reseller)'
                            : 'System'
                    }
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(license.created_at).toISOString().split('T')[0]}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button onclick="copyLicense('${license.code}')" class="text-primary-600 hover:text-primary-900 mr-3">Copy</button>
                    ${!license.is_used 
                        ? `<button onclick="deleteLicense(${license.id})" class="text-red-600 hover:text-red-900">Delete</button>`
                        : ''
                    }
                </td>
            </tr>
        `).join('');
    }

    function closeLicenseModal() {
        document.getElementById('licenseModal').classList.add('hidden');
        document.getElementById('licenseModal').classList.remove('flex');
    }

    function copyLicense(code) {
        navigator.clipboard.writeText(code).then(() => {
            showToast('License key copied to clipboard!', 'success');
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
                showToast(data.message, 'success');
                loadAllLicensesGrouped();
            } else {
                showToast(data.message, 'error');
            }
        });
    }

    document.getElementById('licenseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
        const data = {
            plan_id: document.getElementById('licensePlanId').value,
            quantity: document.getElementById('licenseQuantity').value,
            app_id: document.getElementById('licenseAppId').value || null,
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
                showToast(data.message, 'success');
                closeLicenseModal();
                loadAllLicensesGrouped();
            } else if (data.errors) {
                alert(Object.values(data.errors).flat().join('\n'));
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
    function openAddPlanModalForApp(appId) {
        document.getElementById('planModalTitle').textContent = 'Add Plan';
        document.getElementById('planForm').reset();
        document.getElementById('planId').value = '';
        document.getElementById('planActive').value = '1';
        
        // Load apps into dropdown
        fetch('/admin/apps')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const appSelect = document.getElementById('planAppSelect');
                    appSelect.innerHTML = '';
                    
                    // Add livekenceng first
                    const livekencengApp = data.apps.find(a => a.identifier === 'livekenceng');
                    if (livekencengApp) {
                        const option = document.createElement('option');
                        option.value = livekencengApp.id;
                        option.textContent = `${livekencengApp.display_name} (Legacy)`;
                        appSelect.appendChild(option);
                    }
                    
                    // Add other apps
                    data.apps.filter(a => a.identifier !== 'livekenceng').forEach(app => {
                        const option = document.createElement('option');
                        option.value = app.id;
                        option.textContent = app.display_name;
                        appSelect.appendChild(option);
                    });
                    
                    // Set the selected app
                    if (appId) {
                        appSelect.value = appId;
                    }
                }
            });
        
        document.getElementById('planModal').classList.remove('hidden');
        document.getElementById('planModal').classList.add('flex');
    }

    function openAddPlanModal() {
        openAddPlanModalForApp(null);
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
                    
                    // Load apps into dropdown
                    fetch('/admin/apps')
                        .then(appRes => appRes.json())
                        .then(appData => {
                            if (appData.success) {
                                const appSelect = document.getElementById('planAppSelect');
                                appSelect.innerHTML = '';
                                
                                // Add livekenceng first
                                const livekencengApp = appData.apps.find(a => a.identifier === 'livekenceng');
                                if (livekencengApp) {
                                    const option = document.createElement('option');
                                    option.value = livekencengApp.id;
                                    option.textContent = `${livekencengApp.display_name} (Legacy)`;
                                    appSelect.appendChild(option);
                                }
                                
                                // Add other apps
                                appData.apps.filter(a => a.identifier !== 'livekenceng').forEach(app => {
                                    const option = document.createElement('option');
                                    option.value = app.id;
                                    option.textContent = app.display_name;
                                    appSelect.appendChild(option);
                                });
                                
                                // Set the current plan's app
                                appSelect.value = plan.app_id || '';
                            }
                        });
                    
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
                showToast(data.message, 'success');
                loadAllPlansGrouped();
            } else {
                showToast(data.message, 'error');
            }
        });
    }

    document.getElementById('planForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('planId').value;
        const url = id ? `/admin/plans/${id}` : '/admin/plans';
        const method = id ? 'PUT' : 'POST';
        const data = {
            app_id: document.getElementById('planAppSelect').value,
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
                showToast(data.message, 'success');
                closePlanModal();
                loadAllPlansGrouped();
            } else if (data.message) {
                showToast(data.message, 'error');
            } else if (data.errors) {
                showToast(Object.values(data.errors).flat().join('\n'), 'error');
            }
        });
    });

    // App Management
    function loadApps() {
        fetch('/admin/apps')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderAppsTable(data.apps);
                }
            })
            .catch(error => {
                console.error('Error loading apps:', error);
            });
    }

    function renderAppsTable(apps) {
        const tbody = document.getElementById('appsTableBody');
        tbody.innerHTML = '';
        
        apps.forEach(app => {
            const row = document.createElement('tr');
            const statusBadge = app.is_active 
                ? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>'
                : '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>';
            
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${app.name}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${app.display_name}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">${app.identifier}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${app.subscriptions_count}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${app.license_keys_count}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${app.license_plans_count}</td>
                <td class="px-6 py-4 whitespace-nowrap">${statusBadge}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button onclick="toggleAppActive(${app.id})" class="text-${app.is_active ? 'yellow' : 'green'}-600 hover:text-${app.is_active ? 'yellow' : 'green'}-900 mr-3">
                        ${app.is_active ? 'Deactivate' : 'Activate'}
                    </button>
                    <button onclick="editApp(${app.id})" class="text-primary-600 hover:text-primary-900 mr-3">Edit</button>
                    <button onclick="deleteApp(${app.id})" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function openAddAppModal() {
        document.getElementById('appModalTitle').textContent = 'Add App';
        document.getElementById('appForm').reset();
        document.getElementById('appId').value = '';
        document.getElementById('appIsActive').checked = true;
        document.getElementById('appModal').classList.remove('hidden');
        document.getElementById('appModal').classList.add('flex');
    }

    function closeAppModal() {
        document.getElementById('appModal').classList.add('hidden');
        document.getElementById('appModal').classList.remove('flex');
    }

    function editApp(id) {
        fetch(`/admin/apps/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const app = data.app;
                    document.getElementById('appModalTitle').textContent = 'Edit App';
                    document.getElementById('appId').value = app.id;
                    document.getElementById('appName').value = app.name;
                    document.getElementById('appDisplayName').value = app.display_name;
                    document.getElementById('appIdentifier').value = app.identifier;
                    document.getElementById('appDescription').value = app.description || '';
                    document.getElementById('appIsActive').checked = app.is_active;
                    document.getElementById('appModal').classList.remove('hidden');
                    document.getElementById('appModal').classList.add('flex');
                }
            });
    }

    function deleteApp(id) {
        if (!confirm('Are you sure you want to delete this app? This will also delete all associated subscriptions, license keys, and plans.')) return;
        
        fetch(`/admin/apps/${id}`, {
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
                loadApps();
            } else {
                showToast(data.message, 'error');
            }
        });
    }

    function toggleAppActive(id) {
        fetch(`/admin/apps/${id}/toggle-active`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
            .then(data => {
                if (data.success) {
                showToast(data.message, 'success');
                loadApps();
            } else {
                showToast(data.message, 'error');
            }
        });
    }

    document.getElementById('appForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('appId').value;
        const url = id ? `/admin/apps/${id}` : '/admin/apps';
        const method = id ? 'PUT' : 'POST';
        const data = {
            name: document.getElementById('appName').value,
            display_name: document.getElementById('appDisplayName').value,
            identifier: document.getElementById('appIdentifier').value,
            description: document.getElementById('appDescription').value,
            is_active: document.getElementById('appIsActive').checked
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
                showToast(data.message, 'success');
                closeAppModal();
                loadApps();
            } else if (data.errors) {
                alert(Object.values(data.errors).flat().join('\n'));
                } else {
                showToast(data.message || 'Error saving app', 'error');
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
        document.getElementById('memberSortBy').addEventListener('change', loadMembers);
        document.getElementById('memberPerPage').addEventListener('change', function() {
            currentMemberPage = 1;
            renderMembersTable();
        });
        
        // Add event listener for app change in license modal
        const licenseAppSelect = document.getElementById('licenseAppId');
        if (licenseAppSelect) {
            licenseAppSelect.addEventListener('change', function() {
                const appId = this.value || null;
                loadPlansForApp(appId);
            });
        }

        const expiredSearchInput = document.getElementById('expiredSearch');
        if (expiredSearchInput) {
            expiredSearchInput.addEventListener('input', debounce(renderExpiredSubscriptions, 300));
        }

        const bulkMachineIdForm = document.getElementById('bulkMachineIdForm');
        if (bulkMachineIdForm) {
            bulkMachineIdForm.addEventListener('submit', handleBulkMachineIdSubmit);
        }
    });

    // Toast notification function
    function showToast(message, type = 'success') {
        // Create toast element if it doesn't exist
        let toast = document.getElementById('toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toast';
            toast.className = 'fixed top-4 right-4 z-50 hidden';
            toast.innerHTML = `
                <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-4 max-w-sm">
                    <div class="flex items-center">
                        <div id="toastIcon" class="flex-shrink-0"></div>
                        <div class="ml-3">
                            <p id="toastMessage" class="text-sm font-medium text-gray-900"></p>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
        }
        
        const toastIcon = document.getElementById('toastIcon');
        const toastMessage = document.getElementById('toastMessage');
        
        toastMessage.textContent = message;
        
        if (type === 'success') {
            toastIcon.innerHTML = '<svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
        } else if (type === 'error') {
            toastIcon.innerHTML = '<svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
        }
        
        toast.classList.remove('hidden');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }

    function handleBulkMachineIdSubmit(event) {
        event.preventDefault();
        const rows = document.querySelectorAll('#bulkMachineIdTableBody tr');
        if (!rows.length) {
            showToast('No subscriptions to update', 'error');
            return;
        }

        const updates = [];
        rows.forEach(row => {
            const subscriptionId = Number(row.dataset.subscriptionId);
            const input = row.querySelector('input');
            if (!input) {
                return;
            }
            const value = input.value.trim();
            const original = input.dataset.original || '';
            if (value === original) {
                return;
            }
            updates.push({
                subscription_id: subscriptionId,
                machine_id: value || null,
            });
        });

        if (!updates.length) {
            showToast('No changes detected', 'error');
            return;
        }

        fetch('/admin/subscriptions/expired/machine-ids', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ updates })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    data.results.forEach(result => {
                        if (result.status === 'updated') {
                            const target = expiredSubscriptions.find(sub => Number(sub.id) === Number(result.subscription_id));
                            if (target) {
                                target.machine_id = result.machine_id;
                            }
                        }
                    });
                    renderExpiredSubscriptions();
                    populateBulkMachineIdTable();
                } else {
                    showToast(data.message || 'Failed to update machine IDs', 'error');
        }
            })
            .catch(error => {
                console.error('Error updating machine IDs:', error);
                showToast('Failed to update machine IDs', 'error');
            });
    }

    </script>
@endpush
@endsection


