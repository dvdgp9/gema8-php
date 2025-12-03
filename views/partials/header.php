<?php
/**
 * Header partial for authenticated pages
 */
?>
<header class="flex justify-between items-center w-full mb-6 sm:mb-8">
    <?php if ($user): ?>
    <a href="<?= BASE_URL ?>/account" class="hidden md:flex items-center space-x-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
        <i data-lucide="user" class="h-4 w-4"></i>
        <span><?= e($user['email']) ?></span>
    </a>
    <?php endif; ?>
    
    <div class="flex items-center space-x-3">
        <!-- Language Switcher -->
        <div class="relative" id="languageSwitcher">
            <button 
                onclick="toggleLanguageDropdown()" 
                class="btn btn-secondary text-sm flex items-center space-x-2"
            >
                <i data-lucide="globe" class="h-4 w-4"></i>
                <span id="currentLanguageLabel"><?= e(getLanguageName($profile['current_language'] ?? 'indonesian')) ?></span>
                <i data-lucide="chevron-down" class="h-4 w-4"></i>
            </button>
            
            <div 
                id="languageDropdown" 
                class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-2 z-50 max-h-64 overflow-y-auto"
            >
                <?php foreach ($supportedLanguages as $code => $name): ?>
                <button 
                    onclick="changeLanguage('<?= e($code) ?>')"
                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 transition-colors <?= ($profile['current_language'] ?? '') === $code ? 'text-primary-600 font-medium' : 'text-gray-700' ?>"
                >
                    <?= e($name) ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Desktop Navigation -->
        <a href="<?= BASE_URL ?>/history" class="hidden md:inline-flex btn btn-secondary text-sm">
            <i data-lucide="history" class="h-4 w-4 mr-2"></i>
            Echoes
        </a>
        
        <a href="<?= BASE_URL ?>/whispers" class="hidden md:inline-flex btn btn-secondary text-sm">
            <i data-lucide="message-square" class="h-4 w-4 mr-2"></i>
            Whispers
        </a>
        
        <a href="<?= BASE_URL ?>/logout" class="hidden md:inline-flex btn btn-ghost text-sm">
            <i data-lucide="log-out" class="h-4 w-4 mr-2"></i>
            Logout
        </a>
    </div>
</header>

<script>
    function toggleLanguageDropdown() {
        const dropdown = document.getElementById('languageDropdown');
        dropdown.classList.toggle('hidden');
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        const switcher = document.getElementById('languageSwitcher');
        if (!switcher.contains(e.target)) {
            document.getElementById('languageDropdown').classList.add('hidden');
        }
    });
    
    async function changeLanguage(code) {
        try {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= BASE_URL ?>/account/update-language';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            const langInput = document.createElement('input');
            langInput.type = 'hidden';
            langInput.name = 'language';
            langInput.value = code;
            form.appendChild(langInput);
            
            document.body.appendChild(form);
            form.submit();
        } catch (error) {
            showToast('Failed to change language', 'error');
        }
    }
</script>
