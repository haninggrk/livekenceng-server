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
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Machine ID</label>
                            <input type="text" id="machine_id" value="{{ $member->machine_id }}" 
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Telegram Username</label>
                            <input type="text" id="telegram_username" value="{{ $member->telegram_username }}" 
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                                   placeholder="@username">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Expiry Date</label>
                            <input type="datetime-local" id="expiry_date" 
                                   value="{{ $member->expiry_date ? $member->expiry_date->format('Y-m-d\TH:i') : '' }}"
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-primary-500 hover:bg-primary-600 text-white py-2 rounded-lg font-semibold transition-colors">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Shopee Accounts Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Shopee Accounts</h2>
                        <button onclick="openAddShopeeModal()" class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            + Add Account
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cookie</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="shopeeAccountsTableBody">
                                @foreach($member->shopeeAccounts as $account)
                                <tr>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $account->name }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $account->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500">
                                        <div class="max-w-xs truncate">{{ $account->cookie }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button onclick="editShopeeAccount({{ $account->id }})" class="text-primary-600 hover:text-primary-900">Edit</button>
                                        <button onclick="deleteShopeeAccount({{ $account->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($member->shopeeAccounts->isEmpty())
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Shopee accounts</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by adding a Shopee account.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Shopee Account Modal -->
<div id="shopeeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-lg w-full mx-4">
        <h3 id="shopeeModalTitle" class="text-2xl font-bold text-gray-900 mb-6">Add Shopee Account</h3>
        <form id="shopeeForm">
            <input type="hidden" id="shopeeAccountId">
            <input type="hidden" id="shopeeMemberId" value="{{ $member->id }}">
            
            <div class="mb-4">
                <label for="shopeeName" class="block text-sm font-medium text-gray-700 mb-2">Account Name</label>
                <input type="text" id="shopeeName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="e.g., My Shopee Account" required>
            </div>

            <div class="mb-4">
                <label for="shopeeCookie" class="block text-sm font-medium text-gray-700 mb-2">Cookie</label>
                <textarea id="shopeeCookie" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="session_id=abc123; user_id=456; ..." required></textarea>
            </div>
            
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" id="shopeeIsActive" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" checked>
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeShopeeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                    Save
                </button>
            </div>
        </form>
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
        machine_id: document.getElementById('machine_id').value,
        telegram_username: document.getElementById('telegram_username').value,
        expiry_date: document.getElementById('expiry_date').value,
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

// Shopee Accounts Management
function openAddShopeeModal() {
    document.getElementById('shopeeModalTitle').textContent = 'Add Shopee Account';
    document.getElementById('shopeeForm').reset();
    document.getElementById('shopeeAccountId').value = '';
    document.getElementById('shopeeModal').classList.remove('hidden');
    document.getElementById('shopeeModal').classList.add('flex');
}

function closeShopeeModal() {
    document.getElementById('shopeeModal').classList.add('hidden');
    document.getElementById('shopeeModal').classList.remove('flex');
}

function editShopeeAccount(id) {
    fetch(`/admin/shopee-accounts/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const account = data.shopee_account;
                document.getElementById('shopeeModalTitle').textContent = 'Edit Shopee Account';
                document.getElementById('shopeeAccountId').value = account.id;
                document.getElementById('shopeeName').value = account.name;
                document.getElementById('shopeeCookie').value = account.cookie;
                document.getElementById('shopeeIsActive').checked = account.is_active;
                document.getElementById('shopeeModal').classList.remove('hidden');
                document.getElementById('shopeeModal').classList.add('flex');
            }
        })
        .catch(error => {
            showToast('Error loading shopee account: ' + error.message, 'error');
        });
}

function deleteShopeeAccount(id) {
    if (confirm('Are you sure you want to delete this Shopee account?')) {
        fetch(`/admin/shopee-accounts/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Shopee account deleted successfully', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showToast('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Error deleting shopee account: ' + error.message, 'error');
        });
    }
}

// Handle Shopee form submission
document.getElementById('shopeeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        member_id: document.getElementById('shopeeMemberId').value,
        name: document.getElementById('shopeeName').value,
        cookie: document.getElementById('shopeeCookie').value,
        is_active: document.getElementById('shopeeIsActive').checked
    };
    
    const accountId = document.getElementById('shopeeAccountId').value;
    const url = accountId ? `/admin/shopee-accounts/${accountId}` : '/admin/shopee-accounts';
    const method = accountId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeShopeeModal();
            showToast(data.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showToast('Error: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        showToast('Error: ' + error.message, 'error');
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
</script>
@endpush
@endsection
