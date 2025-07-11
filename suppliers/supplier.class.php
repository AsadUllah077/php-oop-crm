<?php
require_once ROOT_PATH . 'database/database.php';

class Supplier extends Database {
    private $table = 'suppliers';
    
    // Properties
    private $id;
    private $name;
    private $contact_person;
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
    
    public function setContactPerson($contact_person) {
        $this->contact_person = $this->con->real_escape_string($contact_person);
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
    
    // Create new supplier
    public function create() {
        $sql = "INSERT INTO {$this->table} (name, contact_person, phone, email, address, status) 
                VALUES ('{$this->name}', '{$this->contact_person}', '{$this->phone}', 
                '{$this->email}', '{$this->address}', {$this->status})";
        
        if ($this->con->query($sql)) {
            return $this->con->insert_id;
        } else {
            return false;
        }
    }
    
    // Get all suppliers with pagination
    public function getAll($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC LIMIT {$offset}, {$perPage}";
        
        $result = $this->con->query($sql);
        $suppliers = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $suppliers[] = $row;
            }
        }
        
        return $suppliers;
    }
    
    // Count total suppliers
    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->con->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    // Get supplier by ID
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
    
    // Update supplier
    public function update($id) {
        $id = (int)$id;
        $sql = "UPDATE {$this->table} SET 
                name = '{$this->name}',
                contact_person = '{$this->contact_person}',
                phone = '{$this->phone}',
                email = '{$this->email}',
                address = '{$this->address}',
                status = {$this->status}
                WHERE id = {$id}";
        
        return $this->con->query($sql);
    }
    
    // Delete supplier
    public function delete($id) {
        $id = (int)$id;
        $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
        return $this->con->query($sql);
    }
    
    // Search suppliers
    public function search($keyword, $page = 1, $perPage = 10) {
        $keyword = $this->con->real_escape_string($keyword);
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table} 
               WHERE (name LIKE '%{$keyword}%' OR contact_person LIKE '%{$keyword}%' 
               OR phone LIKE '%{$keyword}%' OR email LIKE '%{$keyword}%')
               ORDER BY id DESC LIMIT {$offset}, {$perPage}";
        
        $result = $this->con->query($sql);
        $suppliers = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $suppliers[] = $row;
            }
        }
        
        return $suppliers;
    }
    
    // Count search results
    public function countSearch($keyword) {
        $keyword = $this->con->real_escape_string($keyword);
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
               WHERE (name LIKE '%{$keyword}%' OR contact_person LIKE '%{$keyword}%' 
               OR phone LIKE '%{$keyword}%' OR email LIKE '%{$keyword}%')";
        
        $result = $this->con->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>