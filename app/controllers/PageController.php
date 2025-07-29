<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class PageController extends Controller {

    private $pageModel;

    public function __construct() {
        Auth::authenticate();
        // Add role check here for security
        $this->pageModel = $this->model('Page');
    }

    /**
     * Display a list of all editable pages.
     */
    public function index() {
        $data = [
            'title' => 'مدیریت محتوا',
            'pages' => $this->pageModel->getAll()
        ];
        $this->view('pages/index', $data);
    }

    /**
     * Show the form for editing a page.
     */
    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) redirect('pages');

        $page = $this->pageModel->findById($id);
        if (!$page) redirect('pages');

        $data = [
            'title' => 'ویرایش صفحه: ' . e($page->page_title),
            'page' => $page
        ];
        $this->view('pages/edit', $data);
    }

    /**
     * Update the page content.
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['page_id'] ?? 0);
            $content = $_POST['page_content'] ?? '';

            if ($id) {
                $this->pageModel->updatePage($id, $content);
            }
        }
        redirect('pages');
    }
}
