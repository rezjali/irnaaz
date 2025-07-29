<?php

namespace App\Models;

use App\Core\Database;

class Suitcase {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($page, $perPage) {
        $this->db->query("SELECT s.id, s.suitcase_code, s.status, u.full_name as creator_name, s.created_at, (SELECT COUNT(id) FROM orders WHERE suitcase_id = s.id) as order_count FROM suitcases s JOIN users u ON s.created_by = u.id ORDER BY s.created_at DESC LIMIT :limit OFFSET :offset");
        $this->db->bind(':limit', $perPage);
        $this->db->bind(':offset', ($page - 1) * $perPage);
        return $this->db->fetchAll();
    }

    public function getTotalCount() {
        $this->db->query("SELECT COUNT(id) as total FROM suitcases");
        return $this->db->fetch()->total ?? 0;
    }

    public function findById($id) {
        $this->db->query("SELECT * FROM suitcases WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    public function createSuitcase($data) {
        $this->db->query("INSERT INTO suitcases (suitcase_code, status, created_by) VALUES (:suitcase_code, :status, :created_by)");
        $this->db->bind(':suitcase_code', $data['suitcase_code']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':created_by', $data['created_by']);
        return $this->db->execute();
    }

    public function getAssignedOrders($suitcase_id) {
        $this->db->query("SELECT o.id, o.product_link, u.full_name as customer_name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.suitcase_id = :suitcase_id");
        $this->db->bind(':suitcase_id', $suitcase_id);
        return $this->db->fetchAll();
    }

    public function assignOrderToSuitcase($order_id, $suitcase_id) {
        $this->db->query("UPDATE orders SET suitcase_id = :suitcase_id WHERE id = :order_id");
        $this->db->bind(':suitcase_id', $suitcase_id);
        $this->db->bind(':order_id', $order_id);
        return $this->db->execute();
    }

    public function removeOrderFromSuitcase($order_id) {
        $this->db->query("UPDATE orders SET suitcase_id = NULL WHERE id = :order_id");
        $this->db->bind(':order_id', $order_id);
        return $this->db->execute();
    }
}
