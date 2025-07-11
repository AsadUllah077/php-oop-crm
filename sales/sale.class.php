<?php
require_once ROOT_PATH . 'database/database.php';
require_once ROOT_PATH . 'customers/customer.class.php';
require_once ROOT_PATH . 'products/product.class.php';

class Sale extends Database {
    private $table = 'sales';
    private $items_table = 'sale_items';
    private $ledger_table = 'ledger';
    
    // Properties
    private $id;
    private $reference_no;
    private $customer_id;
    private $total_amount;
    private $paid_amount;
    private $due_amount;
    private $payment_method_id;
    private $sale_date;
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
    
    public function setCustomerId($customer_id) {
        $this->customer_id = (int)$customer_id;
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
    
    public function setSaleDate($sale_date) {
        $this->sale_date = $this->con->real_escape_string($sale_date);
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
        $prefix = 'SALE-';
        $date = date('Ymd');
        $sql = "SELECT MAX(id) as max_id FROM {$this->table}";
        $result = $this->con->query($sql);
        $row = $result->fetch_assoc();
        $next_id = ($row['max_id'] ?? 0) + 1;
        return $prefix . $date . '-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
    }
    
    // Create new sale with items and ledger entry
    public function create() {
        // Start transaction
        $this->con->begin_transaction();
        
        try {
            // Insert sale
            $sql = "INSERT INTO {$this->table} 
                    (reference_no, customer_id, total_amount, paid_amount, due_amount, 
                    payment_method_id, sale_date, notes, status) 
                    VALUES ('{$this->reference_no}', {$this->customer_id}, {$this->total_amount}, 
                    {$this->paid_amount}, {$this->due_amount}, {$this->payment_method_id}, 
                    '{$this->sale_date}', '{$this->notes}', {$this->status})";
            
            $this->con->query($sql);
            $sale_id = $this->con->insert_id;
            
            // Insert sale items
            foreach ($this->items as $item) {
                $sql = "INSERT INTO {$this->items_table} 
                        (sale_id, product_id, quantity, unit_price, total_price) 
                        VALUES ({$sale_id}, {$item['product_id']}, {$item['quantity']}, 
                        {$item['unit_price']}, {$item['total_price']})";
                $this->con->query($sql);
                
                // Update product quantity
                $sql = "UPDATE products SET quantity = quantity - {$item['quantity']} 
                        WHERE id = {$item['product_id']}";
                $this->con->query($sql);
            }
            
            // Create ledger entry for sale
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
                        VALUES ('{$this->sale_date}', '{$this->reference_no}', 'sale', 
                        {$sale_id}, '{$account_type}', {$this->payment_method_id}, 
                        {$this->paid_amount}, 0, 'Sale receipt')";
                $this->con->query($sql);
            }
            
            // Commit transaction
            $this->con->commit();
            return $sale_id;
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->con->rollback();
            return false;
        }
    }
    
    // Get all sales with pagination
    public function getAll($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT s.*, c.name as customer_name, pm.name as payment_method 
               FROM {$this->table} s 
               LEFT JOIN customers c ON s.customer_id = c.id 
               LEFT JOIN payment_methods pm ON s.payment_method_id = pm.id 
               ORDER BY s.id DESC LIMIT {$offset}, {$perPage}";
        
        $result = $this->con->query($sql);
        $sales = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $sales[] = $row;
            }
        }
        
        return $sales;
    }
    
    // Count total sales
    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->con->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    // Get sale by ID with items
    public function getById($id) {
        $id = (int)$id;
        $sql = "SELECT s.*, c.name as customer_name, pm.name as payment_method 
               FROM {$this->table} s 
               LEFT JOIN customers c ON s.customer_id = c.id 
               LEFT JOIN payment_methods pm ON s.payment_method_id = pm.id 
               WHERE s.id = {$id}";
        
        $result = $this->con->query($sql);
        
        if ($result->num_rows > 0) {
            $sale = $result->fetch_assoc();
            
            // Get sale items
            $sql = "SELECT si.*, p.name as product_name 
                   FROM {$this->items_table} si 
                   LEFT JOIN products p ON si.product_id = p.id 
                   WHERE si.sale_id = {$id}";
            
            $result = $this->con->query($sql);
            $items = [];
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $items[] = $row;
                }
            }
            
            $sale['items'] = $items;
            return $sale;
        } else {
            return false;
        }
    }
    
    // Delete sale
    public function delete($id) {
        $id = (int)$id;
        
        // Start transaction
        $this->con->begin_transaction();
        
        try {
            // Get sale items to update product quantities
            $sql = "SELECT product_id, quantity FROM {$this->items_table} WHERE sale_id = {$id}";
            $result = $this->con->query($sql);
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Increase product quantity
                    $sql = "UPDATE products SET quantity = quantity + {$row['quantity']} 
                            WHERE id = {$row['product_id']}";
                    $this->con->query($sql);
                }
            }
            
            // Delete sale items
            $sql = "DELETE FROM {$this->items_table} WHERE sale_id = {$id}";
            $this->con->query($sql);
            
            // Delete ledger entries
            $sql = "DELETE FROM {$this->ledger_table} WHERE transaction_type = 'sale' AND reference_id = {$id}";
            $this->con->query($sql);
            
            // Delete sale
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
    
    // Update sale
    public function update($id) {
        $id = (int)$id;
        
        // Start transaction
        $this->con->begin_transaction();
        
        try {
            // Get existing sale items to update product quantities
            $sql = "SELECT product_id, quantity FROM {$this->items_table} WHERE sale_id = {$id}";
            $result = $this->con->query($sql);
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Increase product quantity (reverse the original deduction)
                    $sql = "UPDATE products SET quantity = quantity + {$row['quantity']} 
                            WHERE id = {$row['product_id']}";
                    $this->con->query($sql);
                }
            }
            
            // Delete old sale items
            $sql = "DELETE FROM {$this->items_table} WHERE sale_id = {$id}";
            $this->con->query($sql);
            
            // Delete old ledger entries
            $sql = "DELETE FROM {$this->ledger_table} WHERE transaction_type = 'sale' AND reference_id = {$id}";
            $this->con->query($sql);
            
            // Update sale
            $sql = "UPDATE {$this->table} SET 
                    reference_no = '{$this->reference_no}',
                    customer_id = {$this->customer_id},
                    total_amount = {$this->total_amount},
                    paid_amount = {$this->paid_amount},
                    due_amount = {$this->due_amount},
                    payment_method_id = {$this->payment_method_id},
                    sale_date = '{$this->sale_date}',
                    notes = '{$this->notes}',
                    status = {$this->status}
                    WHERE id = {$id}";
            
            $this->con->query($sql);
            
            // Insert new sale items
            foreach ($this->items as $item) {
                $sql = "INSERT INTO {$this->items_table} 
                        (sale_id, product_id, quantity, unit_price, total_price) 
                        VALUES ({$id}, {$item['product_id']}, {$item['quantity']}, 
                        {$item['unit_price']}, {$item['total_price']})";
                $this->con->query($sql);
                
                // Update product quantity
                $sql = "UPDATE products SET quantity = quantity - {$item['quantity']} 
                        WHERE id = {$item['product_id']}";
                $this->con->query($sql);
            }
            
            // Create ledger entry for sale
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
                        VALUES ('{$this->sale_date}', '{$this->reference_no}', 'sale', 
                        {$id}, '{$account_type}', {$this->payment_method_id}, 
                        {$this->paid_amount}, 0, 'Sale receipt')";
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
    
    // Search sales
    public function search($keyword, $start_date = null, $end_date = null, $customer_id = null, $page = 1, $perPage = 10) {
        $keyword = $this->con->real_escape_string($keyword);
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT s.*, c.name as customer_name, pm.name as payment_method 
               FROM {$this->table} s 
               LEFT JOIN customers c ON s.customer_id = c.id 
               LEFT JOIN payment_methods pm ON s.payment_method_id = pm.id 
               WHERE (s.reference_no LIKE '%{$keyword}%' OR c.name LIKE '%{$keyword}%')";
        
        if ($start_date && $end_date) {
            $start_date = $this->con->real_escape_string($start_date);
            $end_date = $this->con->real_escape_string($end_date);
            $sql .= " AND s.sale_date BETWEEN '{$start_date}' AND '{$end_date}'";
        }
        
        if ($customer_id) {
            $customer_id = (int)$customer_id;
            $sql .= " AND s.customer_id = {$customer_id}";
        }
        
        $sql .= " ORDER BY s.id DESC LIMIT {$offset}, {$perPage}";
        
        $result = $this->con->query($sql);
        $sales = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $sales[] = $row;
            }
        }
        
        return $sales;
    }
    
    // Count search results
    public function countSearch($keyword, $start_date = null, $end_date = null, $customer_id = null) {
        $keyword = $this->con->real_escape_string($keyword);
        
        $sql = "SELECT COUNT(*) as total 
               FROM {$this->table} s 
               LEFT JOIN customers c ON s.customer_id = c.id 
               WHERE (s.reference_no LIKE '%{$keyword}%' OR c.name LIKE '%{$keyword}%')";
        
        if ($start_date && $end_date) {
            $start_date = $this->con->real_escape_string($start_date);
            $end_date = $this->con->real_escape_string($end_date);
            $sql .= " AND s.sale_date BETWEEN '{$start_date}' AND '{$end_date}'";
        }
        
        if ($customer_id) {
            $customer_id = (int)$customer_id;
            $sql .= " AND s.customer_id = {$customer_id}";
        }
        
        $result = $this->con->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>