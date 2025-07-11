<?php
require_once ROOT_PATH . 'database/database.php';
require_once ROOT_PATH . 'suppliers/supplier.class.php';
require_once ROOT_PATH . 'products/product.class.php';

class Purchase extends Database {
    private $table = 'purchases';
    private $items_table = 'purchase_items';
    private $ledger_table = 'ledger';
    
    // Properties
    private $id;
    private $reference_no;
    private $supplier_id;
    private $total_amount;
    private $paid_amount;
    private $due_amount;
    private $payment_method_id;
    private $purchase_date;
    private $notes;
    private $status;
    private $items = [];
    
    // Constructor
    public function __construct() {
        parent::__construct();
    }
    
    // Setters
    public function setReferenceNo($reference_no) {
        $this->reference_no = $this->con->real_escape_string($reference_no);
    }
    
    public function setSupplierId($supplier_id) {
        $this->supplier_id = (int)$supplier_id;
    }
    
    public function setTotalAmount($total_amount) {
        $this->total_amount = (float)$total_amount;
    }
    
    public function setPaidAmount($paid_amount) {
        $this->paid_amount = (float)$paid_amount;
        $this->due_amount = $this->total_amount - $this->paid_amount;
    }
    
    public function setPaymentMethodId($payment_method_id) {
        $this->payment_method_id = (int)$payment_method_id;
    }
    
    public function setPurchaseDate($purchase_date) {
        $this->purchase_date = $this->con->real_escape_string($purchase_date);
    }
    
    public function setNotes($notes) {
        $this->notes = $this->con->real_escape_string($notes);
    }
    
    public function setStatus($status) {
        $this->status = (int)$status;
    }
    
    public function addItem($product_id, $quantity, $unit_price) {
        $this->items[] = [
            'product_id' => (int)$product_id,
            'quantity' => (int)$quantity,
            'unit_price' => (float)$unit_price,
            'total_price' => (float)$quantity * $unit_price
        ];
    }
    
    // Generate a unique reference number
    public function generateReferenceNo() {
        $prefix = 'PUR-';
        $date = date('Ymd');
        $sql = "SELECT MAX(id) as max_id FROM {$this->table}";
        $result = $this->con->query($sql);
        $row = $result->fetch_assoc();
        $next_id = ($row['max_id'] ?? 0) + 1;
        return $prefix . $date . '-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
    }
    
    // Create new purchase with items and ledger entry
    public function create() {
        // Start transaction
        $this->con->begin_transaction();
        
        try {
            // Insert purchase
            $sql = "INSERT INTO {$this->table} 
                    (reference_no, supplier_id, total_amount, paid_amount, due_amount, 
                    payment_method_id, purchase_date, notes, status) 
                    VALUES ('{$this->reference_no}', {$this->supplier_id}, {$this->total_amount}, 
                    {$this->paid_amount}, {$this->due_amount}, {$this->payment_method_id}, 
                    '{$this->purchase_date}', '{$this->notes}', {$this->status})";
            
            $this->con->query($sql);
            $purchase_id = $this->con->insert_id;
            
            // Insert purchase items
            foreach ($this->items as $item) {
                $sql = "INSERT INTO {$this->items_table} 
                        (purchase_id, product_id, quantity, unit_price, total_price) 
                        VALUES ({$purchase_id}, {$item['product_id']}, {$item['quantity']}, 
                        {$item['unit_price']}, {$item['total_price']})";
                $this->con->query($sql);
                
                // Update product quantity
                $sql = "UPDATE products SET quantity = quantity + {$item['quantity']} 
                        WHERE id = {$item['product_id']}";
                $this->con->query($sql);
            }
            
            // Create ledger entry for purchase
            $account_type = '';
            switch ($this->payment_method_id) {
                case 1: // Cash
                    $account_type = 'cash';
                    break;
                case 2: // Bank Transfer
                    $account_type = 'bank';
                    break;
                case 3: // Credit Card
                case 4: // Debit Card
                    $account_type = 'card';
                    break;
                default:
                    $account_type = 'cash';
            }
            
            if ($this->paid_amount > 0) {
                $sql = "INSERT INTO {$this->ledger_table} 
                        (transaction_date, reference_no, transaction_type, reference_id, 
                        account_type, payment_method_id, debit, credit, notes) 
                        VALUES ('{$this->purchase_date}', '{$this->reference_no}', 'purchase', 
                        {$purchase_id}, '{$account_type}', {$this->payment_method_id}, 
                        0, {$this->paid_amount}, 'Purchase payment')";
                $this->con->query($sql);
            }
            
            // Commit transaction
            $this->con->commit();
            return $purchase_id;
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->con->rollback();
            return false;
        }
    }
    
    // Get all purchases with pagination
    public function getAll($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT p.*, s.name as supplier_name, pm.name as payment_method 
               FROM {$this->table} p 
               LEFT JOIN suppliers s ON p.supplier_id = s.id 
               LEFT JOIN payment_methods pm ON p.payment_method_id = pm.id 
               ORDER BY p.id DESC LIMIT {$offset}, {$perPage}";
        
        $result = $this->con->query($sql);
        $purchases = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $purchases[] = $row;
            }
        }
        
        return $purchases;
    }
    
    // Count total purchases
    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->con->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    // Get purchase by ID with items
    public function getById($id) {
        $id = (int)$id;
        $sql = "SELECT p.*, s.name as supplier_name, pm.name as payment_method 
               FROM {$this->table} p 
               LEFT JOIN suppliers s ON p.supplier_id = s.id 
               LEFT JOIN payment_methods pm ON p.payment_method_id = pm.id 
               WHERE p.id = {$id}";
        
        $result = $this->con->query($sql);
        
        if ($result->num_rows > 0) {
            $purchase = $result->fetch_assoc();
            
            // Get purchase items
            $sql = "SELECT pi.*, p.name as product_name 
                   FROM {$this->items_table} pi 
                   LEFT JOIN products p ON pi.product_id = p.id 
                   WHERE pi.purchase_id = {$id}";
            
            $result = $this->con->query($sql);
            $items = [];
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $items[] = $row;
                }
            }
            
            $purchase['items'] = $items;
            return $purchase;
        } else {
            return false;
        }
    }
    
    // Delete purchase
    public function delete($id) {
        $id = (int)$id;
        
        // Start transaction
        $this->con->begin_transaction();
        
        try {
            // Get purchase items to update product quantities
            $sql = "SELECT product_id, quantity FROM {$this->items_table} WHERE purchase_id = {$id}";
            $result = $this->con->query($sql);
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Decrease product quantity
                    $sql = "UPDATE products SET quantity = quantity - {$row['quantity']} 
                            WHERE id = {$row['product_id']}";
                    $this->con->query($sql);
                }
            }
            
            // Delete purchase items
            $sql = "DELETE FROM {$this->items_table} WHERE purchase_id = {$id}";
            $this->con->query($sql);
            
            // Delete ledger entries
            $sql = "DELETE FROM {$this->ledger_table} WHERE transaction_type = 'purchase' AND reference_id = {$id}";
            $this->con->query($sql);
            
            // Delete purchase
            $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
            $this->con->query($sql);
            
            // Commit transaction
            $this->con->commit();
            return true;
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->con->rollback();
            return false;
        }
    }
    
    // Update purchase
    public function update($id) {
        $id = (int)$id;
        
        // Start transaction
        $this->con->begin_transaction();
        
        try {
            // Get existing purchase items to update product quantities
            $sql = "SELECT product_id, quantity FROM {$this->items_table} WHERE purchase_id = {$id}";
            $result = $this->con->query($sql);
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Decrease product quantity (reverse the original addition)
                    $sql = "UPDATE products SET quantity = quantity - {$row['quantity']} 
                            WHERE id = {$row['product_id']}";
                    $this->con->query($sql);
                }
            }
            
            // Delete old purchase items
            $sql = "DELETE FROM {$this->items_table} WHERE purchase_id = {$id}";
            $this->con->query($sql);
            
            // Delete old ledger entries
            $sql = "DELETE FROM {$this->ledger_table} WHERE transaction_type = 'purchase' AND reference_id = {$id}";
            $this->con->query($sql);
            
            // Update purchase
            $sql = "UPDATE {$this->table} SET 
                    reference_no = '{$this->reference_no}',
                    supplier_id = {$this->supplier_id},
                    total_amount = {$this->total_amount},
                    paid_amount = {$this->paid_amount},
                    due_amount = {$this->due_amount},
                    payment_method_id = {$this->payment_method_id},
                    purchase_date = '{$this->purchase_date}',
                    notes = '{$this->notes}',
                    status = {$this->status}
                    WHERE id = {$id}";
            
            $this->con->query($sql);
            
            // Insert new purchase items
            foreach ($this->items as $item) {
                $sql = "INSERT INTO {$this->items_table} 
                        (purchase_id, product_id, quantity, unit_price, total_price) 
                        VALUES ({$id}, {$item['product_id']}, {$item['quantity']}, 
                        {$item['unit_price']}, {$item['total_price']})";
                $this->con->query($sql);
                
                // Update product quantity
                $sql = "UPDATE products SET quantity = quantity + {$item['quantity']} 
                        WHERE id = {$item['product_id']}";
                $this->con->query($sql);
            }
            
            // Create ledger entry for purchase
            $account_type = '';
            switch ($this->payment_method_id) {
                case 1: // Cash
                    $account_type = 'cash';
                    break;
                case 2: // Bank Transfer
                    $account_type = 'bank';
                    break;
                case 3: // Credit Card
                case 4: // Debit Card
                    $account_type = 'card';
                    break;
                default:
                    $account_type = 'cash';
            }
            
            if ($this->paid_amount > 0) {
                $sql = "INSERT INTO {$this->ledger_table} 
                        (transaction_date, reference_no, transaction_type, reference_id, 
                        account_type, payment_method_id, debit, credit, notes) 
                        VALUES ('{$this->purchase_date}', '{$this->reference_no}', 'purchase', 
                        {$id}, '{$account_type}', {$this->payment_method_id}, 
                        0, {$this->paid_amount}, 'Purchase payment')";
                $this->con->query($sql);
            }
            
            // Commit transaction
            $this->con->commit();
            return true;
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->con->rollback();
            return false;
        }
    }
    
    // Search purchases
    public function search($keyword, $start_date = null, $end_date = null, $supplier_id = null, $page = 1, $perPage = 10) {
        $keyword = $this->con->real_escape_string($keyword);
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT p.*, s.name as supplier_name, pm.name as payment_method 
               FROM {$this->table} p 
               LEFT JOIN suppliers s ON p.supplier_id = s.id 
               LEFT JOIN payment_methods pm ON p.payment_method_id = pm.id 
               WHERE (p.reference_no LIKE '%{$keyword}%' OR s.name LIKE '%{$keyword}%')";
        
        if ($start_date && $end_date) {
            $start_date = $this->con->real_escape_string($start_date);
            $end_date = $this->con->real_escape_string($end_date);
            $sql .= " AND p.purchase_date BETWEEN '{$start_date}' AND '{$end_date}'";
        }
        
        if ($supplier_id) {
            $supplier_id = (int)$supplier_id;
            $sql .= " AND p.supplier_id = {$supplier_id}";
        }
        
        $sql .= " ORDER BY p.id DESC LIMIT {$offset}, {$perPage}";
        
        $result = $this->con->query($sql);
        $purchases = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $purchases[] = $row;
            }
        }
        
        return $purchases;
    }
    
    // Count search results
    public function countSearch($keyword, $start_date = null, $end_date = null, $supplier_id = null) {
        $keyword = $this->con->real_escape_string($keyword);
        
        $sql = "SELECT COUNT(*) as total 
               FROM {$this->table} p 
               LEFT JOIN suppliers s ON p.supplier_id = s.id 
               WHERE (p.reference_no LIKE '%{$keyword}%' OR s.name LIKE '%{$keyword}%')";
        
        if ($start_date && $end_date) {
            $start_date = $this->con->real_escape_string($start_date);
            $end_date = $this->con->real_escape_string($end_date);
            $sql .= " AND p.purchase_date BETWEEN '{$start_date}' AND '{$end_date}'";
        }
        
        if ($supplier_id) {
            $supplier_id = (int)$supplier_id;
            $sql .= " AND p.supplier_id = {$supplier_id}";
        }
        
        $result = $this->con->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>