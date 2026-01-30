<?php
/**
 * Admin Dashboard View
 */
?>
<main class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Admin Panel</h1>
            <p class="text-slate-500 mt-1">Oracle Control Center</p>
        </div>
        <a href="<?= BASE_URL ?>/" class="btn btn-secondary">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to App
        </a>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <div class="card !p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center">
                    <i data-lucide="users" class="w-5 h-5 text-primary-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800"><?= number_format($stats['total_users']) ?></p>
                    <p class="text-xs text-slate-500">Total Users</p>
                </div>
            </div>
        </div>
        
        <div class="card !p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-5 h-5 text-green-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800"><?= number_format($stats['new_today']) ?></p>
                    <p class="text-xs text-slate-500">New Today</p>
                </div>
            </div>
        </div>
        
        <div class="card !p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800"><?= number_format($stats['new_week']) ?></p>
                    <p class="text-xs text-slate-500">This Week</p>
                </div>
            </div>
        </div>
        
        <div class="card !p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                    <i data-lucide="coins" class="w-5 h-5 text-amber-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800"><?= number_format($stats['total_credits']) ?></p>
                    <p class="text-xs text-slate-500">Total Credits</p>
                </div>
            </div>
        </div>
        
        <div class="card !p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i data-lucide="crown" class="w-5 h-5 text-purple-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800"><?= $stats['by_role']['Oracle'] ?? 0 ?></p>
                    <p class="text-xs text-slate-500">Oracles</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Search and Users Table -->
    <div class="card">
        <div class="p-4 border-b border-slate-100">
            <form action="<?= BASE_URL ?>/admin" method="GET" class="flex gap-2">
                <div class="relative flex-1">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <input 
                        type="text" 
                        name="search" 
                        value="<?= e($search) ?>" 
                        placeholder="Search by email..."
                        class="input !pl-10 !py-2"
                    >
                </div>
                <button type="submit" class="btn btn-primary !py-2">Search</button>
                <?php if ($search): ?>
                    <a href="<?= BASE_URL ?>/admin" class="btn btn-secondary !py-2">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">ID</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Credits</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Language</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Joined</th>
                        <th class="text-right py-3 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">
                                No users found
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-4 text-sm text-slate-600">#<?= $user['id'] ?></td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-accent-400 flex items-center justify-center text-white text-sm font-medium">
                                            <?= strtoupper(substr($user['email'], 0, 1)) ?>
                                        </div>
                                        <span class="text-sm font-medium text-slate-700"><?= e($user['email']) ?></span>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <?php
                                    $roleColors = [
                                        'Whisper' => 'bg-slate-100 text-slate-600',
                                        'Voice' => 'bg-blue-100 text-blue-600',
                                        'Oracle' => 'bg-purple-100 text-purple-600'
                                    ];
                                    $roleColor = $roleColors[$user['role']] ?? 'bg-slate-100 text-slate-600';
                                    ?>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium <?= $roleColor ?>">
                                        <?= e($user['role']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-slate-700">
                                            <?php if ($user['role'] === ROLE_ORACLE): ?>
                                                ∞
                                            <?php else: ?>
                                                <?= number_format($user['credits']) ?>
                                            <?php endif; ?>
                                        </span>
                                        <button 
                                            onclick="quickAddCredits(<?= $user['id'] ?>)"
                                            class="p-1 hover:bg-green-100 rounded text-green-600 transition-colors"
                                            title="Add 500 credits"
                                        >
                                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-sm text-slate-600 capitalize"><?= e($user['current_language'] ?? '-') ?></td>
                                <td class="py-3 px-4 text-sm text-slate-500"><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                <td class="py-3 px-4 text-right">
                                    <a 
                                        href="<?= BASE_URL ?>/admin/user?id=<?= $user['id'] ?>" 
                                        class="inline-flex items-center gap-1 text-sm text-primary-600 hover:text-primary-700 font-medium"
                                    >
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                <p class="text-sm text-slate-500">
                    Showing <?= (($currentPage - 1) * 20) + 1 ?> - <?= min($currentPage * 20, $totalUsers) ?> of <?= $totalUsers ?> users
                </p>
                <div class="flex gap-1">
                    <?php if ($currentPage > 1): ?>
                        <a href="<?= BASE_URL ?>/admin?page=<?= $currentPage - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="px-3 py-1 rounded text-sm font-medium text-slate-600 hover:bg-slate-100">
                            Previous
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                        <a 
                            href="<?= BASE_URL ?>/admin?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                            class="px-3 py-1 rounded text-sm font-medium <?= $i === $currentPage ? 'bg-primary-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>"
                        >
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="<?= BASE_URL ?>/admin?page=<?= $currentPage + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="px-3 py-1 rounded text-sm font-medium text-slate-600 hover:bg-slate-100">
                            Next
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
    lucide.createIcons();
    
    async function quickAddCredits(userId) {
        const amount = prompt('Enter amount of credits to add:', '500');
        if (!amount || isNaN(amount) || parseInt(amount) <= 0) return;
        
        try {
            const response = await fetch('<?= BASE_URL ?>/admin/add-credits', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `user_id=${userId}&amount=${parseInt(amount)}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Failed to add credits');
            }
        } catch (error) {
            alert('Error adding credits');
        }
    }
</script>
