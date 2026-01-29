<?php
/**
 * Header partial for authenticated pages
 */
?>
<header class="flex justify-between items-center w-full mb-6">
    <?php if ($user): ?>
    <a href="<?= BASE_URL ?>/account" class="hidden md:flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-white/50 transition-all group">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center text-white font-semibold text-sm">
            <?= strtoupper(substr($user['email'], 0, 1)) ?>
        </div>
        <span class="text-sm text-slate-600 group-hover:text-slate-900 transition-colors"><?= e($user['email']) ?></span>
    </a>
    <?php else: ?>
    <div></div>
    <?php endif; ?>
    
    <div class="flex items-center gap-2">
        <!-- Language Switcher -->
        <div class="relative" id="languageSwitcher">
            <button 
                onclick="toggleLanguageDropdown()" 
                class="btn btn-secondary flex items-center gap-2"
            >
                <i data-lucide="globe" class="h-4 w-4"></i>
                <span id="currentLanguageLabel" class="hidden sm:inline"><?= e(getLanguageName($profile['current_language'] ?? 'indonesian')) ?></span>
                <i data-lucide="chevron-down" class="h-4 w-4"></i>
            </button>
            
            <div 
                id="languageDropdown" 
                class="hidden absolute right-0 mt-2 w-56 bg-white/95 backdrop-blur-xl rounded-xl shadow-xl border border-slate-100 py-2 z-50 max-h-72 overflow-y-auto animate-fade-in"
            >
                <div class="px-3 py-2 border-b border-slate-100 mb-1">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Select Language</p>
                </div>
                <?php foreach ($supportedLanguages as $code => $name): ?>
                <button 
                    onclick="changeLanguage('<?= e($code) ?>')"
                    class="w-full text-left px-4 py-2.5 text-sm hover:bg-primary-50 transition-colors flex items-center justify-between <?= ($profile['current_language'] ?? '') === $code ? 'text-primary-600 font-semibold bg-primary-50' : 'text-slate-700' ?>"
                >
                    <?= e($name) ?>
                    <?php if (($profile['current_language'] ?? '') === $code): ?>
                    <i data-lucide="check" class="h-4 w-4 text-primary-600"></i>
                    <?php endif; ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Desktop Navigation -->
        <a href="<?= BASE_URL ?>/history" class="hidden md:inline-flex btn btn-secondary">
            <i data-lucide="layers" class="h-4 w-4 mr-2"></i>
            Echoes
        </a>
        
        <a href="<?= BASE_URL ?>/whispers" class="hidden md:inline-flex btn btn-secondary">
            <i data-lucide="sparkles" class="h-4 w-4 mr-2"></i>
            Whispers
        </a>
        
        <a href="<?= BASE_URL ?>/logout" class="hidden md:inline-flex btn btn-ghost">
            <i data-lucide="log-out" class="h-4 w-4"></i>
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
        if (switcher && !switcher.contains(e.target)) {
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
