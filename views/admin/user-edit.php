<?php
/**
 * Admin User Edit View
 */
?>
<main class="max-w-3xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <a href="<?= BASE_URL ?>/admin" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5 text-slate-600"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit User</h1>
            <p class="text-slate-500"><?= e($user['email']) ?></p>
        </div>
    </div>
    
    <div class="grid gap-6">
        <!-- User Info Card -->
        <div class="card !p-6">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">User Information</h2>
            
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-slate-500">User ID</p>
                    <p class="font-medium text-slate-700">#<?= $user['id'] ?></p>
                </div>
                <div>
                    <p class="text-slate-500">Email</p>
                    <p class="font-medium text-slate-700"><?= e($user['email']) ?></p>
                </div>
                <div>
                    <p class="text-slate-500">Joined</p>
                    <p class="font-medium text-slate-700"><?= date('F j, Y \a\t g:i A', strtotime($user['created_at'])) ?></p>
                </div>
                <div>
                    <p class="text-slate-500">Current Language</p>
                    <p class="font-medium text-slate-700 capitalize"><?= e($user['current_language'] ?? 'Not set') ?></p>
                </div>
            </div>
            
            <?php if (!empty($user['language_progress'])): ?>
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-sm text-slate-500 mb-2">Language Progress</p>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($user['language_progress'] as $lang => $progress): ?>
                            <span class="px-2 py-1 bg-slate-100 rounded text-xs text-slate-600 capitalize">
                                <?= e($lang) ?>: <?= $progress['days_active'] ?> days
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Edit Form -->
        <form action="<?= BASE_URL ?>/admin/user/update" method="POST" class="card !p-6">
            <?= csrfField() ?>
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
            
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Edit Settings</h2>
            
            <div class="grid gap-5">
                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-slate-700 mb-2">Role</label>
                    <select name="role" id="role" class="input">
                        <option value="<?= ROLE_WHISPER ?>" <?= $user['role'] === ROLE_WHISPER ? 'selected' : '' ?>>
                            Whisper (Basic)
                        </option>
                        <option value="<?= ROLE_VOICE ?>" <?= $user['role'] === ROLE_VOICE ? 'selected' : '' ?>>
                            Voice (Premium)
                        </option>
                        <option value="<?= ROLE_ORACLE ?>" <?= $user['role'] === ROLE_ORACLE ? 'selected' : '' ?>>
                            Oracle (Admin)
                        </option>
                    </select>
                    <p class="mt-1 text-xs text-slate-500">
                        Oracle = Unlimited credits + Admin access
                    </p>
                </div>
                
                <!-- Credits -->
                <div>
                    <label for="credits" class="block text-sm font-medium text-slate-700 mb-2">Credits</label>
                    <div class="flex gap-2">
                        <input 
                            type="number" 
                            name="credits" 
                            id="credits" 
                            value="<?= $user['credits'] ?>" 
                            min="0"
                            class="input flex-1"
                        >
                        <button type="button" onclick="document.getElementById('credits').value = parseInt(document.getElementById('credits').value || 0) + 500" class="btn btn-secondary">
                            +500
                        </button>
                        <button type="button" onclick="document.getElementById('credits').value = parseInt(document.getElementById('credits').value || 0) + 1000" class="btn btn-secondary">
                            +1000
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">
                        Current balance: <?= number_format($user['credits']) ?> credits
                    </p>
                </div>
                
                <!-- Submit -->
                <div class="flex gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Save Changes
                    </button>
                    <a href="<?= BASE_URL ?>/admin" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
        
        <!-- Danger Zone -->
        <?php if ($user['id'] !== userId()): ?>
            <div class="card !p-6 border-red-200 bg-red-50">
                <h2 class="text-lg font-semibold text-red-800 mb-2">Danger Zone</h2>
                <p class="text-sm text-red-600 mb-4">
                    Deleting a user is permanent and cannot be undone. All their data will be lost.
                </p>
                <form action="<?= BASE_URL ?>/admin/user/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                    <?= csrfField() ?>
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <button type="submit" class="btn bg-red-600 hover:bg-red-700 text-white">
                        <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                        Delete User
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
    lucide.createIcons();
</script>
