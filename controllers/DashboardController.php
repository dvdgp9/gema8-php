<?php
/**
 * Dashboard Controller
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class DashboardController extends Controller {
    /**
     * Show main dashboard
     */
    public function index(): void {
        requireAuth();
        
        $profile = currentProfile();
        $currentLanguage = $profile['current_language'] ?? 'indonesian';
        $nativeLanguage = $profile['native_language'] ?? 'english';
        
        // Get today's tip (if exists)
        $todaysTip = Tip::getTodaysTip(userId(), $currentLanguage, $nativeLanguage);
        
        // Get recent translations count
        $translationCount = Translation::countForUser(userId());
        
        // Get recent whispers count
        $whisperCount = Whisper::countForUser(userId());
        
        $this->render('dashboard/index', [
            'title' => 'Gema∞ - Language Learning',
            'todaysTip' => $todaysTip,
            'currentLanguage' => $currentLanguage,
            'nativeLanguage' => $nativeLanguage,
            'translationCount' => $translationCount,
            'whisperCount' => $whisperCount
        ]);
    }
}
