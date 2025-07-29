<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class UserController extends Controller {

    private $userModel;

    public function __construct() {
        Auth::authenticate();
        $this->userModel = $this->model('User');
    }

    /**
     * Display a list of all users with pagination.
     */
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 15;
        $users = $this->userModel->getAll($page, $perPage);
        $totalUsers = $this->userModel->getTotalCount();
        $totalPages = ceil($totalUsers / $perPage);
        $data = [
            'title' => 'مدیریت کاربران',
            'users' => $users,
            'pagination' => ['current_page' => $page, 'total_pages' => $totalPages, 'per_page' => $perPage, 'total_items' => $totalUsers]
        ];
        $this->view('users/index', $data);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create() {
        $roleModel = $this->model('Role');
        $data = [
            'title' => 'افزودن کاربر جدید',
            'roles' => $roleModel->getAll()
        ];
        $this->view('users/create', $data);
    }

    /**
     * Store a new user in the database.
     */
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
            if ($this->userModel->createUser($data)) {
                set_flash_message('success', 'کاربر جدید با موفقیت ایجاد شد.');
            }
        }
        redirect('users');
    }

    /**
     * Show the form for editing an existing user.
     */
    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) redirect('users');
        
        $user = $this->userModel->findById($id);
        if (!$user) redirect('users');

        $roleModel = $this->model('Role');
        $data = [
            'title' => 'ویرایش کاربر: ' . e($user->full_name),
            'user' => $user,
            'roles' => $roleModel->getAll()
        ];
        $this->view('users/edit', $data);
    }

    /**
     * Update an existing user's data.
     */
    public function updateUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['user_id'] ?? 0);
            $data = [
                'id' => $id,
                'full_name' => $_POST['full_name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'role_id' => (int)($_POST['role_id'] ?? 0),
                'password' => $_POST['password'] ?? ''
            ];
            if ($this->userModel->update($data)) {
                 set_flash_message('success', 'اطلاعات کاربر با موفقیت به‌روز شد.');
            }
        }
        redirect('users/edit?id=' . $id);
    }


    /**
     * Show the user's profile page.
     */
    public function profile() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        $data = [
            'title' => 'پروفایل کاربری',
            'user' => $user
        ];
        $this->view('users/profile', $data);
    }

    /**
     * Update the user's profile.
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id' => $_SESSION['user_id'],
                'full_name' => $_POST['full_name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'password' => $_POST['password'] ?? ''
            ];
            if ($this->userModel->update($data)) {
                set_flash_message('success', 'پروفایل شما با موفقیت به‌روز شد.');
            }
        }
        redirect('profile');
    }
}
