<?php
/**
 * Base Controller
 */

if (!defined('GEMA8')) {
    die('Direct access not permitted');
}

class Controller {
    /**
     * Render a view
     */
    protected function view(string $view, array $data = []): void {
        extract($data);
        
        // Common data available in all views
        $user = currentUser();
        $profile = currentProfile();
        $csrfToken = csrfToken();
        
        global $SUPPORTED_LANGUAGES;
        $supportedLanguages = $SUPPORTED_LANGUAGES;
        
        require ROOT_PATH . '/views/' . str_replace('.', '/', $view) . '.php';
    }
    
    /**
     * Render view with layout
     */
    protected function render(string $view, array $data = [], string $layout = 'layouts/main'): void {
        // Capture view content
        extract($data);
        
        // Common data
        $user = currentUser();
        $profile = currentProfile();
        $csrfToken = csrfToken();
        
        global $SUPPORTED_LANGUAGES;
        $supportedLanguages = $SUPPORTED_LANGUAGES;
        
        ob_start();
        require ROOT_PATH . '/views/' . str_replace('.', '/', $view) . '.php';
        $content = ob_get_clean();
        
        // Render layout with content
        require ROOT_PATH . '/views/' . str_replace('.', '/', $layout) . '.php';
    }
    
    /**
     * Return JSON response
     */
    protected function json(array $data, int $status = 200): void {
        jsonResponse($data, $status);
    }
    
    /**
     * Validate required fields
     */
    protected function validate(array $rules): array {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $_POST[$field] ?? '';
            $fieldRules = explode('|', $fieldRules);
            
            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && empty(trim($value))) {
                    $errors[$field] = ucfirst($field) . ' is required';
                    break;
                }
                
                if ($rule === 'email' && !isValidEmail($value)) {
                    $errors[$field] = 'Please enter a valid email';
                    break;
                }
                
                if (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field] = ucfirst($field) . " must be at least $min characters";
                        break;
                    }
                }
                
                if (strpos($rule, 'max:') === 0) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field] = ucfirst($field) . " must not exceed $max characters";
                        break;
                    }
                }
            }
        }
        
        return $errors;
    }
}
