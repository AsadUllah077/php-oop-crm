<?php
include '../config.php';
require_once ROOT_PATH . 'purchases/purchase.class.php';
require_once ROOT_PATH . 'suppliers/supplier.class.php';

// Initialize Purchase class
$purchaseObj = new Purchase();
$supplierObj = new Supplier();

// Handle delete action
if(isset($_GET['delete'])){
    $id = $_GET['delete'] ?? 0;
    $purchase = new Purchase();
    if(!$purchase->delete($id)){
        die("error while deleting");
    }
}

// Pagination settings
$perPage = 5; // Number of items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Handle search
$search_keyword = '';
$start_date = '';
$end_date = '';
$supplier_id = null;
$totalItems = 0;

if (isset($_GET['search'])) {
    $search_keyword = $_GET['search_keyword'] ?? '';
    $start_date = $_GET['start_date'] ?? '';
    $end_date = $_GET['end_date'] ?? '';
    $supplier_id = isset($_GET['supplier_id']) && $_GET['supplier_id'] !== '' ? (int)$_GET['supplier_id'] : null;
    
    // Get filtered purchases with pagination
    $purchases = $purchaseObj->search($search_keyword, $start_date, $end_date, $supplier_id, $page, $perPage);
    $totalItems = $purchaseObj->countSearch($search_keyword, $start_date, $end_date, $supplier_id);
} else {
    // Get all purchases with pagination
    $purchases = $purchaseObj->getAll($page, $perPage);
    $totalItems = $purchaseObj->countAll();
}

// Get all suppliers for dropdown
$suppliers = $supplierObj->getAll(1, 1000); // Get all suppliers

// Calculate pagination values
$totalPages = ceil($totalItems / $perPage);

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
            <h2>Purchases</h2>
            <div>
                <a href="create_purchase.php" class="btn btn-primary btn-sm me-2">
                    <i class="fas fa-plus me-1"></i> Add Purchase
                </a>
                <button class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-bell"></i>
                </button>
            </div>
        </div>

        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Search Purchases</h5>
            </div>
            <div class="card-body">
                <form action="" method="get" class="row g-3">
                    <div class="col-md-3">
                        <label for="searchKeyword" class="form-label">Search Keyword</label>
                        <input type="text" class="form-control" id="searchKeyword" name="search_keyword" value="<?php echo htmlspecialchars($search_keyword); ?>" placeholder="Search by reference no">
                    </div>
                    <div class="col-md-2">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="endDate" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="supplierFilter" class="form-label">Supplier</label>
                        <select class="form-select" id="supplierFilter" name="supplier_id">
                            <option value="">All Suppliers</option>
                            <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?php echo $supplier['id']; ?>" <?php echo $supplier_id == $supplier['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($supplier['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" name="search" value="1" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                    </div>
                    <!-- Keep page parameter if it exists -->
                    <?php if (isset($_GET['page'])): ?>
                    <input type="hidden" name="page" value="<?php echo $page; ?>">
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Purchases Table -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Purchase Transactions</h5>
                <?php if (isset($_GET['search']) && (!empty($search_keyword) || !empty($start_date) || !empty($end_date) || $supplier_id)): ?>
                <div>
                    <span class="badge bg-info">Search results</span>
                    <a href="purchases.php" class="btn btn-sm btn-outline-secondary ms-2">Clear</a>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Reference No</th>
                                <th>Supplier</th>
                                <th>Date</th>
                                <th>Total Amount</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($purchases)): ?>
                            <tr>
                                <td colspan="9" class="text-center">No purchases found</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($purchases as $purchase): ?>
                                <tr>
                                    <td>#<?php echo $purchase['id']; ?></td>
                                    <td><?php echo $purchase['reference_no']; ?></td>
                                    <td><?php echo $purchase['supplier_name']; ?></td>
                                    <td><?php echo date('d M Y', strtotime($purchase['purchase_date'])); ?></td>
                                    <td><?php echo number_format($purchase['total_amount'], 2); ?></td>
                                    <td><?php echo number_format($purchase['paid_amount'], 2); ?></td>
                                    <td><?php echo number_format($purchase['due_amount'], 2); ?></td>
                                    <td>
                                        <?php if ($purchase['status'] == 1): ?>
                                            <span class="badge bg-success">Completed</span>
                                        <?php elseif ($purchase['status'] == 2): ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Cancelled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                    <a href="update_purchase.php?id=<?php echo $purchase['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></a>
                                        <a href="view_purchase.php?id=<?php echo $purchase['id']; ?>" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-eye"></i></a>
                                        <a href="purchases.php?delete=<?php echo $purchase['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this purchase? This will also update product quantities and remove ledger entries.')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Controls -->
                <?php if ($totalPages > 1): ?>
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <!-- Previous Page Link -->
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <?php else: ?>
                            <li class="page-item disabled">
                                <a class="page-link" href="#" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <!-- Page Numbers -->
                            <?php 
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $startPage + 4);
                            if ($endPage - $startPage < 4) {
                                $startPage = max(1, $endPage - 4);
                            }
                            
                            for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                            
                            <!-- Next Page Link -->
                            <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            <?php else: ?>
                            <li class="page-item disabled">
                                <a class="page-link" href="#" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <div class="text-center mt-2">
                    <small class="text-muted">Showing <?php echo count($purchases); ?> of <?php echo $totalItems; ?> purchases (Page <?php echo $page; ?> of <?php echo $totalPages; ?>)</small>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
require ROOT_PATH . 'includes/footer.php';
?>