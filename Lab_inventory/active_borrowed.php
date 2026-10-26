<?php
session_start();
require_once 'db.php';

// Kunin ang mga active/hiniram na items mula sa database
try {
    $stmt = $pdo->prepare("
        SELECT t.*, i.ItemName, i.Category, i.Image 
        FROM transactions t 
        JOIN items i ON t.ItemID = i.id 
        WHERE t.Status = 'Active' 
        ORDER BY t.ExpectedReturnDate ASC
    ");
    $stmt->execute();
    $activeBorrowed = $stmt->fetchAll();
} catch (\Exception $e) {
    // Fallback kung iba ang pangalan ng tables/columns
    $activeBorrowed = [];
}
?>

<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Borrowed Items - Laboratory Inventory System</title>
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
        .table-dark-custom th, .table-dark-custom td { border-color: #2a3d7d; vertical-align: middle; font-size: 13px; }
        .badge-overdue { background-color: #b32d39; color: #ffffff; font-weight: bold; padding: 5px 8px; border-radius: 4px; }
        .badge-ontime { background-color: #2b7a78; color: #ffffff; font-weight: bold; padding: 5px 8px; border-radius: 4px; }
    </style>
</head>
<body>

    <nav class="navbar top-navbar px-4 d-flex justify-content-between align-items-center">
        <div><a href="dashboard.php" class="text-white text-decoration-none" style="font-size: 20px;">🏠</a></div>
        <div class="navbar-brand text-white fw-bold" style="letter-spacing: 1px; font-size: 16px;">ACTIVE BORROWED ITEMS</div>
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
                    <a href="active_borrowed.php" class="btn btn-menu py-2 active">ACTIVE BORROWED ITEM</a>
                    <a href="transaction.php" class="btn btn-menu py-2">Transaction History</a>
                </div>
                <div><a href="logout.php" class="btn btn-menu btn-logout w-100 py-2">LOG OUT</a></div>
            </div>

            <div class="col-md-9 col-lg-10 main-content">
                <div class="content-card">
                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold m-0" style="letter-spacing: 1px;">Kasalukuyang Pinahiram na Gamit</h4>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-bordered table-dark-custom">
                            <thead>
                                <tr>
                                    <th>Item Details</th>
                                    <th>Borrower Details</th>
                                    <th>Schedule & Status</th>
                                    <th>Metadata / Purpose</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($activeBorrowed)): ?>
                                    <?php foreach ($activeBorrowed as $row): ?>
                                        <?php 
                                            $isOverdue = (strtotime($row['ExpectedReturnDate']) < time());
                                        ?>
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-info"><?= htmlspecialchars($row['ItemName']) ?></span><br>
                                                <small class="text-muted">ID: <?= htmlspecialchars($row['ItemID']) ?> | Cat: <?= htmlspecialchars($row['Category'] ?? 'N/A') ?></small><br>
                                                <small class="text-secondary">Serial: <?= htmlspecialchars($row['SerialNumber'] ?? 'N/A') ?></small>
                                            </td>
                                            <td>
                                                <span class="fw-bold"><?= htmlspecialchars($row['BorrowerName']) ?></span><br>
                                                <small class="text-muted">ID: <?= htmlspecialchars($row['BorrowerID']) ?> | Dept: <?= htmlspecialchars($row['Department']) ?></small><br>
                                                <small class="text-secondary">Contact: <?= htmlspecialchars($row['ContactInfo'] ?? 'N/A') ?></small>
                                            </td>
                                            <td>
                                                <small>Hiniram: <?= htmlspecialchars($row['DateBorrowed']) ?></small><br>
                                                <small>Dapat Isoli: <?= htmlspecialchars($row['ExpectedReturnDate']) ?></small><br>
                                                <div class="mt-1">
                                                    <?php if ($isOverdue): ?>
                                                        <span class="badge-overdue">OVERDUE</span>
                                                    <?php else: ?>
                                                        <span class="badge-ontime">ON-TIME</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <small><b>Layunin:</b> <?= htmlspecialchars($row['Purpose'] ?? 'Walang nota') ?></small><br>
                                                <small class="text-muted">Issued By: <?= htmlspecialchars($row['IssuedBy'] ?? 'Admin') ?></small>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex flex-column gap-1">
                                                    <a href="return_process.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm fw-bold">Return</a>
                                                    <a href="extend_process.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm fw-bold text-dark">Extend</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Walang kasalukuyang active borrowed items.</td>
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