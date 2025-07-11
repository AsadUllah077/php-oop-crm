<?php
include '../config.php';
require_once ROOT_PATH . 'purchases/purchase.class.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: purchases.php");
    exit();
}

$id = (int)$_GET['id'];
$purchaseObj = new Purchase();
$purchase = $purchaseObj->getById($id);

// If purchase not found, redirect to purchases list
if (!$purchase) {
    header("Location: purchases.php");
    exit();
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
            <h2>Purchase Details</h2>
            <div>
                <a href="purchases.php" class="btn btn-secondary btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Purchases
                </a>
                <button class="btn btn-primary btn-sm" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print
                </button>
            </div>
        </div>

        <!-- Purchase Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Purchase #<?php echo $purchase['id']; ?></h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Purchase Information</h6>
                        <p><strong>Reference No:</strong> <?php echo $purchase['reference_no']; ?></p>
                        <p><strong>Purchase Date:</strong> <?php echo date('d M Y', strtotime($purchase['purchase_date'])); ?></p>
                        <p><strong>Status:</strong> 
                            <?php if ($purchase['status'] == 1): ?>
                                <span class="badge bg-success">Completed</span>
                            <?php elseif ($purchase['status'] == 2): ?>
                                <span class="badge bg-warning">Pending</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Cancelled</span>
                            <?php endif; ?>
                        </p>
                        <p><strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($purchase['notes'])); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Supplier Information</h6>
                        <p><strong>Supplier:</strong> <?php echo $purchase['supplier_name']; ?></p>
                        <h6 class="fw-bold mt-3">Payment Information</h6>
                        <p><strong>Payment Method:</strong> <?php echo $purchase['payment_method']; ?></p>
                        <p><strong>Total Amount:</strong> <?php echo number_format($purchase['total_amount'], 2); ?></p>
                        <p><strong>Paid Amount:</strong> <?php echo number_format($purchase['paid_amount'], 2); ?></p>
                        <p><strong>Due Amount:</strong> <?php echo number_format($purchase['due_amount'], 2); ?></p>
                    </div>
                </div>

                <h6 class="fw-bold">Purchase Items</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 1; foreach ($purchase['items'] as $item): ?>
                            <tr>
                                <td><?php echo $counter++; ?></td>
                                <td><?php echo $item['product_name']; ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo number_format($item['unit_price'], 2); ?></td>
                                <td><?php echo number_format($item['total_price'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total:</th>
                                <th><?php echo number_format($purchase['total_amount'], 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .sidebar, .main-content > div:first-child, .card-header {
            display: none;
        }
        .main-content {
            margin-left: 0;
            padding: 0;
        }
        .card {
            border: none;
        }
    }
</style>

<?php
require ROOT_PATH . 'includes/footer.php';
?>