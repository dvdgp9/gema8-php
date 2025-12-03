<?php
/**
 * Account View
 */
?>
<main class="flex min-h-screen flex-col items-center p-4 sm:p-6 lg:p-8">
    <div class="w-full max-w-2xl">
        <!-- Header -->
        <div class="flex items-center gap-4 mb-8">
            <a href="<?= BASE_URL ?>/" class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center hover:shadow-md transition-all">
                <i data-lucide="arrow-left" class="h-5 w-5 text-slate-600"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold gradient-text flex items-center gap-2">
                    <i data-lucide="user-circle" class="h-6 w-6 text-primary-500"></i>
                    My Account
                </h1>
                <p class="text-sm text-slate-500"><?= e($user['email']) ?></p>
            </div>
        </div>
        
        <!-- Profile Hero Card -->
        <div class="card !p-0 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-primary-500 via-primary-600 to-accent-500 p-6 text-center">
                <div class="w-20 h-20 mx-auto rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center mb-4 shadow-lg">
                    <span class="text-4xl font-bold text-white"><?= strtoupper(substr($user['email'], 0, 1)) ?></span>
                </div>
                <h2 class="text-white font-semibold text-lg"><?= e($user['email']) ?></h2>
                <div class="inline-flex items-center gap-2 mt-2 px-3 py-1 bg-white/20 backdrop-blur rounded-full">
                    <i data-lucide="shield" class="h-4 w-4 text-white/80"></i>
                    <span class="text-white/90 text-sm font-medium"><?= e($profile['role']) ?></span>
                </div>
            </div>
            
            <div class="grid grid-cols-2 divide-x divide-slate-100">
                <div class="p-5 text-center">
                    <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center">
                        <i data-lucide="zap" class="h-5 w-5 text-white"></i>
                    </div>
                    <p class="text-3xl font-bold text-slate-800"><?= $profile['credits'] ?></p>
                    <p class="text-sm text-slate-500">Credits</p>
                </div>
                <div class="p-5 text-center">
                    <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                        <i data-lucide="globe" class="h-5 w-5 text-white"></i>
                    </div>
                    <p class="text-xl font-bold text-slate-800"><?= e(getLanguageName($profile['current_language'])) ?></p>
                    <p class="text-sm text-slate-500">Current Language</p>
                </div>
            </div>
        </div>
        
        <!-- Stats Card -->
        <div class="card mb-6">
            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i data-lucide="bar-chart-3" class="h-5 w-5 text-primary-500"></i>
                Statistics
            </h3>
            
            <div class="grid grid-cols-3 gap-3">
                <div class="p-4 bg-gradient-to-br from-primary-50 to-slate-50 rounded-xl text-center">
                    <p class="text-2xl font-bold text-primary-600"><?= $translationCount ?></p>
                    <p class="text-xs text-slate-500 font-medium">Translations</p>
                </div>
                <div class="p-4 bg-gradient-to-br from-emerald-50 to-slate-50 rounded-xl text-center">
                    <p class="text-2xl font-bold text-emerald-600"><?= $whisperCount ?></p>
                    <p class="text-xs text-slate-500 font-medium">Whispers</p>
                </div>
                <div class="p-4 bg-gradient-to-br from-amber-50 to-slate-50 rounded-xl text-center">
                    <p class="text-2xl font-bold text-amber-600"><?= $tipCount ?></p>
                    <p class="text-xs text-slate-500 font-medium">Tips</p>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-2 text-sm text-slate-500">
                <i data-lucide="calendar" class="h-4 w-4"></i>
                Member since <?= formatDate($user['created_at'], 'M d, Y') ?>
            </div>
        </div>
        
        <!-- Language Progress -->
        <?php 
        $progress = $profile['language_progress'] ?? [];
        if (!empty($progress)): 
        ?>
        <div class="card mb-6">
            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i data-lucide="trending-up" class="h-5 w-5 text-emerald-500"></i>
                Language Progress
            </h3>
            
            <div class="space-y-3">
                <?php foreach ($progress as $lang => $data): ?>
                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-slate-50 to-white rounded-xl border border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white font-bold text-sm">
                            <?= strtoupper(substr($lang, 0, 2)) ?>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800"><?= e(getLanguageName($lang)) ?></p>
                            <p class="text-xs text-slate-400">
                                Last active: <?= formatDate($data['last_active'] ?? 'N/A', 'M d, Y') ?>
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-primary-600"><?= $data['days_active'] ?? 0 ?></p>
                        <p class="text-xs text-slate-400">days active</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Actions -->
        <div class="card">
            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i data-lucide="settings" class="h-5 w-5 text-slate-500"></i>
                Actions
            </h3>
            
            <div class="space-y-3">
                <a href="<?= BASE_URL ?>/logout" class="btn btn-secondary w-full justify-center !py-3">
                    <i data-lucide="log-out" class="h-4 w-4 mr-2"></i>
                    Sign Out
                </a>
                
                <button 
                    onclick="showDeleteModal()"
                    class="btn w-full justify-center !py-3 bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                >
                    <i data-lucide="trash-2" class="h-4 w-4 mr-2"></i>
                    Delete Account
                </button>
            </div>
        </div>
    </div>
</main>

<!-- Delete Account Modal -->
<div id="deleteModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl animate-slide-up">
        <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-red-100 flex items-center justify-center">
            <i data-lucide="alert-triangle" class="h-7 w-7 text-red-600"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-800 text-center mb-2">Delete Account</h3>
        <p class="text-slate-500 text-center mb-6">
            This action cannot be undone. All your data will be permanently deleted.
        </p>
        
        <form action="<?= BASE_URL ?>/account/delete" method="POST">
            <?= csrfField() ?>
            
            <div class="mb-5">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Enter your password to confirm
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <i data-lucide="lock" class="w-5 h-5"></i>
                    </span>
                    <input 
                        type="password" 
                        name="password" 
                        class="input !pl-12" 
                        placeholder="••••••••"
                        required
                    >
                </div>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="hideDeleteModal()" class="btn btn-secondary flex-1 !py-3">
                    Cancel
                </button>
                <button type="submit" class="btn btn-danger flex-1 !py-3">
                    Delete Forever
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();
    
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
