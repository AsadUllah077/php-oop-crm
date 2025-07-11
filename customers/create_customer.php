<?php
include '../config.php';
require_once ROOT_PATH . 'customers/customer.class.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer = new Customer();
    $customer->setName($_POST['customer_name']);
    $customer->setPhone($_POST['phone']);
    $customer->setEmail($_POST['email']);
    $customer->setAddress($_POST['address']);
    $customer->setStatus($_POST['status']);
    
    if ($customer->create()) {
        $success_message = "Customer created successfully!";
        // Redirect to customers list after successful creation
        header("Location: customers.php");
        exit();
    } else {
        $error_message = "Failed to create customer. Please try again.";
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
            <h2>Create New Customer</h2>
            <div>
                <a href="customers.php" class="btn btn-secondary btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Customers
                </a>
            </div>
        </div>

        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>

        <!-- Customer Create Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Customer Information</h5>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="customerName" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customerName" name="customer_name" required>
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
                        <i class="fas fa-save me-1"></i> Save Customer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require ROOT_PATH . 'includes/footer.php';
?>