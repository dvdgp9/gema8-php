<?php
/**
 * Reset Password View
 */
?>
<main class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <h1 class="text-5xl font-bold gradient-text mb-2">Gema∞</h1>
            <p class="text-gray-600">Create a new password</p>
        </div>
        
        <!-- Reset Card -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-6">Reset Password</h2>
            
            <form action="<?= BASE_URL ?>/auth/reset-password" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="token" value="<?= e($token) ?>">
                
                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="input" 
                            placeholder="At least 8 characters"
                            minlength="8"
                            required
                        >
                    </div>
                    
                    <div>
                        <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="password_confirm" 
                            class="input" 
                            placeholder="••••••••"
                            required
                        >
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-full py-3">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
