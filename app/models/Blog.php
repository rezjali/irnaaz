<?php

namespace App\Models;

use App\Core\Database;

class Blog {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // --- Post Methods ---
    public function getAllPosts() {
        $this->db->query("SELECT p.id, p.title, p.status, p.updated_at, c.name as category_name, u.full_name as author_name FROM blog_posts p JOIN blog_categories c ON p.category_id = c.id JOIN users u ON p.author_id = u.id ORDER BY p.updated_at DESC");
        return $this->db->fetchAll();
    }

    public function createPost($data) {
        $this->db->query("INSERT INTO blog_posts (title, slug, category_id, content, status, author_id) VALUES (:title, :slug, :category_id, :content, :status, :author_id)");
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':author_id', $data['author_id']);
        return $this->db->execute();
    }

    // --- Category Methods ---
    public function getAllCategories() {
        $this->db->query("SELECT * FROM blog_categories ORDER BY name ASC");
        return $this->db->fetchAll();
    }

    public function createCategory($name, $slug) {
        $this->db->query("INSERT INTO blog_categories (name, slug) VALUES (:name, :slug)");
        $this->db->bind(':name', $name);
        $this->db->bind(':slug', $slug);
        return $this->db->execute();
    }
}
