<?php
include '../config.php';
require ROOT_PATH . 'includes/header.php';
require_once ROOT_PATH . 'customers/customer.class.php';
$id = $_GET['id'] ?? 0;
// Initialize Customer class
$customerObj = new Customer();

// Get customer by ID
$customer_record = $customerObj->getById($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['customer_id'];
    $customer = new Customer();

    $customer->setName($_POST['customer_name']);
    $customer->setPhone($_POST['phone']);
    $customer->setEmail($_POST['email']);
    $customer->setAddress($_POST['address']);
    $customer->setStatus($_POST['status']);

    
    if ($customer->update($id)) {
        $success_message = "Customer updated successfully!";
        // Redirect to customers list after successful update
        header("Location: customers.php");
        exit();
    } else {
        $error_message = "Failed to update customer. Please try again.";
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
            <h2>Update Customer</h2>
            <div>
                <a href="customers.php" class="btn btn-secondary btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Customers
                </a>
            </div>
        </div>

        <!-- Customer Update Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Edit Customer Information</h5>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <input type="hidden" name="customer_id" value="<?php echo $id;?>"> <!-- This would be dynamically set -->
                    <div class="mb-3">
                        <label for="customerName" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customerName" name="customer_name" value="<?php echo $customer_record['name'] ?? ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $customer_record['phone'] ?? ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $customer_record['email'] ?? ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo $customer_record['address'] ?? ''; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="1" <?php echo $customer_record['status'] == 1 ? 'selected' : '' ?> >Active</option>
                            <option value="0" <?php  echo $customer_record['status'] == 0 ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Customer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require ROOT_PATH . 'includes/footer.php';
?>