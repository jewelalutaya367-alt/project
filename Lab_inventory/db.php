<?php
$host = 'localhost';
$db   = 'inventory_management_system';
$user = 'root';
$pass = ''; // Default password sa XAMPP ay walang laman

try {
    // Pagkonekta gamit ang PDO (PHP Data Objects)
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    
    // I-set ang error mode para madaling makita kung may problema sa query
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Optional: I-uncomment ang susunod na linyang ito para ma-test kung connected na
    // echo "Connected successfully to the database!";
    
} catch (\PDOException $e) {
    // Kapag nagka-error sa koneksyon
    echo "Connection failed: " . $e->getMessage();
}
?>