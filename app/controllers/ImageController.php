<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class ImageController extends Controller {

    private $imageModel;

    public function __construct() {
        Auth::authenticate();
        $this->imageModel = $this->model('Image');
    }

    /**
     * Display the image gallery.
     */
    public function index() {
        $data = [
            'title' => 'مدیریت تصاویر',
            'images' => $this->imageModel->getAll()
        ];
        $this->view('images/index', $data);
    }

    /**
     * Handle image upload.
     */
    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
            $file = $_FILES['image'];
            // Basic validation
            if ($file['error'] === 0 && $file['size'] > 0) {
                $this->imageModel->uploadImage($file, $_SESSION['user_id']);
            }
        }
        redirect('images');
    }

    /**
     * Handle image deletion.
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $this->imageModel->deleteImage((int)$_POST['id']);
        }
        redirect('images');
    }
}
