<?php
session_start();
require_once 'db.php';
require_once 'mailer.php'; // PHPMailer file mo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['FullName'] ?? '';
    $email = $_POST['Email'] ?? '';
    $department = $_POST['Department'] ?? '';
    $role = 'Student'; // Fixed as Student

    try {
        // 1. I-save muna sa 'users' table ayon sa iyong database columns
        $stmt = $pdo->prepare("INSERT INTO users (FullName, Email, Role_ENUM, Department) VALUES (?, ?, ?, ?)");
        $success = $stmt->execute([$fullName, $email, $role, $department]);

        if ($success) {
            // 2. Kung pumasok sa database, i-trigger ang email notification
            sendSystemEmail($email, $fullName, "Welcome to Lab Inventory System", "Hello $fullName, matagumpay na nagrehistro ang iyong student account.");
            
            $_SESSION['message'] = "Tagumpay na na-save at naipadala ang email!";
            header("Location: students.php");
            exit();
        }
    } catch (\Exception $e) {
        echo "Error sa database o email: " . $e->getMessage();
    }
}
?>