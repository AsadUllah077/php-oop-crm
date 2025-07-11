<?php
include '../config.php';
require_once ROOT_PATH . 'products/product.class.php';
require_once ROOT_PATH . 'categories/category.class.php';

// Initialize Category class to get all categories for the dropdown
$categoryObj = new Category();
$categories = $categoryObj->getAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create uploads directory if it doesn't exist
    $uploadDir = ROOT_PATH . 'uploads/products/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Handle image upload
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowedTypes)) {
            $imageName = time() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $imageName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                // Image uploaded successfully
            } else {
                die("Error uploading image");
            }
        } else {
            die("Invalid image format. Only JPG, PNG, and GIF are allowed.");
        }
    }
    
    // Create new product
    $product = new Product();
    $product->setCategoryId($_POST['category_id']);
    $product->setName($_POST['name']);
    $product->setDescription($_POST['description']);
    $product->setPrice($_POST['price']);
    $product->setQuantity($_POST['quantity']);
    $product->setImage($imageName);
    $product->setStatus(isset($_POST['status']) ? 1 : 0);
    
    if ($product->create()) {
        // Redirect to products list
        header("Location: products.php");
        exit;
    } else {
        die("Error creating product");
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
            <h2>Add New Product</h2>
            <a href="products.php" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Products
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Product Information</h5>
            </div>
            <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo $category['name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fileInput" class="form-label">Product Image</label>
                        <div class="drop-zone mb-2" id="dropZone">
                            <div class="drop-zone-prompt">
                                <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                <p>Drag & Drop your image here or click to browse</p>
                            </div>
                            <div class="drop-zone-thumb" id="dropZoneThumb"></div>
                            <input type="file" class="drop-zone-input" id="fileInput" name="image" accept="image/jpeg,image/png,image/gif">
                        </div>
                        <small class="text-muted">Supported formats: JPG, PNG, GIF. Max size: 2MB</small>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="status" name="status" checked>
                        <label class="form-check-label" for="status">Active</label>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require ROOT_PATH . 'includes/footer.php';
?>