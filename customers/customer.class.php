<?php
require_once ROOT_PATH . 'database/database.php';

class Customer extends Database {
    private $table = 'customers';
    
    // Properties
    private $id;
    private $name;
    private $phone;
    private $email;
    private $address;
    private $status;
    
    // Constructor
    public function __construct() {
        parent::__construct();
    }
    
    // Setters
    public function setName($name) {
        $this->name = $this->con->real_escape_string($name);
    }
    
    public function setPhone($phone) {
        $this->phone = $this->con->real_escape_string($phone);
    }
    
    public function setEmail($email) {
        $this->email = $this->con->real_escape_string($email);
    }
    
    public function setAddress($address) {
        $this->address = $this->con->real_escape_string($address);
    }
    
    public function setStatus($status) {
        $this->status = (int)$status;
    }
    
    // Create new customer
    public function create() {
        $sql = "INSERT INTO {$this->table} (name, phone, email, address, status) 
                VALUES ('{$this->name}', '{$this->phone}', '{$this->email}', 
                '{$this->address}', {$this->status})";
        
        if ($this->con->query($sql)) {
            return $this->con->insert_id;
        } else {
            return false;
        }
    }
    
    // Get all customers with pagination
    public function getAll($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC LIMIT {$offset}, {$perPage}";
        
        $result = $this->con->query($sql);
        $customers = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $customers[] = $row;
            }
        }
        
        return $customers;
    }
    
    // Count total customers
    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->con->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    // Get customer by ID
    public function getById($id) {
        $id = (int)$id;
        $sql = "SELECT * FROM {$this->table} WHERE id = {$id}";
        
        $result = $this->con->query($sql);
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return false;
        }
    }
    
    // Update customer
    public function update($id) {
        $id = (int)$id;
        $sql = "UPDATE {$this->table} SET 
                name = '{$this->name}',
                phone = '{$this->phone}',
                email = '{$this->email}',
                address = '{$this->address}',
                status = {$this->status}
                WHERE id = {$id}";
        
        return $this->con->query($sql);
    }
    
    // Delete customer
    public function delete($id) {
        $id = (int)$id;
        $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
        return $this->con->query($sql);
    }
    
    // Search customers
    public function search($keyword, $page = 1, $perPage = 10) {
        $keyword = $this->con->real_escape_string($keyword);
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table} 
               WHERE (name LIKE '%{$keyword}%' OR phone LIKE '%{$keyword}%' 
               OR email LIKE '%{$keyword}%')
               ORDER BY id DESC LIMIT {$offset}, {$perPage}";
        
        $result = $this->con->query($sql);
        $customers = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $customers[] = $row;
            }
        }
        
        return $customers;
    }
    
    // Count search results
    public function countSearch($keyword) {
        $keyword = $this->con->real_escape_string($keyword);
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
               WHERE (name LIKE '%{$keyword}%' OR phone LIKE '%{$keyword}%' 
               OR email LIKE '%{$keyword}%')";
        
        $result = $this->con->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>