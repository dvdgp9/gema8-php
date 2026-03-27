<?php
/**
 * Conversation Controller
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class ConversationController extends Controller {
    /**
     * Show conversations list
     */
    public function index(): void {
        requireAuth();
        
        $profile = currentProfile();
        $currentLanguage = $profile['current_language'] ?? 'indonesian';
        
        $search = trim($_GET['q'] ?? '');
        $showArchived = (bool) ($_GET['archived'] ?? false);
        
        if (!empty($search)) {
            $conversations = Conversation::search(userId(), $search);
        } elseif ($showArchived) {
            $conversations = Conversation::getArchivedForUser(userId());
        } else {
            $conversations = Conversation::getForUser(userId());
        }
        
        $this->render('conversations/index', [
            'title' => 'Conversations',
            'conversations' => $conversations,
            'currentLanguage' => $currentLanguage,
            'search' => $search,
            'showArchived' => $showArchived
        ]);
    }
    
    /**
     * Show single conversation (chat view)
     */
    public function view(): void {
        requireAuth();
        
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            redirect('/conversations');
        }
        
        $conversation = Conversation::findForUser($id, userId());
        if (!$conversation) {
            redirect('/conversations');
        }
        
        $messages = ConversationMessage::getForConversation($id, 200);
        $profile = currentProfile();
        
        $this->render('conversations/view', [
            'title' => e($conversation['title']) . ' - Conversation',
            'conversation' => $conversation,
            'messages' => $messages,
            'currentLanguage' => $profile['current_language'] ?? 'indonesian'
        ]);
    }
    
    /**
     * Create new conversation (POST)
     */
    public function create(): void {
        requireAuth();
        requireCsrf();
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $title = trim($input['title'] ?? '');
        $targetLanguage = sanitize($input['target_language'] ?? '');
        $level = sanitize($input['level'] ?? 'intermediate');
        $tone = sanitize($input['tone'] ?? 'keep');
        $fidelity = sanitize($input['fidelity'] ?? 'natural');
        
        if (empty($title)) {
            $this->json(['error' => 'Title is required'], 400);
        }
        
        if (empty($targetLanguage) || !isValidLanguage($targetLanguage)) {
            $this->json(['error' => 'Invalid target language'], 400);
        }
        
        $validLevels = ['beginner', 'intermediate', 'advanced'];
        $validTones = ['keep', 'formal', 'casual', 'funny'];
        $validFidelities = ['literal', 'natural', 'free'];
        
        if (!in_array($level, $validLevels)) $level = 'intermediate';
        if (!in_array($tone, $validTones)) $tone = 'keep';
        if (!in_array($fidelity, $validFidelities)) $fidelity = 'natural';
        
        $conversation = Conversation::create(
            userId(), $title, $targetLanguage, $level, $tone, $fidelity
        );
        
        if (!$conversation) {
            $this->json(['error' => 'Failed to create conversation'], 500);
        }
        
        $this->json($conversation);
    }
    
    /**
     * Update conversation settings (POST)
     */
    public function update(): void {
        requireAuth();
        requireCsrf();
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $id = (int) ($input['id'] ?? 0);
        $title = trim($input['title'] ?? '');
        $level = sanitize($input['level'] ?? 'intermediate');
        $tone = sanitize($input['tone'] ?? 'keep');
        $fidelity = sanitize($input['fidelity'] ?? 'natural');
        
        if ($id <= 0 || empty($title)) {
            $this->json(['error' => 'Invalid data'], 400);
        }
        
        $conversation = Conversation::findForUser($id, userId());
        if (!$conversation) {
            $this->json(['error' => 'Conversation not found'], 404);
        }
        
        $validLevels = ['beginner', 'intermediate', 'advanced'];
        $validTones = ['keep', 'formal', 'casual', 'funny'];
        $validFidelities = ['literal', 'natural', 'free'];
        
        if (!in_array($level, $validLevels)) $level = 'intermediate';
        if (!in_array($tone, $validTones)) $tone = 'keep';
        if (!in_array($fidelity, $validFidelities)) $fidelity = 'natural';
        
        if (Conversation::updateSettings($id, userId(), $title, $level, $tone, $fidelity)) {
            $this->json(['success' => true]);
        }
        
        $this->json(['error' => 'Failed to update conversation'], 500);
    }
    
    /**
     * Archive conversation (POST)
     */
    public function archive(): void {
        requireAuth();
        requireCsrf();
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $id = (int) ($input['id'] ?? 0);
        
        if ($id <= 0) {
            $this->json(['error' => 'Invalid conversation ID'], 400);
        }
        
        if (Conversation::archive($id, userId())) {
            $this->json(['success' => true]);
        }
        
        $this->json(['error' => 'Failed to archive conversation'], 500);
    }
    
    /**
     * Unarchive conversation (POST)
     */
    public function unarchive(): void {
        requireAuth();
        requireCsrf();
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $id = (int) ($input['id'] ?? 0);
        
        if ($id <= 0) {
            $this->json(['error' => 'Invalid conversation ID'], 400);
        }
        
        if (Conversation::unarchive($id, userId())) {
            $this->json(['success' => true]);
        }
        
        $this->json(['error' => 'Failed to unarchive conversation'], 500);
    }
    
    /**
     * Delete conversation (POST)
     */
    public function delete(): void {
        requireAuth();
        requireCsrf();
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $id = (int) ($input['id'] ?? 0);
        
        if ($id <= 0) {
            $this->json(['error' => 'Invalid conversation ID'], 400);
        }
        
        if (Conversation::delete($id, userId())) {
            $this->json(['success' => true]);
        }
        
        $this->json(['error' => 'Failed to delete conversation'], 500);
    }
}
