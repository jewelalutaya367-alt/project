<?php
session_start();
require_once 'db.php'; 

$message = '';
$messageType = '';
$itemNameFromDB = '';

// Kunin ang item name o ID mula sa URL (sinusubukan ang 'item', 'id', o 'name')
$itemParam = $_GET['item'] ?? ($_GET['id'] ?? ($_GET['name'] ?? null));

try {
    if (!isset($pdo)) {
        throw new Exception("Database connection (\$pdo) not found in db.php");
    }
    
    if ($itemParam !== null && $itemParam !== '') {
        // Hanapin sa database gamit ang ItemName o AssetID
        $stmt = $pdo->prepare("SELECT * FROM inventory WHERE ItemName = ? OR AssetID = ?");
        $stmt->execute([$itemParam, $itemParam]);
        $fetchedItem = $stmt->fetch();
        
        if ($fetchedItem && !empty($fetchedItem['ItemName'])) {
            $itemNameFromDB = $fetchedItem['ItemName'];
        } else {
            // Kung wala sa database pero may ipinasang pangalan sa URL, gamitin na lang yun
            $itemNameFromDB = $itemParam;
        }
    } else {
        $itemNameFromDB = "No item selected.";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $borrowerName    = trim($_POST['borrower_name'] ?? '');
        $borrowerContact = trim($_POST['borrower_contact'] ?? '');
        $courseSection   = trim($_POST['course_section'] ?? '');
        $quantity        = intval($_POST['quantity'] ?? 1);
        $expectedReturn  = trim($_POST['expected_return_date'] ?? '');
        $conditionOut    = trim($_POST['condition_out'] ?? '');
        $purpose         = trim($_POST['purpose'] ?? '');
        $postedItemName  = trim($_POST['item_name_val'] ?? '');

        if ($borrowerName && $courseSection && $expectedReturn && $postedItemName !== '') {
            
            $pdo->beginTransaction();

            // I-save sa 'borrows' table
            $stmt = $pdo->prepare("INSERT INTO borrows (item_name, borrower_name, borrower_contact, course_section, quantity, borrow_date, expected_return_date, condition_out, purpose, status) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, 'Active')");
            $stmt->execute([
                $postedItemName,
                $borrowerName,
                $borrowerContact,
                $courseSection,
                $quantity,
                $expectedReturn,
                $conditionOut,
                $purpose
            ]);

            // I-update ang status sa inventory table
            $updateStock = $pdo->prepare("UPDATE inventory SET ConditionStatus = 'Borrowed' WHERE ItemName = ? OR AssetID = ?");
            $updateStock->execute([$postedItemName, $postedItemName]);

            $pdo->commit();
            $message = "Item successfully checked out!";
            $messageType = "success";
        } else {
            $message = "Please fill in all required fields.";
            $messageType = "error";
        }
    }
} catch (\Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $message = "Database Error: " . $e->getMessage();
    $messageType = "error";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout Borrow Item</title>
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
        }
        input[type="date"], input[type="number"] { color-scheme: dark; }
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
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Checkout Borrow Item</h2>
    
    <?php if ($message): ?>
        <div class="message <?= $messageType ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <form action="borrow.php<?= $itemParam !== null ? '?item=' . urlencode($itemParam) : '' ?>" method="POST">
        <input type="hidden" name="item_name_val" value="<?= htmlspecialchars($itemNameFromDB) ?>">

        <div class="form-group">
            <label for="item_name">Item Info *</label>
            <input type="text" id="item_name" value="<?= htmlspecialchars($itemNameFromDB) ?>" readonly style="background-color: #1f212a; cursor: not-allowed;">
        </div>

        <div class="form-group">
            <label for="borrower_name">Borrower Name *</label>
            <input type="text" id="borrower_name" name="borrower_name" required>
        </div>

        <div class="form-group">
            <label for="course_section">Course & Section *</label>
            <input type="text" id="course_section" name="course_section" required>
        </div>

        <div class="form-group">
            <label for="borrower_contact">Contact Info (Email / Phone)</label>
            <input type="text" id="borrower_contact" name="borrower_contact">
        </div>

        <div class="form-group">
            <label for="quantity">Quantity *</label>
            <input type="number" id="quantity" name="quantity" value="1" min="1" required>
        </div>

        <div class="form-group">
            <label for="expected_return_date">Expected Return Date *</label>
            <input type="date" id="expected_return_date" name="expected_return_date" required>
        </div>

        <div class="form-group">
            <label for="condition_out">Pre-Borrow Condition Notes</label>
            <textarea id="condition_out" name="condition_out"></textarea>
        </div>

        <div class="form-group">
            <label for="purpose">Purpose / Project</label>
            <input type="text" id="purpose" name="purpose">
        </div>

        <div class="btn-container">
            <button type="submit" class="uiverse-btn">Mag-checkout</button>
        </div>

        <div class="form-footer">
            <a href="cpe_items.php" class="uiverse-dashboard-btn">Return to dashboard</a>
        </div>
    </form>
</div>

</body>
</html>