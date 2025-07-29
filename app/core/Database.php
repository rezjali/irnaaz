<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * کلاس مدیریت پایگاه داده با استفاده از PDO
 * از الگوی Singleton برای تضمین یک اتصال در هر درخواست استفاده می‌کند.
 */
class Database {
    private static $instance = null;
    private $pdo;
    private $stmt;
    private $error;

    private function __construct() {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            // In production, log this error instead of echoing
            die('Database Connection Error: ' . $this->error);
        }
    }

    /**
     * دریافت یک نمونه از کلاس دیتابیس (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * آماده‌سازی کوئری SQL
     */
    public function query($sql) {
        $this->stmt = $this->pdo->prepare($sql);
    }

    /**
     * بایند کردن مقادیر به کوئری
     */
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * اجرای کوئری آماده شده
     */
    public function execute() {
        try {
            return $this->stmt->execute();
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            // Log error
            error_log('SQL Error: ' . $this->error);
            return false;
        }
    }

    /**
     * دریافت تمام نتایج به صورت آرایه‌ای از اشیاء
     */
    public function fetchAll() {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    /**
     * دریافت یک نتیجه به صورت یک شیء
     */
    public function fetch() {
        $this->execute();
        return $this->stmt->fetch();
    }

    /**
     * دریافت تعداد ردیف‌های تحت تاثیر قرار گرفته
     */
    public function rowCount() {
        return $this->stmt->rowCount();
    }

    /**
     * دریافت آخرین ID وارد شده
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}
