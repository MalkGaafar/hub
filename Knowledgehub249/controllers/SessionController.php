<?php
class SessionController {
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function login($userId, $username, $isAdmin = false) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['is_admin'] = $isAdmin ? 1 : 0;
        $_SESSION['login_time'] = time();
        
        // Store language preference, default to Arabic
        if (!isset($_SESSION['language'])) {
            $_SESSION['language'] = 'ar';
        }
        
        // Set cookie if remember me is checked
        if (isset($_POST['remember_me']) && $_POST['remember_me'] == 'on') {
            $cookie_value = base64_encode($userId . ':' . $username);
            setcookie('user_remember', $cookie_value, time() + (86400 * 30), '/'); // 30 days
        }
    }
    
    public function logout() {
        // Clear cookies
        if (isset($_COOKIE['user_remember'])) {
            setcookie('user_remember', '', time() - 3600, '/');
        }
        
        // Clear session
        session_unset();
        session_destroy();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function isAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }
    
    public function getCurrentUserId() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }
    
    public function setLanguage($lang) {
        $_SESSION['language'] = $lang;
    }
    
    public function getLanguage() {
        return isset($_SESSION['language']) ? $_SESSION['language'] : 'ar';
    }
    
    public function getDirection() {
        return $this->getLanguage() === 'ar' ? 'rtl' : 'ltr';
    }
    
    public function checkRememberMeCookie() {
        if (!$this->isLoggedIn() && isset($_COOKIE['user_remember'])) {
            $cookie_data = base64_decode($_COOKIE['user_remember']);
            list($userId, $username) = explode(':', $cookie_data);
            
            // Re-login user
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            
            // Get admin status from database
            // Here you would need to check the database for admin status
            // This is a placeholder - in a real implementation you'd query the database
        }
    }
}
?>
