<?php
/**
 * Whisper Controller (Situational Phrases)
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class WhisperController extends Controller {
    /**
     * Show whispers list
     */
    public function index(): void {
        requireAuth();
        
        $profile = currentProfile();
        $currentLanguage = $profile['current_language'] ?? 'indonesian';
        
        // Filter by language if specified
        $filterLanguage = $_GET['language'] ?? '';
        
        if (!empty($filterLanguage) && isValidLanguage($filterLanguage)) {
            $whispers = Whisper::getForUserByLanguage(userId(), $filterLanguage, 50);
        } else {
            $whispers = Whisper::getForUser(userId(), 50);
        }
        
        $this->render('whispers/index', [
            'title' => 'Whispers - Situational Phrases',
            'whispers' => $whispers,
            'filterLanguage' => $filterLanguage,
            'currentLanguage' => $currentLanguage
        ]);
    }
}
