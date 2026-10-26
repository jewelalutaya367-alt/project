<?php
session_start();
require_once 'db.php';

// Kunin ang lahat ng items mula sa parehong 'items' at 'inventory' tables para sigurado
try {
    $stmt = $pdo->query("
        SELECT Category, ItemName, TotalQuantity, Description FROM items
        UNION
        SELECT Category, ItemName, TotalQuantity, Description FROM inventory
        ORDER BY Category ASC, ItemName ASC
    ");
    $allItems = $stmt->fetchAll();
} catch (\Exception $e) {
    // Fallback kung sakaling may isang table na wala pa
    try {
        $stmt = $pdo->query("SELECT * FROM items ORDER BY Category ASC, ItemName ASC");
        $allItems = $stmt->fetchAll();
    } catch (\Exception $ex) {
        $allItems = [];
    }
}
?>

<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - InventoryLAB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #0b1329; 
            color: #fff; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden; 
            margin: 0; 
            background-image: linear-gradient(rgba(11, 19, 41, 0.85), rgba(11, 19, 41, 0.85)), url('https://t3.ftcdn.net/jpg/04/25/65/24/360_F_425652429_A311W0NFAGrxzsTtmOQEkczAl3d3GlYG.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
        }

        /* Floating Navigation Bar */
        .top-nav-container {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 1100px;
            background-color: rgba(18, 24, 38, 0.85);
            border: 1px solid #2a3d7d;
            border-radius: 40px;
            padding: 10px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.4);
        }

        .nav-brand {
            font-weight: bold;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            letter-spacing: 1px;
        }

        .nav-links {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 1px;
            transition: color 0.2s;
        }

        .nav-links a:hover, .nav-links a.active {
            color: #5bc0de;
        }

        /* Main Scrollable Wrapper */
        .main-container {
            max-width: 1100px;
            margin: 120px auto 60px auto;
            padding: 0 20px;
        }

        /* Glassmorphism Card Style */
        .content-card {
            background-color: rgba(18, 24, 38, 0.85);
            border: 1px solid #2a3d7d;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }

        .section-title {
            text-align: center;
            font-weight: bold;
            letter-spacing: 2px;
            color: #bce8f1;
            margin-bottom: 25px;
            text-transform: uppercase;
            font-size: 24px;
        }

        /* Table Design */
        .table-dark-custom { 
            background-color: rgba(22, 31, 51, 0.9); 
            color: #fff; 
            border-color: #2a3d7d; 
            width: 100%;
        }
        .table-dark-custom th, .table-dark-custom td { 
            border-color: #2a3d7d; 
            vertical-align: middle; 
            padding: 16px;
            font-size: 14px;
        }
        .table-dark-custom th {
            background-color: #121826;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .table-dark-custom td {
            background-color: rgba(22, 31, 51, 0.6);
            color: #fff;
        }
        .category-text {
            text-transform: lowercase;
            font-weight: 600;
            color: #79aec8;
        }
    </style>
</head>
<body>

    <!-- Floating Navbar -->
    <div class="top-nav-container">
        <a href="dashboard.php" class="nav-brand">
            <span style="color: #5bc0de; font-size: 20px;">✦</span> InventoryLAB
        </a>
        <div class="nav-links">
            <a href="dashboard.php">HOME</a>
            <a href="admin.php">ADMIN</a>
            <a href="inventory.php" class="active">INVENTORY</a>
            <a href="active_borrowed.php">BORROW</a>
            <a href="profile.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                </svg>
            </a>
        </div>
    </div>

    <!-- Scrollable Main Container -->
    <div class="main-container">
        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title m-0 text-start">Lahat ng Imbentaryo</h2>
                <a href="add_item.php" class="btn btn-success btn-sm fw-bold px-3 py-2" style="border-radius: 8px;">+ Magdagdag ng Item</a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-dark-custom">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Category</th>
                            <th style="width: 25%;">Item Name</th>
                            <th style="width: 15%;">Total Stock</th>
                            <th style="width: 45%;">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($allItems)): ?>
                            <?php foreach ($allItems as $item): ?>
                                <tr>
                                    <td><span class="category-text"><?= htmlspecialchars($item['Category'] ?? 'CPE') ?></span></td>
                                    <td class="fw-bold"><?= htmlspecialchars($item['ItemName']) ?></td>
                                    <td><?= htmlspecialchars($item['TotalQuantity'] ?? 1) ?> pcs</td>
                                    <td><?= htmlspecialchars($item['Description'] ?? 'Wala pang description') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Walang nakitang items sa database.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>