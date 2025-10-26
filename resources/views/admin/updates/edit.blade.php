@extends('layouts.app')

@section('title', 'Edit Software Update - Livekenceng Admin')

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
            <h1 class="text-3xl font-bold text-gray-900">Edit Software Update</h1>
            <p class="text-gray-600 mt-2">Update: {{ $update->target }} v{{ $update->version }}</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <form id="editUpdateForm">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label for="target" class="block text-sm font-medium text-gray-700 mb-2">Target Application</label>
                        <input type="text" id="target" name="target" required value="{{ $update->target }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <div>
                        <label for="version" class="block text-sm font-medium text-gray-700 mb-2">Version</label>
                        <input type="text" id="version" name="version" required value="{{ $update->version }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <div class="mb-8">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Release Notes</label>
                    <textarea id="notes" name="notes" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ $update->notes }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label for="pub_date" class="block text-sm font-medium text-gray-700 mb-2">Release Date</label>
                        <input type="datetime-local" id="pub_date" name="pub_date" required 
                               value="{{ $update->pub_date->format('Y-m-d\TH:i') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="flex items-center">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ $update->is_active ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                Active (available for updates)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Current Platforms -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Current Platforms</h3>
                    @foreach($update->platforms as $platform => $data)
                    <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50" id="platform-{{ $loop->index }}">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="text-md font-medium text-gray-900">{{ $platform }}</h4>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500">Current</span>
                                <button type="button" onclick="deletePlatform('{{ $platform }}', {{ $loop->index }})" 
                                        class="text-red-600 hover:text-red-800 text-sm font-medium bg-red-50 hover:bg-red-100 px-3 py-1 rounded-lg transition-colors">
                                    Delete
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Signature</label>
                                <input type="text" value="{{ $data['signature'] }}" readonly
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Download URL</label>
                                <a href="{{ $data['url'] }}" target="_blank" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-blue-600 hover:text-blue-800 truncate block">
                                    {{ $data['url'] }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Add New Platforms -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Platforms</h3>
                    <div id="newPlatformsContainer">
                        <!-- New platform files will be added here -->
                    </div>
                    <button type="button" id="addNewPlatformBtn" 
                            class="mt-4 inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add New Platform
                    </button>
                    <p class="text-sm text-gray-500 mt-2">
                        <strong>Note:</strong> You can add new platforms to existing updates. Files will be stored with uniform naming: target-version.filetype
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.updates.index') }}" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                        Update Release
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
let newPlatformCount = 0;

function addNewPlatform() {
    newPlatformCount++;
    const container = document.getElementById('newPlatformsContainer');
    
    const platformDiv = document.createElement('div');
    platformDiv.className = 'border border-gray-200 rounded-lg p-4 mb-4';
    platformDiv.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-md font-medium text-gray-900">New Platform ${newPlatformCount}</h4>
            <button type="button" onclick="removeNewPlatform(this)" 
                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                Remove
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Platform Name</label>
                <input type="text" name="new_platforms[${newPlatformCount}][name]" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="e.g., macos, linux">
                <p class="text-xs text-gray-500 mt-1">Platform identifier</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Signature (Optional)</label>
                <input type="text" name="new_platforms[${newPlatformCount}][signature]"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="Base64 encoded signature (optional)">
                <p class="text-xs text-gray-500 mt-1">Update signature for verification (optional)</p>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Upload File</label>
            <input type="file" name="new_platforms[${newPlatformCount}][file]" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <p class="text-xs text-gray-500 mt-1">Any file type supported (max 100MB)</p>
        </div>
    `;
    
    container.appendChild(platformDiv);
}

function removeNewPlatform(button) {
    button.closest('.border').remove();
}

function deletePlatform(platformName, index) {
    if (confirm(`Are you sure you want to delete the "${platformName}" platform? This action cannot be undone.`)) {
        // Add to deletion list
        if (!window.platformsToDelete) {
            window.platformsToDelete = [];
        }
        window.platformsToDelete.push(platformName);
        
        // Hide the platform div
        const platformDiv = document.getElementById(`platform-${index}`);
        if (platformDiv) {
            platformDiv.style.opacity = '0.5';
            platformDiv.style.pointerEvents = 'none';
            
            // Add a "deleted" indicator
            const deleteButton = platformDiv.querySelector('button[onclick*="deletePlatform"]');
            if (deleteButton) {
                deleteButton.textContent = 'Deleted';
                deleteButton.disabled = true;
                deleteButton.classList.remove('text-red-600', 'hover:text-red-800', 'bg-red-50', 'hover:bg-red-100');
                deleteButton.classList.add('text-gray-500', 'bg-gray-200');
            }
        }
        
        showToast(`Platform "${platformName}" marked for deletion`, 'success');
    }
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

// Add event listener for add platform button
document.getElementById('addNewPlatformBtn').addEventListener('click', addNewPlatform);

document.getElementById('editUpdateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Add platforms to delete
    if (window.platformsToDelete && window.platformsToDelete.length > 0) {
        window.platformsToDelete.forEach((platform, index) => {
            formData.append(`platforms_to_delete[${index}]`, platform);
        });
    }
    
    const submitBtn = this.querySelector('button[type="submit"]');
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Updating...';
    
    fetch('{{ route("admin.updates.update", $update) }}', {
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
            showToast(data.errors ? Object.values(data.errors).flat().join(', ') : 'Error updating release', 'error');
        }
    })
    .catch(error => {
        showToast('Error updating release', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Update Release';
    });
});
</script>
@endsection

