<?php

namespace App\Models;

use App\Core\Database;

class Financial {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAllTransactions($page, $perPage, $filters = []) {
        $sql = "SELECT t.id, u.full_name as customer_name, t.amount, t.wallet_type, t.transaction_type, t.description, t.created_at FROM transactions t JOIN users u ON t.user_id = u.id WHERE 1=1";
        if (!empty($filters['wallet_type'])) $sql .= " AND t.wallet_type = :wallet_type";
        $sql .= " ORDER BY t.created_at DESC LIMIT :limit OFFSET :offset";
        $this->db->query($sql);
        if (!empty($filters['wallet_type'])) $this->db->bind(':wallet_type', $filters['wallet_type']);
        $this->db->bind(':limit', $perPage);
        $this->db->bind(':offset', ($page - 1) * $perPage);
        return $this->db->fetchAll();
    }

    public function getTotalTransactionCount($filters = []) {
        $sql = "SELECT COUNT(id) as total FROM transactions WHERE 1=1";
        if (!empty($filters['wallet_type'])) $sql .= " AND wallet_type = :wallet_type";
        $this->db->query($sql);
        if (!empty($filters['wallet_type'])) $this->db->bind(':wallet_type', $filters['wallet_type']);
        return $this->db->fetch()->total ?? 0;
    }

    public function getRecentTransactions($limit = 5) {
        $this->db->query("SELECT t.id, u.full_name as customer_name, t.amount, t.description, t.created_at FROM transactions t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->fetchAll();
    }

    public function addManualTransaction($data) {
        $amount = $data['amount'];
        $walletColumn = ($data['wallet_type'] === 'product') ? 'wallet_product' : 'wallet_shipping';
        if ($data['transaction_type'] === 'deposit') {
            $this->db->query("UPDATE users SET {$walletColumn} = {$walletColumn} + :amount WHERE id = :user_id");
        } else {
            $this->db->query("UPDATE users SET {$walletColumn} = {$walletColumn} - :amount WHERE id = :user_id");
        }
        $this->db->bind(':amount', $amount);
        $this->db->bind(':user_id', $data['user_id']);
        if (!$this->db->execute()) return false;
        $transactionAmount = ($data['transaction_type'] === 'deposit') ? $amount : -$amount;
        $this->db->query("INSERT INTO transactions (user_id, amount, wallet_type, transaction_type, description, is_approved) VALUES (:user_id, :amount, :wallet_type, :transaction_type, :description, 1)");
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':amount', $transactionAmount);
        $this->db->bind(':wallet_type', $data['wallet_type']);
        $this->db->bind(':transaction_type', $data['transaction_type']);
        $this->db->bind(':description', $data['description']);
        return $this->db->execute();
    }

    public function getDebtors() {
        $this->db->query("SELECT id, full_name, username, email, phone, wallet_product, wallet_shipping FROM users WHERE wallet_product < 0 OR wallet_shipping < 0 ORDER BY full_name ASC");
        return $this->db->fetchAll();
    }

    public function getTotalSalesRevenue() {
        $this->db->query("SELECT SUM(total_cost) as total FROM orders WHERE status_id > 1");
        return $this->db->fetch()->total ?? 0;
    }

    public function getSalesRevenueForPeriod($period = 'month') {
        $dateFilter = '';
        if ($period === 'month') $dateFilter = "AND YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())";
        elseif ($period === 'year') $dateFilter = "AND YEAR(created_at) = YEAR(CURDATE())";
        $this->db->query("SELECT SUM(total_cost) as total FROM orders WHERE status_id > 1 {$dateFilter}");
        return $this->db->fetch()->total ?? 0;
    }

    public function getAverageOrderValue() {
        $this->db->query("SELECT AVG(total_cost) as average FROM orders WHERE status_id > 1");
        return $this->db->fetch()->average ?? 0;
    }

    public function getMonthlySalesForYear($year) {
        list($g_y, $g_m, $g_d) = jalali_to_gregorian($year, 1, 1);
        $this->db->query("SELECT MONTH(created_at) as month, SUM(total_cost) as total FROM orders WHERE status_id > 1 AND YEAR(created_at) = :year GROUP BY MONTH(created_at)");
        $this->db->bind(':year', $g_y);
        $results = $this->db->fetchAll();
        $sales = array_fill(1, 12, 0);
        foreach ($results as $result) {
            $sales[(int)$result->month] = (float)$result->total;
        }
        return array_values($sales);
    }

    public function getPendingReceipts($filters = []) {
        $sql = "SELECT pr.*, u.full_name as user_name FROM payment_receipts pr JOIN users u ON pr.user_id = u.id WHERE pr.status = 'pending'";
        if (!empty($filters['wallet_type'])) $sql .= " AND pr.wallet_type = :wallet_type";
        $sql .= " ORDER BY pr.created_at ASC";
        $this->db->query($sql);
        if (!empty($filters['wallet_type'])) $this->db->bind(':wallet_type', $filters['wallet_type']);
        return $this->db->fetchAll();
    }

    public function processReceipt($receipt_id, $action, $admin_id) {
        $this->db->query("SELECT * FROM payment_receipts WHERE id = :id AND status = 'pending'");
        $this->db->bind(':id', $receipt_id);
        $receipt = $this->db->fetch();
        if (!$receipt) return false;
        if ($action === 'approve') {
            $transactionData = ['user_id' => $receipt->user_id, 'amount' => $receipt->amount, 'wallet_type' => $receipt->wallet_type, 'description' => 'تایید فیش واریزی به شماره ' . $receipt->id, 'transaction_type' => 'deposit'];
            if (!$this->addManualTransaction($transactionData)) return false;
        }
        $this->db->query("UPDATE payment_receipts SET status = :status, reviewed_by = :admin_id, reviewed_at = CURRENT_TIMESTAMP WHERE id = :id");
        $this->db->bind(':status', $action);
        $this->db->bind(':admin_id', $admin_id);
        $this->db->bind(':id', $receipt_id);
        return $this->db->execute();
    }
    
    public function getCurrentMonthSummary() {
        $this->db->query("SELECT SUM(total_cost) as total FROM orders WHERE status_id > 1 AND YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())");
        $revenue = $this->db->fetch()->total ?? 0;
        $expenses = 0; // Placeholder
        $net_profit = $revenue - $expenses;
        return ['revenue' => $revenue, 'expenses' => $expenses, 'net_profit' => $net_profit];
    }
}
