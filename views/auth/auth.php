<?php
/**
 * Combined Auth View (Login/Register)
 */
?>
<main class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Decorative elements -->
    <div class="absolute top-20 left-10 w-72 h-72 bg-primary-400/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-20 right-10 w-96 h-96 bg-accent-400/20 rounded-full blur-3xl"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-gradient-to-br from-primary-200/10 to-accent-200/10 rounded-full blur-3xl"></div>
    
    <div class="w-full max-w-md relative z-10 animate-slide-up">
        <!-- Logo -->
        <div class="text-center mb-10">
            <div class="inline-block mb-4">
                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-primary-500 to-accent-500 rounded-2xl flex items-center justify-center shadow-xl shadow-primary-500/25 rotate-3 hover:rotate-0 transition-transform duration-500">
                    <span class="text-4xl font-bold text-white">∞</span>
                </div>
            </div>
            <h1 class="text-5xl font-extrabold gradient-text mb-3">Gema∞</h1>
            <p class="text-slate-500 text-lg">Where words shift dimensions</p>
        </div>
        
        <!-- Auth Card -->
        <div class="card !p-8">
            <!-- Tabs -->
            <div class="flex gap-2 p-1.5 bg-slate-100 rounded-xl mb-8">
                <button 
                    id="loginTab" 
                    onclick="switchTab('login')"
                    class="flex-1 py-3 px-4 text-center font-semibold rounded-lg transition-all duration-300 bg-white shadow-sm text-primary-600"
                >
                    Sign In
                </button>
                <button 
                    id="registerTab" 
                    onclick="switchTab('register')"
                    class="flex-1 py-3 px-4 text-center font-semibold rounded-lg transition-all duration-300 text-slate-500 hover:text-slate-700"
                >
                    Sign Up
                </button>
            </div>
            
            <!-- Login Form -->
            <form id="loginForm" action="<?= BASE_URL ?>/auth/login" method="POST">
                <?= csrfField() ?>
                
                <div class="space-y-5">
                    <div>
                        <label for="login_email" class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <i data-lucide="mail" class="w-5 h-5"></i>
                            </span>
                            <input 
                                type="email" 
                                id="login_email" 
                                name="email" 
                                class="input !pl-12" 
                                placeholder="you@example.com"
                                value="<?= e(old('email')) ?>"
                                required
                            >
                        </div>
                    </div>
                    
                    <div>
                        <label for="login_password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <i data-lucide="lock" class="w-5 h-5"></i>
                            </span>
                            <input 
                                type="password" 
                                id="login_password" 
                                name="password" 
                                class="input !pl-12" 
                                placeholder="••••••••"
                                required
                            >
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-full !py-3.5 text-base font-semibold">
                        <i data-lucide="log-in" class="w-5 h-5 mr-2"></i>
                        Sign In
                    </button>
                </div>
                
                <div class="mt-6 text-center">
                    <a href="<?= BASE_URL ?>/auth/forgot-password" class="text-sm text-primary-600 hover:text-primary-700 font-medium hover:underline">
                        Forgot your password?
                    </a>
                </div>
            </form>
            
            <!-- Register Form -->
            <form id="registerForm" action="<?= BASE_URL ?>/auth/register" method="POST" class="hidden">
                <?= csrfField() ?>
                
                <div class="space-y-5">
                    <div>
                        <label for="register_email" class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <i data-lucide="mail" class="w-5 h-5"></i>
                            </span>
                            <input 
                                type="email" 
                                id="register_email" 
                                name="email" 
                                class="input !pl-12" 
                                placeholder="you@example.com"
                                required
                            >
                        </div>
                    </div>
                    
                    <div>
                        <label for="register_password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <i data-lucide="lock" class="w-5 h-5"></i>
                            </span>
                            <input 
                                type="password" 
                                id="register_password" 
                                name="password" 
                                class="input !pl-12" 
                                placeholder="At least 8 characters"
                                minlength="8"
                                required
                            >
                        </div>
                    </div>
                    
                    <div>
                        <label for="register_password_confirm" class="block text-sm font-semibold text-slate-700 mb-2">Confirm Password</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <i data-lucide="shield-check" class="w-5 h-5"></i>
                            </span>
                            <input 
                                type="password" 
                                id="register_password_confirm" 
                                name="password_confirm" 
                                class="input !pl-12" 
                                placeholder="••••••••"
                                required
                            >
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-full !py-3.5 text-base font-semibold">
                        <i data-lucide="user-plus" class="w-5 h-5 mr-2"></i>
                        Create Account
                    </button>
                </div>
                
                <div class="mt-6 p-4 bg-gradient-to-r from-primary-50 to-accent-50 rounded-xl border border-primary-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white">
                            <i data-lucide="gift" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-700">Welcome bonus!</p>
                            <p class="text-sm text-slate-500">Start with <strong class="text-primary-600">500 credits</strong> free</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Footer -->
        <p class="mt-8 text-center text-sm text-slate-400">
            Let your words echo infinitely ✨
        </p>
    </div>
</main>

<script>
    lucide.createIcons();
    
    function switchTab(tab) {
        const loginTab = document.getElementById('loginTab');
        const registerTab = document.getElementById('registerTab');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        
        if (tab === 'login') {
            loginTab.classList.add('bg-white', 'shadow-sm', 'text-primary-600');
            loginTab.classList.remove('text-slate-500');
            registerTab.classList.remove('bg-white', 'shadow-sm', 'text-primary-600');
            registerTab.classList.add('text-slate-500');
            loginForm.classList.remove('hidden');
            registerForm.classList.add('hidden');
        } else {
            registerTab.classList.add('bg-white', 'shadow-sm', 'text-primary-600');
            registerTab.classList.remove('text-slate-500');
            loginTab.classList.remove('bg-white', 'shadow-sm', 'text-primary-600');
            loginTab.classList.add('text-slate-500');
            registerForm.classList.remove('hidden');
            loginForm.classList.add('hidden');
        }
        lucide.createIcons();
    }
</script>
