<?php
/**
 * Whispers View
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
                        <i data-lucide="sparkles" class="h-6 w-6 text-emerald-500"></i>
                        Whispers
                    </h1>
                    <p class="text-sm text-slate-500">Your situational phrase collections</p>
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
        
        <!-- Whispers List -->
        <?php if (empty($whispers)): ?>
        <div class="card text-center py-16">
            <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-emerald-50 flex items-center justify-center">
                <i data-lucide="mic" class="h-10 w-10 text-emerald-300"></i>
            </div>
            <h3 class="text-xl font-semibold text-slate-700 mb-2">No whispers yet</h3>
            <p class="text-slate-500 mb-6">Generate situational phrases to prepare for conversations</p>
            <a href="<?= BASE_URL ?>/" class="btn btn-primary" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i data-lucide="wand-2" class="h-4 w-4 mr-2"></i>
                Go to Whisperer
            </a>
        </div>
        <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($whispers as $index => $whisper): ?>
            <div class="card !p-0 overflow-hidden animate-slide-up" id="whisper-<?= $whisper['id'] ?>" style="animation-delay: <?= $index * 0.05 ?>s">
                <!-- Header -->
                <div class="p-4 cursor-pointer hover:bg-slate-50/50 transition-colors" onclick="toggleWhisper(<?= $whisper['id'] ?>)">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="message-circle" class="h-5 w-5 text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-800"><?= e($whisper['title']) ?></h3>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <span class="badge badge-primary text-xs"><?= e(ucfirst($whisper['target_language'])) ?></span>
                                        <span class="text-xs text-slate-400"><?= $whisper['phrase_count'] ?> phrases</span>
                                        <span class="text-xs text-slate-400">•</span>
                                        <span class="text-xs text-slate-400"><?= timeAgo($whisper['created_at']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-slate-600 text-sm pl-13 ml-13">
                                <?= e(truncate($whisper['situation_context'], 100)) ?>
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center transition-transform" id="chevron-<?= $whisper['id'] ?>">
                                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-500"></i>
                            </div>
                            <button 
                                onclick="event.stopPropagation(); deleteWhisper(<?= $whisper['id'] ?>)"
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all"
                                title="Delete"
                            >
                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Phrases (collapsible) -->
                <div id="phrases-<?= $whisper['id'] ?>" class="hidden border-t border-slate-100">
                    <div class="p-4 grid gap-3">
                        <?php foreach ($whisper['phrases'] as $i => $phrase): ?>
                        <div class="p-4 bg-gradient-to-br from-emerald-50 to-slate-50 rounded-xl border border-emerald-100 hover:shadow-sm transition-all relative group" data-phrase="<?= e($phrase['target_sentence']) ?>" data-lang="<?= e($whisper['target_language']) ?>">
                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold flex items-center justify-center flex-shrink-0"><?= $i + 1 ?></span>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-slate-800 text-lg"><?= e($phrase['target_sentence']) ?></p>
                                    <p class="text-slate-600 mt-1"><?= e($phrase['translation']) ?></p>
                                    <p class="text-sm text-emerald-600 mt-2 flex items-center gap-1">
                                        <i data-lucide="volume-2" class="h-3 w-3"></i>
                                        <?= e($phrase['pronunciation']) ?>
                                    </p>
                                </div>
                                <button 
                                    onclick="event.stopPropagation(); speakWhisperPhrase(this)"
                                    class="p-2 rounded-lg bg-white border border-emerald-200 text-emerald-600 hover:bg-emerald-100 hover:border-emerald-300 transition-all flex-shrink-0"
                                    title="Listen"
                                >
                                    <i data-lucide="volume-2" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

<script src="<?= BASE_URL ?>/js/tts.js"></script>
<script>
    // Set BASE_URL for TTS module
    window.BASE_URL = '<?= BASE_URL ?>';
    
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
            const el = document.getElementById(`whisper-${id}`);
            el.style.opacity = '0';
            el.style.transform = 'scale(0.95)';
            setTimeout(() => el.remove(), 300);
            showToast('Whisper deleted');
        } catch (error) {
            showToast(error.message, 'error');
        }
    }
    
    function speakWhisperPhrase(btn) {
        const phraseDiv = btn.closest('[data-phrase]');
        const text = phraseDiv.dataset.phrase;
        const lang = phraseDiv.dataset.lang;
        
        if (!text || !lang) return;
        
        const originalHTML = btn.innerHTML;
        
        speakText(text, lang, {
            onStart: () => {
                btn.innerHTML = '<span class="spinner" style="width: 16px; height: 16px; border-width: 2px;"></span>';
                btn.classList.add('animate-pulse');
            },
            onEnd: () => {
                btn.innerHTML = originalHTML;
                btn.classList.remove('animate-pulse');
                lucide.createIcons();
            },
            onError: () => {
                btn.innerHTML = originalHTML;
                btn.classList.remove('animate-pulse');
                showToast('Voice playback not available', 'error');
                lucide.createIcons();
            }
        }).catch(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('animate-pulse');
            lucide.createIcons();
        });
    }
</script>
