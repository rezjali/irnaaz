<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class ReceiptController extends Controller {

    private $financialModel;

    public function __construct() {
        Auth::authenticate();
        $this->financialModel = $this->model('Financial');
    }

    /**
     * Display product-related receipts.
     */
    public function productReceipts() {
        $this->displayReceipts('product', 'فیش‌های واریزی کالا');
    }

    /**
     * Display shipping-related receipts.
     */
    public function shippingReceipts() {
        $this->displayReceipts('shipping', 'فیش‌های واریزی باربری');
    }

    /**
     * A private helper method to display receipts based on wallet type.
     */
    private function displayReceipts($walletType, $title) {
        $filters = ['wallet_type' => $walletType];
        $data = [
            'title' => $title,
            'receipts' => $this->financialModel->getPendingReceipts($filters)
        ];
        $this->view('financial/receipts', $data);
    }

    public function process() {
        // ... (existing code for processing receipts)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/');
        $receipt_id = (int)($_POST['receipt_id'] ?? 0);
        $action = $_POST['action'] ?? '';
        $admin_id = $_SESSION['user_id'];
        if ($receipt_id && ($action === 'approve' || $action === 'reject')) {
            $this->financialModel->processReceipt($receipt_id, $action, $admin_id);
        }
        // Redirect back to the previous page
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
