<?php
/**
 * Admin Controller - Oracle (superadmin) Panel
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class AdminController extends Controller {
    /**
     * Admin dashboard with stats and user list
     */
    public function index(): void {
        requireOracle();
        
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $search = trim($_GET['search'] ?? '');
        
        $users = User::getAllWithProfiles($limit, $offset, $search);
        $totalUsers = User::count($search);
        $totalPages = ceil($totalUsers / $limit);
        $stats = Profile::getStats();
        
        $this->render('admin/index', [
            'title' => 'Admin Panel - Gema∞',
            'users' => $users,
            'stats' => $stats,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers,
            'search' => $search
        ]);
    }
    
    /**
     * Show edit user form
     */
    public function editUser(): void {
        requireOracle();
        
        $userId = (int) ($_GET['id'] ?? 0);
        
        if ($userId <= 0) {
            flash('error', 'Invalid user ID');
            redirect('/admin');
        }
        
        $user = User::findWithProfile($userId);
        
        if (!$user) {
            flash('error', 'User not found');
            redirect('/admin');
        }
        
        $this->render('admin/user-edit', [
            'title' => 'Edit User - Gema∞',
            'user' => $user
        ]);
    }
    
    /**
     * Update user (credits, role)
     */
    public function updateUser(): void {
        requireOracle();
        requireCsrf();
        
        $userId = (int) ($_POST['user_id'] ?? 0);
        $credits = (int) ($_POST['credits'] ?? 0);
        $role = $_POST['role'] ?? '';
        
        if ($userId <= 0) {
            flash('error', 'Invalid user ID');
            redirect('/admin');
        }
        
        $user = User::find($userId);
        if (!$user) {
            flash('error', 'User not found');
            redirect('/admin');
        }
        
        // Validate role
        if (!in_array($role, [ROLE_WHISPER, ROLE_VOICE, ROLE_ORACLE])) {
            flash('error', 'Invalid role');
            redirect('/admin/user?id=' . $userId);
        }
        
        // Validate credits
        if ($credits < 0) {
            $credits = 0;
        }
        
        // Update profile
        $success = Profile::setCredits($userId, $credits) && Profile::updateRole($userId, $role);
        
        if ($success) {
            flash('success', 'User updated successfully');
        } else {
            flash('error', 'Failed to update user');
        }
        
        redirect('/admin/user?id=' . $userId);
    }
    
    /**
     * Delete user
     */
    public function deleteUser(): void {
        requireOracle();
        requireCsrf();
        
        $userId = (int) ($_POST['user_id'] ?? 0);
        
        if ($userId <= 0) {
            flash('error', 'Invalid user ID');
            redirect('/admin');
        }
        
        // Prevent self-deletion
        if ($userId === userId()) {
            flash('error', 'You cannot delete your own account from here');
            redirect('/admin');
        }
        
        $user = User::find($userId);
        if (!$user) {
            flash('error', 'User not found');
            redirect('/admin');
        }
        
        if (User::delete($userId)) {
            flash('success', 'User deleted successfully');
        } else {
            flash('error', 'Failed to delete user');
        }
        
        redirect('/admin');
    }
    
    /**
     * Quick add credits (AJAX)
     */
    public function addCredits(): void {
        requireOracle();
        
        if (!isAjax()) {
            redirect('/admin');
        }
        
        $userId = (int) ($_POST['user_id'] ?? 0);
        $amount = (int) ($_POST['amount'] ?? 0);
        
        if ($userId <= 0 || $amount <= 0) {
            jsonResponse(['error' => 'Invalid parameters'], 400);
        }
        
        if (Profile::addCredits($userId, $amount)) {
            $profile = Profile::findByUserId($userId);
            jsonResponse([
                'success' => true,
                'new_credits' => $profile['credits']
            ]);
        } else {
            jsonResponse(['error' => 'Failed to add credits'], 500);
        }
    }
}
