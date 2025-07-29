<?php

namespace App\Models;

use App\Core\Database;

class Ticket {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ... متدهای getAll و getTotalCount از قبل اینجا هستند ...
    public function getAll($page, $perPage) {
        $sql = "SELECT t.id, t.subject, t.status, u.full_name as user_name, t.created_at, t.updated_at FROM tickets t JOIN users u ON t.user_id = u.id ORDER BY t.updated_at DESC LIMIT :limit OFFSET :offset";
        $this->db->query($sql);
        $this->db->bind(':limit', $perPage);
        $this->db->bind(':offset', ($page - 1) * $perPage);
        return $this->db->fetchAll();
    }
    public function getTotalCount() {
        $this->db->query("SELECT COUNT(id) as total FROM tickets");
        $row = $this->db->fetch();
        return $row ? $row->total : 0;
    }

    /**
     * Find a single ticket by its ID.
     * @param int $id
     * @return object|false
     */
    public function findById($id) {
        $this->db->query("SELECT t.*, u.full_name as user_name FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    /**
     * Get all replies for a given ticket ID.
     * @param int $ticket_id
     * @return array
     */
    public function getReplies($ticket_id) {
        $this->db->query("SELECT tr.*, u.full_name as replier_name, u.role_id FROM ticket_replies tr JOIN users u ON tr.user_id = u.id WHERE tr.ticket_id = :ticket_id ORDER BY tr.created_at ASC");
        $this->db->bind(':ticket_id', $ticket_id);
        return $this->db->fetchAll();
    }

    /**
     * Add a new reply to a ticket and update ticket status.
     * @param array $data
     * @return bool
     */
    public function addReply($data) {
        // Insert the new reply
        $this->db->query("INSERT INTO ticket_replies (ticket_id, user_id, message) VALUES (:ticket_id, :user_id, :message)");
        $this->db->bind(':ticket_id', $data['ticket_id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':message', $data['message']);
        
        if ($this->db->execute()) {
            // Update the ticket's `updated_at` timestamp and set status to 'answered'
            $this->db->query("UPDATE tickets SET status = 'answered', updated_at = CURRENT_TIMESTAMP WHERE id = :ticket_id");
            $this->db->bind(':ticket_id', $data['ticket_id']);
            return $this->db->execute();
        }
        
        return false;
    }
}
