<?php
include '../config.php';
require_once ROOT_PATH . 'products/product.class.php';
require_once ROOT_PATH . 'categories/category.class.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$id = (int)$_GET['id'];

// Initialize Product class
$productObj = new Product();
$product = $productObj->getById($id);

// If product not found, redirect to products list
if (!$product) {
    header("Location: products.php");
    exit;
}

// Get related products from the same category
$relatedProducts = $productObj->getByCategory($product['category_id'], 4);

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
            <h2>Product Details</h2>
            <a href="products.php" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Products
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <!-- Product Image -->
                    <div class="col-md-4 mb-4 mb-md-0">
                        <div class="text-center">
                            <?php if (!empty($product['image']) && file_exists(ROOT_PATH . 'uploads/products/' . $product['image'])): ?>
                                <img src="<?php echo ROOT_URL; ?>uploads/products/<?php echo $product['image']; ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="img-fluid rounded" style="max-height: 300px;">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300" alt="No Image" class="img-fluid rounded">
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Product Details -->
                    <div class="col-md-8">
                        <h3 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h3>
                        
                        <div class="mb-3">
                            <span class="badge bg-primary"><?php echo htmlspecialchars($product['category_name']); ?></span>
                            <?php if ($product['status'] == 1): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <h4 class="text-primary">$<?php echo number_format($product['price'], 2); ?></h4>
                        </div>
                        
                        <div class="mb-3">
                            <p><strong>Availability:</strong> 
                                <?php if ($product['quantity'] > 0): ?>
                                    <span class="text-success"><?php echo $product['quantity']; ?> in stock</span>
                                <?php else: ?>
                                    <span class="text-danger">Out of stock</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Description</h5>
                            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="update_product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-edit me-1"></i> Edit Product
                            </a>
                            <a href="products.php?delete=<?php echo $product['id']; ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this product?')">
                                <i class="fas fa-trash me-1"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <?php if (!empty($relatedProducts) && count($relatedProducts) > 1): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Related Products</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($relatedProducts as $relatedProduct): 
                        // Skip the current product
                        if ($relatedProduct['id'] == $product['id']) continue;
                    ?>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="text-center pt-3">
                                <?php if (!empty($relatedProduct['image'])): ?>
                                    <img src="<?php echo ROOT_URL; ?>uploads/products/<?php echo $relatedProduct['image']; ?>" 
                                         alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>" 
                                         class="img-fluid" style="height: 120px; object-fit: contain;">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/120" alt="No Image" class="img-fluid">
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title"><?php echo htmlspecialchars($relatedProduct['name']); ?></h6>
                                <p class="card-text text-primary">$<?php echo number_format($relatedProduct['price'], 2); ?></p>
                                <a href="view_product.php?id=<?php echo $relatedProduct['id']; ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
require ROOT_PATH . 'includes/footer.php';
?>