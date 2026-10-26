<?php
session_start();
require_once 'db.php'; // Tiyaking tama ang path ng iyong db.php

// Kunin ang mga items mula sa database para sa BLIS Laboratory
try {
    $stmt = $pdo->prepare("SELECT * FROM items WHERE Category LIKE ? OR Category LIKE ? ORDER BY ItemName ASC");
    $stmt->execute(['%BLIS%', '%Library%']);
    $blisItems = $stmt->fetchAll();
} catch (\Exception $e) {
    $blisItems = [];
}
?>

<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BLIS Laboratory - InventoryLABS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }

        body { 
            background: linear-gradient(rgba(7, 9, 15, 0.88), rgba(7, 9, 15, 0.95)), 
                        url('https://png.pngtree.com/thumb_back/fh260/background/20240914/pngtree-grand-library-interior-with-bookcases-and-wooden-floor-stock-photo-image_16204662.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #fff; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .page-header {
            width: 100%;
            max-width: 1200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
            flex-shrink: 0;
        }

        .nav-top-bar {
            width: 100%;
            display: flex;
            justify-content: flex-start;
            margin-bottom: 15px;
        }

        .btn-back {
            background: rgba(15, 22, 36, 0.9);
            border: 1.5px solid #00f0ff;
            color: #00f0ff;
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 0 10px rgba(0, 240, 255, 0.3);
            letter-spacing: 1px;
        }

        .btn-back:hover {
            background: #00f0ff;
            color: #07090f;
            box-shadow: 0 0 25px rgba(0, 240, 255, 0.8);
            transform: translateY(-2px);
        }

        .lab-heading {
            text-align: center;
            width: 100%;
        }

        .lab-heading .title-top {
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 2px;
            color: #bce8f1;
            text-transform: uppercase;
        }

        .lab-heading .title-bottom {
            font-size: 34px;
            font-weight: 800;
            letter-spacing: 3px;
            color: #ffffff;
            text-transform: uppercase;
            text-shadow: 0 0 15px rgba(0, 240, 255, 0.6);
            margin-top: -5px;
        }

        .scrollable-container {
            width: 100%;
            max-width: 1200px;
            flex-grow: 1;
            overflow-y: auto;
            padding: 10px 30px 40px 30px;
            display: flex;
            justify-content: center;
            scrollbar-width: thin;
            scrollbar-color: #00f0ff rgba(15, 22, 36, 0.6);
        }

        .scrollable-container::-webkit-scrollbar {
            width: 8px;
        }

        .scrollable-container::-webkit-scrollbar-track {
            background: rgba(15, 22, 36, 0.6);
            border-radius: 10px;
        }

        .scrollable-container::-webkit-scrollbar-thumb {
            background: #00f0ff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 240, 255, 0.6);
            border: 1px solid #00bcd4;
        }

        .items-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            width: 100%;
            max-width: 1150px;
            align-content: start;
            padding-bottom: 20px;
        }

        @media (max-width: 900px) {
            .items-grid {
                grid-template-columns: repeat(1, 1fr);
            }
        }

        .item-card {
            background: linear-gradient(135deg, rgba(15, 22, 36, 0.95), rgba(10, 14, 23, 0.98));
            border: 1.5px solid #00f0ff;
            border-radius: 28px;
            padding: 25px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 0 25px rgba(0, 240, 255, 0.3), inset 0 0 15px rgba(0, 240, 255, 0.08);
            transition: all 0.3s ease-in-out;
            min-height: 450px;
        }

        .item-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 0 40px rgba(0, 240, 255, 0.6), inset 0 0 25px rgba(0, 240, 255, 0.15);
            border-color: #38bdf8;
        }

        .image-box {
            width: 100%;
            height: 190px;
            background: linear-gradient(180deg, rgba(100, 170, 230, 0.2) 0%, rgba(50, 100, 160, 0.3) 100%);
            border: 1px solid rgba(0, 240, 255, 0.5);
            border-radius: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: inset 0 0 10px rgba(0, 240, 255, 0.3);
        }

        .image-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-title {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            margin-bottom: 12px;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .item-desc {
            font-size: 13px;
            color: #e2e8f0;
            line-height: 1.7;
            text-align: center;
            margin: 0;
            flex-grow: 1;
        }

        .item-stock {
            margin-top: 20px;
            font-size: 11px;
            color: #38bdf8;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .no-items-msg {
            text-align: center;
            grid-column: 1 / -1;
            color: #94a3b8;
            padding: 60px;
            font-size: 18px;
            background: rgba(15, 22, 36, 0.7);
            border-radius: 20px;
            border: 1px solid #2a3d7d;
        }
    </style>
</head>
<body>

    <div class="page-header">
        <div class="nav-top-bar">
            <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
        </div>

        <div class="lab-heading">
            <div class="title-top">BACHELOR OF LIBRARY AND INFORMATION SCIENCE</div>
            <div class="title-bottom">LABORATORY</div>
        </div>
    </div>

    <div class="scrollable-container">
        <div class="items-grid">
            <?php if (!empty($blisItems)): ?>
                <?php foreach ($blisItems as $item): ?>
                    <div class="item-card">
                        <div class="image-box">
                            <!-- Ginawang ItemImage alinsunod sa database column mo -->
                            <?php if (!empty($item['ItemImage'])): ?>
                                <img src="<?= htmlspecialchars($item['ItemImage']) ?>" alt="<?= htmlspecialchars($item['ItemName']) ?>">
                            <?php else: ?>
                                <img src="https://m.media-amazon.com/images/I/71g4cchYx5L.jpg_BO30,255,255,255_UF750,750_SR1910,1000,0,C_QL100_.jpg" alt="Default Image">
                            <?php endif; ?>
                        </div>
                        <div class="item-title"><?= htmlspecialchars($item['ItemName']) ?></div>
                        <p class="item-desc">
                            <?= htmlspecialchars($item['Description']) ?>
                        </p>
                        <div class="item-stock">
                            Stock: <?= htmlspecialchars($item['TotalQuantity']) ?> pcs
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Mensahe na lang kung walang nahanap sa database sa halip na fake item -->
                <div class="no-items-msg">
                    Walang nakitang mga item para sa BLIS Laboratory.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>