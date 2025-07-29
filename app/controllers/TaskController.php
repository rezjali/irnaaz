<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class TaskController extends Controller {

    private $orderModel;
    private $userModel;

    public function __construct() {
        Auth::authenticate();
        $this->orderModel = $this->model('Order');
        $this->userModel = $this->model('User');
    }

    /**
     * Display the task assignment page.
     * It shows orders that are ready to be assigned.
     */
    public function index() {
        $data = [
            'title' => 'تفکیک کار',
            'assignable_orders' => $this->orderModel->getOrdersForAssignment(),
            // Fetch users with the 'purchaser' role to assign tasks to them.
            'purchasers' => $this->userModel->getUsersByRole('purchaser')
        ];
        $this->view('tasks/index', $data);
    }

    /**
     * Assign an order to a user.
     */
    public function assign() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_id = (int)($_POST['order_id'] ?? 0);
            $user_id = (int)($_POST['user_id'] ?? 0);

            if ($order_id && $user_id) {
                // Assign the order and potentially update its status
                $this->orderModel->assignOrderToUser($order_id, $user_id);
            }
        }
        redirect('tasks');
    }
}
