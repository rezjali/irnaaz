<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class AuthController extends Controller {

    /**
     * نمایش فرم ورود
     */
    public function showLoginForm() {
        Auth::guest();
        require_once TEMPLATES_PATH . '/pages/auth/login.phtml';
    }

    /**
     * پردازش درخواست ورود
     */
    public function login() {
        Auth::guest();

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            set_flash_message('error', 'نام کاربری و رمز عبور الزامی است.');
            redirect('login');
            return;
        }

        if (Auth::attempt($username, $password)) {
            // Login successful
            redirect(''); // Redirect to dashboard
        } else {
            // Login failed
            set_flash_message('error', 'نام کاربری یا رمز عبور اشتباه است.');
            redirect('login');
        }
    }

    /**
     * خروج کاربر
     */
    public function logout() {
        Auth::logout();
        redirect('login');
    }
}
