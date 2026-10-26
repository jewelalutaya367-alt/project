<?php
session_start();
require_once 'db.php';

if (isset($_GET['id'])) {
    $transactionId = $_GET['id'];

    try {
        // Kunin ang ItemID mula sa transaction para muling madagdagan ang stock
        $stmt = $pdo->prepare("SELECT ItemID FROM transactions WHERE id = ?");
        $stmt->execute([$transactionId]);
        $trans = $stmt->fetch();

        if ($trans) {
            $itemId = $trans['ItemID'];

            // 1. I-update ang status ng transaction patungong 'Returned'
            $updateTrans = $pdo->prepare("UPDATE transactions SET Status = 'Returned' WHERE id = ?");
            $updateTrans->execute([$transactionId]);

            // 2. Ibalik ang stock sa items table (depende sa kung TotalQuantity o Stock ang column mo)
            $updateStock = $pdo->prepare("UPDATE items SET TotalQuantity = TotalQuantity + 1 WHERE id = ?");
            $updateStock->execute([$itemId]);
        }
    } catch (\Exception $e) {
        // Handle error kung kinakailangan
    }
}

// Bumalik sa active borrowed page pagkatapos
header("Location: active_borrowed.php");
exit();
?>