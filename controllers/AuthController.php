<?php
/**
 * Authentication Controller
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class AuthController extends Controller {
    /**
     * Show auth page (combined login/register)
     */
    public function showAuth(): void {
        requireGuest();
        $this->render('auth/auth', ['title' => 'Sign In - Gema∞']);
    }
    
    /**
     * Show login form
     */
    public function showLogin(): void {
        requireGuest();
        $this->render('auth/login', ['title' => 'Login - Gema∞']);
    }
    
    /**
     * Show register form
     */
    public function showRegister(): void {
        requireGuest();
        $this->render('auth/register', ['title' => 'Register - Gema∞']);
    }
    
    /**
     * Handle login
     */
    public function login(): void {
        requireGuest();
        requireCsrf();
        
        $email = sanitizeEmail($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validate
        if (empty($email) || empty($password)) {
            flash('error', 'Please fill in all fields');
            setOldInput(['email' => $email]);
            redirect('/auth');
        }
        
        // Rate limiting
        $rateLimitKey = 'login_' . $email;
        if (!checkRateLimit($rateLimitKey, 5, 15)) {
            flash('error', 'Too many login attempts. Please wait 15 minutes.');
            redirect('/auth');
        }
        
        // Verify credentials
        $user = User::verifyCredentials($email, $password);
        
        if (!$user) {
            flash('error', 'Invalid email or password');
            setOldInput(['email' => $email]);
            redirect('/auth');
        }
        
        // Clear rate limit on successful login
        clearRateLimit($rateLimitKey);
        
        // Log in
        login($user['id']);
        clearOldInput();
        
        flash('success', 'Welcome back!');
        redirect('/');
    }
    
    /**
     * Handle registration
     */
    public function register(): void {
        requireGuest();
        requireCsrf();
        
        $email = sanitizeEmail($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        // Validate
        $errors = $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        
        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Passwords do not match';
        }
        
        if (User::emailExists($email)) {
            $errors['email'] = 'This email is already registered';
        }
        
        if (!empty($errors)) {
            flash('error', implode('. ', $errors));
            setOldInput(['email' => $email]);
            redirect('/auth');
        }
        
        // Rate limiting for registration
        $rateLimitKey = 'register_' . $_SERVER['REMOTE_ADDR'];
        if (!checkRateLimit($rateLimitKey, 3, 60)) {
            flash('error', 'Too many registration attempts. Please wait.');
            redirect('/auth');
        }
        
        // Create user
        $userId = User::create($email, $password);
        
        if (!$userId) {
            flash('error', 'Registration failed. Please try again.');
            setOldInput(['email' => $email]);
            redirect('/auth');
        }
        
        // Log in automatically
        login($userId);
        clearOldInput();
        
        flash('success', 'Welcome to Gema∞! Start exploring languages.');
        redirect('/');
    }
    
    /**
     * Handle logout
     */
    public function logout(): void {
        logout();
        flash('success', 'You have been logged out');
        redirect('/auth');
    }
    
    /**
     * Show forgot password form
     */
    public function showForgotPassword(): void {
        requireGuest();
        $this->render('auth/forgot-password', ['title' => 'Reset Password - Gema∞']);
    }
    
    /**
     * Handle forgot password request
     */
    public function forgotPassword(): void {
        requireGuest();
        requireCsrf();
        
        $email = sanitizeEmail($_POST['email'] ?? '');
        
        if (empty($email) || !isValidEmail($email)) {
            flash('error', 'Please enter a valid email');
            redirect('/auth/forgot-password');
        }
        
        // Always show success message to prevent email enumeration
        $token = User::createResetToken($email);
        
        if ($token) {
            // In production, send email with reset link
            // For now, just log it
            $resetLink = BASE_URL . '/auth/reset-password?token=' . $token;
            error_log("Password reset link for $email: $resetLink");
            
            // In development, show the token
            if (ENV === 'development') {
                flash('info', "Development: Reset token is $token");
            }
        }
        
        flash('success', 'If an account exists with that email, you will receive a password reset link.');
        redirect('/auth');
    }
    
    /**
     * Show reset password form
     */
    public function showResetPassword(): void {
        requireGuest();
        
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            flash('error', 'Invalid reset link');
            redirect('/auth');
        }
        
        $user = User::findByResetToken($token);
        
        if (!$user) {
            flash('error', 'Invalid or expired reset link');
            redirect('/auth');
        }
        
        $this->render('auth/reset-password', [
            'title' => 'Reset Password - Gema∞',
            'token' => $token
        ]);
    }
    
    /**
     * Handle password reset
     */
    public function resetPassword(): void {
        requireGuest();
        requireCsrf();
        
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        if (strlen($password) < 8) {
            flash('error', 'Password must be at least 8 characters');
            redirect('/auth/reset-password?token=' . $token);
        }
        
        if ($password !== $passwordConfirm) {
            flash('error', 'Passwords do not match');
            redirect('/auth/reset-password?token=' . $token);
        }
        
        if (!User::resetPasswordWithToken($token, $password)) {
            flash('error', 'Invalid or expired reset link');
            redirect('/auth');
        }
        
        flash('success', 'Password has been reset. You can now log in.');
        redirect('/auth');
    }
}
