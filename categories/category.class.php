<?php
require_once ROOT_PATH . 'database/database.php';

class Category extends Database {
    private $table = 'categories';
    
    // Properties
    private $id;
    private $name;
    private $description;
    private $status;
    
    // Constructor
    public function __construct() {
        parent::__construct();
    }
    
    // Setters
    public function setName($name) {
        $this->name = $this->con->real_escape_string($name);
    }
    
    public function setDescription($description) {
        $this->description = $this->con->real_escape_string($description);
    }
    
    public function setStatus($status) {
        $this->status = (int)$status;
    }
    
    // Create new category
    public function create() {
        $sql = "INSERT INTO {$this->table} (name, description, status) VALUES ('{$this->name}', '{$this->description}', {$this->status})";
        
        if ($this->con->query($sql)) {
            return $this->con->insert_id;
        } else {
            return false;
        }
    }
    
    // Get all categories with pagination
    public function getAll($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC LIMIT {$offset}, {$perPage}";
        $result = $this->con->query($sql);
        $categories = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }
        
        return $categories;
    }
    
    // Count total categories
    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->con->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    // Search categories with pagination
    public function search($keyword, $status = null, $page = 1, $perPage = 10) {
        $keyword = $this->con->real_escape_string($keyword);
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table} WHERE 
               (name LIKE '%{$keyword}%' OR description LIKE '%{$keyword}%')";
        
        if ($status !== null) {
            $status = (int)$status;
            $sql .= " AND status = {$status}";
        }
        
        $sql .= " ORDER BY id DESC LIMIT {$offset}, {$perPage}";
        
        $result = $this->con->query($sql);
        $categories = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }
        
        return $categories;
    }
    
    // Count total search results
    public function countSearch($keyword, $status = null) {
        $keyword = $this->con->real_escape_string($keyword);
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 
               (name LIKE '%{$keyword}%' OR description LIKE '%{$keyword}%')";
        
        if ($status !== null) {
            $status = (int)$status;
            $sql .= " AND status = {$status}";
        }
        
        $result = $this->con->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    // Get category by ID
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
    
    // Update category
    public function update($id) {
        $id = (int)$id;
        $sql = "UPDATE {$this->table} SET 
                name = '{$this->name}',
                description = '{$this->description}',
                status = {$this->status}
                WHERE id = {$id}";
        
        return $this->con->query($sql);
    }
    
    // Delete category
    public function delete($id) {
        $id = (int)$id;
        $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
        $res = $this->con->query($sql);
        return $res;
    }
}
?>