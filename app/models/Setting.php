<?php

namespace App\Models;

use App\Core\Database;

class Setting {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get all settings from the database.
     * @return array
     */
    public function getAll() {
        $this->db->query("SELECT * FROM settings");
        return $this->db->fetchAll();
    }

    /**
     * Get all settings as an associative array (key => value).
     * @return array
     */
    public function getAllAsAssoc() {
        $settings = $this->getAll();
        $assoc = [];
        foreach ($settings as $setting) {
            $assoc[$setting->setting_key] = $setting->setting_value;
        }
        return $assoc;
    }

    /**
     * Update a specific setting by its key.
     * If the key doesn't exist, it will be created.
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function updateSetting($key, $value) {
        // Check if the key already exists
        $this->db->query("SELECT setting_key FROM settings WHERE setting_key = :key");
        $this->db->bind(':key', $key);
        $exists = $this->db->fetch();

        if ($exists) {
            // If it exists, update it
            $this->db->query("UPDATE settings SET setting_value = :value WHERE setting_key = :key");
        } else {
            // If it does not exist, insert it
            $this->db->query("INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)");
        }
        
        $this->db->bind(':key', $key);
        $this->db->bind(':value', $value);
        
        return $this->db->execute();
    }
}
