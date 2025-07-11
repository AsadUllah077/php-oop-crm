<?php
include '../config.php';
require_once ROOT_PATH . 'ledger/ledger.class.php';

// Initialize Ledger class
$ledgerObj = new Ledger();

// Set default values
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$account_type = isset($_GET['account_type']) ? $_GET['account_type'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Get ledger entries based on search parameters
if (!empty($keyword) || !empty($account_type) || (!empty($start_date) && !empty($end_date))) {
    $entries = $ledgerObj->search($keyword, $account_type, $start_date, $end_date, $page, $perPage);
    $totalEntries = $ledgerObj->countSearch($keyword, $account_type, $start_date, $end_date);
} else {
    $entries = $ledgerObj->getAll($page, $perPage);
    $totalEntries = $ledgerObj->countAll();
}

// Calculate total pages
$totalPages = ceil($totalEntries / $perPage);

// Get account balances
$cashBalance = $ledgerObj->getBalanceByAccountType('cash');
$bankBalance = $ledgerObj->getBalanceByAccountType('bank');
$cardBalance = $ledgerObj->getBalanceByAccountType('card');

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
            <h2>Ledger</h2>
        </div>

        <!-- Account Balances -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Cash Balance</h5>
                        <h3 class="card-text text-primary"><?php echo number_format($cashBalance, 2); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Bank Balance</h5>
                        <h3 class="card-text text-success"><?php echo number_format($bankBalance, 2); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Card Balance</h5>
                        <h3 class="card-text text-info"><?php echo number_format($cardBalance, 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="" method="get" class="row g-3">
                    <div class="col-md-3">
                        <label for="keyword" class="form-label">Keyword</label>
                        <input type="text" class="form-control" id="keyword" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Reference No, Notes">
                    </div>
                    <div class="col-md-3">
                        <label for="account_type" class="form-label">Account Type</label>
                        <select class="form-select" id="account_type" name="account_type">
                            <option value="">All Accounts</option>
                            <option value="cash" <?php echo ($account_type == 'cash') ? 'selected' : ''; ?>>Cash</option>
                            <option value="bank" <?php echo ($account_type == 'bank') ? 'selected' : ''; ?>>Bank</option>
                            <option value="card" <?php echo ($account_type == 'card') ? 'selected' : ''; ?>>Card</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                        <a href="ledger.php" class="btn btn-secondary">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Ledger Entries Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Ledger Entries</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Reference No</th>
                                <th>Transaction Type</th>
                                <th>Account Type</th>
                                <th>Payment Method</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($entries) > 0): ?>
                                <?php foreach ($entries as $entry): ?>
                                <tr>
                                    <td><?php echo $entry['id']; ?></td>
                                    <td><?php echo date('d M Y', strtotime($entry['transaction_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($entry['reference_no']); ?></td>
                                    <td>
                                        <?php 
                                        $transaction_type = $entry['transaction_type'];
                                        $reference_id = $entry['reference_id'];
                                        $link = '';
                                        
                                        if ($transaction_type == 'sale') {
                                            $link = "<a href='../sales/view_sale.php?id={$reference_id}'>Sale</a>";
                                        } elseif ($transaction_type == 'purchase') {
                                            $link = "<a href='../purchases/view_purchase.php?id={$reference_id}'>Purchase</a>";
                                        } else {
                                            $link = ucfirst($transaction_type);
                                        }
                                        
                                        echo $link;
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $account_type_label = '';
                                        switch ($entry['account_type']) {
                                            case 'cash':
                                                $account_type_label = '<span class="badge bg-primary">Cash</span>';
                                                break;
                                            case 'bank':
                                                $account_type_label = '<span class="badge bg-success">Bank</span>';
                                                break;
                                            case 'card':
                                                $account_type_label = '<span class="badge bg-info">Card</span>';
                                                break;
                                            default:
                                                $account_type_label = '<span class="badge bg-secondary">Other</span>';
                                        }
                                        echo $account_type_label;
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($entry['payment_method']); ?></td>
                                    <td class="text-end"><?php echo number_format($entry['debit'], 2); ?></td>
                                    <td class="text-end"><?php echo number_format($entry['credit'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($entry['notes']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">No ledger entries found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&keyword=<?php echo urlencode($keyword); ?>&account_type=<?php echo urlencode($account_type); ?>&start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&keyword=<?php echo urlencode($keyword); ?>&account_type=<?php echo urlencode($account_type); ?>&start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&keyword=<?php echo urlencode($keyword); ?>&account_type=<?php echo urlencode($account_type); ?>&start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
require ROOT_PATH . 'includes/footer.php';
?>