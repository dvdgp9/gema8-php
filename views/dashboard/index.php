<?php
/**
 * Main Dashboard View
 */
?>
<main class="flex min-h-screen flex-col items-center p-4 sm:p-8 md:p-12 lg:p-16">
    <div class="w-full max-w-6xl">
        <?php require ROOT_PATH . '/views/partials/header.php'; ?>
        
        <!-- Hero -->
        <div class="flex justify-center w-full flex-col items-center mt-5">
            <h1 class="text-4xl sm:text-5xl font-bold text-center mb-2 mt-5 gradient-text">
                Gema∞
            </h1>
            <p class="text-sm text-gray-600 mb-8">Where words shift dimensions and return brighter.</p>
        </div>
        
        <!-- Daily Tip -->
        <div id="dailyTipSection" class="card mb-8 border-l-4 border-l-amber-400">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                        <i data-lucide="lightbulb" class="h-5 w-5 text-amber-600"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-gray-900">Daily Tip</h3>
                        <?php require ROOT_PATH . '/views/partials/credits-badge.php'; ?>
                    </div>
                    <div id="dailyTipContent">
                        <?php if ($todaysTip): ?>
                        <p class="text-gray-700"><?= e($todaysTip) ?></p>
                        <?php else: ?>
                        <p class="text-gray-500 italic">Loading your daily tip...</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
            <!-- Translator Card -->
            <div class="card" id="translatorCard">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold flex items-center">
                        <span class="mr-2" id="translatorEmoji">🇬🇧</span>
                        Translator
                    </h3>
                    
                    <div class="flex items-center space-x-2">
                        <!-- Direction Toggle -->
                        <div class="flex bg-gray-100 rounded-lg p-1">
                            <button 
                                id="dirToTarget" 
                                onclick="setDirection('to-target')"
                                class="px-3 py-1 text-xs rounded-md bg-white shadow-sm font-medium"
                            >
                                Eng → <span id="targetLangShort"><?= e(substr(getLanguageName($currentLanguage), 0, 3)) ?></span>
                            </button>
                            <button 
                                id="dirFromTarget" 
                                onclick="setDirection('from-target')"
                                class="px-3 py-1 text-xs rounded-md text-gray-600"
                            >
                                <span id="sourceLangShort"><?= e(substr(getLanguageName($currentLanguage), 0, 3)) ?></span> → Eng
                            </button>
                        </div>
                    </div>
                </div>
                
                <textarea 
                    id="translateInput" 
                    class="input min-h-24 resize-none mb-4" 
                    placeholder="Type or paste text ready to ripple…"
                ></textarea>
                
                <div id="translationResult" class="hidden mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-700">Translation:</p>
                        <span id="ephemeralBadge" class="hidden px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded-full">
                            <i data-lucide="eye-off" class="h-3 w-3 inline mr-1"></i>
                            Ephemeral
                        </span>
                    </div>
                    <p id="translatedText" class="text-gray-900 whitespace-pre-wrap"></p>
                    <p id="seenCount" class="mt-2 text-xs text-gray-500 hidden"></p>
                </div>
                
                <div class="flex items-center justify-between">
                    <button 
                        id="ephemeralToggle"
                        onclick="toggleEphemeral()"
                        class="btn btn-ghost text-xs text-gray-500"
                    >
                        <i data-lucide="eye-off" class="h-3 w-3 mr-1"></i>
                        Ephemeral
                    </button>
                    
                    <button 
                        id="translateBtn"
                        onclick="handleTranslate()"
                        class="btn btn-primary"
                    >
                        Echo It
                    </button>
                </div>
            </div>
            
            <!-- Language Doubts Card -->
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold flex items-center">
                        <span class="mr-2">❓</span>
                        Language Doubts
                    </h3>
                </div>
                
                <textarea 
                    id="questionInput" 
                    class="input min-h-24 resize-none mb-4" 
                    placeholder="Ask anything about <?= e(getLanguageName($currentLanguage)) ?>..."
                ></textarea>
                
                <div id="answerResult" class="hidden mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-sm font-medium text-blue-700 mb-2">Answer:</p>
                    <div id="answerText" class="text-gray-900 prose prose-sm max-w-none"></div>
                </div>
                
                <div class="flex justify-end">
                    <button 
                        id="askBtn"
                        onclick="askQuestion()"
                        class="btn btn-primary"
                    >
                        <i data-lucide="send" class="h-4 w-4 mr-2"></i>
                        Ask
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Whisperer Section -->
        <div class="card mt-6 lg:mt-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold flex items-center">
                    <span class="mr-2">🗣️</span>
                    Whisperer
                </h3>
                <span class="text-sm text-gray-500">Situational phrases generator</span>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4">
                <input 
                    type="text" 
                    id="situationInput" 
                    class="input flex-1" 
                    placeholder="Describe your situation (e.g., 'ordering coffee at a cafe')"
                >
                <button 
                    id="whisperBtn"
                    onclick="generateWhisper()"
                    class="btn btn-primary whitespace-nowrap"
                >
                    <i data-lucide="sparkles" class="h-4 w-4 mr-2"></i>
                    Generate Phrases
                </button>
            </div>
            
            <div id="whisperResult" class="hidden mt-6">
                <h4 id="whisperTitle" class="font-semibold text-lg mb-4"></h4>
                <div id="whisperPhrases" class="space-y-3"></div>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="mt-12 sm:mt-16 text-center text-gray-500 text-xs">
            <p>Let your words echo infinitely | <?= date('Y') ?></p>
        </footer>
    </div>
</main>

<script>
    // State
    let translationDirection = 'to-target';
    let ephemeralMode = false;
    const currentLanguage = '<?= e($currentLanguage) ?>';
    
    // Load daily tip if not cached
    <?php if (!$todaysTip): ?>
    document.addEventListener('DOMContentLoaded', loadDailyTip);
    <?php endif; ?>
    
    async function loadDailyTip() {
        try {
            const result = await api('<?= BASE_URL ?>/api/generate-tip', { language: currentLanguage });
            document.getElementById('dailyTipContent').innerHTML = 
                `<p class="text-gray-700">${escapeHtml(result.tip)}</p>`;
            updateCredits();
        } catch (error) {
            document.getElementById('dailyTipContent').innerHTML = 
                `<p class="text-red-500">${escapeHtml(error.message)}</p>`;
        }
    }
    
    function setDirection(dir) {
        translationDirection = dir;
        const toTarget = document.getElementById('dirToTarget');
        const fromTarget = document.getElementById('dirFromTarget');
        const emoji = document.getElementById('translatorEmoji');
        
        if (dir === 'to-target') {
            toTarget.classList.add('bg-white', 'shadow-sm', 'font-medium');
            toTarget.classList.remove('text-gray-600');
            fromTarget.classList.remove('bg-white', 'shadow-sm', 'font-medium');
            fromTarget.classList.add('text-gray-600');
            emoji.textContent = '🇬🇧';
        } else {
            fromTarget.classList.add('bg-white', 'shadow-sm', 'font-medium');
            fromTarget.classList.remove('text-gray-600');
            toTarget.classList.remove('bg-white', 'shadow-sm', 'font-medium');
            toTarget.classList.add('text-gray-600');
            emoji.textContent = '🌐';
        }
    }
    
    function toggleEphemeral() {
        ephemeralMode = !ephemeralMode;
        const btn = document.getElementById('ephemeralToggle');
        const card = document.getElementById('translatorCard');
        
        if (ephemeralMode) {
            btn.classList.add('bg-orange-100', 'text-orange-700');
            btn.classList.remove('text-gray-500');
            card.classList.add('border-orange-200', 'bg-orange-50/50');
        } else {
            btn.classList.remove('bg-orange-100', 'text-orange-700');
            btn.classList.add('text-gray-500');
            card.classList.remove('border-orange-200', 'bg-orange-50/50');
        }
    }
    
    async function handleTranslate() {
        const text = document.getElementById('translateInput').value.trim();
        if (!text) {
            showToast('Please enter some text to translate', 'error');
            return;
        }
        
        const btn = document.getElementById('translateBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner mr-2"></span>Echoing...';
        
        try {
            const sourceLanguage = translationDirection === 'to-target' ? 'english' : currentLanguage;
            const targetLanguage = translationDirection === 'to-target' ? currentLanguage : 'english';
            
            const result = await api('<?= BASE_URL ?>/api/translate', {
                text,
                source_language: sourceLanguage,
                target_language: targetLanguage,
                ephemeral: ephemeralMode
            });
            
            document.getElementById('translatedText').textContent = result.translated_text;
            document.getElementById('translationResult').classList.remove('hidden');
            
            const seenCountEl = document.getElementById('seenCount');
            const ephemeralBadge = document.getElementById('ephemeralBadge');
            
            if (result.ephemeral) {
                ephemeralBadge.classList.remove('hidden');
                seenCountEl.classList.add('hidden');
            } else {
                ephemeralBadge.classList.add('hidden');
                if (result.count > 1) {
                    seenCountEl.textContent = `(Previously translated ${result.count - 1} times)`;
                    seenCountEl.classList.remove('hidden');
                } else {
                    seenCountEl.classList.add('hidden');
                }
            }
            
            updateCredits();
            
        } catch (error) {
            showToast(error.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Echo It';
        }
    }
    
    async function askQuestion() {
        const question = document.getElementById('questionInput').value.trim();
        if (!question) {
            showToast('Please enter a question', 'error');
            return;
        }
        
        const btn = document.getElementById('askBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner mr-2"></span>Thinking...';
        
        try {
            const result = await api('<?= BASE_URL ?>/api/ask-question', {
                question,
                language: currentLanguage
            });
            
            document.getElementById('answerText').innerHTML = formatMarkdown(result.answer);
            document.getElementById('answerResult').classList.remove('hidden');
            updateCredits();
            
        } catch (error) {
            showToast(error.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="send" class="h-4 w-4 mr-2"></i>Ask';
            lucide.createIcons();
        }
    }
    
    async function generateWhisper() {
        const situation = document.getElementById('situationInput').value.trim();
        if (!situation) {
            showToast('Please describe a situation', 'error');
            return;
        }
        
        const btn = document.getElementById('whisperBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner mr-2"></span>Generating...';
        
        try {
            const result = await api('<?= BASE_URL ?>/api/generate-whisper', {
                situation,
                target_language: currentLanguage
            });
            
            document.getElementById('whisperTitle').textContent = result.title;
            
            const phrasesContainer = document.getElementById('whisperPhrases');
            phrasesContainer.innerHTML = result.phrases.map((phrase, i) => `
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="font-medium text-lg">${escapeHtml(phrase.target_sentence)}</p>
                    <p class="text-gray-600 mt-1">${escapeHtml(phrase.translation)}</p>
                    <p class="text-sm text-gray-500 mt-1 italic">${escapeHtml(phrase.pronunciation)}</p>
                </div>
            `).join('');
            
            document.getElementById('whisperResult').classList.remove('hidden');
            updateCredits();
            
        } catch (error) {
            showToast(error.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="sparkles" class="h-4 w-4 mr-2"></i>Generate Phrases';
            lucide.createIcons();
        }
    }
    
    async function updateCredits() {
        // Refresh the page credits display
        try {
            const response = await fetch('<?= BASE_URL ?>/', { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            // For now just reload will work, or we can make a dedicated endpoint
        } catch (e) {}
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function formatMarkdown(text) {
        // Basic markdown formatting
        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/`(.*?)`/g, '<code class="bg-gray-100 px-1 rounded">$1</code>')
            .replace(/\n/g, '<br>');
    }
</script>
