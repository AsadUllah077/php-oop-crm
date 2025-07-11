<?php
include '../config.php';
require_once ROOT_PATH . 'categories/category.class.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = new Category();
    $category->setName($_POST['category_name']);
    $category->setDescription($_POST['category_description']);
    $category->setStatus($_POST['category_status']);
    
    if ($category->create()) {
        $success_message = "Category created successfully!";
        // Redirect to categories list after successful creation
        header("Location: categories.php");
        exit();
    } else {
        $error_message = "Failed to create category. Please try again.";
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
            <h2>Create New Category</h2>
            <div>
                <a href="categories.php" class="btn btn-secondary btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Categories
                </a>
            </div>
        </div>

        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>

        <!-- Category Create Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Category Information</h5>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="categoryName" name="category_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="categoryDescription" name="category_description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="categoryStatus" class="form-label">Status</label>
                        <select class="form-select" id="categoryStatus" name="category_status">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Category
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require ROOT_PATH . 'includes/footer.php';
?>