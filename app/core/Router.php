<?php

namespace App\Core;

use Exception;

/**
 * کلاس مسیریاب ساده برای مدیریت URL ها
 */
class Router {
    protected $routes = [
        'GET' => [],
        'POST' => []
    ];

    /**
     * ثبت یک مسیر GET
     */
    public function get($uri, $action) {
        $this->routes['GET'][$this->formatUri($uri)] = $action;
    }

    /**
     * ثبت یک مسیر POST
     */
    public function post($uri, $action) {
        $this->routes['POST'][$this->formatUri($uri)] = $action;
    }

    /**
     * فرمت کردن URI برای حذف اسلش‌های اضافی
     */
    private function formatUri($uri) {
        return trim($uri, '/');
    }

    /**
     * پیدا کردن و اجرای کنترلر مربوط به درخواست فعلی
     */
    public function dispatch() {
        $uri = $this->getUri();
        $method = $_SERVER['REQUEST_METHOD'];

        if (array_key_exists($uri, $this->routes[$method])) {
            $action = $this->routes[$method][$uri];
            $this->callAction(...$action);
            return;
        }

        throw new Exception('No route defined for this URI.');
    }

    /**
     * دریافت URI از درخواست
     */
    protected function getUri() {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        // Remove the base directory from the URI if the app is in a subdirectory
        $baseUrl = trim(parse_url(APP_URL, PHP_URL_PATH), '/');
        if (strpos($uri, $baseUrl) === 0) {
            $uri = substr($uri, strlen($baseUrl));
        }
        return trim($uri, '/');
    }

    /**
     * فراخوانی متد کنترلر
     */
    protected function callAction($controller, $method) {
        if (!class_exists($controller)) {
            throw new Exception("Controller {$controller} does not exist.");
        }

        $controllerInstance = new $controller();

        if (!method_exists($controllerInstance, $method)) {
            throw new Exception("Method {$method} does not exist in controller {$controller}.");
        }

        // Call the controller method
        $controllerInstance->$method();
    }
}
