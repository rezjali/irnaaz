<?php

namespace App\Models;

use App\Core\Database;

class Log {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // --- Visitor Logs ---
    public function logVisit() {
        $this->db->query("INSERT INTO visitor_logs (user_id, ip_address, user_agent, request_uri) VALUES (:user_id, :ip_address, :user_agent, :request_uri)");
        $this->db->bind(':user_id', $_SESSION['user_id'] ?? null);
        $this->db->bind(':ip_address', $_SERVER['REMOTE_ADDR']);
        $this->db->bind(':user_agent', $_SERVER['HTTP_USER_AGENT']);
        $this->db->bind(':request_uri', $_SERVER['REQUEST_URI']);
        return $this->db->execute();
    }

    public function getVisitorLogs($page, $perPage) {
        $this->db->query("SELECT vl.*, u.full_name FROM visitor_logs vl LEFT JOIN users u ON vl.user_id = u.id ORDER BY vl.timestamp DESC LIMIT :limit OFFSET :offset");
        $this->db->bind(':limit', $perPage);
        $this->db->bind(':offset', ($page - 1) * $perPage);
        return $this->db->fetchAll();
    }

    public function getVisitorLogCount() {
        $this->db->query("SELECT COUNT(id) as total FROM visitor_logs");
        return $this->db->fetch()->total ?? 0;
    }

    // --- Activity Logs ---
    public function getActivityLogs($page, $perPage) {
        $this->db->query("SELECT al.*, u.full_name FROM activity_logs al JOIN users u ON al.user_id = u.id ORDER BY al.created_at DESC LIMIT :limit OFFSET :offset");
        $this->db->bind(':limit', $perPage);
        $this->db->bind(':offset', ($page - 1) * $perPage);
        return $this->db->fetchAll();
    }

    public function getActivityLogCount() {
        $this->db->query("SELECT COUNT(id) as total FROM activity_logs");
        return $this->db->fetch()->total ?? 0;
    }
}
