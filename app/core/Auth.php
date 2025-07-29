<?php

namespace App\Core;

/**
 * کلاس مدیریت احراز هویت و نشست‌ها
 */
class Auth {
    
    /**
     * بررسی اینکه آیا کاربر وارد شده است یا خیر
     */
    public static function check() {
        return isset($_SESSION['user_id']);
    }

    /**
     * دریافت اطلاعات کاربر وارد شده
     */
    public static function user() {
        if (self::check()) {
            $db = Database::getInstance();
            $db->query("SELECT id, full_name, username, email, role_id FROM users WHERE id = :id");
            $db->bind(':id', $_SESSION['user_id']);
            return $db->fetch();
        }
        return null;
    }

    /**
     * تلاش برای ورود کاربر
     */
    public static function attempt($username, $password) {
        $db = Database::getInstance();
        
        // --- FIX: Use two different placeholders or bind twice ---
        $db->query("SELECT * FROM users WHERE (username = :username OR email = :email) AND is_active = 1");
        $db->bind(':username', $username);
        $db->bind(':email', $username); // Bind the same value to the second placeholder
        
        $user = $db->fetch();

        if ($user && password_verify($password, $user->password)) {
            // Set session variables
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;
            $_SESSION['role_id'] = $user->role_id;
            session_regenerate_id(true); // Prevent session fixation
            return true;
        }

        return false;
    }

    /**
     * خروج کاربر از سیستم
     */
    public static function logout() {
        session_unset();
        session_destroy();
    }

    /**
     * هدایت کاربر به صفحه ورود اگر لاگین نکرده باشد
     */
    public static function guest() {
        if (self::check()) {
            redirect(''); // Redirect to dashboard
        }
    }
    
    /**
     * هدایت کاربر به داشبورد اگر لاگین نکرده باشد
     */
    public static function authenticate() {
        if (!self::check()) {
            redirect('login');
        }
    }
}
