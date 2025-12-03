<?php
/**
 * Account Controller
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class AccountController extends Controller {
    /**
     * Show account page
     */
    public function index(): void {
        requireAuth();
        
        $profile = currentProfile();
        $user = currentUser();
        
        // Get stats
        $translationCount = Translation::countForUser(userId());
        $whisperCount = Whisper::countForUser(userId());
        $tipCount = Tip::countForUser(userId());
        
        $this->render('account/index', [
            'title' => 'My Account - Gema∞',
            'translationCount' => $translationCount,
            'whisperCount' => $whisperCount,
            'tipCount' => $tipCount
        ]);
    }
    
    /**
     * Update language preference
     */
    public function updateLanguage(): void {
        requireAuth();
        requireCsrf();
        
        $language = sanitize($_POST['language'] ?? '');
        
        if (!isValidLanguage($language)) {
            if (isAjax()) {
                $this->json(['error' => 'Invalid language'], 400);
            }
            flash('error', 'Invalid language selected');
            redirect('/account');
        }
        
        Profile::updateLanguage(userId(), $language);
        refreshProfile();
        
        if (isAjax()) {
            $this->json(['success' => true, 'language' => $language]);
        }
        
        flash('success', 'Language updated to ' . getLanguageName($language));
        redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }
    
    /**
     * Delete account
     */
    public function delete(): void {
        requireAuth();
        requireCsrf();
        
        $password = $_POST['password'] ?? '';
        $user = User::findByEmail(currentUser()['email']);
        
        if (!$user || !verifyPassword($password, $user['password_hash'])) {
            if (isAjax()) {
                $this->json(['error' => 'Invalid password'], 401);
            }
            flash('error', 'Invalid password');
            redirect('/account');
        }
        
        $userId = userId();
        logout();
        
        if (User::delete($userId)) {
            flash('success', 'Your account has been deleted');
        } else {
            flash('error', 'Failed to delete account');
        }
        
        redirect('/auth');
    }
}
