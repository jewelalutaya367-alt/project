<?php
session_start();
require_once 'db.php';

try {
    // Kunin ang mga Admin o Teacher mula sa database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE Role_ENUM IN (?, ?) OR Role IN (?, ?) ORDER BY FullName ASC");
    $stmt->execute(['admin', 'teacher', 'Admin', 'Teacher']);
    $adminsTeachers = $stmt->fetchAll();
} catch (\Exception $e) {
    $adminsTeachers = [];
}
?>

<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Laboratory Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #07090f; 
            color: #fff; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            overflow-x: hidden;
            padding-bottom: 50px;
        }

        /* Floating Navigation Bar (Istilong Uiverse / Neon) */
        .top-navbar { 
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 85%;
            max-width: 950px;
            background-color: rgba(15, 22, 36, 0.95);
            border: 1px solid rgba(0, 240, 255, 0.3);
            border-radius: 40px;
            padding: 8px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.6);
        }

        /* Main Content Wrapper */
        .main-container {
            max-width: 1050px;
            margin: 110px auto 50px auto;
            padding: 0 20px;
        }

        /* Glass Panel para sa mga Modulo at Table */
        .content-panel {
            background: rgba(13, 18, 30, 0.88);
            border-radius: 20px;
            padding: 30px;
            border: 1.5px solid rgba(0, 240, 255, 0.3);
            box-shadow: 0 0 20px rgba(0, 240, 255, 0.2);
            backdrop-filter: blur(8px);
            margin-bottom: 25px;
        }

        .section-title { 
            font-size: 16px; 
            font-weight: bold; 
            letter-spacing: 1.5px; 
            margin-bottom: 20px; 
            color: #bce8f1;
            text-transform: uppercase;
        }

        /* Uiverse Outline Buttons */
        .btn-uiverse-outline {
            background: rgba(15, 22, 36, 0.8);
            border: 1px solid #00f0ff;
            color: #fff;
            border-radius: 14px;
            transition: all 0.3s ease;
            text-align: left;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 240, 255, 0.1);
        }

        .btn-uiverse-outline:hover {
            background-color: rgba(0, 240, 255, 0.1);
            border-color: #38bdf8;
            color: #38bdf8;
            transform: translateY(-3px);
            box-shadow: 0 0 20px rgba(0, 240, 255, 0.4);
        }

        /* Custom Glowing Grid Table Styling */
        .table-custom {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #00f0ff;
            background-color: rgba(15, 22, 36, 0.8);
        }

        .table-custom th, .table-custom td {
            border: 1px solid rgba(0, 240, 255, 0.3);
            padding: 15px 20px;
            vertical-align: middle;
            color: #ffffff;
            font-size: 14px;
        }

        .table-custom th {
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            background-color: rgba(7, 9, 15, 0.9);
            color: #bce8f1;
        }

        .table-custom td {
            font-weight: 600;
        }
    </style>
</head>
<body>

    <!-- Floating Top Navbar -->
    <nav class="navbar top-navbar px-4">
        <div>
            <a href="dashboard.php" class="text-white text-decoration-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-house-door-fill" viewBox="0 0 16 16">
                    <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"/>
                </svg>
            </a>
        </div>
        <div class="navbar-brand text-white fw-bold" style="letter-spacing: 1.5px; font-size: 15px;">ADMIN CONTROL CENTER</div>
        <div>
            <a href="profile.php" class="text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                </svg>
            </a>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="main-container">
        
        <!-- System Modules Panel -->
        <div class="content-panel">
            <div class="section-title">Mga Modulo at Pamamahala ng Sistema</div>
            <div class="row g-3">
                <div class="col-md-4">
                    <a href="inventory.php" class="btn btn-uiverse-outline w-100 text-decoration-none">
                        <div class="fw-bold text-white" style="font-size: 13px;">📦 Inventory Management</div>
                        <small class="text-secondary d-block mt-1" style="font-size: 11px;">Chemicals, Equipment & Consumables</small>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="transactions.php" class="btn btn-uiverse-outline w-100 text-decoration-none">
                        <div class="fw-bold text-white" style="font-size: 13px;">📊 Tracking & Audit Trails</div>
                        <small class="text-secondary d-block mt-1" style="font-size: 11px;">Transaction history & Waste logs</small>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="active_borrowed.php" class="btn btn-uiverse-outline w-100 text-decoration-none">
                        <div class="fw-bold text-white" style="font-size: 13px;">📋 Active Borrowed Items</div>
                        <small class="text-secondary d-block mt-1" style="font-size: 11px;">Real-time monitoring</small>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="#" class="btn btn-uiverse-outline w-100 text-decoration-none">
                        <div class="fw-bold text-white" style="font-size: 13px;">🛡️ Safety & Compliance</div>
                        <small class="text-secondary d-block mt-1" style="font-size: 11px;">SDS / MSDS Integration</small>
                    </a>
                </div>
            </div>
        </div>

        <!-- List of Admin or Teacher Panel -->
        <div class="content-panel">
            <div class="section-title mb-4">List of Admin or Teacher</div>
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th style="width: 40%;">NAME OF TEACHER</th>
                            <th style="width: 40%;">EMAIL</th>
                            <th style="width: 20%;">COURSE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($adminsTeachers)): ?>
                            <?php foreach ($adminsTeachers as $person): ?>
                                <tr>
                                    <td class="text-uppercase"><?= htmlspecialchars($person['FullName'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($person['Email'] ?? '') ?></td>
                                    <td class="text-center text-uppercase"><?= htmlspecialchars($person['Course'] ?? 'N/A') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    Walang nakitang Admin o Teacher sa database.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>