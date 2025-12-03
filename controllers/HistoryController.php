<?php
/**
 * History Controller (Echoes)
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class HistoryController extends Controller {
    /**
     * Show translation history
     */
    public function index(): void {
        requireAuth();
        
        $profile = currentProfile();
        $currentLanguage = $profile['current_language'] ?? null;
        
        // Filter by language if specified
        $filterLanguage = $_GET['language'] ?? '';
        
        if (!empty($filterLanguage) && isValidLanguage($filterLanguage)) {
            $translations = Translation::getHistoryByLanguage(userId(), $filterLanguage, 100);
        } else {
            $translations = Translation::getHistory(userId(), 100);
        }
        
        $this->render('history/index', [
            'title' => 'Echoes - Translation History',
            'translations' => $translations,
            'filterLanguage' => $filterLanguage,
            'currentLanguage' => $currentLanguage
        ]);
    }
}
