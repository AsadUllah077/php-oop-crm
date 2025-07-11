<?php
include 'config.php';
include ROOT_PATH.'includes/header.php';
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
                <button class="btn btn-primary me-2">
                    <i class="fas fa-plus me-1"></i> Add Product
                </button>
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
                        <h2 class="card-text">1,254</h2>
                        <p class="card-text"><small>+12% from last month</small></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Orders</h5>
                        <h2 class="card-text">568</h2>
                        <p class="card-text"><small>+8% from last month</small></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Revenue</h5>
                        <h2 class="card-text">$24,589</h2>
                        <p class="card-text"><small>+15% from last month</small></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Pending Orders</h5>
                        <h2 class="card-text">42</h2>
                        <p class="card-text"><small>-5% from last month</small></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Products Table -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Products</h5>
                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
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
                            <tr>
                                <td>#PRD-001</td>
                                <td>Smartphone X Pro</td>
                                <td>Electronics</td>
                                <td>$899.99</td>
                                <td>45</td>
                                <td><span class="badge bg-success">In Stock</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>#PRD-002</td>
                                <td>Wireless Headphones</td>
                                <td>Electronics</td>
                                <td>$129.99</td>
                                <td>78</td>
                                <td><span class="badge bg-success">In Stock</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>#PRD-003</td>
                                <td>Running Shoes</td>
                                <td>Sports</td>
                                <td>$79.99</td>
                                <td>12</td>
                                <td><span class="badge bg-warning">Low Stock</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>#PRD-004</td>
                                <td>Organic Face Cream</td>
                                <td>Beauty</td>
                                <td>$29.99</td>
                                <td>0</td>
                                <td><span class="badge bg-danger">Out of Stock</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>#PRD-005</td>
                                <td>Smart Watch</td>
                                <td>Electronics</td>
                                <td>$199.99</td>
                                <td>32</td>
                                <td><span class="badge bg-success">In Stock</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Sales Overview</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                            <p class="text-muted">Sales Chart Placeholder</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Top Categories</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                            <p class="text-muted">Pie Chart Placeholder</p>
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