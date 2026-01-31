<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#6366f1">
    <meta name="description" content="Where words shift dimensions. Learn languages with AI-powered translations.">
    
    <!-- PWA Meta Tags -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Gema∞">
    <meta name="application-name" content="Gema∞">
    <meta name="msapplication-TileColor" content="#6366f1">
    <meta name="msapplication-tap-highlight" content="no">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">
    
    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="<?= BASE_URL ?>/assets/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= BASE_URL ?>/assets/icons/icon-192x192.png">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?>/assets/icons/icon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= BASE_URL ?>/assets/icons/icon-72x72.png">
    
    <title><?= e($title ?? 'Gema∞') ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        },
                        accent: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'fade-in': 'fadeIn 0.4s ease-out',
                    }
                }
            }
        }
    </script>
    
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        * { font-family: 'Inter', system-ui, sans-serif; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c7d2fe; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #a5b4fc; }
        
        /* Animated gradient background */
        .bg-mesh {
            background: 
                radial-gradient(at 40% 20%, rgba(99, 102, 241, 0.08) 0px, transparent 50%),
                radial-gradient(at 80% 0%, rgba(249, 115, 22, 0.06) 0px, transparent 50%),
                radial-gradient(at 0% 50%, rgba(99, 102, 241, 0.05) 0px, transparent 50%),
                radial-gradient(at 80% 50%, rgba(249, 115, 22, 0.04) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(99, 102, 241, 0.06) 0px, transparent 50%),
                linear-gradient(180deg, #fafbff 0%, #f8fafc 100%);
        }
        
        /* Logo gradient */
        .gradient-text {
            background: linear-gradient(135deg, #f97316 0%, #ec4899 50%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Glass morphism card */
        .card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 1.25rem;
            padding: 1.5rem;
            box-shadow: 
                0 4px 6px -1px rgba(0, 0, 0, 0.05),
                0 2px 4px -2px rgba(0, 0, 0, 0.05),
                0 0 0 1px rgba(99, 102, 241, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card:hover {
            box-shadow: 
                0 20px 25px -5px rgba(0, 0, 0, 0.08),
                0 8px 10px -6px rgba(0, 0, 0, 0.05),
                0 0 0 1px rgba(99, 102, 241, 0.1);
            transform: translateY(-2px);
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.625rem 1.25rem;
            border-radius: 0.75rem;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
            outline: none;
        }
        
        .btn:focus-visible {
            outline: 2px solid #6366f1;
            outline-offset: 2px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.35);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            box-shadow: 0 6px 20px 0 rgba(99, 102, 241, 0.45);
            transform: translateY(-1px);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .btn-secondary {
            background: rgba(99, 102, 241, 0.08);
            color: #4f46e5;
        }
        
        .btn-secondary:hover {
            background: rgba(99, 102, 241, 0.15);
        }
        
        .btn-ghost {
            background: transparent;
            color: #64748b;
        }
        
        .btn-ghost:hover {
            background: rgba(99, 102, 241, 0.08);
            color: #4f46e5;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 4px 14px 0 rgba(239, 68, 68, 0.35);
        }
        
        .btn-danger:hover {
            box-shadow: 0 6px 20px 0 rgba(239, 68, 68, 0.45);
        }
        
        /* Inputs */
        .input {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            border: 2px solid #e2e8f0;
            background: white;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
            outline: none;
        }
        
        .input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }
        
        .input::placeholder {
            color: #94a3b8;
        }
        
        /* Toast notifications */
        .toast {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 100;
            padding: 1rem 1.5rem;
            border-radius: 1rem;
            font-weight: 500;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            transform: translateX(0);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .toast-success { 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        .toast-error { 
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        .toast-info { 
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
        }
        
        /* Spinner */
        .spinner {
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid currentColor;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes glow {
            from { filter: drop-shadow(0 0 20px rgba(99, 102, 241, 0.3)); }
            to { filter: drop-shadow(0 0 30px rgba(249, 115, 22, 0.3)); }
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .animate-slide-up {
            animation: slideUp 0.5s ease-out forwards;
        }
        
        .animate-fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }
        
        /* Mobile nav */
        .mobile-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid rgba(99, 102, 241, 0.1);
            padding: 0.5rem 1rem;
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 40;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
        }
        
        @media (min-width: 768px) {
            .mobile-nav { display: none; }
        }
        
        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.625rem;
            font-weight: 500;
            color: #64748b;
            transition: all 0.2s ease;
        }
        
        .mobile-nav-item:hover,
        .mobile-nav-item.active {
            color: #4f46e5;
            background: rgba(99, 102, 241, 0.08);
        }
        
        .mobile-nav-item.active {
            color: #4f46e5;
        }
        
        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-primary {
            background: rgba(99, 102, 241, 0.1);
            color: #4f46e5;
        }
        
        .badge-accent {
            background: rgba(249, 115, 22, 0.1);
            color: #ea580c;
        }
        
        /* Stagger children animation */
        .stagger-children > * {
            opacity: 0;
            animation: slideUp 0.5s ease-out forwards;
        }
        
        .stagger-children > *:nth-child(1) { animation-delay: 0.1s; }
        .stagger-children > *:nth-child(2) { animation-delay: 0.2s; }
        .stagger-children > *:nth-child(3) { animation-delay: 0.3s; }
        .stagger-children > *:nth-child(4) { animation-delay: 0.4s; }
        .stagger-children > *:nth-child(5) { animation-delay: 0.5s; }
    </style>
</head>
<body class="bg-mesh min-h-screen antialiased">
    <?php require ROOT_PATH . '/views/partials/toast.php'; ?>
    
    <div class="pb-24 md:pb-0">
        <?= $content ?>
    </div>
    
    <?php if (isLoggedIn()): ?>
    <!-- Mobile Navigation -->
    <nav class="mobile-nav">
        <a href="<?= BASE_URL ?>/" class="mobile-nav-item <?= ($uri ?? '') === '/' ? 'active' : '' ?>">
            <i data-lucide="home" class="w-5 h-5"></i>
            <span>Home</span>
        </a>
        <a href="<?= BASE_URL ?>/history" class="mobile-nav-item <?= strpos($uri ?? '', '/history') === 0 ? 'active' : '' ?>">
            <i data-lucide="layers" class="w-5 h-5"></i>
            <span>Echoes</span>
        </a>
        <a href="<?= BASE_URL ?>/whispers" class="mobile-nav-item <?= strpos($uri ?? '', '/whispers') === 0 ? 'active' : '' ?>">
            <i data-lucide="sparkles" class="w-5 h-5"></i>
            <span>Whispers</span>
        </a>
        <a href="<?= BASE_URL ?>/account" class="mobile-nav-item <?= strpos($uri ?? '', '/account') === 0 ? 'active' : '' ?>">
            <i data-lucide="user-circle" class="w-5 h-5"></i>
            <span>Account</span>
        </a>
    </nav>
    <?php endif; ?>
    
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Global CSRF token
        const csrfToken = '<?= csrfToken() ?>';
        
        // Global BASE_URL for TTS module
        window.BASE_URL = '<?= BASE_URL ?>';
        
        // Toast auto-hide
        document.querySelectorAll('.toast').forEach(toast => {
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(120%)';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
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
        
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= BASE_URL ?>/sw.js')
                    .then(reg => console.log('SW registered:', reg.scope))
                    .catch(err => console.log('SW registration failed:', err));
            });
        }
        
        // Show toast
        function showToast(message, type = 'success') {
            const icons = {
                success: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
                error: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
                info: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            };
            
            const toast = document.createElement('div');
            toast.className = `toast toast-${type} animate-fade-in`;
            toast.innerHTML = `${icons[type] || ''}${message}`;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(120%)';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }
    </script>
    
    <!-- TTS Module -->
    <script src="<?= BASE_URL ?>/js/tts.js"></script>
</body>
</html>
