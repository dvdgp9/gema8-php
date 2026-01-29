<?php
/**
 * History (Echoes) View
 */
?>
<main class="flex min-h-screen flex-col items-center p-4 sm:p-6 lg:p-8">
    <div class="w-full max-w-4xl">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="<?= BASE_URL ?>/" class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center hover:shadow-md transition-all">
                    <i data-lucide="arrow-left" class="h-5 w-5 text-slate-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold gradient-text flex items-center gap-2">
                        <i data-lucide="layers" class="h-6 w-6 text-primary-500"></i>
                        Echoes
                    </h1>
                    <p class="text-sm text-slate-500">Your translation history</p>
                </div>
            </div>
            
            <!-- Language Filter -->
            <div class="relative">
                <select 
                    id="languageFilter" 
                    onchange="filterByLanguage(this.value)"
                    class="input !pr-10 !py-2.5 text-sm appearance-none cursor-pointer"
                >
                    <option value="">All Languages</option>
                    <?php foreach ($supportedLanguages as $code => $name): ?>
                    <option value="<?= e($code) ?>" <?= $filterLanguage === $code ? 'selected' : '' ?>>
                        <?= e($name) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <i data-lucide="chevron-down" class="h-4 w-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="card !p-5 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                    <i data-lucide="message-square-text" class="h-6 w-6 text-white"></i>
                </div>
                <p class="text-3xl font-bold text-slate-800"><?= count($translations) ?></p>
                <p class="text-sm text-slate-500">Total Echoes</p>
            </div>
            <div class="card !p-5 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center">
                    <i data-lucide="repeat" class="h-6 w-6 text-white"></i>
                </div>
                <p class="text-3xl font-bold text-slate-800">
                    <?= array_sum(array_column($translations, 'count')) ?>
                </p>
                <p class="text-sm text-slate-500">Total Uses</p>
            </div>
        </div>
        
        <!-- Translations List -->
        <?php if (empty($translations)): ?>
        <div class="card text-center py-16">
            <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-slate-100 flex items-center justify-center">
                <i data-lucide="inbox" class="h-10 w-10 text-slate-300"></i>
            </div>
            <h3 class="text-xl font-semibold text-slate-700 mb-2">No echoes yet</h3>
            <p class="text-slate-500 mb-6">Start translating to build your history</p>
            <a href="<?= BASE_URL ?>/" class="btn btn-primary">
                <i data-lucide="zap" class="h-4 w-4 mr-2"></i>
                Go to Translator
            </a>
        </div>
        <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($translations as $index => $translation): ?>
            <div class="card !p-4 hover:!shadow-lg transition-all group animate-slide-up" id="translation-<?= $translation['id'] ?>" style="animation-delay: <?= $index * 0.05 ?>s">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <span class="badge badge-primary">
                                <?= e(ucfirst($translation['source_language'])) ?> → <?= e(ucfirst($translation['target_language'])) ?>
                            </span>
                            <?php if ($translation['count'] > 1): ?>
                            <span class="badge badge-accent">
                                <i data-lucide="repeat" class="h-3 w-3"></i>
                                ×<?= $translation['count'] ?>
                            </span>
                            <?php endif; ?>
                            <span class="text-xs text-slate-400"><?= timeAgo($translation['updated_at']) ?></span>
                        </div>
                        
                        <p class="text-slate-800 font-medium mb-2"><?= e(truncate($translation['original_text'], 150)) ?></p>
                        <p class="text-slate-600 text-sm"><?= e(truncate($translation['translated_text'], 150)) ?></p>
                    </div>
                    
                    <button 
                        onclick="deleteTranslation(<?= $translation['id'] ?>)"
                        class="w-9 h-9 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all opacity-0 group-hover:opacity-100"
                        title="Delete"
                    >
                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

<script>
    function filterByLanguage(language) {
        const url = new URL(window.location);
        if (language) {
            url.searchParams.set('language', language);
        } else {
            url.searchParams.delete('language');
        }
        window.location = url;
    }
    
    async function deleteTranslation(id) {
        if (!confirm('Delete this translation from your history?')) return;
        
        try {
            await api('<?= BASE_URL ?>/api/delete-translation', { id });
            const el = document.getElementById(`translation-${id}`);
            el.style.opacity = '0';
            el.style.transform = 'translateX(20px)';
            setTimeout(() => el.remove(), 300);
            showToast('Translation deleted');
        } catch (error) {
            showToast(error.message, 'error');
        }
    }
</script>
