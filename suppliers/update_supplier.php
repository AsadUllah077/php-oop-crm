<?php
include '../config.php';
require ROOT_PATH . 'includes/header.php';
require_once ROOT_PATH . 'suppliers/supplier.class.php';
$id = $_GET['id'] ?? 0;
// Initialize Supplier class
$supplierObj = new Supplier();

// Get supplier by ID
$supplier_record = $supplierObj->getById($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['supplier_id'];
    $supplier = new Supplier();

    $supplier->setName($_POST['supplier_name']);
    $supplier->setContactPerson($_POST['contact_person']);
    $supplier->setPhone($_POST['phone']);
    $supplier->setEmail($_POST['email']);
    $supplier->setAddress($_POST['address']);
    $supplier->setStatus($_POST['status']);

    
    if ($supplier->update($id)) {
        $success_message = "Supplier updated successfully!";
        // Redirect to suppliers list after successful update
        header("Location: suppliers.php");
        exit();
    } else {
        $error_message = "Failed to update supplier. Please try again.";
    }
    
}
?>
<div class="d-flex">
    <!-- Sidebar -->

    <?php
    require ROOT_PATH . 'includes/sidebar.php';
    ?>
    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Update Supplier</h2>
            <div>
                <a href="suppliers.php" class="btn btn-secondary btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Suppliers
                </a>
            </div>
        </div>

        <!-- Supplier Update Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Edit Supplier Information</h5>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <input type="hidden" name="supplier_id" value="<?php echo $id;?>"> <!-- This would be dynamically set -->
                    <div class="mb-3">
                        <label for="supplierName" class="form-label">Supplier Name</label>
                        <input type="text" class="form-control" id="supplierName" name="supplier_name" value="<?php echo $supplier_record['name'] ?? ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="contactPerson" class="form-label">Contact Person</label>
                        <input type="text" class="form-control" id="contactPerson" name="contact_person" value="<?php echo $supplier_record['contact_person'] ?? ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $supplier_record['phone'] ?? ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $supplier_record['email'] ?? ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo $supplier_record['address'] ?? ''; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="1" <?php echo $supplier_record['status'] == 1 ? 'selected' : '' ?> >Active</option>
                            <option value="0" <?php  echo $supplier_record['status'] == 0 ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Supplier
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require ROOT_PATH . 'includes/footer.php';
?>