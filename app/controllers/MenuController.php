<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class MenuController extends Controller {

    private $menuModel;

    public function __construct() {
        Auth::authenticate();
        $this->menuModel = $this->model('Menu');
    }

    /**
     * Display menu groups management page.
     */
    public function groups() {
        $data = [
            'title' => 'دسته منو',
            'groups' => $this->menuModel->getAllGroups()
        ];
        $this->view('menus/groups', $data);
    }

    /**
     * Display menu items management page for a specific group.
     */
    public function items() {
        $group_id = (int)($_GET['group_id'] ?? 1); // Default to the first group
        $data = [
            'title' => 'منو ها',
            'groups' => $this->menuModel->getAllGroups(),
            'current_group_id' => $group_id,
            'menu_items' => $this->menuModel->getItemsByGroup($group_id)
        ];
        $this->view('menus/items', $data);
    }

    /**
     * Store a new menu item.
     */
    public function storeItem() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'group_id' => (int)$_POST['group_id'],
                'title' => $_POST['title'],
                'url' => $_POST['url']
            ];
            $this->menuModel->createItem($data);
        }
        redirect('menus?group_id=' . $data['group_id']);
    }
}
