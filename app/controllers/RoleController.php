<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class RoleController extends Controller {

    private $roleModel;

    public function __construct() {
        Auth::authenticate();
        $this->roleModel = $this->model('Role');
    }

    /**
     * Display a list of all roles.
     */
    public function index() {
        $data = [
            'title' => 'مدیریت گروه‌های کاربری',
            'roles' => $this->roleModel->getAll()
        ];
        $this->view('roles/index', $data);
    }

    /**
     * Store a new role in the database.
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['role_name'])) {
            $slug = str_replace(' ', '-', trim(strtolower($_POST['role_name'])));
            if ($this->roleModel->createRole($_POST['role_name'], $slug)) {
                set_flash_message('success', 'گروه جدید با موفقیت ایجاد شد.');
            } else {
                set_flash_message('error', 'خطا در ایجاد گروه جدید.');
            }
        }
        redirect('roles');
    }

    /**
     * Delete a role.
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $role = $this->roleModel->findById($id);

            // Safety check: Do not delete roles that have users assigned.
            if ($role && $role->user_count > 0) {
                set_flash_message('error', 'امکان حذف گروهی که دارای کاربر است، وجود ندارد.');
            } elseif ($role && $role->role_key === 'super_admin') {
                set_flash_message('error', 'امکان حذف گروه مدیر کل وجود ندارد.');
            } else {
                if ($this->roleModel->deleteRole($id)) {
                    set_flash_message('success', 'گروه با موفقیت حذف شد.');
                } else {
                    set_flash_message('error', 'خطا در حذف گروه.');
                }
            }
        }
        redirect('roles');
    }

    /**
     * Show the form for editing a role's permissions.
     */
    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) redirect('roles');

        $role = $this->roleModel->findById($id);
        if (!$role || $role->role_key === 'super_admin') {
            redirect('roles');
        }

        $data = [
            'title' => 'ویرایش دسترسی‌های: ' . $role->role_name,
            'role' => $role,
            'permissions' => $this->roleModel->getAllPermissionsGrouped(),
            'role_permissions' => $this->roleModel->getRolePermissionIds($id)
        ];

        $this->view('roles/edit', $data);
    }

    /**
     * Update a role's permissions.
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('roles');
        }

        $role_id = isset($_POST['role_id']) ? (int)$_POST['role_id'] : 0;
        $permission_ids = $_POST['permissions'] ?? [];

        if (!$role_id) {
            redirect('roles');
        }

        if ($this->roleModel->updatePermissions($role_id, $permission_ids)) {
            set_flash_message('success', 'دسترسی‌ها با موفقیت به‌روز شد.');
            redirect('roles/edit?id=' . $role_id);
        } else {
            redirect('roles/edit?id=' . $role_id);
        }
    }
}
