<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class FinancialController extends Controller {

    private $financialModel;
    private $userModel;

    public function __construct() {
        Auth::authenticate();
        $this->financialModel = $this->model('Financial');
        $this->userModel = $this->model('User');
    }

    public function productTransactions() { $this->displayTransactions('product', 'تراکنش‌های کالا'); }
    public function shippingTransactions() { $this->displayTransactions('shipping', 'تراکنش‌های باربری'); }

    private function displayTransactions($walletType, $title) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 15;
        $filters = ['wallet_type' => $walletType];
        $transactions = $this->financialModel->getAllTransactions($page, $perPage, $filters);
        $totalTransactions = $this->financialModel->getTotalTransactionCount($filters);
        $totalPages = ceil($totalTransactions / $perPage);
        $data = [
            'title' => $title,
            'transactions' => $transactions,
            'pagination' => ['current_page' => $page, 'total_pages' => $totalPages, 'per_page' => $perPage, 'total_items' => $totalTransactions]
        ];
        $this->view('financial/index', $data);
    }

    public function productWallets() { $this->displayWallets('product', 'کیف پول کالا'); }
    public function shippingWallets() { $this->displayWallets('shipping', 'کیف پول باربری'); }

    private function displayWallets($walletType, $title) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        $users = $this->userModel->getUsersWithWallets($page, $perPage);
        $totalUsers = $this->userModel->getTotalCount(['is_customer' => true]);
        $totalPages = ceil($totalUsers / $perPage);
        $data = [
            'title' => $title,
            'users' => $users,
            'wallet_type' => $walletType,
            'pagination' => ['current_page' => $page, 'total_pages' => $totalPages, 'per_page' => $perPage, 'total_items' => $totalUsers]
        ];
        $this->view('financial/wallets', $data);
    }

    public function salesStats() {
        $currentYear = jdate('Y', '', '', '', 'en');
        $data = [
            'title' => 'آمار فروش',
            'stats' => [
                'total_revenue' => $this->financialModel->getTotalSalesRevenue(),
                'monthly_revenue' => $this->financialModel->getSalesRevenueForPeriod('month'),
                'yearly_revenue' => $this->financialModel->getSalesRevenueForPeriod('year'),
                'average_order_value' => $this->financialModel->getAverageOrderValue()
            ],
            'monthly_sales_chart' => $this->financialModel->getMonthlySalesForYear($currentYear)
        ];
        $this->view('financial/sales_stats', $data);
    }

    public function debtorsList() {
        $data = ['title' => 'لیست بدهکاران', 'debtors' => $this->financialModel->getDebtors()];
        $this->view('financial/debtors_list', $data);
    }

    public function assignCredit() {
        $data = [
            'title' => 'اختصاص اعتبار به کاربر',
            'customers' => $this->userModel->getAllCustomers(),
            'preselected_user_id' => isset($_GET['user_id']) ? (int)$_GET['user_id'] : null
        ];
        $this->view('financial/assign_credit', $data);
    }

    public function storeCredit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/');
        $data = [
            'user_id' => (int)($_POST['user_id'] ?? 0),
            'amount' => (float)($_POST['amount'] ?? 0),
            'wallet_type' => $_POST['wallet_type'] ?? '',
            'description' => $_POST['description'] ?? 'اختصاص اعتبار دستی توسط مدیر',
            'transaction_type' => ($_POST['amount'] >= 0) ? 'deposit' : 'withdrawal'
        ];
        if ($data['transaction_type'] === 'withdrawal') $data['amount'] = abs($data['amount']);
        if (empty($data['user_id']) || empty($data['wallet_type']) || !isset($_POST['amount'])) {
            redirect('financial/assign-credit');
            return;
        }
        if ($this->financialModel->addManualTransaction($data)) redirect('financial/transactions/' . $data['wallet_type']);
        else redirect('financial/assign-credit');
    }
}
