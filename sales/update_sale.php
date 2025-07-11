<?php
include '../config.php';
require_once ROOT_PATH . 'sales/sale.class.php';
require_once ROOT_PATH . 'customers/customer.class.php';
require_once ROOT_PATH . 'products/product.class.php';

// Initialize classes
$saleObj = new Sale();
$customerObj = new Customer();
$productObj = new Product();

// Get all customers for dropdown
$customers = $customerObj->getAll(1, 1000);

// Get all products for dropdown
$products = $productObj->getAll(1, 1000);

// Get sale ID from URL
$id = $_GET['id'] ?? 0;

// Get sale by ID
$sale = $saleObj->getById($id);

if (!$sale) {
    // Redirect to sales list if sale not found
    header("Location: sales.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set sale details
    $saleObj->setReferenceNo($_POST['reference_no']);
    $saleObj->setCustomerId($_POST['customer_id']);
    $saleObj->setTotalAmount($_POST['total_amount']);
    $saleObj->setPaidAmount($_POST['paid_amount']);
    $saleObj->setPaymentMethodId($_POST['payment_method_id']);
    $saleObj->setSaleDate($_POST['sale_date']);
    $saleObj->setNotes($_POST['notes']);
    $saleObj->setStatus($_POST['status']);
    
    // Add sale items
    $product_ids = $_POST['product_id'];
    $quantities = $_POST['quantity'];
    $unit_prices = $_POST['unit_price'];
    
    for ($i = 0; $i < count($product_ids); $i++) {
        if (!empty($product_ids[$i]) && !empty($quantities[$i]) && !empty($unit_prices[$i])) {
            $saleObj->addItem($product_ids[$i], $quantities[$i], $unit_prices[$i]);
        }
    }
    
    // Update sale
    if ($saleObj->update($id)) {
        $success_message = "Sale updated successfully!";
        // Redirect to sales list after successful update
        header("Location: sales.php");
        exit();
    } else {
        $error_message = "Failed to update sale. Please try again.";
    }
}

require ROOT_PATH . 'includes/header.php';
?>
<div class="d-flex">
    <!-- Sidebar -->

    <?php
    require ROOT_PATH . 'includes/sidebar.php';
    ?>
    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Update Sale</h2>
            <div>
                <a href="sales.php" class="btn btn-secondary btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Sales
                </a>
            </div>
        </div>

        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>

        <!-- Sale Update Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Sale Information</h5>
            </div>
            <div class="card-body">
                <form action="" method="post" id="saleForm">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="referenceNo" class="form-label">Reference No</label>
                            <input type="text" class="form-control" id="referenceNo" name="reference_no" value="<?php echo htmlspecialchars($sale['reference_no']); ?>" readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="saleDate" class="form-label">Sale Date</label>
                            <input type="date" class="form-control" id="saleDate" name="sale_date" value="<?php echo $sale['sale_date']; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="customerId" class="form-label">Customer</label>
                            <select class="form-select" id="customerId" name="customer_id" required>
                                <option value="">Select Customer</option>
                                <?php foreach ($customers as $customer): ?>
                                <option value="<?php echo $customer['id']; ?>" <?php echo ($customer['id'] == $sale['customer_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($customer['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="1" <?php echo ($sale['status'] == 1) ? 'selected' : ''; ?>>Completed</option>
                                <option value="2" <?php echo ($sale['status'] == 2) ? 'selected' : ''; ?>>Pending</option>
                                <option value="0" <?php echo ($sale['status'] == 0) ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Sale Items -->
                    <h5 class="mb-3">Sale Items</h5>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered" id="saleItemsTable">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th width="150">Quantity</th>
                                    <th width="150">Unit Price</th>
                                    <th width="150">Total</th>
                                    <th width="50">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sale['items'] as $index => $item): ?>
                                <tr>
                                    <td>
                                        <select class="form-select product-select" name="product_id[]" required>
                                            <option value="">Select Product</option>
                                            <?php foreach ($products as $product): ?>
                                            <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>" <?php echo ($product['id'] == $item['product_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($product['name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control quantity" name="quantity[]" min="1" value="<?php echo $item['quantity']; ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control unit-price" name="unit_price[]" min="0.01" value="<?php echo $item['unit_price']; ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control item-total" value="<?php echo $item['total_price']; ?>" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5">
                                        <button type="button" class="btn btn-success btn-sm" id="addItemBtn">
                                            <i class="fas fa-plus me-1"></i> Add Item
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Payment Information -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($sale['notes']); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="mb-3 row">
                                        <label for="totalAmount" class="col-sm-4 col-form-label">Total Amount</label>
                                        <div class="col-sm-8">
                                            <input type="number" step="0.01" class="form-control" id="totalAmount" name="total_amount" value="<?php echo $sale['total_amount']; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="paidAmount" class="col-sm-4 col-form-label">Paid Amount</label>
                                        <div class="col-sm-8">
                                            <input type="number" step="0.01" class="form-control" id="paidAmount" name="paid_amount" value="<?php echo $sale['paid_amount']; ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="dueAmount" class="col-sm-4 col-form-label">Due Amount</label>
                                        <div class="col-sm-8">
                                            <input type="number" step="0.01" class="form-control" id="dueAmount" value="<?php echo $sale['due_amount']; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="paymentMethod" class="col-sm-4 col-form-label">Payment Method</label>
                                        <div class="col-sm-8">
                                            <select class="form-select" id="paymentMethod" name="payment_method_id">
                                                <option value="1" <?php echo ($sale['payment_method_id'] == 1) ? 'selected' : ''; ?>>Cash</option>
                                                <option value="2" <?php echo ($sale['payment_method_id'] == 2) ? 'selected' : ''; ?>>Bank Transfer</option>
                                                <option value="3" <?php echo ($sale['payment_method_id'] == 3) ? 'selected' : ''; ?>>Credit Card</option>
                                                <option value="4" <?php echo ($sale['payment_method_id'] == 4) ? 'selected' : ''; ?>>Debit Card</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Sale
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for dynamic sale form -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add new item row
        document.getElementById('addItemBtn').addEventListener('click', function() {
            const tbody = document.querySelector('#saleItemsTable tbody');
            const firstRow = tbody.querySelector('tr');
            const newRow = firstRow.cloneNode(true);
            
            // Clear input values
            newRow.querySelectorAll('input').forEach(input => {
                if (input.classList.contains('quantity')) {
                    input.value = 1;
                } else if (!input.readOnly) {
                    input.value = '';
                }
            });
            
            // Reset select
            newRow.querySelector('select').selectedIndex = 0;
            
            // Add event listeners to new row
            addRowEventListeners(newRow);
            
            tbody.appendChild(newRow);
        });
        
        // Add event listeners to all existing rows
        document.querySelectorAll('#saleItemsTable tbody tr').forEach(row => {
            addRowEventListeners(row);
        });
        
        // Function to add event listeners to a row
        function addRowEventListeners(row) {
            // Product selection change
            const productSelect = row.querySelector('.product-select');
            productSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.getAttribute('data-price');
                const unitPriceInput = row.querySelector('.unit-price');
                unitPriceInput.value = price;
                updateRowTotal(row);
            });
            
            // Quantity or unit price change
            row.querySelector('.quantity').addEventListener('input', function() {
                updateRowTotal(row);
            });
            
            row.querySelector('.unit-price').addEventListener('input', function() {
                updateRowTotal(row);
            });
            
            // Remove item button
            row.querySelector('.remove-item').addEventListener('click', function() {
                if (document.querySelectorAll('#saleItemsTable tbody tr').length > 1) {
                    row.remove();
                    updateTotalAmount();
                } else {
                    alert('At least one item is required.');
                }
            });
        }
        
        // Function to update row total
        function updateRowTotal(row) {
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
            const total = quantity * unitPrice;
            row.querySelector('.item-total').value = total.toFixed(2);
            updateTotalAmount();
        }
        
        // Function to update total amount
        function updateTotalAmount() {
            let total = 0;
            document.querySelectorAll('.item-total').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            
            document.getElementById('totalAmount').value = total.toFixed(2);
            updateDueAmount();
        }
        
        // Function to update due amount
        function updateDueAmount() {
            const totalAmount = parseFloat(document.getElementById('totalAmount').value) || 0;
            const paidAmount = parseFloat(document.getElementById('paidAmount').value) || 0;
            const dueAmount = totalAmount - paidAmount;
            document.getElementById('dueAmount').value = dueAmount.toFixed(2);
        }
        
        // Update due amount when paid amount changes
        document.getElementById('paidAmount').addEventListener('input', updateDueAmount);
        
        // Form submission validation
        document.getElementById('saleForm').addEventListener('submit', function(e) {
            const totalAmount = parseFloat(document.getElementById('totalAmount').value) || 0;
            if (totalAmount <= 0) {
                e.preventDefault();
                alert('Please add at least one item to the sale.');
            }
        });
    });
</script>

<?php
require ROOT_PATH . 'includes/footer.php';
?>