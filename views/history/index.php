<?php
/**
 * History (Echoes) View
 */
?>
<main class="flex min-h-screen flex-col items-center p-4 sm:p-8 md:p-12 lg:p-16">
    <div class="w-full max-w-4xl">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <a href="<?= BASE_URL ?>/" class="btn btn-ghost p-2">
                    <i data-lucide="arrow-left" class="h-5 w-5"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold gradient-text">Echoes</h1>
                    <p class="text-sm text-gray-600">Your translation history</p>
                </div>
            </div>
            
            <!-- Language Filter -->
            <div class="relative">
                <select 
                    id="languageFilter" 
                    onchange="filterByLanguage(this.value)"
                    class="input pr-8 text-sm"
                >
                    <option value="">All Languages</option>
                    <?php foreach ($supportedLanguages as $code => $name): ?>
                    <option value="<?= e($code) ?>" <?= $filterLanguage === $code ? 'selected' : '' ?>>
                        <?= e($name) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="card text-center">
                <p class="text-3xl font-bold text-primary-600"><?= count($translations) ?></p>
                <p class="text-sm text-gray-600">Total Echoes</p>
            </div>
            <div class="card text-center">
                <p class="text-3xl font-bold text-amber-600">
                    <?= array_sum(array_column($translations, 'count')) ?>
                </p>
                <p class="text-sm text-gray-600">Total Uses</p>
            </div>
        </div>
        
        <!-- Translations List -->
        <?php if (empty($translations)): ?>
        <div class="card text-center py-12">
            <i data-lucide="inbox" class="h-12 w-12 text-gray-300 mx-auto mb-4"></i>
            <h3 class="text-lg font-medium text-gray-700 mb-2">No echoes yet</h3>
            <p class="text-gray-500 mb-4">Start translating to build your history</p>
            <a href="<?= BASE_URL ?>/" class="btn btn-primary">
                Go to Translator
            </a>
        </div>
        <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($translations as $translation): ?>
            <div class="card hover:shadow-md transition-shadow" id="translation-<?= $translation['id'] ?>">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="px-2 py-1 text-xs bg-gray-100 rounded-full">
                                <?= e(ucfirst($translation['source_language'])) ?> → <?= e(ucfirst($translation['target_language'])) ?>
                            </span>
                            <?php if ($translation['count'] > 1): ?>
                            <span class="px-2 py-1 text-xs bg-amber-100 text-amber-700 rounded-full">
                                ×<?= $translation['count'] ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <p class="text-gray-900 font-medium mb-1"><?= e($translation['original_text']) ?></p>
                        <p class="text-gray-600"><?= e($translation['translated_text']) ?></p>
                        
                        <p class="text-xs text-gray-400 mt-2">
                            <?= timeAgo($translation['updated_at']) ?>
                        </p>
                    </div>
                    
                    <button 
                        onclick="deleteTranslation(<?= $translation['id'] ?>)"
                        class="btn btn-ghost p-2 text-gray-400 hover:text-red-500"
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
            document.getElementById(`translation-${id}`).remove();
            showToast('Translation deleted');
        } catch (error) {
            showToast(error.message, 'error');
        }
    }
</script>
