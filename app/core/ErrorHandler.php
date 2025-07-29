<?php

namespace App\Core;

/**
 * کلاس مدیریت خطا و استثنا
 * تمام خطاها را گرفته و در یک فایل لاگ ثبت می‌کند.
 */
class ErrorHandler {

    /**
     * Register the error and exception handlers.
     */
    public static function register() {
        // Turn off error display to the user in production
        ini_set('display_errors', '0');
        ini_set('log_errors', '1');

        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
    }

    /**
     * Custom error handler.
     * Converts errors to ErrorException and logs them.
     */
    public static function handleError($level, $message, $file, $line) {
        if (error_reporting() !== 0) {
            // This error is not suppressed by an @ operator
            self::logError($level, $message, $file, $line);
        }
        // Don't execute PHP internal error handler
        return true;
    }

    /**
     * Custom exception handler.
     * Logs uncaught exceptions.
     */
    public static function handleException(\Throwable $exception) {
        self::logError(
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        // Show a generic error page to the user
        self::showErrorPage();
    }

    /**
     * Logs the error details to a file.
     */
    protected static function logError($level, $message, $file, $line) {
        $logPath = LOG_PATH . '/' . date('Y-m-d') . '.log';
        $logMessage = sprintf(
            "[%s] [%s] %s in %s on line %d" . PHP_EOL,
            date('Y-m-d H:i:s'),
            $level,
            $message,
            $file,
            $line
        );

        // Append the message to the log file
        error_log($logMessage, 3, $logPath);
    }

    /**
     * Displays a user-friendly error page.
     */
    protected static function showErrorPage() {
        // In production, you don't want to show detailed errors.
        if (error_reporting() !== 0) { // If in development mode
            http_response_code(500);
            echo "<h1>An error occurred</h1>";
            echo "<p>Something went wrong. Please check the log files for more details.</p>";
        } else {
            http_response_code(500);
            // You can create a nice HTML page for this
            echo "<h1>خطایی رخ داده است</h1>";
            echo "<p>مشکلی در سیستم به وجود آمده است. لطفاً بعداً تلاش کنید.</p>";
        }
    }
}
