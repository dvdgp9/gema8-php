<?php
/**
 * Combined Auth View (Login/Register)
 */
?>
<main class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <h1 class="text-5xl font-bold gradient-text mb-2">Gema∞</h1>
            <p class="text-gray-600">Where words shift dimensions and return brighter</p>
        </div>
        
        <!-- Auth Card -->
        <div class="card">
            <!-- Tabs -->
            <div class="flex border-b border-gray-200 mb-6">
                <button 
                    id="loginTab" 
                    onclick="switchTab('login')"
                    class="flex-1 py-3 text-center font-medium border-b-2 border-primary-600 text-primary-600"
                >
                    Sign In
                </button>
                <button 
                    id="registerTab" 
                    onclick="switchTab('register')"
                    class="flex-1 py-3 text-center font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700"
                >
                    Sign Up
                </button>
            </div>
            
            <!-- Login Form -->
            <form id="loginForm" action="<?= BASE_URL ?>/auth/login" method="POST">
                <?= csrfField() ?>
                
                <div class="space-y-4">
                    <div>
                        <label for="login_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input 
                            type="email" 
                            id="login_email" 
                            name="email" 
                            class="input" 
                            placeholder="you@example.com"
                            value="<?= e(old('email')) ?>"
                            required
                        >
                    </div>
                    
                    <div>
                        <label for="login_password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input 
                            type="password" 
                            id="login_password" 
                            name="password" 
                            class="input" 
                            placeholder="••••••••"
                            required
                        >
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-full py-3">
                        Sign In
                    </button>
                </div>
                
                <div class="mt-4 text-center">
                    <a href="<?= BASE_URL ?>/auth/forgot-password" class="text-sm text-primary-600 hover:text-primary-700">
                        Forgot your password?
                    </a>
                </div>
            </form>
            
            <!-- Register Form -->
            <form id="registerForm" action="<?= BASE_URL ?>/auth/register" method="POST" class="hidden">
                <?= csrfField() ?>
                
                <div class="space-y-4">
                    <div>
                        <label for="register_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input 
                            type="email" 
                            id="register_email" 
                            name="email" 
                            class="input" 
                            placeholder="you@example.com"
                            required
                        >
                    </div>
                    
                    <div>
                        <label for="register_password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input 
                            type="password" 
                            id="register_password" 
                            name="password" 
                            class="input" 
                            placeholder="At least 8 characters"
                            minlength="8"
                            required
                        >
                    </div>
                    
                    <div>
                        <label for="register_password_confirm" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input 
                            type="password" 
                            id="register_password_confirm" 
                            name="password_confirm" 
                            class="input" 
                            placeholder="••••••••"
                            required
                        >
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-full py-3">
                        Create Account
                    </button>
                </div>
                
                <p class="mt-4 text-center text-sm text-gray-600">
                    You'll start with <strong>500 credits</strong> to explore languages!
                </p>
            </form>
        </div>
        
        <!-- Footer -->
        <p class="mt-8 text-center text-sm text-gray-500">
            Let your words echo infinitely
        </p>
    </div>
</main>

<script>
    function switchTab(tab) {
        const loginTab = document.getElementById('loginTab');
        const registerTab = document.getElementById('registerTab');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        
        if (tab === 'login') {
            loginTab.classList.add('border-primary-600', 'text-primary-600');
            loginTab.classList.remove('border-transparent', 'text-gray-500');
            registerTab.classList.remove('border-primary-600', 'text-primary-600');
            registerTab.classList.add('border-transparent', 'text-gray-500');
            loginForm.classList.remove('hidden');
            registerForm.classList.add('hidden');
        } else {
            registerTab.classList.add('border-primary-600', 'text-primary-600');
            registerTab.classList.remove('border-transparent', 'text-gray-500');
            loginTab.classList.remove('border-primary-600', 'text-primary-600');
            loginTab.classList.add('border-transparent', 'text-gray-500');
            registerForm.classList.remove('hidden');
            loginForm.classList.add('hidden');
        }
    }
</script>
