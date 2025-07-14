<div class="sidebar" style="width: 250px;">
            <div class="sidebar-header d-flex justify-content-between align-items-center">
                <h4 class="m-0">MyOwn CRM</h4>
                <!-- <button class="toggle-btn" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button> -->
            </div>
            <nav class="nav flex-column mt-3">
                <a href="<?= ROOT_URL ?>index.php" class="nav-link ">
                    <i class="fas fa-tachometer-alt"></i>
                     <span>Dashboard</span>
                </a>
                
                <a href="<?= ROOT_URL ?>categories/categories.php" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Categories</span>
                </a>
                <a href="<?php echo ROOT_URL; ?>products/products.php" class="nav-link">
                    <i class="fas fa-box me-2"></i> Products
                </a>
                
                <!-- Purchase Management -->
                <a href="<?php echo ROOT_URL; ?>purchases/purchases.php" class="nav-link">
                    <i class="fas fa-shopping-cart me-2"></i> Purchases
                </a>
                
                <!-- Sales Management -->
                <a href="<?php echo ROOT_URL; ?>sales/sales.php" class="nav-link">
                    <i class="fas fa-cash-register me-2"></i> Sales
                </a>
                
                <!-- Supplier Management -->
                <a href="<?php echo ROOT_URL; ?>suppliers/suppliers.php" class="nav-link">
                    <i class="fas fa-truck me-2"></i> Suppliers
                </a>
                
                <!-- Customer Management -->
                <a href="<?php echo ROOT_URL; ?>customers/customers.php" class="nav-link">
                    <i class="fas fa-users me-2"></i> Customers
                </a>
                
                <!-- Ledger Management -->
                <a href="<?php echo ROOT_URL; ?>ledger/ledger.php" class="nav-link">
                    <i class="fas fa-book me-2"></i> Ledger
                </a>
            </nav>
        </div>