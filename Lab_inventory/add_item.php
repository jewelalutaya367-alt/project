<?php
// I-on ang error reporting para makita kung may error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php';

// Auto-check at dagdag ng lahat ng kulang na columns sa parehong items at inventory tables
try {
    $tables = ['items', 'inventory'];
    foreach ($tables as $table) {
        $pdo->exec("ALTER TABLE `$table` ADD COLUMN IF NOT EXISTS Category VARCHAR(100) NULL");
        $pdo->exec("ALTER TABLE `$table` ADD COLUMN IF NOT EXISTS TotalQuantity INT DEFAULT 1");
        $pdo->exec("ALTER TABLE `$table` ADD COLUMN IF NOT EXISTS ItemImage VARCHAR(255) NULL");
        $pdo->exec("ALTER TABLE `$table` ADD COLUMN IF NOT EXISTS Description TEXT NULL");
        $pdo->exec("ALTER TABLE `$table` ADD COLUMN IF NOT EXISTS SerialNumber VARCHAR(100) NULL");
        $pdo->exec("ALTER TABLE `$table` ADD COLUMN IF NOT EXISTS ConditionStatus VARCHAR(50) NULL");
    }
} catch (\Exception $e) {
    // Hayaan lang kung sakaling hindi suportado sa lumang MySQL version
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category     = trim($_POST['category'] ?? 'CPE');
    $itemName     = trim($_POST['item_name'] ?? '');
    $totalQty     = intval($_POST['total_quantity'] ?? 1);
    $serialNumber = trim($_POST['serial_number'] ?? '');
    $condition    = trim($_POST['condition_status'] ?? 'New');
    $description  = trim($_POST['description'] ?? '');
    $imageName    = '';

    // Proseso ng pag-upload ng image
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['item_image']['tmp_name'];
        $fileName = $_FILES['item_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = './uploads/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $imageName = $newFileName;
            }
        }
    }

    if (!empty($itemName)) {
        try {
            // 1. I-save muna sa items table
            $stmt = $pdo->prepare("INSERT INTO items (Category, ItemName, TotalQuantity, SerialNumber, ConditionStatus, Description, ItemImage) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$category, $itemName, $totalQty, $serialNumber, $condition, $description, $imageName]);
            
            // 2. I-save rin sa inventory table
            $stmtInv = $pdo->prepare("INSERT INTO inventory (Category, ItemName, TotalQuantity, SerialNumber, ConditionStatus, Description, ItemImage) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtInv->execute([$category, $itemName, $totalQty, $serialNumber, $condition, $description, $imageName]);

            $message = "Item successfully added to dashboard!";
            $messageType = "success";
        } catch (\Exception $e) {
            $message = "Database Error: " . $e->getMessage();
            $messageType = "error";
        }
    } else {
        $message = "Please enter the item name.";
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Item</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)), url('https://static.vecteezy.com/system/resources/thumbnails/033/524/184/small/a-dark-warehouse-full-of-boxes-free-photo.jpg') no-repeat center center fixed; 
            background-size: cover; 
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }
        .form-container { 
            background: #181920; 
            padding: 40px; 
            border-radius: 12px; 
            max-width: 550px; 
            width: 100%; 
            box-shadow: 0 8px 24px rgba(0,0,0,0.6); 
            border: 1px solid #4f46e5;
            color: #fff;
            box-sizing: border-box;
        }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #cfd3dc; font-size: 14px; }
        input, select, textarea { 
            width: 100%; 
            padding: 12px 15px; 
            box-sizing: border-box; 
            background: #111217; 
            border: 1px solid #4f46e5; 
            border-radius: 6px; 
            color: #fff;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }
        textarea { resize: vertical; min-height: 80px; }
        input[type="file"] { color: #cfd3dc; padding: 10px; }
        .btn-container { display: flex; justify-content: center; margin-top: 25px; }
        button.uiverse-btn {
            padding: 14px 40px;
            border-radius: 50px;
            cursor: pointer;
            border: 0;
            background-color: white;
            color: #111;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-size: 15px;
            font-weight: bold;
            transition: all 0.5s ease;
            width: 100%;
        }
        button.uiverse-btn:hover {
            letter-spacing: 3px;
            background-color: hsl(261deg 80% 48%);
            color: #fff;
        }
        .message { margin-bottom: 15px; font-weight: bold; text-align: center; }
        .message.success { color: #34d399; }
        .message.error { color: #f87171; }
        h2 { margin-top: 0; margin-bottom: 25px; color: #fff; text-align: center; font-size: 24px; letter-spacing: 1.5px; text-transform: uppercase; }
        .form-footer { text-align: center; margin-top: 20px; }
        a.uiverse-dashboard-btn {
            display: inline-block;
            padding: 14px 40px;
            border-radius: 50px;
            background-color: white;
            color: #111;
            text-decoration: none;
            font-weight: bold;
            width: 100%;
            box-sizing: border-box;
            text-align: center;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-size: 15px;
            transition: all 0.5s ease;
        }
        a.uiverse-dashboard-btn:hover {
            letter-spacing: 3px;
            background-color: hsl(261deg 80% 48%);
            color: #fff;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Add New Item</h2>
    
    <?php if ($message): ?>
        <div id="flash-message" class="message <?= $messageType ?>"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <form action="add_item.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Category *</label>
            <select name="category">
                <option value="CPE">CPE</option>
                <option value="blis">blis</option>
            </select>
        </div>

        <div class="form-group">
            <label>Item Name *</label>
            <input type="text" name="item_name" required>
        </div>

        <div class="form-group">
            <label>Total Quantity (Stock) *</label>
            <input type="number" name="total_quantity" value="1" min="1" required>
        </div>

        <div class="form-group">
            <label>Serial Number</label>
            <input type="text" name="serial_number">
        </div>

        <div class="form-group">
            <label>Condition Status *</label>
            <select name="condition_status">
                <option value="New">New</option>
                <option value="Good">Good</option>
                <option value="Needs Repair">Needs Repair</option>
            </select>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" placeholder="Ilagay ang deskripsyon ng item dito..."></textarea>
        </div>

        <div class="form-group">
            <label>Item Image *</label>
            <input type="file" name="item_image" accept="image/*" required>
        </div>

        <div class="btn-container">
            <button type="submit" class="uiverse-btn">Save Item</button>
        </div>

        <div class="form-footer">
            <a href="inventory.php" class="uiverse-dashboard-btn">Return to Dashboard</a>
        </div>
    </form>
</div>

<script>
    setTimeout(function() {
        const msgElement = document.getElementById('flash-message');
        if (msgElement) {
            msgElement.style.transition = 'opacity 0.5s ease';
            msgElement.style.opacity = '0';
            setTimeout(function() {
                msgElement.style.display = 'none';
            }, 500);
        }
    }, 3000);
</script>

</body>
</html>