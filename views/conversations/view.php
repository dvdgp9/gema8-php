<?php
/**
 * Single Conversation Chat View
 */
$targetLangName = getLanguageName($conversation['target_language']);
$targetLangShort = getLanguageShortName($conversation['target_language']);
?>
<main class="flex min-h-screen flex-col items-center">
    <div class="w-full max-w-2xl flex flex-col min-h-screen">
        
        <!-- Sticky Header -->
        <div class="sticky top-0 z-20 bg-white/80 backdrop-blur-lg border-b border-slate-100 px-4 py-3">
            <div class="flex items-center gap-3">
                <a href="<?= BASE_URL ?>/conversations" class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center hover:bg-slate-200 transition-all flex-shrink-0">
                    <i data-lucide="arrow-left" class="h-4 w-4 text-slate-600"></i>
                </a>
                <div class="flex-1 min-w-0">
                    <h1 class="font-bold text-slate-800 truncate"><?= e($conversation['title']) ?></h1>
                    <div class="flex items-center gap-2 text-xs text-slate-400">
                        <span class="badge badge-primary text-xs !py-0"><?= e($targetLangName) ?></span>
                        <span><?= count($messages) ?> msgs</span>
                    </div>
                </div>
                <button 
                    onclick="showSettingsModal()" 
                    class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center hover:bg-slate-200 transition-all flex-shrink-0"
                    title="Settings"
                >
                    <i data-lucide="settings-2" class="h-4 w-4 text-slate-600"></i>
                </button>
            </div>
        </div>
        
        <!-- Messages Area -->
        <div class="flex-1 overflow-y-auto px-4 py-4 space-y-3" id="messagesContainer">
            <?php if (empty($messages)): ?>
            <div class="text-center py-16">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-blue-50 flex items-center justify-center">
                    <i data-lucide="messages-square" class="h-8 w-8 text-blue-300"></i>
                </div>
                <p class="text-slate-500 text-sm">Start the conversation!<br>Type what you want to say or what they told you.</p>
            </div>
            <?php else: ?>
            <?php foreach ($messages as $msg): ?>
            <?php if ($msg['direction'] === 'me'): ?>
            <!-- My message (right aligned, blue) -->
            <div class="flex justify-end" id="msg-<?= $msg['id'] ?>">
                <div class="max-w-[85%] space-y-1">
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-500 text-white rounded-2xl rounded-br-md px-4 py-3 shadow-sm">
                        <p class="text-sm opacity-75 mb-1"><?= e($msg['original_text']) ?></p>
                        <p class="font-medium"><?= e($msg['translated_text']) ?></p>
                    </div>
                    <?php if (!empty($msg['cultural_note'])): ?>
                    <div class="bg-amber-50 border border-amber-200 rounded-xl px-3 py-2 text-xs text-amber-700 flex items-start gap-1.5">
                        <i data-lucide="lightbulb" class="h-3 w-3 mt-0.5 flex-shrink-0"></i>
                        <span><?= e($msg['cultural_note']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="flex items-center justify-end gap-1">
                        <button 
                            onclick="copyText(this, '<?= e(addslashes($msg['translated_text'])) ?>')"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-slate-500 hover:bg-slate-100 transition-all"
                            title="Copy translation"
                        >
                            <i data-lucide="copy" class="h-3.5 w-3.5"></i>
                        </button>
                        <button 
                            onclick="playTTS(this, '<?= e(addslashes($msg['translated_text'])) ?>', '<?= e($conversation['target_language']) ?>')"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-blue-500 hover:bg-blue-50 transition-all"
                            title="Listen"
                        >
                            <i data-lucide="volume-2" class="h-3.5 w-3.5"></i>
                        </button>
                        <button 
                            onclick="deleteMessage(<?= $msg['id'] ?>)"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all"
                            title="Delete"
                        >
                            <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                        </button>
                        <span class="text-xs text-slate-300 ml-1"><?= date('H:i', strtotime($msg['created_at'])) ?></span>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- Their message (left aligned, gray) -->
            <div class="flex justify-start" id="msg-<?= $msg['id'] ?>">
                <div class="max-w-[85%] space-y-1">
                    <div class="bg-slate-100 text-slate-800 rounded-2xl rounded-bl-md px-4 py-3">
                        <p class="text-sm text-slate-500 mb-1"><?= e($msg['original_text']) ?></p>
                        <p class="font-medium"><?= e($msg['translated_text']) ?></p>
                    </div>
                    <?php if (!empty($msg['cultural_note'])): ?>
                    <div class="bg-amber-50 border border-amber-200 rounded-xl px-3 py-2 text-xs text-amber-700 flex items-start gap-1.5">
                        <i data-lucide="lightbulb" class="h-3 w-3 mt-0.5 flex-shrink-0"></i>
                        <span><?= e($msg['cultural_note']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="flex items-center justify-start gap-1">
                        <span class="text-xs text-slate-300 mr-1"><?= date('H:i', strtotime($msg['created_at'])) ?></span>
                        <button 
                            onclick="copyText(this, '<?= e(addslashes($msg['translated_text'])) ?>')"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-slate-500 hover:bg-slate-100 transition-all"
                            title="Copy translation"
                        >
                            <i data-lucide="copy" class="h-3.5 w-3.5"></i>
                        </button>
                        <button 
                            onclick="playTTS(this, '<?= e(addslashes($msg['original_text'])) ?>', '<?= e($conversation['target_language']) ?>')"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-blue-500 hover:bg-blue-50 transition-all"
                            title="Listen in <?= e($targetLangName) ?>"
                        >
                            <i data-lucide="volume-2" class="h-3.5 w-3.5"></i>
                        </button>
                        <button 
                            onclick="deleteMessage(<?= $msg['id'] ?>)"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all"
                            title="Delete"
                        >
                            <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Sticky Input Area -->
        <div class="sticky bottom-0 bg-white/80 backdrop-blur-lg border-t border-slate-100 px-4 pt-3 pb-4" style="padding-bottom: max(1rem, env(safe-area-inset-bottom, 1rem));">
            <!-- Direction Toggle -->
            <div class="flex items-center justify-center gap-2 mb-3">
                <button 
                    id="dirMe" 
                    onclick="setDirection('me')"
                    class="flex-1 py-2 px-3 rounded-xl text-sm font-semibold transition-all bg-blue-500 text-white shadow-sm"
                >
                    I'm saying
                </button>
                <button 
                    id="dirThem" 
                    onclick="setDirection('them')"
                    class="flex-1 py-2 px-3 rounded-xl text-sm font-semibold transition-all bg-slate-100 text-slate-500"
                >
                    They said
                </button>
            </div>
            
            <!-- Input Row -->
            <div class="flex items-end gap-2">
                <div class="flex-1 relative">
                    <textarea 
                        id="messageInput" 
                        rows="1"
                        class="input !py-3 !pr-4 resize-none text-base"
                        placeholder="Type in English..."
                        style="min-height: 48px; max-height: 120px;"
                    ></textarea>
                </div>
                <button 
                    id="sendBtn"
                    onclick="sendMessage()"
                    class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white flex items-center justify-center shadow-sm hover:shadow-md transition-all flex-shrink-0 active:scale-95"
                    title="Send"
                >
                    <i data-lucide="send" class="h-5 w-5"></i>
                </button>
            </div>
            <div class="text-center mt-2">
                <span class="text-xs text-slate-400" id="inputHint">
                    EN → <?= e($targetLangShort) ?> · 1 credit per message
                </span>
            </div>
        </div>
    </div>
</main>

<!-- Settings Modal -->
<div id="settingsModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="hideSettingsModal()"></div>
    <div class="absolute inset-x-4 top-1/2 -translate-y-1/2 mx-auto max-w-md">
        <div class="card !p-6 relative animate-slide-up">
            <button onclick="hideSettingsModal()" class="absolute top-4 right-4 w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-all">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
            
            <h2 class="text-xl font-bold text-slate-800 mb-1">Conversation Settings</h2>
            <p class="text-sm text-slate-500 mb-6">Adjust how translations work</p>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Title</label>
                    <input type="text" id="settingsTitle" class="input" value="<?= e($conversation['title']) ?>">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Level</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="selectSetting('level', 'beginner', this)" class="setting-opt py-2 px-3 rounded-lg border text-sm font-medium transition-all <?= $conversation['level'] === 'beginner' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-slate-200 text-slate-600 hover:border-blue-300' ?>">
                            Beginner
                        </button>
                        <button type="button" onclick="selectSetting('level', 'intermediate', this)" class="setting-opt py-2 px-3 rounded-lg border text-sm font-medium transition-all <?= $conversation['level'] === 'intermediate' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-slate-200 text-slate-600 hover:border-blue-300' ?>">
                            Medium
                        </button>
                        <button type="button" onclick="selectSetting('level', 'advanced', this)" class="setting-opt py-2 px-3 rounded-lg border text-sm font-medium transition-all <?= $conversation['level'] === 'advanced' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-slate-200 text-slate-600 hover:border-blue-300' ?>">
                            Advanced
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tone</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" onclick="selectSetting('tone', 'keep', this)" class="setting-opt py-2 px-3 rounded-lg border text-sm font-medium transition-all <?= $conversation['tone'] === 'keep' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-slate-200 text-slate-600 hover:border-blue-300' ?>">
                            Keep original
                        </button>
                        <button type="button" onclick="selectSetting('tone', 'formal', this)" class="setting-opt py-2 px-3 rounded-lg border text-sm font-medium transition-all <?= $conversation['tone'] === 'formal' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-slate-200 text-slate-600 hover:border-blue-300' ?>">
                            Formal
                        </button>
                        <button type="button" onclick="selectSetting('tone', 'casual', this)" class="setting-opt py-2 px-3 rounded-lg border text-sm font-medium transition-all <?= $conversation['tone'] === 'casual' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-slate-200 text-slate-600 hover:border-blue-300' ?>">
                            Casual
                        </button>
                        <button type="button" onclick="selectSetting('tone', 'funny', this)" class="setting-opt py-2 px-3 rounded-lg border text-sm font-medium transition-all <?= $conversation['tone'] === 'funny' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-slate-200 text-slate-600 hover:border-blue-300' ?>">
                            Funny
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Fidelity</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="selectSetting('fidelity', 'literal', this)" class="setting-opt py-2 px-3 rounded-lg border text-sm font-medium transition-all <?= $conversation['fidelity'] === 'literal' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-slate-200 text-slate-600 hover:border-blue-300' ?>">
                            Literal
                        </button>
                        <button type="button" onclick="selectSetting('fidelity', 'natural', this)" class="setting-opt py-2 px-3 rounded-lg border text-sm font-medium transition-all <?= $conversation['fidelity'] === 'natural' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-slate-200 text-slate-600 hover:border-blue-300' ?>">
                            Natural
                        </button>
                        <button type="button" onclick="selectSetting('fidelity', 'free', this)" class="setting-opt py-2 px-3 rounded-lg border text-sm font-medium transition-all <?= $conversation['fidelity'] === 'free' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-slate-200 text-slate-600 hover:border-blue-300' ?>">
                            Free
                        </button>
                    </div>
                </div>
                
                <button 
                    onclick="saveSettings()" 
                    id="saveSettingsBtn"
                    class="btn btn-primary w-full !py-3 font-semibold"
                    style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
                >
                    Save Settings
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const CONV_ID = <?= (int) $conversation['id'] ?>;
    const TARGET_LANG = '<?= e($conversation['target_language']) ?>';
    const TARGET_LANG_SHORT = '<?= e($targetLangShort) ?>';
    let currentDirection = 'me';
    
    const currentSettings = {
        level: '<?= e($conversation['level']) ?>',
        tone: '<?= e($conversation['tone']) ?>',
        fidelity: '<?= e($conversation['fidelity']) ?>'
    };
    
    // Auto-scroll to bottom on load
    const messagesContainer = document.getElementById('messagesContainer');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    // Auto-resize textarea
    const messageInput = document.getElementById('messageInput');
    messageInput.addEventListener('input', function() {
        this.style.height = '48px';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
    
    // Ctrl+Enter or Enter to send
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    
    function setDirection(dir) {
        currentDirection = dir;
        const dirMe = document.getElementById('dirMe');
        const dirThem = document.getElementById('dirThem');
        const hint = document.getElementById('inputHint');
        
        if (dir === 'me') {
            dirMe.className = 'flex-1 py-2 px-3 rounded-xl text-sm font-semibold transition-all bg-blue-500 text-white shadow-sm';
            dirThem.className = 'flex-1 py-2 px-3 rounded-xl text-sm font-semibold transition-all bg-slate-100 text-slate-500';
            messageInput.placeholder = 'Type in English...';
            hint.textContent = 'EN → ' + TARGET_LANG_SHORT + ' · 1 credit per message';
        } else {
            dirThem.className = 'flex-1 py-2 px-3 rounded-xl text-sm font-semibold transition-all bg-slate-500 text-white shadow-sm';
            dirMe.className = 'flex-1 py-2 px-3 rounded-xl text-sm font-semibold transition-all bg-slate-100 text-slate-500';
            messageInput.placeholder = 'Type what they said in ' + '<?= e($targetLangName) ?>' + '...';
            hint.textContent = TARGET_LANG_SHORT + ' → EN · 1 credit per message';
        }
    }
    
    async function sendMessage() {
        const text = messageInput.value.trim();
        if (!text) return;
        
        const sendBtn = document.getElementById('sendBtn');
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<span class="spinner"></span>';
        messageInput.disabled = true;
        
        // Add temporary "sending" bubble
        const tempId = 'temp-' + Date.now();
        addMessageBubble(tempId, currentDirection, text, 'Translating...', null, true);
        
        try {
            const result = await api(window.BASE_URL + '/api/conversation/send', {
                conversation_id: CONV_ID,
                text: text,
                direction: currentDirection
            });
            
            // Remove temporary bubble
            document.getElementById(tempId)?.remove();
            
            // Add real bubble
            addMessageBubble('msg-' + result.id, result.direction, result.original_text, result.translated_text, result.cultural_note, false, result.id);
            
            // Clear input
            messageInput.value = '';
            messageInput.style.height = '48px';
            
            // Update credits
            if (typeof updateCredits === 'function') {
                updateCredits();
            }
            
        } catch (error) {
            // Remove temp bubble
            document.getElementById(tempId)?.remove();
            showToast(error.message || 'Failed to translate', 'error');
        } finally {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i data-lucide="send" class="h-5 w-5"></i>';
            messageInput.disabled = false;
            messageInput.focus();
            lucide.createIcons();
        }
    }
    
    function addMessageBubble(elId, direction, original, translated, culturalNote, isTemp, msgId) {
        const container = document.getElementById('messagesContainer');
        const emptyState = container.querySelector('.text-center.py-16');
        if (emptyState) emptyState.remove();
        
        const time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
        const escapedTranslated = escapeHtml(translated);
        const escapedOriginal = escapeHtml(original);
        
        let culturalNoteHtml = '';
        if (culturalNote) {
            culturalNoteHtml = `
                <div class="bg-amber-50 border border-amber-200 rounded-xl px-3 py-2 text-xs text-amber-700 flex items-start gap-1.5">
                    <i data-lucide="lightbulb" class="h-3 w-3 mt-0.5 flex-shrink-0"></i>
                    <span>${escapeHtml(culturalNote)}</span>
                </div>`;
        }
        
        let actionsHtml = '';
        if (!isTemp && msgId) {
            const copyTxt = translated.replace(/'/g, "\\'");
            const ttsTxt = direction === 'me' ? translated.replace(/'/g, "\\'") : original.replace(/'/g, "\\'");
            
            if (direction === 'me') {
                actionsHtml = `
                    <div class="flex items-center justify-end gap-1">
                        <button onclick="copyText(this, '${copyTxt}')" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-slate-500 hover:bg-slate-100 transition-all" title="Copy"><i data-lucide="copy" class="h-3.5 w-3.5"></i></button>
                        <button onclick="playTTS(this, '${ttsTxt}', '${TARGET_LANG}')" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-blue-500 hover:bg-blue-50 transition-all" title="Listen"><i data-lucide="volume-2" class="h-3.5 w-3.5"></i></button>
                        <button onclick="deleteMessage(${msgId})" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all" title="Delete"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i></button>
                        <span class="text-xs text-slate-300 ml-1">${time}</span>
                    </div>`;
            } else {
                actionsHtml = `
                    <div class="flex items-center justify-start gap-1">
                        <span class="text-xs text-slate-300 mr-1">${time}</span>
                        <button onclick="copyText(this, '${copyTxt}')" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-slate-500 hover:bg-slate-100 transition-all" title="Copy"><i data-lucide="copy" class="h-3.5 w-3.5"></i></button>
                        <button onclick="playTTS(this, '${ttsTxt}', '${TARGET_LANG}')" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-blue-500 hover:bg-blue-50 transition-all" title="Listen"><i data-lucide="volume-2" class="h-3.5 w-3.5"></i></button>
                        <button onclick="deleteMessage(${msgId})" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all" title="Delete"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i></button>
                    </div>`;
            }
        }
        
        let html;
        if (direction === 'me') {
            html = `
                <div class="flex justify-end" id="${elId}">
                    <div class="max-w-[85%] space-y-1">
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-500 text-white rounded-2xl rounded-br-md px-4 py-3 shadow-sm ${isTemp ? 'opacity-60' : ''}">
                            <p class="text-sm opacity-75 mb-1">${escapedOriginal}</p>
                            <p class="font-medium">${isTemp ? '<span class="spinner-sm"></span> ' + escapedTranslated : escapedTranslated}</p>
                        </div>
                        ${culturalNoteHtml}
                        ${actionsHtml}
                    </div>
                </div>`;
        } else {
            html = `
                <div class="flex justify-start" id="${elId}">
                    <div class="max-w-[85%] space-y-1">
                        <div class="bg-slate-100 text-slate-800 rounded-2xl rounded-bl-md px-4 py-3 ${isTemp ? 'opacity-60' : ''}">
                            <p class="text-sm text-slate-500 mb-1">${escapedOriginal}</p>
                            <p class="font-medium">${isTemp ? '<span class="spinner-sm"></span> ' + escapedTranslated : escapedTranslated}</p>
                        </div>
                        ${culturalNoteHtml}
                        ${actionsHtml}
                    </div>
                </div>`;
        }
        
        container.insertAdjacentHTML('beforeend', html);
        container.scrollTop = container.scrollHeight;
        lucide.createIcons();
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    async function copyText(btn, text) {
        try {
            await navigator.clipboard.writeText(text);
            const original = btn.innerHTML;
            btn.innerHTML = '<i data-lucide="check" class="h-3.5 w-3.5 text-green-500"></i>';
            lucide.createIcons();
            setTimeout(() => { btn.innerHTML = original; lucide.createIcons(); }, 1500);
        } catch (e) {
            showToast('Failed to copy', 'error');
        }
    }
    
    function playTTS(btn, text, language) {
        if (text && typeof TTS !== 'undefined') {
            TTS.play(text, language, btn);
        }
    }
    
    async function deleteMessage(msgId) {
        if (!confirm('Delete this message?')) return;
        
        try {
            await api(window.BASE_URL + '/api/conversation/delete-message', {
                message_id: msgId,
                conversation_id: CONV_ID
            });
            const el = document.getElementById('msg-' + msgId);
            if (el) {
                el.style.opacity = '0';
                el.style.transform = 'scale(0.95)';
                setTimeout(() => el.remove(), 200);
            }
        } catch (error) {
            showToast(error.message, 'error');
        }
    }
    
    // Settings Modal
    function showSettingsModal() {
        document.getElementById('settingsModal').classList.remove('hidden');
        lucide.createIcons();
    }
    
    function hideSettingsModal() {
        document.getElementById('settingsModal').classList.add('hidden');
    }
    
    function selectSetting(setting, value, btn) {
        currentSettings[setting] = value;
        const parent = btn.closest('.grid');
        parent.querySelectorAll('.setting-opt').forEach(b => {
            b.classList.remove('border-blue-500', 'bg-blue-50', 'text-blue-700');
            b.classList.add('border-slate-200', 'text-slate-600');
        });
        btn.classList.remove('border-slate-200', 'text-slate-600');
        btn.classList.add('border-blue-500', 'bg-blue-50', 'text-blue-700');
    }
    
    async function saveSettings() {
        const title = document.getElementById('settingsTitle').value.trim();
        if (!title) {
            showToast('Title is required', 'error');
            return;
        }
        
        const btn = document.getElementById('saveSettingsBtn');
        btn.disabled = true;
        btn.textContent = 'Saving...';
        
        try {
            await api(window.BASE_URL + '/api/conversation/update', {
                id: CONV_ID,
                title: title,
                level: currentSettings.level,
                tone: currentSettings.tone,
                fidelity: currentSettings.fidelity
            });
            showToast('Settings saved');
            hideSettingsModal();
            // Update header title
            document.querySelector('h1.font-bold.text-slate-800').textContent = title;
        } catch (error) {
            showToast(error.message, 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Save Settings';
        }
    }
    
    // Focus input on load
    messageInput.focus();
</script>
