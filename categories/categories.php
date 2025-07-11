<?php
include '../config.php';
require_once ROOT_PATH . 'categories/category.class.php';

// Initialize Category class
$categoryObj = new Category();

// Handle delete action
if(isset($_GET['delete'])){
    $id = $_GET['delete'] ?? 0;
    $cat = new Category();
    if(!$cat->delete($id)){
        die("error while deleting");
    }
}

// Pagination settings
$perPage = 5; // Number of items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Handle search
$search_keyword = '';
$status_filter = null;
$totalItems = 0;

if (isset($_GET['search'])) {
    $search_keyword = $_GET['search_keyword'] ?? '';
    $status_filter = isset($_GET['status_filter']) && $_GET['status_filter'] !== '' ? (int)$_GET['status_filter'] : null;
    
    // Get filtered categories with pagination
    $categories = $categoryObj->search($search_keyword, $status_filter, $page, $perPage);
    $totalItems = $categoryObj->countSearch($search_keyword, $status_filter);
} else {
    // Get all categories with pagination
    $categories = $categoryObj->getAll($page, $perPage);
    $totalItems = $categoryObj->countAll();
}

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
            <h2>Categories</h2>
            <div>
                <a href="create_category.php" class="btn btn-primary btn-sm me-2">
                    <i class="fas fa-plus me-1"></i> Add Category
                </a>
                <button class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-bell"></i>
                </button>
            </div>
        </div>

        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Search Categories</h5>
            </div>
            <div class="card-body">
                <form action="" method="get" class="row g-3">
                    <div class="col-md-6">
                        <label for="searchKeyword" class="form-label">Search Keyword</label>
                        <input type="text" class="form-control" id="searchKeyword" name="search_keyword" value="<?php echo htmlspecialchars($search_keyword); ?>" placeholder="Search by name or description">
                    </div>
                    <div class="col-md-4">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter" name="status_filter">
                            <option value="">All Statuses</option>
                            <option value="1" <?php echo $status_filter === 1 ? 'selected' : ''; ?>>Active</option>
                            <option value="0" <?php echo $status_filter === 0 ? 'selected' : ''; ?>>Inactive</option>
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

        <!-- Categories Table -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Product Categories</h5>
                <?php if (isset($_GET['search']) && !empty($search_keyword)): ?>
                <div>
                    <span class="badge bg-info">Search results for: <?php echo htmlspecialchars($search_keyword); ?></span>
                    <a href="categories.php" class="btn btn-sm btn-outline-secondary ms-2">Clear</a>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No categories found</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td>#<?php echo $category['id']; ?></td>
                                    <td><?php echo $category['name']; ?></td>
                                    <td><?php echo substr($category['description'], 0, 50) . (strlen($category['description']) > 50 ? '...' : ''); ?></td>
                                    <td>
                                        <?php if ($category['status'] == 1): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="update_category.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></a>
                                        <a href="categories.php?delete=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this category?')"><i class="fas fa-trash"></i></a>
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
                    <small class="text-muted">Showing <?php echo count($categories); ?> of <?php echo $totalItems; ?> categories (Page <?php echo $page; ?> of <?php echo $totalPages; ?>)</small>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
require ROOT_PATH . 'includes/footer.php';
?>