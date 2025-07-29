<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class OrderController extends Controller {

    private $orderModel;
    private $userModel;

    public function __construct() {
        Auth::authenticate();
        $this->orderModel = $this->model('Order');
        $this->userModel = $this->model('User');
    }

    /**
     * Display active orders.
     */
    public function index() {
        $this->displayOrders('active', 'مدیریت سفارشات');
    }

    /**
     * Display cancelled orders.
     */
    public function cancelledIndex() {
        $this->displayOrders('cancelled', 'سفارشات کنسل شده');
    }

    /**
     * Display deleted orders.
     */
    public function deletedIndex() {
        $this->displayOrders('deleted', 'سفارشات حذف شده');
    }

    /**
     * Display suspended orders.
     */
    public function suspendedIndex() {
        $this->displayOrders('suspended', 'سفارشات معلق شده');
    }

    /**
     * Generic method to display orders based on category.
     */
    private function displayOrders($category, $title) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 15;
        $filters = [
            'search' => $_GET['search'] ?? null,
            'status_id' => isset($_GET['status_id']) && $_GET['status_id'] !== '' ? (int)$_GET['status_id'] : null,
            'category' => $category
        ];

        $orders = $this->orderModel->getAll($page, $perPage, $filters);
        $totalOrders = $this->orderModel->getTotalCount($filters);
        $statuses = $this->orderModel->getStatusesByCategory($category);
        $totalPages = ceil($totalOrders / $perPage);

        if (!empty($filters['status_id'])) {
            foreach ($statuses as $status) {
                if ($status->id == $filters['status_id']) {
                    $title = $status->status_name;
                    break;
                }
            }
        }

        $data = [
            'title' => $title,
            'orders' => $orders,
            'statuses' => $statuses,
            'pagination' => ['current_page' => $page, 'total_pages' => $totalPages, 'per_page' => $perPage, 'total_items' => $totalOrders],
            'current_category' => $category
        ];
        $this->view('orders/index', $data);
    }
    
    /**
     * Show the form for creating a new order.
     */
    public function create() {
        $data = [
            'title' => 'ثبت سفارش جدید',
            'customers' => $this->userModel->getAllCustomers()
        ];
        $this->view('orders/create', $data);
    }

    /**
     * Store a new order in the database.
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('orders');
        }

        if (empty($_POST['user_id']) || empty($_POST['product_link']) || empty($_POST['price_try'])) {
            redirect('orders/create');
            return;
        }

        $data = [
            'user_id' => (int)$_POST['user_id'],
            'product_link' => $_POST['product_link'],
            'product_details' => json_encode(['size' => $_POST['size'] ?? '', 'color' => $_POST['color'] ?? '', 'quantity' => $_POST['quantity'] ?? 1]),
            'price_try' => (float)$_POST['price_try'],
            'exchange_rate' => (float)$_POST['exchange_rate'],
            'commission_fee' => (float)$_POST['commission_fee'],
            'shipping_cost' => (float)$_POST['shipping_cost'],
            'status_id' => 1 // Default status: 'ثبت شد'
        ];
        
        $total_try = $data['price_try'] + ($data['price_try'] * ($data['commission_fee'] / 100));
        $data['total_cost'] = ($total_try * $data['exchange_rate']) + $data['shipping_cost'];

        if ($this->orderModel->createOrder($data)) {
            redirect('orders');
        } else {
            redirect('orders/create');
        }
    }
}
