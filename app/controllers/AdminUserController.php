<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class AdminUserController extends Controller {

    private $userModel;

    public function __construct() {
        Auth::authenticate();
        $this->userModel = $this->model('User');
    }

    public function index() {
        $data = [
            'title' => 'مدیریت مدیران میانی',
            'admin_users' => $this->userModel->getAdminUsers()
        ];
        $this->view('admins/index', $data);
    }
    
    public function create() {
        $roleModel = $this->model('Role');
        $data = [
            'title' => 'افزودن مدیر جدید',
            'roles' => $roleModel->getAdminRoles() // Get non-customer roles
        ];
        $this->view('admins/create', $data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'full_name' => $_POST['full_name'],
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'role_id' => (int)$_POST['role_id'],
                'phone' => $_POST['phone']
            ];
            $this->userModel->createUser($data);
        }
        redirect('admins');
    }
}
