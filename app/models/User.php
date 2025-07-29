<?php

namespace App\Models;

use App\Core\Database;

class User {
    private $db;

    public function __construct() {
        // FIX: Corrected syntax from $this.db to $this->db
        $this->db = Database::getInstance();
    }

    /**
     * Get all users with pagination.
     */
    public function getAll($page, $perPage) {
        $sql = "SELECT u.id, u.full_name, u.username, u.email, u.phone, u.is_active, u.created_at, r.role_name
                FROM users u
                JOIN roles r ON u.role_id = r.id
                ORDER BY u.created_at DESC
                LIMIT :limit OFFSET :offset";
        $this->db->query($sql);
        $this->db->bind(':limit', $perPage);
        $this->db->bind(':offset', ($page - 1) * $perPage);
        return $this->db->fetchAll();
    }

    /**
     * Get total count of users, with optional filters.
     */
    public function getTotalCount($filters = []) {
        $sql = "SELECT COUNT(id) as total FROM users WHERE 1=1";
        if (!empty($filters['is_customer'])) {
            $sql .= " AND role_id = 2";
        }
        $this->db->query($sql);
        return $this->db->fetch()->total ?? 0;
    }
    
    /**
     * Find a single user by their ID.
     */
    public function findById($id) {
        $this->db->query("SELECT id, full_name, username, email, phone FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    /**
     * Update a user's profile information.
     */
    public function update($data) {
        $sql = "UPDATE users SET full_name = :full_name, email = :email, phone = :phone";
        if (!empty($data['password'])) {
            $sql .= ", password = :password";
        }
        $sql .= " WHERE id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        if (!empty($data['password'])) {
            $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        }
        return $this->db->execute();
    }
    
    /**
     * Create a new user.
     */
    public function createUser($data) {
        $this->db->query("INSERT INTO users (full_name, username, email, password, role_id, phone) VALUES (:full_name, :username, :email, :password, :role_id, :phone)");
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind(':role_id', $data['role_id']);
        $this->db->bind(':phone', $data['phone']);
        return $this->db->execute();
    }

    /**
     * Get all non-customer users (admins, staff, etc.).
     */
    public function getAdminUsers() {
        $this->db->query(
            "SELECT u.id, u.full_name, u.username, u.email, u.is_active, r.role_name 
             FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE u.role_id != 2 
             ORDER BY r.id, u.full_name"
        );
        return $this->db->fetchAll();
    }

    /**
     * Get a list of all customers for dropdowns.
     */
    public function getAllCustomers() {
        $this->db->query("SELECT id, full_name, username FROM users WHERE role_id = 2 ORDER BY full_name ASC");
        return $this->db->fetchAll();
    }

    /**
     * Get users by their specific role key.
     */
    public function getUsersByRole($role_key) {
        $this->db->query(
            "SELECT u.id, u.full_name 
             FROM users u JOIN roles r ON u.role_id = r.id 
             WHERE r.role_key = :role_key AND u.is_active = 1"
        );
        $this->db->bind(':role_key', $role_key);
        return $this->db->fetchAll();
    }

    /**
     * Get all customers with their wallet balances, with pagination.
     */
    public function getUsersWithWallets($page, $perPage) {
        $this->db->query(
            "SELECT id, full_name, username, wallet_product, wallet_shipping 
             FROM users 
             WHERE role_id = 2 -- Only customers
             ORDER BY full_name ASC
             LIMIT :limit OFFSET :offset"
        );
        $this->db->bind(':limit', $perPage);
        $this->db->bind(':offset', ($page - 1) * $perPage);
        return $this->db->fetchAll();
    }
}
