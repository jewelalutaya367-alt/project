<?php
session_start();
require_once 'db.php';

// Kunin ang lahat ng transaction history mula sa database
try {
    $stmt = $pdo->prepare("
        SELECT t.*, i.ItemName, i.Category 
        FROM transaction t 
        JOIN items i ON t.ItemID = i.id 
        ORDER BY t.Timestamp DESC
    ");
    $stmt->execute();
    $transaction = $stmt->fetchAll();
} catch (\Exception $e) {
    $transaction = [];
}
?>

<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - Laboratory Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0b1329; color: #fff; overflow-x: hidden; margin: 0; }
        .top-navbar { background-color: #121826; border-bottom: 1px solid #1f2d5a; height: 60px; }
        .sidebar {
            background: linear-gradient(rgba(11, 19, 41, 0.85), rgba(11, 19, 41, 0.85)), url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT0unSDBmqk9M-x2jx7v_uFEADwjwf0JRbrLFQL_xCIuZ4ZbdUL_dnjzZ8&s=10');
            background-size: cover; background-position: center; min-height: calc(100vh - 60px);
            border-right: 1px solid #1f2d5a; padding: 20px 10px;
        }
        .sidebar .btn-menu {
            background-color: rgba(26, 35, 58, 0.9); color: #fff; border: 1px solid #2a3d7d;
            font-weight: bold; font-size: 13px; text-transform: uppercase; border-radius: 6px;
            margin-bottom: 12px; text-align: center; transition: all 0.2s ease;
        }
        .sidebar .btn-menu:hover { background-color: #2a3d7d; color: #fff; }
        .sidebar .btn-logout { background-color: #b32d39; color: #fff; border: none; }
        .sidebar .btn-logout:hover { background-color: #c9302c; color: #fff; }
        .main-content {
            background: linear-gradient(rgba(11, 19, 41, 0.75), rgba(11, 19, 41, 0.75)), url('https://t3.ftcdn.net/jpg/04/25/65/24/360_F_425652429_A311W0NFAGrxzsTtmOQEkczAl3d3GlYG.jpg');
            background-size: cover; background-position: center; min-height: calc(100vh - 60px); padding: 30px;
        }
        .content-card {
            background-color: rgba(18, 24, 38, 0.9); border: 1px solid #2a3d7d;
            border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }
        .table-dark-custom { background-color: #161f33; color: #fff; border-color: #2a3d7d; }
        .table-dark-custom th, .table-dark-custom td { border-color: #2a3d7d; vertical-align: middle; font-size: 12px; }
        .badge-type { background-color: #2a3d7d; color: #fff; font-weight: bold; padding: 4px 8px; border-radius: 4px; font-size: 11px; }
        .status-change { font-size: 11px; color: #94a3b8; }
    </style>
</head>
<body>

    <nav class="navbar top-navbar px-4 d-flex justify-content-between align-items-center">
        <div><a href="dashboard.php" class="text-white text-decoration-none" style="font-size: 20px;">🏠</a></div>
        <div class="navbar-brand text-white fw-bold" style="letter-spacing: 1px; font-size: 16px;">TRANSACTION HISTORY</div>
        <div class="d-flex align-items-center gap-3">
            <a href="profile.php" class="text-white" style="font-size: 20px;">👤</a>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 sidebar d-flex flex-column justify-content-between">
                <div class="d-flex flex-column">
                    <a href="cpe_items.php" class="btn btn-menu py-2">CPE ITEMS</a>
                    <a href="blis_items.php" class="btn btn-menu py-2">BLIS ITEMS</a>
                    <a href="active_borrowed.php" class="btn btn-menu py-2">ACTIVE BORROWED ITEM</a>
                    <a href="transaction.php" class="btn btn-menu py-2 active">Transaction History</a>
                </div>
                <div><a href="logout.php" class="btn btn-menu btn-logout w-100 py-2">LOG OUT</a></div>
            </div>

            <div class="col-md-9 col-lg-10 main-content">
                <div class="content-card">
                    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h4 class="fw-bold m-0" style="letter-spacing: 1px;">Kasaysayan ng mga Transaksyon</h4>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-light btn-sm fw-bold">Export to Excel / PDF</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-bordered table-dark-custom">
                            <thead>
                                <tr>
                                    <th>Trans ID & Type</th>
                                    <th>Timestamp</th>
                                    <th>Item Details</th>
                                    <th>Quantity</th>
                                    <th>People (Borrower / Admin)</th>
                                    <th>Status Flow</th>
                                    <th>Notes / Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($transaction)): ?>
                                    <?php foreach ($transaction as $row): ?>
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-info">#TRX-<?= htmlspecialchars($row['id']) ?></span><br>
                                                <span class="badge-type"><?= htmlspecialchars($row['TransactionType'] ?? 'Borrow') ?></span>
                                            </td>
                                            <td>
                                                <small><?= htmlspecialchars($row['Timestamp'] ?? $row['DateBorrowed'] ?? 'N/A') ?></small>
                                            </td>
                                            <td>
                                                <span class="fw-bold"><?= htmlspecialchars($row['ItemName']) ?></span><br>
                                                <small class="text-muted">Code ID: <?= htmlspecialchars($row['ItemID']) ?></small>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-warning"><?= htmlspecialchars($row['Quantity'] ?? 1) ?> pcs</span>
                                            </td>
                                            <td>
                                                <small><b>Borrower:</b> <?= htmlspecialchars($row['BorrowerName'] ?? 'N/A') ?></small><br>
                                                <small class="text-muted"><b>Staff/Admin:</b> <?= htmlspecialchars($row['IssuedBy'] ?? 'Admin') ?></small>
                                            </td>
                                            <td>
                                                <div class="status-change">
                                                    <span><?= htmlspecialchars($row['PreviousStatus'] ?? 'Available') ?></span> 
                                                    ➔ 
                                                    <span class="text-info fw-bold"><?= htmlspecialchars($row['Status'] ?? 'Active') ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <small><?= htmlspecialchars($row['Purpose'] ?? $row['Notes'] ?? 'Walang tala') ?></small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Walang nakitang tala ng transaksyon.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>