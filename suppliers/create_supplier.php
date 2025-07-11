<?php
include '../config.php';
require_once ROOT_PATH . 'suppliers/supplier.class.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier = new Supplier();
    $supplier->setName($_POST['supplier_name']);
    $supplier->setContactPerson($_POST['contact_person']);
    $supplier->setPhone($_POST['phone']);
    $supplier->setEmail($_POST['email']);
    $supplier->setAddress($_POST['address']);
    $supplier->setStatus($_POST['status']);
    
    if ($supplier->create()) {
        $success_message = "Supplier created successfully!";
        // Redirect to suppliers list after successful creation
        header("Location: suppliers.php");
        exit();
    } else {
        $error_message = "Failed to create supplier. Please try again.";
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
            <h2>Create New Supplier</h2>
            <div>
                <a href="suppliers.php" class="btn btn-secondary btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Suppliers
                </a>
            </div>
        </div>

        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>

        <!-- Supplier Create Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Supplier Information</h5>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="supplierName" class="form-label">Supplier Name</label>
                        <input type="text" class="form-control" id="supplierName" name="supplier_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="contactPerson" class="form-label">Contact Person</label>
                        <input type="text" class="form-control" id="contactPerson" name="contact_person">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Supplier
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require ROOT_PATH . 'includes/footer.php';
?>