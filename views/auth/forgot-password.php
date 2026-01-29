<?php
/**
 * Forgot Password View
 */
?>
<main class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <h1 class="text-5xl font-bold gradient-text mb-2">Gema∞</h1>
            <p class="text-gray-600">Reset your password</p>
        </div>
        
        <!-- Reset Card -->
        <div class="card">
            <div class="flex items-center space-x-3 mb-6">
                <a href="<?= BASE_URL ?>/auth" class="btn btn-ghost p-2">
                    <i data-lucide="arrow-left" class="h-5 w-5"></i>
                </a>
                <h2 class="text-xl font-semibold">Forgot Password</h2>
            </div>
            
            <p class="text-gray-600 mb-6">
                Enter your email address and we'll send you a link to reset your password.
            </p>
            
            <form action="<?= BASE_URL ?>/auth/forgot-password" method="POST">
                <?= csrfField() ?>
                
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="input" 
                            placeholder="you@example.com"
                            required
                        >
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-full py-3">
                        Send Reset Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
