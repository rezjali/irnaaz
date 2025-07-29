<?php

namespace App\Models;

use App\Core\Database;

class Order {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($page, $perPage, $filters = []) {
        $sql = "SELECT o.id, u.full_name as customer_name, os.status_name, o.total_cost, o.created_at FROM orders o JOIN users u ON o.user_id = u.id JOIN order_statuses os ON o.status_id = os.id WHERE 1=1";
        if (!empty($filters['category'])) $sql .= " AND os.status_category = :category";
        if (!empty($filters['search'])) $sql .= " AND (o.id LIKE :search OR u.full_name LIKE :search)";
        if (!empty($filters['status_id'])) $sql .= " AND o.status_id = :status_id";
        $sql .= " ORDER BY o.created_at DESC LIMIT :limit OFFSET :offset";
        $this->db->query($sql);
        if (!empty($filters['category'])) $this->db->bind(':category', $filters['category']);
        if (!empty($filters['search'])) $this->db->bind(':search', '%' . $filters['search'] . '%');
        if (!empty($filters['status_id'])) $this->db->bind(':status_id', $filters['status_id']);
        $this->db->bind(':limit', $perPage);
        $this->db->bind(':offset', ($page - 1) * $perPage);
        return $this->db->fetchAll();
    }

    public function getTotalCount($filters = []) {
        $sql = "SELECT COUNT(o.id) as total FROM orders o JOIN users u ON o.user_id = u.id JOIN order_statuses os ON o.status_id = os.id WHERE 1=1";
        if (!empty($filters['category'])) $sql .= " AND os.status_category = :category";
        if (!empty($filters['search'])) $sql .= " AND (o.id LIKE :search OR u.full_name LIKE :search)";
        if (!empty($filters['status_id'])) $sql .= " AND o.status_id = :status_id";
        $this->db->query($sql);
        if (!empty($filters['category'])) $this->db->bind(':category', $filters['category']);
        if (!empty($filters['search'])) $this->db->bind(':search', '%' . $filters['search'] . '%');
        if (!empty($filters['status_id'])) $this->db->bind(':status_id', $filters['status_id']);
        return $this->db->fetch()->total ?? 0;
    }

    public function getOrderStatuses($category = null) {
        $sql = "SELECT id, status_name FROM order_statuses";
        if ($category) $sql .= " WHERE status_category = :category";
        $sql .= " ORDER BY id";
        $this->db->query($sql);
        if ($category) $this->db->bind(':category', $category);
        return $this->db->fetchAll();
    }

    public function getStatusesByCategory($category) {
        return $this->getOrderStatuses($category);
    }

    public function getStatusCount($statusId) {
        $this->db->query("SELECT COUNT(id) as count FROM orders WHERE status_id = :status_id");
        $this->db->bind(':status_id', $statusId);
        return $this->db->fetch()->count ?? 0;
    }

    public function getRecentOrders($limit = 5) {
        $this->db->query("SELECT o.id, u.full_name as customer_name, os.status_name, o.created_at FROM orders o JOIN users u ON o.user_id = u.id JOIN order_statuses os ON o.status_id = os.id ORDER BY o.created_at DESC LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->fetchAll();
    }

    public function createOrder($data) {
        $this->db->query("INSERT INTO orders (user_id, status_id, product_link, product_details, price_try, exchange_rate, commission_fee, shipping_cost, total_cost) VALUES (:user_id, :status_id, :product_link, :product_details, :price_try, :exchange_rate, :commission_fee, :shipping_cost, :total_cost)");
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':status_id', $data['status_id']);
        $this->db->bind(':product_link', $data['product_link']);
        $this->db->bind(':product_details', $data['product_details']);
        $this->db->bind(':price_try', $data['price_try']);
        $this->db->bind(':exchange_rate', $data['exchange_rate']);
        $this->db->bind(':commission_fee', $data['commission_fee']);
        $this->db->bind(':shipping_cost', $data['shipping_cost']);
        $this->db->bind(':total_cost', $data['total_cost']);
        return $this->db->execute();
    }

    public function getOrdersForAssignment() {
        $this->db->query("SELECT o.id, o.product_link, u.full_name as customer_name, o.created_at FROM orders o JOIN users u ON o.user_id = u.id WHERE o.status_id = 1 AND o.assigned_to_user_id IS NULL");
        return $this->db->fetchAll();
    }

    public function assignOrderToUser($order_id, $user_id) {
        $this->db->query("UPDATE orders SET assigned_to_user_id = :user_id WHERE id = :order_id");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':order_id', $order_id);
        return $this->db->execute();
    }

    public function getUnassignedOrders() {
        $this->db->query("SELECT o.id, u.full_name as customer_name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.suitcase_id IS NULL AND o.status_id IN (2, 3)");
        return $this->db->fetchAll();
    }

    public function getUrgentOrders($limit = 5) {
        $this->db->query("SELECT o.id, u.full_name as customer_name, o.created_at FROM orders o JOIN users u ON o.user_id = u.id WHERE o.status_id = 1 AND o.created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR) ORDER BY o.created_at ASC LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->fetchAll();
    }
}
