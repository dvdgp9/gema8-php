<?php
/**
 * Main Dashboard View
 */
?>
<main class="flex min-h-screen flex-col items-center p-4 sm:p-6 lg:p-8">
    <div class="w-full max-w-6xl">
        <?php require ROOT_PATH . '/views/partials/header.php'; ?>
        
        <!-- Hero -->
        <div class="flex justify-center w-full flex-col items-center mt-4 mb-8 stagger-children">
            <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-accent-500 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/20 mb-4 hover:scale-105 transition-transform">
                <span class="text-3xl font-bold text-white">∞</span>
            </div>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-center gradient-text">
                Gema∞
            </h1>
            <p class="text-slate-500 mt-2">Where words shift dimensions and return brighter</p>
        </div>
        
        <!-- Daily Tip -->
        <div id="dailyTipSection" class="card mb-8 !p-0 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                        <i data-lucide="sparkles" class="h-5 w-5 text-white"></i>
                    </div>
                    <h3 class="font-bold text-white text-lg">Daily Tip</h3>
                </div>
                <?php require ROOT_PATH . '/views/partials/credits-badge.php'; ?>
            </div>
            <div class="p-5">
                <div id="dailyTipContent">
                    <?php if ($todaysTip): ?>
                    <p class="text-slate-700 leading-relaxed" id="dailyTipText"><?= e($todaysTip) ?></p>
                    <?php else: ?>
                    <div class="flex items-center gap-3 text-slate-400">
                        <span class="spinner"></span>
                        <span>Loading your daily tip...</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Translator Card -->
            <div class="card !p-0 overflow-hidden" id="translatorCard">
                <div class="bg-gradient-to-r from-primary-500 to-primary-600 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center" id="translatorIcon">
                                <i data-lucide="languages" class="h-5 w-5 text-white"></i>
                            </div>
                            <h3 class="font-bold text-white text-lg">Translator</h3>
                        </div>
                        
                        <!-- Direction Toggle -->
                        <div class="flex bg-white/20 backdrop-blur rounded-lg p-1">
                            <button 
                                id="dirToTarget" 
                                onclick="setDirection('to-target')"
                                class="px-3 py-1.5 text-xs rounded-md bg-white text-primary-600 font-semibold transition-all"
                            >
                                EN → <span id="targetLangShort"><?= e(getLanguageShortName($currentLanguage)) ?></span>
                            </button>
                            <button 
                                id="dirFromTarget" 
                                onclick="setDirection('from-target')"
                                class="px-3 py-1.5 text-xs rounded-md text-white/80 font-medium transition-all hover:text-white"
                            >
                                <span id="sourceLangShort"><?= e(getLanguageShortName($currentLanguage)) ?></span> → EN
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="p-5">
                    <textarea 
                        id="translateInput" 
                        class="input min-h-28 resize-none mb-4" 
                        placeholder="Type or paste text ready to ripple…"
                    ></textarea>
                    
                    <div id="translationResult" class="hidden mb-4 p-4 bg-gradient-to-br from-primary-50 to-slate-50 rounded-xl border border-primary-100 relative group">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-semibold text-primary-700 flex items-center gap-2">
                                <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                Translation
                            </p>
                            <div class="flex items-center gap-2">
                                <button 
                                    id="playTranslationBtn"
                                    onclick="playAudio(document.getElementById('translatedText').textContent, translationDirection === 'to-target' ? currentLanguage : 'english', this)"
                                    class="btn-audio"
                                    title="Listen to translation"
                                >
                                    <i data-lucide="volume-2" class="w-5 h-5"></i>
                                </button>
                                <span id="ephemeralBadge" class="hidden badge badge-accent">
                                    <i data-lucide="eye-off" class="h-3 w-3"></i>
                                    Ephemeral
                                </span>
                            </div>
                        </div>
                        <p id="translatedText" class="text-slate-800 whitespace-pre-wrap text-lg"></p>
                        <p id="seenCount" class="mt-3 text-xs text-slate-500 hidden"></p>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <button 
                            id="ephemeralToggle"
                            onclick="toggleEphemeral()"
                            class="btn btn-ghost text-xs"
                        >
                            <i data-lucide="eye-off" class="h-4 w-4 mr-1.5"></i>
                            Ephemeral
                        </button>
                        
                        <button 
                            id="translateBtn"
                            onclick="handleTranslate()"
                            class="btn btn-primary"
                        >
                            <i data-lucide="zap" class="h-4 w-4 mr-2"></i>
                            Echo It
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Language Doubts Card -->
            <div class="card !p-0 overflow-hidden">
                <div class="bg-gradient-to-r from-violet-500 to-purple-600 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                            <i data-lucide="help-circle" class="h-5 w-5 text-white"></i>
                        </div>
                        <h3 class="font-bold text-white text-lg">Language Doubts</h3>
                    </div>
                </div>
                
                <div class="p-5">
                    <textarea 
                        id="questionInput" 
                        class="input min-h-28 resize-none mb-4" 
                        placeholder="Ask anything about <?= e(getLanguageName($currentLanguage)) ?>..."
                    ></textarea>
                    
                    <div id="answerResult" class="hidden mb-4 p-4 bg-gradient-to-br from-violet-50 to-slate-50 rounded-xl border border-violet-100">
                        <p class="text-sm font-semibold text-violet-700 mb-3 flex items-center gap-2">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                            Answer
                        </p>
                        <div id="answerText" class="text-slate-700 leading-relaxed prose prose-sm max-w-none"></div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button 
                            id="askBtn"
                            onclick="askQuestion()"
                            class="btn btn-primary"
                            style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); box-shadow: 0 4px 14px 0 rgba(139, 92, 246, 0.35);"
                        >
                            <i data-lucide="send" class="h-4 w-4 mr-2"></i>
                            Ask
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Whisperer Section -->
        <div class="card !p-0 overflow-hidden mt-6">
            <div class="bg-gradient-to-r from-emerald-500 to-teal-500 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                            <i data-lucide="mic" class="h-5 w-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-white text-lg">Whisperer</h3>
                            <p class="text-white/70 text-sm">Situational phrases generator</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-5">
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
                        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.35);"
                    >
                        <i data-lucide="wand-2" class="h-4 w-4 mr-2"></i>
                        Generate Phrases
                    </button>
                </div>
                
                <div id="whisperResult" class="hidden mt-6">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <i data-lucide="list-checks" class="w-4 h-4 text-emerald-600"></i>
                        </div>
                        <h4 id="whisperTitle" class="font-bold text-lg text-slate-800"></h4>
                    </div>
                    <div id="whisperPhrases" class="grid gap-3"></div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="mt-12 text-center">
            <p class="text-slate-400 text-sm">Let your words echo infinitely ✨</p>
            <p class="text-slate-300 text-xs mt-1"><?= date('Y') ?> Gema∞</p>
        </footer>
    </div>
</main>

<script>
    // State
    let translationDirection = 'to-target';
    let ephemeralMode = false;
    const currentLanguage = '<?= e($currentLanguage) ?>';
    
    // Load daily tip
    document.addEventListener('DOMContentLoaded', () => {
        <?php if ($todaysTip): ?>
        const tipEl = document.getElementById('dailyTipText');
        if (tipEl) {
            tipEl.innerHTML = formatMarkdown(tipEl.textContent);
        }
        <?php else: ?>
        loadDailyTip();
        <?php endif; ?>
    });
    
    async function loadDailyTip() {
        try {
            const result = await api('<?= BASE_URL ?>/api/generate-tip', { language: currentLanguage });
            document.getElementById('dailyTipContent').innerHTML = 
                `<p class="text-slate-700 leading-relaxed">${formatMarkdown(result.tip)}</p>`;
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
        
        if (dir === 'to-target') {
            toTarget.classList.add('bg-white', 'text-primary-600', 'font-semibold');
            toTarget.classList.remove('text-white/80');
            fromTarget.classList.remove('bg-white', 'text-primary-600', 'font-semibold');
            fromTarget.classList.add('text-white/80');
        } else {
            fromTarget.classList.add('bg-white', 'text-primary-600', 'font-semibold');
            fromTarget.classList.remove('text-white/80');
            toTarget.classList.remove('bg-white', 'text-primary-600', 'font-semibold');
            toTarget.classList.add('text-white/80');
        }
    }
    
    function toggleEphemeral() {
        ephemeralMode = !ephemeralMode;
        const btn = document.getElementById('ephemeralToggle');
        const card = document.getElementById('translatorCard');
        
        if (ephemeralMode) {
            btn.classList.add('!bg-accent-100', '!text-accent-700');
            card.style.boxShadow = '0 0 0 2px rgba(249, 115, 22, 0.3)';
        } else {
            btn.classList.remove('!bg-accent-100', '!text-accent-700');
            card.style.boxShadow = '';
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
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 flex justify-between items-start gap-4">
                    <div class="flex-1">
                        <p class="font-medium text-lg">${escapeHtml(phrase.target_sentence)}</p>
                        <p class="text-gray-600 mt-1">${escapeHtml(phrase.translation)}</p>
                        <p class="text-sm text-gray-500 mt-1 italic">${escapeHtml(phrase.pronunciation)}</p>
                    </div>
                    <button 
                        onclick="playAudio('${escapeHtml(phrase.target_sentence)}', currentLanguage, this)"
                        class="btn-audio shrink-0 mt-1"
                        title="Listen"
                    >
                        <i data-lucide="volume-2" class="w-5 h-5"></i>
                    </button>
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
