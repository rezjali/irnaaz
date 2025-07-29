<?php

/**
 * ---------------------------------------------------------------
 * HELPER FUNCTIONS
 * ---------------------------------------------------------------
 */

/**
 * Redirect to a specific page.
 * This version includes session_write_close() to ensure the session is saved
 * before the redirect happens, fixing potential login loop issues.
 */
function redirect($path) {
    // Ensure all session data is written before we redirect
    session_write_close();
    
    header('Location: ' . APP_URL . '/' . trim($path, '/'));
    exit();
}

/**
 * Sanitize input data.
 */
function e($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Set a flash message in the session.
 */
function set_flash_message($key, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'][$key] = $message;
}

/**
 * Get and display a flash message.
 */
function display_flash_message($key) {
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        
        $color = ($key === 'error') ? 'red' : 'green';
        
        echo "<div class='bg-{$color}-100 border border-{$color}-400 text-{$color}-700 px-4 py-3 rounded relative mb-4' role='alert'>
                <span class='block sm:inline'>{$message}</span>
              </div>";
    }
}
