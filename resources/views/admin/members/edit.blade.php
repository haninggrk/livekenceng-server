@extends('layouts.app')

@section('title', 'Edit Member - Livekenceng Admin')

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
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900 font-medium">
                        ← Back to Dashboard
                    </a>
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
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Edit Member</h1>
            <p class="text-gray-600 mt-2">Manage member profile and Shopee accounts</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Member Profile Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Member Profile</h2>
                    
                    <form id="memberForm" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email" id="email" value="{{ $member->email }}" required 
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                            <input type="password" id="password" 
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Telegram Username</label>
                            <input type="text" id="telegram_username" value="{{ $member->telegram_username }}" 
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                                   placeholder="@username">
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-primary-500 hover:bg-primary-600 text-white py-2 rounded-lg font-semibold transition-colors">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Subscriptions & Shopee Accounts Section -->
            <div class="lg:col-span-2 space-y-6">
                <!-- App Subscriptions -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">App Subscriptions</h2>
                    </div>

                    <div class="space-y-4" id="subscriptionsContainer">
                        @foreach($member->subscriptions as $subscription)
                        <div class="border border-gray-200 rounded-lg p-4" data-subscription-id="{{ $subscription->id }}">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $subscription->app->display_name ?? 'Unknown App' }}</h3>
                                    <p class="text-sm text-gray-500">{{ $subscription->app->identifier ?? 'N/A' }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $subscription->expiry_date && $subscription->expiry_date->isFuture() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $subscription->expiry_date && $subscription->expiry_date->isFuture() ? 'Active' : 'Expired' }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <p class="text-xs text-gray-500">Machine ID</p>
                                        <button onclick="editMachineId({{ $subscription->id }}, '{{ addslashes($subscription->machine_id ?? '') }}', '{{ $subscription->app->identifier }}')" class="text-primary-600 hover:text-primary-900 text-xs font-medium">Edit</button>
                                    </div>
                                    <p id="machine-id-display-{{ $subscription->id }}" class="text-sm font-medium text-gray-900 font-mono">{{ $subscription->machine_id ? Str::limit($subscription->machine_id, 20) : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Expiry Date</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $subscription->expiry_date ? $subscription->expiry_date->format('Y-m-d H:i') : '-' }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($member->subscriptions->isEmpty())
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No subscriptions</h3>
                        <p class="mt-1 text-sm text-gray-500">This member has no app subscriptions yet.</p>
                    </div>
                    @endif
                </div>

                <!-- Shopee Accounts -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Shopee Accounts</h2>
                    </div>

                    <div class="space-y-4" id="shopeeAccountsContainer">
                        @foreach($member->shopeeAccounts as $account)
                        <div class="border border-gray-200 rounded-lg p-4" data-account-id="{{ $account->id }}">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $account->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $account->email }}</p>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $account->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <p class="text-xs text-gray-500 mb-1">Cookie</p>
                                <p class="text-xs font-mono bg-gray-50 p-2 rounded border border-gray-200 break-all">{{ Str::limit($account->cookie, 150) }}</p>
                            </div>
                            <div class="mt-3" id="session-info-{{ $account->id }}">
                                <button onclick="loadActiveSession({{ $account->id }})" class="text-xs text-primary-600 hover:text-primary-900 font-medium">
                                    🔍 Check Active Live Session
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($member->shopeeAccounts->isEmpty())
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Shopee accounts</h3>
                        <p class="mt-1 text-sm text-gray-500">This member has no Shopee accounts yet.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Member form submission
document.getElementById('memberForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const data = {
        email: document.getElementById('email').value,
        telegram_username: document.getElementById('telegram_username').value,
    };
    
    const password = document.getElementById('password').value;
    if (password) {
        data.password = password;
    }
    
    fetch(`/admin/members/{{ $member->id }}`, {
        method: 'PUT',
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
            showToast('Member updated successfully', 'success');
        } else if (data.errors) {
            showToast(Object.values(data.errors).flat().join('\n'), 'error');
        }
    })
    .catch(error => {
        showToast('Error updating member', 'error');
    });
});

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

function editMachineId(subscriptionId, currentMachineId, appIdentifier) {
    const newMachineId = prompt('Enter new Machine ID:', currentMachineId || '');
    
    if (newMachineId === null) return; // User cancelled
    
    if (!newMachineId.trim()) {
        showToast('Machine ID cannot be empty', 'error');
        return;
    }
    
    const data = {
        email: '{{ $member->email }}',
        machine_id: newMachineId.trim(),
        app_identifier: appIdentifier
    };
    
    fetch('/api/members/machine-id', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update display
            const displayEl = document.getElementById(`machine-id-display-${subscriptionId}`);
            if (displayEl) {
                displayEl.textContent = data.machine_id.length > 20 ? data.machine_id.substring(0, 20) + '...' : data.machine_id;
            }
            showToast('Machine ID updated successfully', 'success');
        } else {
            showToast(data.message || 'Error updating machine ID', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating machine ID', 'error');
    });
}

function loadActiveSession(accountId) {
    const sessionInfoEl = document.getElementById(`session-info-${accountId}`);
    
    // Show loading state
    sessionInfoEl.innerHTML = `
        <span class="text-xs text-gray-500">⏳ Checking...</span>
    `;
    
    // We need to call the ShopeeService from the backend, so let's create an admin endpoint
    // For now, we'll call it via AJAX
    fetch(`/admin/shopee-accounts/${accountId}/active-session`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.session_id) {
            const liveUrl = `http://live.shopee.co.id/share?from=live&session=${data.session_id}`;
            sessionInfoEl.innerHTML = `
                <div class="flex items-center gap-2">
                    <a href="${liveUrl}" target="_blank" class="text-xs text-primary-600 hover:text-primary-900 font-medium underline">
                        🔴 Go to Live: Session ${data.session_id}
                    </a>
                    <button onclick="loadActiveSession(${accountId})" class="text-xs text-gray-500 hover:text-gray-700">
                        🔄 Refresh
                    </button>
                </div>
            `;
        } else {
            sessionInfoEl.innerHTML = `
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500">❌ No active live session</span>
                    <button onclick="loadActiveSession(${accountId})" class="text-xs text-gray-500 hover:text-gray-700">
                        🔄 Refresh
                    </button>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        sessionInfoEl.innerHTML = `
            <div class="flex items-center gap-2">
                <span class="text-xs text-red-600">⚠️ Error checking session</span>
                <button onclick="loadActiveSession(${accountId})" class="text-xs text-gray-500 hover:text-gray-700">
                    🔄 Retry
                </button>
            </div>
        `;
    });
}
</script>
@endpush
@endsection
