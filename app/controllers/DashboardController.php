<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class DashboardController extends Controller {

    public function index() {
        Auth::authenticate();

        $orderModel = $this->model('Order');
        $userModel = $this->model('User');
        $financialModel = $this->model('Financial');
        
        /**
         * =================================================================
         * FIX Nهایی: به جای استفاده از jdate()، سال میلادی را با تابع date()
         * دریافت کرده و با تابع gregorian_to_jalali به سال شمسی تبدیل می‌کنیم.
         * این کار تضمین می‌کند که متغیر $currentYear همیشه یک عدد انگلیسی
         * و مناسب برای محاسبات است و از خطای TypeError جلوگیری می‌کند.
         * =================================================================
         */
        list($j_y, $j_m, $j_d) = gregorian_to_jalali(date('Y'), date('m'), date('d'));
        $currentYear = $j_y;

        $data = [
            'title' => 'داشبورد',
            'stats' => [
                'active_orders' => $orderModel->getStatusCount(1), // 'ثبت شد'
                'total_users' => $userModel->getTotalCount(['is_customer' => true]),
                'open_tickets' => 0, // Placeholder
                'total_revenue' => $financialModel->getTotalSalesRevenue()
            ],
            'financial_summary' => $financialModel->getCurrentMonthSummary(),
            'urgent_orders' => $orderModel->getUrgentOrders(),
            'recent_orders' => $orderModel->getRecentOrders(5),
            'recent_transactions' => $financialModel->getRecentTransactions(5),
            'monthly_sales_chart' => $financialModel->getMonthlySalesForYear($currentYear)
        ];

        $this->view('dashboard/index', $data);
    }
}
