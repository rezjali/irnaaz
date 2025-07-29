<?php

namespace App\Models;

use App\Core\Database;

class Role {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $this->db->query("SELECT r.id, r.role_name, r.role_key, COUNT(u.id) as user_count FROM roles r LEFT JOIN users u ON r.id = u.role_id GROUP BY r.id, r.role_name, r.role_key ORDER BY r.id");
        return $this->db->fetchAll();
    }
    
    public function getAdminRoles() {
        $this->db->query("SELECT id, role_name FROM roles WHERE role_key != 'customer' ORDER BY id");
        return $this->db->fetchAll();
    }

    public function findById($id) {
        // We also fetch user_count here for the delete check
        $this->db->query("SELECT r.*, (SELECT COUNT(id) FROM users WHERE role_id = r.id) as user_count FROM roles r WHERE r.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    public function createRole($name, $key) {
        $this->db->query("INSERT INTO roles (role_name, role_key) VALUES (:name, :key)");
        $this->db->bind(':name', $name);
        $this->db->bind(':key', $key);
        return $this->db->execute();
    }

    /**
     * Delete a role by its ID.
     */
    public function deleteRole($id) {
        $this->db->query("DELETE FROM roles WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getAllPermissionsGrouped() {
        $this->db->query("SELECT * FROM permissions ORDER BY category, id");
        $permissions = $this->db->fetchAll();
        $grouped = [];
        foreach ($permissions as $permission) {
            $grouped[$permission->category][] = $permission;
        }
        return $grouped;
    }

    public function getRolePermissionIds($role_id) {
        $this->db->query("SELECT permission_id FROM role_permissions WHERE role_id = :role_id");
        $this->db->bind(':role_id', $role_id);
        $results = $this->db->fetchAll();
        $ids = [];
        foreach ($results as $result) {
            $ids[] = $result->permission_id;
        }
        return $ids;
    }

    public function updatePermissions($role_id, $permission_ids) {
        $this->db->query("DELETE FROM role_permissions WHERE role_id = :role_id");
        $this->db->bind(':role_id', $role_id);
        $this->db->execute();
        if (!empty($permission_ids)) {
            $this->db->query("INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)");
            foreach ($permission_ids as $p_id) {
                $this->db->bind(':role_id', $role_id);
                $this->db->bind(':permission_id', (int)$p_id);
                if (!$this->db->execute()) {
                    return false;
                }
            }
        }
        return true;
    }
}
