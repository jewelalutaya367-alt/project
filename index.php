<?php
// Isama ang connection file
require_once 'db.php';

// Kunin ang lahat ng items mula sa database
$stmt = $pdo->query("SELECT * FROM items");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab Inventory System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f4f7f6; }
        h2 { color: #333; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { 
            display: inline-block; 
            margin-right: 10px; 
            padding: 8px 15px; 
            background-color: #007bff; 
            color: white; 
            text-decoration: none; 
            border-radius: 4px; 
            font-size: 14px;
        }
        .nav-links a:hover { background-color: #0056b3; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 10px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #343a40; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <h2>School Laboratory Inventory - All Items</h2>
    
    <!-- Mga Button para dumiretso sa hiwalay na Laboratory Pages -->
    <div class="nav-links">
        <a href="bliss_items.php">📚 Blis Laboratory Items</a>
        <a href="cpe_items.php">💻 CPE Laboratory Items</a>
    </div>

    <table>
        <tr>
            <th>ItemID</th>
            <th>Item Name</th>
            <th>Total Quantity</th>
            <th>Unit</th>
            <th>Storage Location</th>
        </tr>
        <?php if (count($items) > 0): ?>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['ItemID']); ?></td>
                    <td><?= htmlspecialchars($item['ItemName']); ?></td>
                    <td><?= htmlspecialchars($item['TotalQuantity']); ?></td>
                    <td><?= htmlspecialchars($item['Unit']); ?></td>
                    <td><?= htmlspecialchars($item['StorageLocation']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center;">Wala pang nakalagay na items sa database.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>