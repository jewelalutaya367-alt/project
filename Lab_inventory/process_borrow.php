<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $itemID = $_POST['ItemID'];
    $assetID = !empty($_POST['AssetID']) ? $_POST['AssetID'] : null;
    $userID = $_POST['UserID'];
    $quantityBorrowed = $_POST['QuantityBorrowed'] ?? 1;
    $dueDate = $_POST['DueDate'];

    try {
        $stmtCheck = $pdo->prepare("SELECT TotalQuantity FROM items WHERE ItemID = ?");
        $stmtCheck->execute([$itemID]);
        $item = $stmtCheck->fetch();

        if ($item && $item['TotalQuantity'] >= $quantityBorrowed) {
            $pdo->beginTransaction();

            $sqlTransaction = "INSERT INTO Transactions (ItemID, AssetID, UserID, QuantityBorrowed, DueDate, Status) 
                               VALUES (:ItemID, :AssetID, :UserID, :QuantityBorrowed, :DueDate, 'Borrowed')";
            $stmtTrans = $pdo->prepare($sqlTransaction);
            $stmtTrans->execute([
                'ItemID' => $itemID,
                'AssetID' => $assetID,
                'UserID' => $userID,
                'QuantityBorrowed' => $quantityBorrowed,
                'DueDate' => $dueDate
            ]);

            $sqlUpdateStock = "UPDATE items SET TotalQuantity = TotalQuantity - :qty WHERE ItemID = :ItemID";
            $stmtUpdate = $pdo->prepare($sqlUpdateStock);
            $stmtUpdate->execute([
                'qty' => $quantityBorrowed,
                'ItemID' => $itemID
            ]);

            $pdo->commit();
            
            echo "<script>alert('Matagumpay na naitala ang paghiram!'); window.location.href='borrow.php';</script>";
        } else {
            echo "<script>alert('Error: Kulang o walang stock ang item na ito.'); window.history.back();</script>";
        }

    } catch (\Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "May error sa proseso: " . $e->getMessage();
    }
}
?>