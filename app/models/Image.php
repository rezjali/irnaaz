<?php

namespace App\Models;

use App\Core\Database;

class Image {
    private $db;
    private $uploadDir = PUBLIC_PATH . '/uploads/';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $this->db->query("SELECT i.*, u.full_name as uploader_name FROM images i JOIN users u ON i.uploader_id = u.id ORDER BY i.uploaded_at DESC");
        return $this->db->fetchAll();
    }

    public function uploadImage($file, $uploader_id) {
        // Create a unique file name to prevent overwriting
        $fileName = time() . '_' . basename($file['name']);
        $targetPath = $this->uploadDir . $fileName;
        $dbPath = 'uploads/' . $fileName; // Relative path for DB

        // Check if upload directory exists
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        // Move the file to the uploads directory
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Save file info to the database
            $this->db->query("INSERT INTO images (uploader_id, file_name, file_path) VALUES (:uploader_id, :file_name, :file_path)");
            $this->db->bind(':uploader_id', $uploader_id);
            $this->db->bind(':file_name', $fileName);
            $this->db->bind(':file_path', $dbPath);
            return $this->db->execute();
        }
        return false;
    }

    public function deleteImage($id) {
        // First, get the file path from DB
        $this->db->query("SELECT file_path FROM images WHERE id = :id");
        $this->db->bind(':id', $id);
        $image = $this->db->fetch();

        if ($image) {
            $filePath = PUBLIC_PATH . '/' . $image->file_path;
            // Delete the file from the server
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            // Delete the record from the database
            $this->db->query("DELETE FROM images WHERE id = :id");
            $this->db->bind(':id', $id);
            return $this->db->execute();
        }
        return false;
    }
}
