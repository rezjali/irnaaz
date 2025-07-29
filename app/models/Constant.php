<?php

namespace App\Models;

use App\Core\Database;

class Constant {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // --- Order Statuses ---
    public function getAllOrderStatuses() {
        $this->db->query("SELECT * FROM order_statuses ORDER BY status_category, id");
        return $this->db->fetchAll();
    }
    public function addOrderStatus($data) {
        $this->db->query("INSERT INTO order_statuses (status_name, status_category) VALUES (:status_name, :status_category)");
        $this->db->bind(':status_name', $data['status_name']);
        $this->db->bind(':status_category', $data['status_category']);
        return $this->db->execute();
    }
    public function deleteOrderStatus($id) {
        $this->db->query("DELETE FROM order_statuses WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // --- Shipping Rates ---
    public function getAllShippingRates() {
        $this->db->query("SELECT * FROM shipping_rates ORDER BY cost ASC");
        return $this->db->fetchAll();
    }
    public function addShippingRate($data) {
        $this->db->query("INSERT INTO shipping_rates (description, cost) VALUES (:description, :cost)");
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':cost', $data['cost']);
        return $this->db->execute();
    }
    public function deleteShippingRate($id) {
        $this->db->query("DELETE FROM shipping_rates WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // --- Source Sites ---
    public function getAllSites() {
        $this->db->query("SELECT * FROM source_sites ORDER BY site_name ASC");
        return $this->db->fetchAll();
    }
    public function addSite($data) {
        $this->db->query("INSERT INTO source_sites (site_name, site_url) VALUES (:site_name, :site_url)");
        $this->db->bind(':site_name', $data['site_name']);
        $this->db->bind(':site_url', $data['site_url']);
        return $this->db->execute();
    }
    public function deleteSite($id) {
        $this->db->query("DELETE FROM source_sites WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // --- Ticket Categories ---
    public function getAllTicketCategories() {
        $this->db->query("SELECT * FROM ticket_categories ORDER BY name ASC");
        return $this->db->fetchAll();
    }
    public function addTicketCategory($name) {
        $this->db->query("INSERT INTO ticket_categories (name) VALUES (:name)");
        $this->db->bind(':name', $name);
        return $this->db->execute();
    }
    public function deleteTicketCategory($id) {
        $this->db->query("DELETE FROM ticket_categories WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
