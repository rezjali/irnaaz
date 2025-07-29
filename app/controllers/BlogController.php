<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class BlogController extends Controller {

    private $blogModel;

    public function __construct() {
        Auth::authenticate();
        $this->blogModel = $this->model('Blog');
    }

    /**
     * Display a list of all blog posts.
     */
    public function index() {
        $data = [
            'title' => 'مدیریت وبلاگ',
            'posts' => $this->blogModel->getAllPosts()
        ];
        $this->view('blog/index', $data);
    }

    /**
     * Show the form for creating a new post.
     */
    public function create() {
        $data = [
            'title' => 'ایجاد نوشته جدید',
            'categories' => $this->blogModel->getAllCategories(),
            'post' => null // For reusing the form in edit mode
        ];
        $this->view('blog/create_edit', $data);
    }

    /**
     * Store a new post.
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Basic slug generation
            $slug = str_replace(' ', '-', trim(strtolower($_POST['title'])));

            $data = [
                'title' => $_POST['title'],
                'slug' => $slug,
                'category_id' => (int)$_POST['category_id'],
                'content' => $_POST['content'],
                'status' => $_POST['status'],
                'author_id' => $_SESSION['user_id']
            ];
            $this->blogModel->createPost($data);
        }
        redirect('blog');
    }
    
    /**
     * Display the category management page.
     */
    public function categories() {
        $data = [
            'title' => 'مدیریت دسته‌بندی‌های وبلاگ',
            'categories' => $this->blogModel->getAllCategories()
        ];
        $this->view('blog/categories', $data);
    }

    /**
     * Store a new category.
     */
    public function storeCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
            $slug = str_replace(' ', '-', trim(strtolower($_POST['name'])));
            $this->blogModel->createCategory($_POST['name'], $slug);
        }
        redirect('blog/categories');
    }
}
