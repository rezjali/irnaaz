<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class AnnouncementController extends Controller {

    private $announcementModel;

    public function __construct() {
        Auth::authenticate();
        $this->announcementModel = $this->model('Announcement');
    }

    /**
     * Display announcement management for admins.
     */
    public function index() {
        // Add role check here for security
        $data = ['title' => 'ارسال اطلاعیه', 'announcements' => $this->announcementModel->getAll()];
        $this->view('announcements/index', $data);
    }

    /**
     * Store a new announcement.
     */
    public function store() {
        // ... (existing code)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['title']) && !empty($_POST['content'])) {
            $this->announcementModel->create(['title' => $_POST['title'], 'content' => $_POST['content'], 'author_id' => $_SESSION['user_id']]);
        }
        redirect('announcements');
    }

    /**
     * Display all announcements for the current user.
     */
    public function userIndex() {
        $user_id = $_SESSION['user_id'];
        $data = [
            'title' => 'صندوق اطلاعیه‌ها',
            'announcements' => $this->announcementModel->getAllForUser($user_id)
        ];
        $this->view('announcements/user_index', $data);
    }

    /**
     * Mark all announcements as read for the current user.
     */
    public function markAllAsRead() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->announcementModel->markAllAsReadForUser($_SESSION['user_id']);
        }
        // Redirect back to the previous page
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
