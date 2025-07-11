<?php
require_once ROOT_PATH . 'database/database.php';

class Ledger extends Database {
    private $table = 'ledger';
    
    // Constructor
    public function __construct() {
        parent::__construct();
    }
    
    // Get ledger entries with pagination
    public function getAll($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT l.*, pm.name as payment_method 
               FROM {$this->table} l 
               LEFT JOIN payment_methods pm ON l.payment_method_id = pm.id 
               ORDER BY l.transaction_date DESC, l.id DESC LIMIT {$offset}, {$perPage}";
        
        $result = $this->con->query($sql);
        $entries = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $entries[] = $row;
            }
        }
        
        return $entries;
    }
    
    // Count total ledger entries
    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->con->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    // Get ledger entries by account type
    public function getByAccountType($account_type, $page = 1, $perPage = 10) {
        $account_type = $this->con->real_escape_string($account_type);
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT l.*, pm.name as payment_method 
               FROM {$this->table} l 
               LEFT JOIN payment_methods pm ON l.payment_method_id = pm.id 
               WHERE l.account_type = '{$account_type}' 
               ORDER BY l.transaction_date DESC, l.id DESC LIMIT {$offset}, {$perPage}";
        
        $result = $this->con->query($sql);
        $entries = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $entries[] = $row;
            }
        }
        
        return $entries;
    }
    
    // Count ledger entries by account type
    public function countByAccountType($account_type) {
        $account_type = $this->con->real_escape_string($account_type);
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE account_type = '{$account_type}'";
        $result = $this->con->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    // Get account balance by type
    public function getBalanceByAccountType($account_type) {
        $account_type = $this->con->real_escape_string($account_type);
        
        $sql = "SELECT SUM(debit) as total_debit, SUM(credit) as total_credit 
               FROM {$this->table} WHERE account_type = '{$account_type}'";
        $result = $this->con->query($sql);
        $row = $result->fetch_assoc();
        
        $balance = ($row['total_debit'] ?? 0) - ($row['total_credit'] ?? 0);
        return $balance;
    }
    
    // Search ledger entries
    public function search($keyword, $account_type = null, $start_date = null, $end_date = null, $page = 1, $perPage = 10) {
        $keyword = $this->con->real_escape_string($keyword);
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT l.*, pm.name as payment_method 
               FROM {$this->table} l 
               LEFT JOIN payment_methods pm ON l.payment_method_id = pm.id 
               WHERE (l.reference_no LIKE '%{$keyword}%' OR l.notes LIKE '%{$keyword}%')";
        
        if ($account_type) {
            $account_type = $this->con->real_escape_string($account_type);
            $sql .= " AND l.account_type = '{$account_type}'";
        }
        
        if ($start_date && $end_date) {
            $start_date = $this->con->real_escape_string($start_date);
            $end_date = $this->con->real_escape_string($end_date);
            $sql .= " AND l.transaction_date BETWEEN '{$start_date}' AND '{$end_date}'";
        }
        
        $sql .= " ORDER BY l.transaction_date DESC, l.id DESC LIMIT {$offset}, {$perPage}";
        
        $result = $this->con->query($sql);
        $entries = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $entries[] = $row;
            }
        }
        
        return $entries;
    }
    
    // Count search results
    public function countSearch($keyword, $account_type = null, $start_date = null, $end_date = null) {
        $keyword = $this->con->real_escape_string($keyword);
        
        $sql = "SELECT COUNT(*) as total 
               FROM {$this->table} l 
               WHERE (l.reference_no LIKE '%{$keyword}%' OR l.notes LIKE '%{$keyword}%')";
        
        if ($account_type) {
            $account_type = $this->con->real_escape_string($account_type);
            $sql .= " AND l.account_type = '{$account_type}'";
        }
        
        if ($start_date && $end_date) {
            $start_date = $this->con->real_escape_string($start_date);
            $end_date = $this->con->real_escape_string($end_date);
            $sql .= " AND l.transaction_date BETWEEN '{$start_date}' AND '{$end_date}'";
        }
        
        $result = $this->con->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>