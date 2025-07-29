<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class SuitcaseController extends Controller {

    private $suitcaseModel;
    private $orderModel;

    public function __construct() {
        Auth::authenticate();
        $this->suitcaseModel = $this->model('Suitcase');
        $this->orderModel = $this->model('Order');
    }

    /**
     * Display a list of all suitcases.
     */
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 15;
        $suitcases = $this->suitcaseModel->getAll($page, $perPage);
        $totalSuitcases = $this->suitcaseModel->getTotalCount();
        $totalPages = ceil($totalSuitcases / $perPage);

        $data = [
            'title' => 'مدیریت چمدان',
            'suitcases' => $suitcases,
            'pagination' => ['current_page' => $page, 'total_pages' => $totalPages, 'per_page' => $perPage, 'total_items' => $totalSuitcases]
        ];
        $this->view('suitcases/index', $data);
    }

    /**
     * Show the form for creating a new suitcase.
     */
    public function create() {
        $data = ['title' => 'ایجاد چمدان جدید'];
        $this->view('suitcases/create', $data);
    }

    /**
     * Store a new suitcase.
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['suitcase_code'])) {
            $data = [
                'suitcase_code' => $_POST['suitcase_code'],
                'status' => 'در حال بسته‌بندی',
                'created_by' => $_SESSION['user_id']
            ];
            $this->suitcaseModel->createSuitcase($data);
        }
        redirect('suitcases');
    }

    /**
     * Show a single suitcase and its contents.
     */
    public function show() {
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) redirect('suitcases');

        $suitcase = $this->suitcaseModel->findById($id);
        if (!$suitcase) redirect('suitcases');

        $data = [
            'title' => 'مشاهده چمدان #' . e($suitcase->suitcase_code),
            'suitcase' => $suitcase,
            'assigned_orders' => $this->suitcaseModel->getAssignedOrders($id),
            'unassigned_orders' => $this->orderModel->getUnassignedOrders()
        ];
        $this->view('suitcases/show', $data);
    }

    /**
     * Assign an order to a suitcase.
     */
    public function assignOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $suitcase_id = (int)($_POST['suitcase_id'] ?? 0);
            $order_id = (int)($_POST['order_id'] ?? 0);
            if ($suitcase_id && $order_id) {
                $this->suitcaseModel->assignOrderToSuitcase($order_id, $suitcase_id);
            }
            redirect('suitcases/show?id=' . $suitcase_id);
        }
    }

    /**
     * Remove an order from a suitcase.
     */
    public function removeOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $suitcase_id = (int)($_POST['suitcase_id'] ?? 0);
            $order_id = (int)($_POST['order_id'] ?? 0);
            if ($suitcase_id && $order_id) {
                $this->suitcaseModel->removeOrderFromSuitcase($order_id);
            }
            redirect('suitcases/show?id=' . $suitcase_id);
        }
    }
}
