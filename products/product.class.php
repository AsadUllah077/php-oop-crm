<?php
require_once ROOT_PATH . 'database/database.php';
require_once ROOT_PATH . 'categories/category.class.php';

class Product extends Database {
    private $table = 'products';
    
    // Properties
    private $id;
    private $category_id;
    private $name;
    private $description;
    private $price;
    private $quantity;
    private $image;
    private $status;
    
    // Constructor
    public function __construct() {
        parent::__construct();
    }
    
    // Setters
    public function setCategoryId($category_id) {
        $this->category_id = (int)$category_id;
    }
    
    public function setName($name) {
        $this->name = $this->con->real_escape_string($name);
    }
    
    public function setDescription($description) {
        $this->description = $this->con->real_escape_string($description);
    }
    
    public function setPrice($price) {
        $this->price = (float)$price;
    }
    
    public function setQuantity($quantity) {
        $this->quantity = (int)$quantity;
    }
    
    public function setImage($image) {
        $this->image = $this->con->real_escape_string($image);
    }
    
    public function setStatus($status) {
        $this->status = (int)$status;
    }
    
    // Create new product
    public function create() {
        $sql = "INSERT INTO {$this->table} (category_id, name, description, price, quantity, image, status) 
                VALUES ({$this->category_id}, '{$this->name}', '{$this->description}', {$this->price}, 
                {$this->quantity}, '{$this->image}', {$this->status})";
        
        if ($this->con->query($sql)) {
            return $this->con->insert_id;
        } else {
            return false;
        }
    }
    
    // Get all products with pagination
    public function getAll($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT p.*, c.name as category_name 
               FROM {$this->table} p 
               LEFT JOIN categories c ON p.category_id = c.id 
               ORDER BY p.id DESC LIMIT {$offset}, {$perPage}";
        
        $result = $this->con->query($sql);
        $products = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        
        return $products;
    }
    
    // Count total products
    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->con->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    // Search products with pagination
    public function search($keyword, $category_id = null, $status = null, $page = 1, $perPage = 10) {
        $keyword = $this->con->real_escape_string($keyword);
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT p.*, c.name as category_name 
               FROM {$this->table} p 
               LEFT JOIN categories c ON p.category_id = c.id 
               WHERE (p.name LIKE '%{$keyword}%' OR p.description LIKE '%{$keyword}%')";
        
        if ($category_id !== null) {
            $category_id = (int)$category_id;
            $sql .= " AND p.category_id = {$category_id}";
        }
        
        if ($status !== null) {
            $status = (int)$status;
            $sql .= " AND p.status = {$status}";
        }
        
        $sql .= " ORDER BY p.id DESC LIMIT {$offset}, {$perPage}";
        
        $result = $this->con->query($sql);
        $products = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        
        return $products;
    }
    
    // Count total search results
    public function countSearch($keyword, $category_id = null, $status = null) {
        $keyword = $this->con->real_escape_string($keyword);
        
        $sql = "SELECT COUNT(*) as total 
               FROM {$this->table} p 
               LEFT JOIN categories c ON p.category_id = c.id 
               WHERE (p.name LIKE '%{$keyword}%' OR p.description LIKE '%{$keyword}%')";
        
        if ($category_id !== null) {
            $category_id = (int)$category_id;
            $sql .= " AND p.category_id = {$category_id}";
        }
        
        if ($status !== null) {
            $status = (int)$status;
            $sql .= " AND p.status = {$status}";
        }
        
        $result = $this->con->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    // Get product by ID
    public function getById($id) {
        $id = (int)$id;
        $sql = "SELECT p.*, c.name as category_name 
               FROM {$this->table} p 
               LEFT JOIN categories c ON p.category_id = c.id 
               WHERE p.id = {$id}";
        
        $result = $this->con->query($sql);
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return false;
        }
    }
    
    // Update product
    public function update($id) {
        $id = (int)$id;
        $sql = "UPDATE {$this->table} SET 
                category_id = {$this->category_id},
                name = '{$this->name}',
                description = '{$this->description}',
                price = {$this->price},
                quantity = {$this->quantity},";
                
        // Only update image if it's set
        if (!empty($this->image)) {
            $sql .= " image = '{$this->image}',";
        }
        
        $sql .= " status = {$this->status}
                WHERE id = {$id}";
        
        return $this->con->query($sql);
    }
    
    // Delete product
    public function delete($id) {
        $id = (int)$id;
        $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
        return $this->con->query($sql);
    }
    
    // Get products by category ID
    public function getByCategory($category_id, $limit = 10) {
        $category_id = (int)$category_id;
        $limit = (int)$limit;
        
        $sql = "SELECT p.*, c.name as category_name 
               FROM {$this->table} p 
               LEFT JOIN categories c ON p.category_id = c.id 
               WHERE p.category_id = {$category_id} AND p.status = 1 
               ORDER BY p.id DESC LIMIT {$limit}";
        
        $result = $this->con->query($sql);
        $products = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        
        return $products;
    }
}
?>