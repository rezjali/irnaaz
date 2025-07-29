<?php

namespace App\Models;

use App\Core\Database;

class Page {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get all pages from the database.
     * @return array
     */
    public function getAll() {
        $this->db->query("SELECT id, page_title, page_slug, updated_at FROM pages ORDER BY page_title ASC");
        return $this->db->fetchAll();
    }

    /**
     * Find a single page by its ID.
     * @param int $id
     * @return object|false
     */
    public function findById($id) {
        $this->db->query("SELECT * FROM pages WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    /**
     * Update the content of a specific page.
     * @param int $id
     * @param string $content
     * @return bool
     */
    public function updatePage($id, $content) {
        $this->db->query("UPDATE pages SET page_content = :content WHERE id = :id");
        $this->db->bind(':content', $content);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
