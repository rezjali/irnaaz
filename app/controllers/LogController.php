<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class LogController extends Controller {

    private $logModel;

    public function __construct() {
        Auth::authenticate();
        // Add role check here for security
        $this->logModel = $this->model('Log');
    }

    /**
     * Display the visitor logs page.
     */
    public function visitorLogs() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        $logs = $this->logModel->getVisitorLogs($page, $perPage);
        $total = $this->logModel->getVisitorLogCount();
        $totalPages = ceil($total / $perPage);

        $data = [
            'title' => 'آمار بازدید',
            'logs' => $logs,
            'pagination' => ['current_page' => $page, 'total_pages' => $totalPages, 'per_page' => $perPage, 'total_items' => $total]
        ];
        $this->view('logs/visitor_logs', $data);
    }

    /**
     * Display the user activity logs page.
     */
    public function activityLogs() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        $logs = $this->logModel->getActivityLogs($page, $perPage);
        $total = $this->logModel->getActivityLogCount();
        $totalPages = ceil($total / $perPage);

        $data = [
            'title' => 'مدیریت لاگ فعالیت‌ها',
            'logs' => $logs,
            'pagination' => ['current_page' => $page, 'total_pages' => $totalPages, 'per_page' => $perPage, 'total_items' => $total]
        ];
        $this->view('logs/activity_logs', $data);
    }
}
