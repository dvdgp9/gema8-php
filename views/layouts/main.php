<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Gema∞') ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        .gradient-text {
            background: linear-gradient(90deg, #fdbb2d, #b21f1f, #1a2a6c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .card {
            @apply bg-white rounded-xl shadow-sm border border-gray-100 p-6;
        }
        
        .btn {
            @apply px-4 py-2 rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2;
        }
        
        .btn-primary {
            @apply bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500;
        }
        
        .btn-secondary {
            @apply bg-gray-100 text-gray-700 hover:bg-gray-200 focus:ring-gray-300;
        }
        
        .btn-ghost {
            @apply text-gray-600 hover:bg-gray-100 focus:ring-gray-300;
        }
        
        .btn-danger {
            @apply bg-red-600 text-white hover:bg-red-700 focus:ring-red-500;
        }
        
        .input {
            @apply w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all duration-200 outline-none;
        }
        
        .toast {
            @apply fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300;
        }
        
        .toast-success { @apply bg-green-500 text-white; }
        .toast-error { @apply bg-red-500 text-white; }
        .toast-info { @apply bg-blue-500 text-white; }
        
        .spinner {
            @apply animate-spin h-5 w-5 border-2 border-current border-t-transparent rounded-full;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
        
        /* Mobile nav */
        .mobile-nav {
            @apply fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 py-2 flex justify-around items-center md:hidden z-40;
        }
        
        .mobile-nav-item {
            @apply flex flex-col items-center text-xs text-gray-500 hover:text-primary-600 transition-colors p-2;
        }
        
        .mobile-nav-item.active {
            @apply text-primary-600;
        }
    </style>
</head>
<body class="bg-gradient-to-b from-blue-50 via-white to-blue-50 min-h-screen">
    <?php require ROOT_PATH . '/views/partials/toast.php'; ?>
    
    <div class="pb-20 md:pb-0">
        <?= $content ?>
    </div>
    
    <?php if (isLoggedIn()): ?>
    <!-- Mobile Navigation -->
    <nav class="mobile-nav">
        <a href="<?= BASE_URL ?>/" class="mobile-nav-item <?= $uri === '/' ? 'active' : '' ?>">
            <i data-lucide="home" class="w-5 h-5"></i>
            <span>Home</span>
        </a>
        <a href="<?= BASE_URL ?>/history" class="mobile-nav-item <?= strpos($uri ?? '', '/history') === 0 ? 'active' : '' ?>">
            <i data-lucide="history" class="w-5 h-5"></i>
            <span>Echoes</span>
        </a>
        <a href="<?= BASE_URL ?>/whispers" class="mobile-nav-item <?= strpos($uri ?? '', '/whispers') === 0 ? 'active' : '' ?>">
            <i data-lucide="message-square" class="w-5 h-5"></i>
            <span>Whispers</span>
        </a>
        <a href="<?= BASE_URL ?>/account" class="mobile-nav-item <?= strpos($uri ?? '', '/account') === 0 ? 'active' : '' ?>">
            <i data-lucide="user" class="w-5 h-5"></i>
            <span>Account</span>
        </a>
    </nav>
    <?php endif; ?>
    
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Global CSRF token
        const csrfToken = '<?= csrfToken() ?>';
        
        // Toast auto-hide
        document.querySelectorAll('.toast').forEach(toast => {
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        });
        
        // API helper
        async function api(endpoint, data = {}) {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({...data, csrf_token: csrfToken})
            });
            
            const result = await response.json();
            
            if (!response.ok) {
                throw new Error(result.error || 'Request failed');
            }
            
            return result;
        }
        
        // Show toast
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast toast-${type} animate-fadeIn`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }
    </script>
</body>
</html>
