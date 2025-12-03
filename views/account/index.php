<?php
/**
 * Account View
 */
?>
<main class="flex min-h-screen flex-col items-center p-4 sm:p-8 md:p-12 lg:p-16">
    <div class="w-full max-w-2xl">
        <!-- Header -->
        <div class="flex items-center space-x-4 mb-8">
            <a href="<?= BASE_URL ?>/" class="btn btn-ghost p-2">
                <i data-lucide="arrow-left" class="h-5 w-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold gradient-text">My Account</h1>
                <p class="text-sm text-gray-600"><?= e($user['email']) ?></p>
            </div>
        </div>
        
        <!-- Profile Card -->
        <div class="card mb-6">
            <h3 class="text-lg font-semibold mb-4">Profile</h3>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg text-center">
                    <p class="text-2xl font-bold text-primary-600"><?= $profile['credits'] ?></p>
                    <p class="text-sm text-gray-600">Credits</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg text-center">
                    <p class="text-2xl font-bold text-amber-600"><?= e($profile['role']) ?></p>
                    <p class="text-sm text-gray-600">Role</p>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-sm text-gray-600">
                    <strong>Current Language:</strong> <?= e(getLanguageName($profile['current_language'])) ?>
                </p>
                <p class="text-sm text-gray-600 mt-1">
                    <strong>Member since:</strong> <?= formatDate($user['created_at'], 'M d, Y') ?>
                </p>
            </div>
        </div>
        
        <!-- Stats Card -->
        <div class="card mb-6">
            <h3 class="text-lg font-semibold mb-4">Statistics</h3>
            
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-700"><?= $translationCount ?></p>
                    <p class="text-xs text-gray-500">Translations</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-700"><?= $whisperCount ?></p>
                    <p class="text-xs text-gray-500">Whispers</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-700"><?= $tipCount ?></p>
                    <p class="text-xs text-gray-500">Tips</p>
                </div>
            </div>
        </div>
        
        <!-- Language Progress -->
        <?php 
        $progress = $profile['language_progress'] ?? [];
        if (!empty($progress)): 
        ?>
        <div class="card mb-6">
            <h3 class="text-lg font-semibold mb-4">Language Progress</h3>
            
            <div class="space-y-3">
                <?php foreach ($progress as $lang => $data): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium"><?= e(getLanguageName($lang)) ?></p>
                        <p class="text-xs text-gray-500">
                            Last active: <?= formatDate($data['last_active'] ?? 'N/A', 'M d, Y') ?>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-bold text-primary-600"><?= $data['days_active'] ?? 0 ?></p>
                        <p class="text-xs text-gray-500">days</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Actions -->
        <div class="card">
            <h3 class="text-lg font-semibold mb-4">Actions</h3>
            
            <div class="space-y-3">
                <a href="<?= BASE_URL ?>/logout" class="btn btn-secondary w-full justify-center">
                    <i data-lucide="log-out" class="h-4 w-4 mr-2"></i>
                    Sign Out
                </a>
                
                <button 
                    onclick="showDeleteModal()"
                    class="btn btn-danger w-full justify-center"
                >
                    <i data-lucide="trash-2" class="h-4 w-4 mr-2"></i>
                    Delete Account
                </button>
            </div>
        </div>
    </div>
</main>

<!-- Delete Account Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 animate-fadeIn">
        <h3 class="text-xl font-semibold text-red-600 mb-4">Delete Account</h3>
        <p class="text-gray-600 mb-4">
            This action cannot be undone. All your data will be permanently deleted.
        </p>
        
        <form action="<?= BASE_URL ?>/account/delete" method="POST">
            <?= csrfField() ?>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Enter your password to confirm
                </label>
                <input 
                    type="password" 
                    name="password" 
                    class="input" 
                    placeholder="••••••••"
                    required
                >
            </div>
            
            <div class="flex space-x-3">
                <button type="button" onclick="hideDeleteModal()" class="btn btn-secondary flex-1">
                    Cancel
                </button>
                <button type="submit" class="btn btn-danger flex-1">
                    Delete Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showDeleteModal() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }
    
    function hideDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
    
    // Close modal on backdrop click
    document.getElementById('deleteModal').addEventListener('click', (e) => {
        if (e.target === e.currentTarget) hideDeleteModal();
    });
</script>
