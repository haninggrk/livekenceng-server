@extends('layouts.app')

@section('title', 'Create Software Update - Livekenceng Admin')

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
                    <a href="{{ route('admin.updates.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">
                        ← Back to Updates
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

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Create Software Update</h1>
            <p class="text-gray-600 mt-2">Upload a new software release for automatic updates</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <form id="createUpdateForm" enctype="multipart/form-data">
                @csrf
                
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label for="target" class="block text-sm font-medium text-gray-700 mb-2">Target Application</label>
                        <input type="text" id="target" name="target" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="e.g., live-kenceng">
                        <p class="text-xs text-gray-500 mt-1">The application identifier for this update</p>
                    </div>
                    
                    <div>
                        <label for="version" class="block text-sm font-medium text-gray-700 mb-2">Version</label>
                        <input type="text" id="version" name="version" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="e.g., 0.1.2">
                        <p class="text-xs text-gray-500 mt-1">Version number (semantic versioning recommended)</p>
                    </div>
                </div>

                <div class="mb-8">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Release Notes</label>
                    <textarea id="notes" name="notes" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Bug fixes and improvements"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Description of changes in this release</p>
                </div>

                <div class="mb-8">
                    <label for="pub_date" class="block text-sm font-medium text-gray-700 mb-2">Release Date</label>
                    <input type="datetime-local" id="pub_date" name="pub_date" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Platform Files -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Platform Files</h3>
                    <div id="platformsContainer">
                        <!-- Platform files will be added here -->
                    </div>
                    <button type="button" id="addPlatformBtn" 
                            class="mt-4 inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Platform
                    </button>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.updates.index') }}" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                        Create Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 z-50 hidden">
    <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-4 max-w-sm">
        <div class="flex items-center">
            <div id="toastIcon" class="flex-shrink-0">
                <!-- Icon will be inserted here -->
            </div>
            <div class="ml-3">
                <p id="toastMessage" class="text-sm font-medium text-gray-900"></p>
            </div>
        </div>
    </div>
</div>

<script>
let platformCount = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Set default publication date to now
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    document.getElementById('pub_date').value = now.toISOString().slice(0, 16);
    
    // Add initial platform
    addPlatform();
});

document.getElementById('addPlatformBtn').addEventListener('click', addPlatform);

function addPlatform() {
    platformCount++;
    const container = document.getElementById('platformsContainer');
    
    const platformDiv = document.createElement('div');
    platformDiv.className = 'border border-gray-200 rounded-lg p-4 mb-4';
    platformDiv.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-md font-medium text-gray-900">Platform ${platformCount}</h4>
            <button type="button" onclick="removePlatform(this)" 
                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                Remove
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Platform Name</label>
                <input type="text" name="platforms[${platformCount}][name]" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="e.g., windows-x86_64">
                <p class="text-xs text-gray-500 mt-1">Platform identifier</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Signature</label>
                <input type="text" name="platforms[${platformCount}][signature]" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="Base64 encoded signature">
                <p class="text-xs text-gray-500 mt-1">Update signature for verification</p>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Upload File</label>
            <input type="file" name="platforms[${platformCount}][file]" required
                   accept=".msi,.exe,.deb,.rpm,.dmg,.pkg"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <p class="text-xs text-gray-500 mt-1">Supported formats: MSI, EXE, DEB, RPM, DMG, PKG (max 100MB)</p>
        </div>
    `;
    
    container.appendChild(platformDiv);
}

function removePlatform(button) {
    button.closest('.border').remove();
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
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

document.getElementById('createUpdateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Creating...';
    
    fetch('{{ route("admin.updates.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => {
                window.location.href = '{{ route("admin.updates.index") }}';
            }, 1500);
        } else {
            showToast(data.errors ? Object.values(data.errors).flat().join(', ') : 'Error creating update', 'error');
        }
    })
    .catch(error => {
        showToast('Error creating update', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Create Update';
    });
});
</script>
@endsection

