<?php

namespace App\Models;

use App\Core\Database;

class Announcement {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() { /* ... */ }
    public function create($data) { /* ... */ }

    /**
     * Get unread announcements count for a user.
     */
    public function getUnreadCountForUser($user_id) {
        $this->db->query("SELECT COUNT(a.id) as total FROM announcements a WHERE NOT EXISTS (SELECT 1 FROM user_announcements ua WHERE ua.announcement_id = a.id AND ua.user_id = :user_id)");
        $this->db->bind(':user_id', $user_id);
        return $this->db->fetch()->total ?? 0;
    }

    /**
     * Get all announcements and mark them as read/unread for a specific user.
     */
    public function getAllForUser($user_id) {
        $this->db->query("SELECT a.*, u.full_name as author_name, ua.read_at FROM announcements a JOIN users u ON a.author_id = u.id LEFT JOIN user_announcements ua ON a.id = ua.announcement_id AND ua.user_id = :user_id ORDER BY a.created_at DESC");
        $this->db->bind(':user_id', $user_id);
        return $this->db->fetchAll();
    }

    /**
     * Mark all announcements as read for a user.
     */
    public function markAllAsReadForUser($user_id) {
        // Get IDs of all announcements that are not yet read by the user
        $this->db->query("SELECT a.id FROM announcements a WHERE NOT EXISTS (SELECT 1 FROM user_announcements ua WHERE ua.announcement_id = a.id AND ua.user_id = :user_id)");
        $this->db->bind(':user_id', $user_id);
        $unread_ids = $this->db->fetchAll();

        if (empty($unread_ids)) {
            return true;
        }

        // Insert them into the user_announcements table
        $sql = "INSERT INTO user_announcements (user_id, announcement_id) VALUES ";
        $values = [];
        foreach ($unread_ids as $item) {
            $values[] = "({$user_id}, {$item->id})";
        }
        $sql .= implode(', ', $values);
        
        $this->db->query($sql);
        return $this->db->execute();
    }
}
