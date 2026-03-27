<?php
/**
 * Conversations List View
 */
?>
<main class="flex min-h-screen flex-col items-center p-4 sm:p-6 lg:p-8">
    <div class="w-full max-w-4xl">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="<?= BASE_URL ?>/" class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center hover:shadow-md transition-all">
                    <i data-lucide="arrow-left" class="h-5 w-5 text-slate-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold gradient-text flex items-center gap-2">
                        <i data-lucide="messages-square" class="h-6 w-6 text-blue-500"></i>
                        Conversations
                    </h1>
                    <p class="text-sm text-slate-500">Real-time travel translations</p>
                </div>
            </div>
            
            <button 
                onclick="showNewConversationModal()"
                class="btn btn-primary !py-2.5 !px-4"
                style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
            >
                <i data-lucide="plus" class="h-4 w-4 mr-1"></i>
                <span class="hidden sm:inline">New</span>
            </button>
        </div>
        
        <!-- Search & Filters -->
        <div class="flex items-center gap-3 mb-6">
            <div class="relative flex-1">
                <i data-lucide="search" class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input 
                    type="text" 
                    id="searchInput"
                    placeholder="Search conversations..."
                    value="<?= e($search) ?>"
                    class="input !pl-10 !py-2.5 text-sm"
                    onkeydown="if(event.key==='Enter') searchConversations()"
                >
            </div>
            <button 
                onclick="toggleArchived()"
                class="w-10 h-10 rounded-xl <?= $showArchived ? 'bg-blue-100 text-blue-600' : 'bg-white text-slate-400' ?> shadow-sm flex items-center justify-center hover:shadow-md transition-all"
                title="<?= $showArchived ? 'Show active' : 'Show archived' ?>"
            >
                <i data-lucide="archive" class="h-5 w-5"></i>
            </button>
        </div>
        
        <?php if ($showArchived): ?>
        <div class="mb-4 px-4 py-2 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700 flex items-center gap-2">
            <i data-lucide="archive" class="h-4 w-4"></i>
            Showing archived conversations
        </div>
        <?php endif; ?>
        
        <!-- Conversations List -->
        <?php if (empty($conversations)): ?>
        <div class="card text-center py-16">
            <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-blue-50 flex items-center justify-center">
                <i data-lucide="messages-square" class="h-10 w-10 text-blue-300"></i>
            </div>
            <?php if (!empty($search)): ?>
            <h3 class="text-xl font-semibold text-slate-700 mb-2">No results found</h3>
            <p class="text-slate-500 mb-6">Try a different search term</p>
            <?php elseif ($showArchived): ?>
            <h3 class="text-xl font-semibold text-slate-700 mb-2">No archived conversations</h3>
            <p class="text-slate-500 mb-6">Archived conversations will appear here</p>
            <?php else: ?>
            <h3 class="text-xl font-semibold text-slate-700 mb-2">No conversations yet</h3>
            <p class="text-slate-500 mb-6">Start a conversation to translate in real-time while talking to someone</p>
            <button 
                onclick="showNewConversationModal()"
                class="btn btn-primary"
                style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
            >
                <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                Start your first conversation
            </button>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($conversations as $index => $conv): ?>
            <a 
                href="<?= BASE_URL ?>/conversations/view?id=<?= $conv['id'] ?>" 
                class="card !p-4 block hover:shadow-md transition-all animate-slide-up group"
                id="conv-<?= $conv['id'] ?>"
                style="animation-delay: <?= $index * 0.03 ?>s"
            >
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center flex-shrink-0 shadow-sm">
                        <span class="text-white font-bold text-lg"><?= mb_strtoupper(mb_substr($conv['title'], 0, 1)) ?></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-bold text-slate-800 truncate"><?= e($conv['title']) ?></h3>
                            <?php if ($conv['is_archived']): ?>
                            <span class="badge text-xs bg-amber-100 text-amber-600">Archived</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="badge badge-primary text-xs"><?= e(ucfirst($conv['target_language'])) ?></span>
                            <span class="text-xs text-slate-400"><?= (int)$conv['message_count'] ?> messages</span>
                            <span class="text-xs text-slate-400">•</span>
                            <span class="text-xs text-slate-400">
                                <?= $conv['last_message_at'] ? timeAgo($conv['last_message_at']) : timeAgo($conv['created_at']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <?php if (!$conv['is_archived']): ?>
                        <button 
                            onclick="event.preventDefault(); event.stopPropagation(); archiveConversation(<?= $conv['id'] ?>)"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:text-amber-500 hover:bg-amber-50 transition-all opacity-0 group-hover:opacity-100"
                            title="Archive"
                        >
                            <i data-lucide="archive" class="h-4 w-4"></i>
                        </button>
                        <?php else: ?>
                        <button 
                            onclick="event.preventDefault(); event.stopPropagation(); unarchiveConversation(<?= $conv['id'] ?>)"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:text-blue-500 hover:bg-blue-50 transition-all opacity-0 group-hover:opacity-100"
                            title="Unarchive"
                        >
                            <i data-lucide="archive-restore" class="h-4 w-4"></i>
                        </button>
                        <?php endif; ?>
                        <button 
                            onclick="event.preventDefault(); event.stopPropagation(); deleteConversation(<?= $conv['id'] ?>)"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all opacity-0 group-hover:opacity-100"
                            title="Delete"
                        >
                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                        </button>
                        <i data-lucide="chevron-right" class="h-5 w-5 text-slate-300 group-hover:text-slate-500 transition-colors"></i>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- New Conversation Modal -->
<div id="newConvModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="hideNewConversationModal()"></div>
    <div class="absolute inset-x-4 top-1/2 -translate-y-1/2 mx-auto max-w-md">
        <div class="card !p-6 relative animate-slide-up">
            <button onclick="hideNewConversationModal()" class="absolute top-4 right-4 w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-all">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
            
            <h2 class="text-xl font-bold text-slate-800 mb-1">New Conversation</h2>
            <p class="text-sm text-slate-500 mb-6">Who are you talking to?</p>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Name / Title</label>
                    <input 
                        type="text" 
                        id="convTitle" 
                        class="input" 
                        placeholder="e.g. Hotel receptionist, Taxi driver..."
                        autofocus
                    >
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Language</label>
                    <select id="convLanguage" class="input">
                        <?php foreach ($supportedLanguages as $code => $name): ?>
                        <option value="<?= e($code) ?>" <?= $currentLanguage === $code ? 'selected' : '' ?>>
                            <?= e($name) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Advanced Settings (collapsible) -->
                <button type="button" onclick="toggleAdvancedSettings()" class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 transition-colors">
                    <i data-lucide="settings-2" class="h-4 w-4"></i>
                    Translation settings
                    <i data-lucide="chevron-down" class="h-3 w-3" id="advancedChevron"></i>
                </button>
                
                <div id="advancedSettings" class="hidden space-y-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Level</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" onclick="selectOption('level', 'beginner', this)" class="setting-btn py-2 px-3 rounded-lg border text-sm font-medium transition-all border-slate-200 text-slate-600 hover:border-blue-300">
                                Beginner
                            </button>
                            <button type="button" onclick="selectOption('level', 'intermediate', this)" class="setting-btn py-2 px-3 rounded-lg border text-sm font-medium transition-all border-blue-500 bg-blue-50 text-blue-700 selected">
                                Medium
                            </button>
                            <button type="button" onclick="selectOption('level', 'advanced', this)" class="setting-btn py-2 px-3 rounded-lg border text-sm font-medium transition-all border-slate-200 text-slate-600 hover:border-blue-300">
                                Advanced
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tone</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" onclick="selectOption('tone', 'keep', this)" class="setting-btn py-2 px-3 rounded-lg border text-sm font-medium transition-all border-blue-500 bg-blue-50 text-blue-700 selected">
                                Keep original
                            </button>
                            <button type="button" onclick="selectOption('tone', 'formal', this)" class="setting-btn py-2 px-3 rounded-lg border text-sm font-medium transition-all border-slate-200 text-slate-600 hover:border-blue-300">
                                Formal
                            </button>
                            <button type="button" onclick="selectOption('tone', 'casual', this)" class="setting-btn py-2 px-3 rounded-lg border text-sm font-medium transition-all border-slate-200 text-slate-600 hover:border-blue-300">
                                Casual
                            </button>
                            <button type="button" onclick="selectOption('tone', 'funny', this)" class="setting-btn py-2 px-3 rounded-lg border text-sm font-medium transition-all border-slate-200 text-slate-600 hover:border-blue-300">
                                Funny
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Fidelity</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" onclick="selectOption('fidelity', 'literal', this)" class="setting-btn py-2 px-3 rounded-lg border text-sm font-medium transition-all border-slate-200 text-slate-600 hover:border-blue-300">
                                Literal
                            </button>
                            <button type="button" onclick="selectOption('fidelity', 'natural', this)" class="setting-btn py-2 px-3 rounded-lg border text-sm font-medium transition-all border-blue-500 bg-blue-50 text-blue-700 selected">
                                Natural
                            </button>
                            <button type="button" onclick="selectOption('fidelity', 'free', this)" class="setting-btn py-2 px-3 rounded-lg border text-sm font-medium transition-all border-slate-200 text-slate-600 hover:border-blue-300">
                                Free
                            </button>
                        </div>
                    </div>
                </div>
                
                <button 
                    onclick="createConversation()" 
                    id="createConvBtn"
                    class="btn btn-primary w-full !py-3 text-base font-semibold"
                    style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
                >
                    <i data-lucide="message-circle-plus" class="h-5 w-5 mr-2"></i>
                    Start Conversation
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const convSettings = {
        level: 'intermediate',
        tone: 'keep',
        fidelity: 'natural'
    };
    
    function searchConversations() {
        const q = document.getElementById('searchInput').value.trim();
        const url = new URL(window.location);
        if (q) {
            url.searchParams.set('q', q);
            url.searchParams.delete('archived');
        } else {
            url.searchParams.delete('q');
        }
        window.location = url;
    }
    
    function toggleArchived() {
        const url = new URL(window.location);
        url.searchParams.delete('q');
        if (url.searchParams.has('archived')) {
            url.searchParams.delete('archived');
        } else {
            url.searchParams.set('archived', '1');
        }
        window.location = url;
    }
    
    function showNewConversationModal() {
        document.getElementById('newConvModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('convTitle').focus(), 100);
        lucide.createIcons();
    }
    
    function hideNewConversationModal() {
        document.getElementById('newConvModal').classList.add('hidden');
    }
    
    function toggleAdvancedSettings() {
        const el = document.getElementById('advancedSettings');
        const chevron = document.getElementById('advancedChevron');
        el.classList.toggle('hidden');
        chevron.style.transform = el.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
    }
    
    function selectOption(setting, value, btn) {
        convSettings[setting] = value;
        const parent = btn.closest('.grid');
        parent.querySelectorAll('.setting-btn').forEach(b => {
            b.classList.remove('border-blue-500', 'bg-blue-50', 'text-blue-700', 'selected');
            b.classList.add('border-slate-200', 'text-slate-600');
        });
        btn.classList.remove('border-slate-200', 'text-slate-600');
        btn.classList.add('border-blue-500', 'bg-blue-50', 'text-blue-700', 'selected');
    }
    
    async function createConversation() {
        const title = document.getElementById('convTitle').value.trim();
        const language = document.getElementById('convLanguage').value;
        
        if (!title) {
            showToast('Please enter a name or title', 'error');
            return;
        }
        
        const btn = document.getElementById('createConvBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Creating...';
        
        try {
            const result = await api(window.BASE_URL + '/api/conversation/create', {
                title: title,
                target_language: language,
                level: convSettings.level,
                tone: convSettings.tone,
                fidelity: convSettings.fidelity
            });
            
            window.location.href = window.BASE_URL + '/conversations/view?id=' + result.id;
        } catch (error) {
            showToast(error.message || 'Failed to create conversation', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="message-circle-plus" class="h-5 w-5 mr-2"></i> Start Conversation';
            lucide.createIcons();
        }
    }
    
    async function archiveConversation(id) {
        try {
            await api(window.BASE_URL + '/api/conversation/archive', { id });
            const el = document.getElementById('conv-' + id);
            el.style.opacity = '0';
            el.style.transform = 'translateX(-20px)';
            setTimeout(() => el.remove(), 300);
            showToast('Conversation archived');
        } catch (error) {
            showToast(error.message, 'error');
        }
    }
    
    async function unarchiveConversation(id) {
        try {
            await api(window.BASE_URL + '/api/conversation/unarchive', { id });
            const el = document.getElementById('conv-' + id);
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 300);
            showToast('Conversation restored');
        } catch (error) {
            showToast(error.message, 'error');
        }
    }
    
    async function deleteConversation(id) {
        if (!confirm('Delete this conversation and all messages? This cannot be undone.')) return;
        
        try {
            await api(window.BASE_URL + '/api/conversation/delete', { id });
            const el = document.getElementById('conv-' + id);
            el.style.opacity = '0';
            el.style.transform = 'scale(0.95)';
            setTimeout(() => el.remove(), 300);
            showToast('Conversation deleted');
        } catch (error) {
            showToast(error.message, 'error');
        }
    }
    
    // Enter key in title input creates conversation
    document.getElementById('convTitle')?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') createConversation();
    });
</script>
