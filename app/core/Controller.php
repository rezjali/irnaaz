<?php

namespace App\Core;

/**
 * کلاس پایه کنترلر
 * سایر کنترلرها از این کلاس ارث‌بری می‌کنند.
 */
abstract class Controller {
    
    /**
     * بارگذاری یک فایل view (قالب) و ارسال داده به آن
     * @param string $view نام فایل view در پوشه pages
     * @param array $data داده‌هایی که به view ارسال می‌شوند
     */
    protected function view($view, $data = []) {
        // تبدیل کلیدهای آرایه به متغیر
        extract($data);

        $viewPath = TEMPLATES_PATH . '/pages/' . $view . '.phtml';

        if (file_exists($viewPath)) {
            // شروع بافر خروجی
            ob_start();
            
            // بارگذاری فایل view
            require $viewPath;
            
            // دریافت محتوای بافر و پاک کردن آن
            $content = ob_get_clean();

            // بارگذاری قالب اصلی و تزریق محتوا به آن
            require_once TEMPLATES_PATH . '/layouts/app.php';
        } else {
            // اگر فایل view وجود نداشت
            die("View '{$view}' not found.");
        }
    }
    
    /**
     * بارگذاری یک مدل
     * @param string $model نام کلاس مدل
     * @return object
     */
    protected function model($model) {
        $modelClass = 'App\\Models\\' . $model;
        if (class_exists($modelClass)) {
            return new $modelClass();
        } else {
            die("Model '{$model}' not found.");
        }
    }
}
