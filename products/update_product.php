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
    $imageName = $product['image']; // Keep existing image by default
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowedTypes)) {
            $imageName = time() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $imageName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                // Delete old image if it exists
                if (!empty($product['image']) && file_exists($uploadDir . $product['image'])) {
                    unlink($uploadDir . $product['image']);
                }
            } else {
                die("Error uploading image");
            }
        } else {
            die("Invalid image format. Only JPG, PNG, and GIF are allowed.");
        }
    }
    
    // Update product
    $productObj->setCategoryId($_POST['category_id']);
    $productObj->setName($_POST['name']);
    $productObj->setDescription($_POST['description']);
    $productObj->setPrice($_POST['price']);
    $productObj->setQuantity($_POST['quantity']);
    if (!empty($imageName)) {
        $productObj->setImage($imageName);
    }
    $productObj->setStatus(isset($_POST['status']) ? 1 : 0);
    
    if ($productObj->update($id)) {
        // Redirect to products list
        header("Location: products.php");
        exit;
    } else {
        die("Error updating product");
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
            <h2>Update Product</h2>
            <a href="products.php" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Products
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Edit Product Information</h5>
            </div>
            <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
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
                                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="0" value="<?php echo $product['quantity']; ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fileInput" class="form-label">Product Image</label>
                        <div class="drop-zone mb-2" id="dropZone">
                            <div class="drop-zone-prompt">
                                <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                <p>Drag & Drop your image here or click to browse</p>
                            </div>
                            <?php if (!empty($product['image']) && file_exists(ROOT_PATH . 'uploads/products/' . $product['image'])): ?>
                            <div class="drop-zone-thumb" id="dropZoneThumb" style="background-image: url('<?php echo ROOT_URL; ?>uploads/products/<?php echo $product['image']; ?>')">
                                <span class="current-image-label"><?php echo $product['image']; ?></span>
                            </div>
                            <?php else: ?>
                            <div class="drop-zone-thumb" id="dropZoneThumb"></div>
                            <?php endif; ?>
                            <input type="file" class="drop-zone-input" id="fileInput" name="image" accept="image/jpeg,image/png,image/gif">
                        </div>
                        <small class="text-muted">Leave empty to keep current image. Supported formats: JPG, PNG, GIF. Max size: 2MB</small>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="status" name="status" <?php echo $product['status'] == 1 ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status">Active</label>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="products.php" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require ROOT_PATH . 'includes/footer.php';
?>