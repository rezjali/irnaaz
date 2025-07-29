<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class ConstantController extends Controller {

    private $constantModel;

    public function __construct() {
        Auth::authenticate();
        $this->constantModel = $this->model('Constant');
    }

    // --- Order Statuses ---
    public function orderStatuses() {
        $data = ['title' => 'مدیریت وضعیت سفارش', 'statuses' => $this->constantModel->getAllOrderStatuses()];
        $this->view('constants/order_statuses', $data);
    }
    public function storeOrderStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['status_name'])) {
            $this->constantModel->addOrderStatus(['status_name' => $_POST['status_name'], 'status_category' => $_POST['status_category']]);
        }
        redirect('constants/order-statuses');
    }
    public function deleteOrderStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
            $this->constantModel->deleteOrderStatus((int)$_POST['id']);
        }
        redirect('constants/order-statuses');
    }

    // --- Shipping Rates ---
    public function shippingRates() {
        $data = ['title' => 'مدیریت نرخ باربری', 'rates' => $this->constantModel->getAllShippingRates()];
        $this->view('constants/shipping_rates', $data);
    }
    public function storeShippingRate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['description']) && isset($_POST['cost'])) {
            $this->constantModel->addShippingRate(['description' => $_POST['description'], 'cost' => (float)$_POST['cost']]);
        }
        redirect('constants/shipping-rates');
    }
    public function deleteShippingRate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
            $this->constantModel->deleteShippingRate((int)$_POST['id']);
        }
        redirect('constants/shipping-rates');
    }

    // --- Source Sites ---
    public function sites() {
        $data = ['title' => 'مدیریت سایت‌ها', 'sites' => $this->constantModel->getAllSites()];
        $this->view('constants/sites', $data);
    }
    public function storeSite() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['site_name']) && !empty($_POST['site_url'])) {
            $this->constantModel->addSite(['site_name' => $_POST['site_name'], 'site_url' => $_POST['site_url']]);
        }
        redirect('constants/sites');
    }
    public function deleteSite() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
            $this->constantModel->deleteSite((int)$_POST['id']);
        }
        redirect('constants/sites');
    }

    // --- Ticket Categories ---
    public function ticketCategories() {
        $data = ['title' => 'دسته‌بندی تیکت', 'categories' => $this->constantModel->getAllTicketCategories()];
        $this->view('constants/ticket_categories', $data);
    }
    public function storeTicketCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
            $this->constantModel->addTicketCategory($_POST['name']);
        }
        redirect('constants/ticket-categories');
    }
    public function deleteTicketCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
            $this->constantModel->deleteTicketCategory((int)$_POST['id']);
        }
        redirect('constants/ticket-categories');
    }
}
