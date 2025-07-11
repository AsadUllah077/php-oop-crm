<?php
include '../config.php';
require ROOT_PATH . 'includes/header.php';
require_once ROOT_PATH . 'categories/category.class.php';
$id = $_GET['id'] ?? 0;
// Initialize Category class
$categoryObj = new Category();

// Get all categories
$category_record = $categoryObj->getById($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['category_id'];
    $category = new Category();

    $category->setName($_POST['category_name']);
    $category->setDescription($_POST['category_description']);
    $category->setStatus($_POST['category_status']);

    
    if ($category->update($id)) {
        $success_message = "Category updated successfully!";
        // Redirect to categories list after successful creation
        header("Location: categories.php");
        exit();
    } else {
        $error_message = "Failed to update category. Please try again.";
    }
    
}
?>
<div class="d-flex">
    <!-- Sidebar -->

    <?php
    require ROOT_PATH . 'includes/sidebar.php';
    ?>
    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Update Category</h2>
            <div>
                <a href="categories.php" class="btn btn-secondary btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Categories
                </a>
            </div>
        </div>

        <!-- Category Update Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Edit Category Information</h5>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <input type="hidden" name="category_id" value="<?php echo $id;?>"> <!-- This would be dynamically set -->
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="categoryName" name="category_name" value="<?php echo $category_record['name'] ?? 'Unknown'; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="categoryDescription" name="category_description" rows="3"><?php echo $category_record['description'] ?? 'Unknown'; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="categoryStatus" class="form-label">Status</label>
                        <select class="form-select" id="categoryStatus" name="category_status">
                            <option value="1" <?php echo $category_record['status'] == 1 ? 'selected' : '' ?> >Active</option>
                            <option value="0" <?php  echo $category_record['status'] == 0 ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Category
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require ROOT_PATH . 'includes/footer.php';
?>