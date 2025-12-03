<?php
/**
 * Whispers View
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
                    <h1 class="text-2xl font-bold gradient-text">Whispers</h1>
                    <p class="text-sm text-gray-600">Your situational phrase collections</p>
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
        
        <!-- Whispers List -->
        <?php if (empty($whispers)): ?>
        <div class="card text-center py-12">
            <i data-lucide="message-square" class="h-12 w-12 text-gray-300 mx-auto mb-4"></i>
            <h3 class="text-lg font-medium text-gray-700 mb-2">No whispers yet</h3>
            <p class="text-gray-500 mb-4">Generate situational phrases to prepare for conversations</p>
            <a href="<?= BASE_URL ?>/" class="btn btn-primary">
                Go to Whisperer
            </a>
        </div>
        <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($whispers as $whisper): ?>
            <div class="card" id="whisper-<?= $whisper['id'] ?>">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold"><?= e($whisper['title']) ?></h3>
                        <p class="text-sm text-gray-500">
                            <?= e(ucfirst($whisper['target_language'])) ?> • 
                            <?= $whisper['phrase_count'] ?> phrases •
                            <?= timeAgo($whisper['created_at']) ?>
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button 
                            onclick="toggleWhisper(<?= $whisper['id'] ?>)"
                            class="btn btn-secondary text-sm"
                        >
                            <i data-lucide="chevron-down" class="h-4 w-4" id="chevron-<?= $whisper['id'] ?>"></i>
                        </button>
                        <button 
                            onclick="deleteWhisper(<?= $whisper['id'] ?>)"
                            class="btn btn-ghost p-2 text-gray-400 hover:text-red-500"
                            title="Delete"
                        >
                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>
                
                <p class="text-gray-600 text-sm mb-4">
                    <span class="font-medium">Situation:</span> <?= e($whisper['situation_context']) ?>
                </p>
                
                <div id="phrases-<?= $whisper['id'] ?>" class="hidden space-y-3">
                    <?php foreach ($whisper['phrases'] as $phrase): ?>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="font-medium text-lg"><?= e($phrase['target_sentence']) ?></p>
                        <p class="text-gray-600 mt-1"><?= e($phrase['translation']) ?></p>
                        <p class="text-sm text-gray-500 mt-1 italic"><?= e($phrase['pronunciation']) ?></p>
                    </div>
                    <?php endforeach; ?>
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
    
    function toggleWhisper(id) {
        const phrases = document.getElementById(`phrases-${id}`);
        const chevron = document.getElementById(`chevron-${id}`);
        
        phrases.classList.toggle('hidden');
        chevron.style.transform = phrases.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
    }
    
    async function deleteWhisper(id) {
        if (!confirm('Delete this whisper collection?')) return;
        
        try {
            await api('<?= BASE_URL ?>/api/delete-whisper', { id });
            document.getElementById(`whisper-${id}`).remove();
            showToast('Whisper deleted');
        } catch (error) {
            showToast(error.message, 'error');
        }
    }
</script>
