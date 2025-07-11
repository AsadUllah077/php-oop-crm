<?php
include '../config.php';
require_once ROOT_PATH . 'purchases/purchase.class.php';
require_once ROOT_PATH . 'suppliers/supplier.class.php';
require_once ROOT_PATH . 'products/product.class.php';

// Initialize classes
$purchaseObj = new Purchase();
$supplierObj = new Supplier();
$productObj = new Product();

// Get all suppliers for dropdown
$suppliers = $supplierObj->getAll(1, 1000);

// Get all products for dropdown
$products = $productObj->getAll(1, 1000);

// Generate reference number
$reference_no = $purchaseObj->generateReferenceNo();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set purchase details
    $purchaseObj->setReferenceNo($_POST['reference_no']);
    $purchaseObj->setSupplierId($_POST['supplier_id']);
    $purchaseObj->setTotalAmount($_POST['total_amount']);
    $purchaseObj->setPaidAmount($_POST['paid_amount']);
    $purchaseObj->setPaymentMethodId($_POST['payment_method_id']);
    $purchaseObj->setPurchaseDate($_POST['purchase_date']);
    $purchaseObj->setNotes($_POST['notes']);
    $purchaseObj->setStatus($_POST['status']);
    
    // Add purchase items
    $product_ids = $_POST['product_id'];
    $quantities = $_POST['quantity'];
    $unit_prices = $_POST['unit_price'];
    
    for ($i = 0; $i < count($product_ids); $i++) {
        if (!empty($product_ids[$i]) && !empty($quantities[$i]) && !empty($unit_prices[$i])) {
            $purchaseObj->addItem($product_ids[$i], $quantities[$i], $unit_prices[$i]);
        }
    }
    
    // Create purchase
    if ($purchaseObj->create()) {
        $success_message = "Purchase created successfully!";
        // Redirect to purchases list after successful creation
        header("Location: purchases.php");
        exit();
    } else {
        $error_message = "Failed to create purchase. Please try again.";
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
            <h2>Create New Purchase</h2>
            <div>
                <a href="purchases.php" class="btn btn-secondary btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Purchases
                </a>
            </div>
        </div>

        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>

        <!-- Purchase Create Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Purchase Information</h5>
            </div>
            <div class="card-body">
                <form action="" method="post" id="purchaseForm">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="referenceNo" class="form-label">Reference No</label>
                            <input type="text" class="form-control" id="referenceNo" name="reference_no" value="<?php echo htmlspecialchars($reference_no); ?>" readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="purchaseDate" class="form-label">Purchase Date</label>
                            <input type="date" class="form-control" id="purchaseDate" name="purchase_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="supplierId" class="form-label">Supplier</label>
                            <select class="form-select" id="supplierId" name="supplier_id" required>
                                <option value="">Select Supplier</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?php echo $supplier['id']; ?>">
                                    <?php echo htmlspecialchars($supplier['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="1" selected>Completed</option>
                                <option value="2">Pending</option>
                                <option value="0">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Purchase Items -->
                    <h5 class="mb-3">Purchase Items</h5>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered" id="purchaseItemsTable">
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
                                <tr>
                                    <td>
                                        <select class="form-select product-select" name="product_id[]" required>
                                            <option value="">Select Product</option>
                                            <?php foreach ($products as $product): ?>
                                            <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>">
                                                <?php echo htmlspecialchars($product['name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control quantity" name="quantity[]" min="1" value="1" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control unit-price" name="unit_price[]" min="0.01" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control item-total" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
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
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="mb-3 row">
                                        <label for="totalAmount" class="col-sm-4 col-form-label">Total Amount</label>
                                        <div class="col-sm-8">
                                            <input type="number" step="0.01" class="form-control" id="totalAmount" name="total_amount" readonly>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="paidAmount" class="col-sm-4 col-form-label">Paid Amount</label>
                                        <div class="col-sm-8">
                                            <input type="number" step="0.01" class="form-control" id="paidAmount" name="paid_amount" value="0">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="dueAmount" class="col-sm-4 col-form-label">Due Amount</label>
                                        <div class="col-sm-8">
                                            <input type="number" step="0.01" class="form-control" id="dueAmount" readonly>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="paymentMethod" class="col-sm-4 col-form-label">Payment Method</label>
                                        <div class="col-sm-8">
                                            <select class="form-select" id="paymentMethod" name="payment_method_id">
                                                <option value="1">Cash</option>
                                                <option value="2">Bank Transfer</option>
                                                <option value="3">Credit Card</option>
                                                <option value="4">Debit Card</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Purchase
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for dynamic purchase form -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add new item row
        document.getElementById('addItemBtn').addEventListener('click', function() {
            const tbody = document.querySelector('#purchaseItemsTable tbody');
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
        
        // Add event listeners to initial row
        const initialRow = document.querySelector('#purchaseItemsTable tbody tr');
        addRowEventListeners(initialRow);
        
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
                if (document.querySelectorAll('#purchaseItemsTable tbody tr').length > 1) {
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
        document.getElementById('purchaseForm').addEventListener('submit', function(e) {
            const totalAmount = parseFloat(document.getElementById('totalAmount').value) || 0;
            if (totalAmount <= 0) {
                e.preventDefault();
                alert('Please add at least one item to the purchase.');
            }
        });
    });
</script>

<?php
require ROOT_PATH . 'includes/footer.php';
?>