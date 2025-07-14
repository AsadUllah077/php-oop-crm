<?php
include 'config.php';
include ROOT_PATH.'includes/header.php';

// Initialize classes
require_once ROOT_PATH . 'products/product.class.php';
require_once ROOT_PATH . 'sales/sale.class.php';
require_once ROOT_PATH . 'purchases/purchase.class.php';
require_once ROOT_PATH . 'customers/customer.class.php';
require_once ROOT_PATH . 'suppliers/supplier.class.php';
require_once ROOT_PATH . 'categories/category.class.php';
require_once ROOT_PATH . 'ledger/ledger.class.php';

// Create objects
$productObj = new Product();
$saleObj = new Sale();
$purchaseObj = new Purchase();
$customerObj = new Customer();
$supplierObj = new Supplier();
$categoryObj = new Category();
$ledgerObj = new Ledger();

// Get counts
$totalProducts = $productObj->countAll();
$totalSales = $saleObj->countAll();
$totalPurchases = $purchaseObj->countAll();
$totalCustomers = $customerObj->countAll();
$totalSuppliers = $supplierObj->countAll();
$totalCategories = $categoryObj->countAll();

// Get financial data
$cashBalance = $ledgerObj->getBalanceByAccountType('cash');
$bankBalance = $ledgerObj->getBalanceByAccountType('bank');
$cardBalance = $ledgerObj->getBalanceByAccountType('card');
$totalRevenue = $cashBalance + $bankBalance + $cardBalance;

// Get recent products
$recentProducts = $productObj->getAll(1, 5);

// Get recent sales
$recentSales = $saleObj->getAll(1, 5);
?>
<div class="d-flex">
    <!-- Sidebar -->

    <?php
     include('includes/sidebar.php'); 

    ?>
    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dashboard Overview</h2>
            <div>
                <!-- <a href="<?php echo ROOT_URL; ?>products/create_product.php" class="btn btn-primary me-2">
                    <i class="fas fa-plus me-1"></i> Add Product
                </a> -->
                <button class="btn btn-outline-secondary">
                    <i class="fas fa-bell"></i>
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Products</h5>
                        <h2 class="card-text"><?php echo number_format($totalProducts); ?></h2>
                        <p class="card-text"><small>Products in inventory</small></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Sales</h5>
                        <h2 class="card-text"><?php echo number_format($totalSales); ?></h2>
                        <p class="card-text"><small>Sales transactions</small></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Revenue</h5>
                        <h2 class="card-text">$<?php echo number_format($totalRevenue, 2); ?></h2>
                        <p class="card-text"><small>Total revenue</small></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Purchases</h5>
                        <h2 class="card-text"><?php echo number_format($totalPurchases); ?></h2>
                        <p class="card-text"><small>Purchase transactions</small></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats Row -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Customers</h5>
                        <h2 class="card-text"><?php echo number_format($totalCustomers); ?></h2>
                        <p class="card-text"><small>Registered customers</small></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5 class="card-title">Suppliers</h5>
                        <h2 class="card-text"><?php echo number_format($totalSuppliers); ?></h2>
                        <p class="card-text"><small>Active suppliers</small></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Categories</h5>
                        <h2 class="card-text"><?php echo number_format($totalCategories); ?></h2>
                        <p class="card-text"><small>Product categories</small></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Products Table -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Products</h5>
                <a href="<?php echo ROOT_URL; ?>products/products.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentProducts)): ?>
                                <?php foreach ($recentProducts as $product): ?>
                                    <tr>
                                        <td>#<?php echo $product['id']; ?></td>
                                        <td><?php echo $product['name']; ?></td>
                                        <td><?php echo $product['category_name']; ?></td>
                                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                                        <td><?php echo $product['quantity']; ?></td>
                                        <td>
                                            <?php if ($product['status'] == 1): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo ROOT_URL; ?>products/view_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-eye"></i></a>
                                            <a href="<?php echo ROOT_URL; ?>products/update_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No products found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Sales Table -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Sales</h5>
                <a href="<?php echo ROOT_URL; ?>sales/sales.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentSales)): ?>
                                <?php foreach ($recentSales as $sale): ?>
                                    <tr>
                                        <td><?php echo $sale['reference_no']; ?></td>
                                        <td><?php echo $sale['customer_name']; ?></td>
                                        <td><?php echo date('d M Y', strtotime($sale['sale_date'])); ?></td>
                                        <td>$<?php echo number_format($sale['total_amount'], 2); ?></td>
                                        <td>$<?php echo number_format($sale['paid_amount'], 2); ?></td>
                                        <td>$<?php echo number_format($sale['due_amount'], 2); ?></td>
                                        <td>
                                            <?php if ($sale['status'] == 1): ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php elseif ($sale['status'] == 2): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Cancelled</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo ROOT_URL; ?>sales/view_sale.php?id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No sales found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Financial Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card bg-light mb-3">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Cash Balance</h6>
                                        <h4>$<?php echo number_format($cashBalance, 2); ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light mb-3">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Bank Balance</h6>
                                        <h4>$<?php echo number_format($bankBalance, 2); ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light mb-3">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Card Balance</h6>
                                        <h4>$<?php echo number_format($cardBalance, 2); ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Links</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <a href="<?php echo ROOT_URL; ?>sales/create_sale.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-plus-circle me-2"></i> New Sale
                            </a>
                            <a href="<?php echo ROOT_URL; ?>purchases/create_purchase.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-shopping-cart me-2"></i> New Purchase
                            </a>
                            <a href="<?php echo ROOT_URL; ?>products/create_product.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-box me-2"></i> Add Product
                            </a>
                            <a href="<?php echo ROOT_URL; ?>customers/create_customer.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-user-plus me-2"></i> Add Customer
                            </a>
                            <a href="<?php echo ROOT_URL; ?>ledger/ledger.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-book me-2"></i> View Ledger
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 <?php
    include 'includes/footer.php';
    ?>