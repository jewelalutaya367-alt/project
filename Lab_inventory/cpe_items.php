<?php
session_start();
require_once 'db.php'; // Tiyaking tama ang path ng iyong db.php

// Kunin ang mga items mula sa database para sa CPE Laboratory
try {
    $stmt = $pdo->prepare("SELECT * FROM items WHERE Category LIKE ? OR Category LIKE ? ORDER BY ItemName ASC");
    $stmt->execute(['%CPE%', '%Computer Engineering%']);
    $cpeItems = $stmt->fetchAll();
} catch (\Exception $e) {
    $cpeItems = [];
}
?>

<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computer Engineering Laboratory - InventoryLABS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }

        body { 
            background: linear-gradient(rgba(7, 9, 15, 0.88), rgba(7, 9, 15, 0.95)), 
                        url('https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=1920&auto=format&fit=crop');
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
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 3px;
            color: #bce8f1;
            text-transform: uppercase;
        }

        .lab-heading .title-bottom {
            font-size: 42px;
            font-weight: 800;
            letter-spacing: 4px;
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

        .scrollable-container::-webkit-scrollbar-thumb:hover {
            background: #38bdf8;
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
            object-fit: contain;
            background-color: #000;
        }

        .placeholder-art {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #bce8f1;
            opacity: 0.85;
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
            <div class="title-top">COMPUTER ENGINEERING</div>
            <div class="title-bottom">LABORATORY</div>
        </div>
    </div>

    <div class="scrollable-container">
        <div class="items-grid">
            <?php if (!empty($cpeItems)): ?>
                <?php foreach ($cpeItems as $item): ?>
                    <?php 
                        // Kinukuha mula sa ItemImage column ng database mo
                        $itemImage = isset($item['ItemImage']) ? trim($item['ItemImage']) : '';
                        
                        // Kung may filename sa database, pagsamahin sa folder path (palitan ang 'uploads/' kung iba ang folder mo)
                        if (!empty($itemImage)) {
                            // Kung buong URL na ang naka-save sa database, huwag nang lagyan ng folder prefix
                            if (filter_var($itemImage, FILTER_VALIDATE_URL)) {
                                $imageSrc = $itemImage;
                            } else {
                                $imageSrc = 'uploads/' . $itemImage; 
                            }
                        } else {
                            // Fallback kung sakaling walang laman ang ItemImage sa DB
                            $imageSrc = '';
                        }
                    ?>
                    <div class="item-card">
                        <div class="image-box">
                            <?php if (!empty($imageSrc)): ?>
                                <img src="<?= htmlspecialchars($imageSrc) ?>" alt="<?= htmlspecialchars($item['ItemName']) ?>">
                            <?php else: ?>
                                <div class="placeholder-art">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="70" height="50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="item-title"><?= htmlspecialchars($item['ItemName']) ?></div>
                        <p class="item-desc">
                            <?= htmlspecialchars($item['Description'] ?? 'Description') ?>
                        </p>
                        <div class="item-stock">
                            Stock: <?= htmlspecialchars($item['TotalQuantity']) ?> pcs
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-items-msg">
                    Wala pang nakatalagang items sa Computer Engineering Laboratory.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>