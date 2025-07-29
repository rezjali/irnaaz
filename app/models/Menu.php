<?php

namespace App\Models;

use App\Core\Database;

class Menu {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllGroups() {
        $this->db->query("SELECT * FROM menu_groups ORDER BY name ASC");
        return $this->db->fetchAll();
    }

    public function getItemsByGroup($group_id) {
        $this->db->query("SELECT * FROM menus WHERE group_id = :group_id ORDER BY sort_order ASC");
        $this->db->bind(':group_id', $group_id);
        return $this->db->fetchAll();
    }

    public function createItem($data) {
        $this->db->query("INSERT INTO menus (group_id, title, url) VALUES (:group_id, :title, :url)");
        $this->db->bind(':group_id', $data['group_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':url', $data['url']);
        return $this->db->execute();
    }
}
