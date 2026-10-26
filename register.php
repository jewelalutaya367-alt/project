<?php
session_start();
require_once 'db.php';
require_once 'mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedRole  = $_POST['value-radio'] ?? 'Student';
    $studentId     = trim($_POST['student_id'] ?? '');
    $fullName      = trim($_POST['full_name'] ?? '');
    $department    = trim($_POST['department'] ?? '');
    $email         = trim($_POST['email'] ?? '');

    if (!empty($fullName) && !empty($email)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (UserID, FullName, Email, Role_ENUM, Department) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$studentId, $fullName, $email, $selectedRole, $department]);

            $subject = "Welcome to Laboratory Inventory System";
            $body    = "Hello <b>{$fullName}</b>,<br><br>Your <b>{$selectedRole}</b> account has been successfully created.";

            @sendSystemEmail($email, $fullName, $subject, $body);

            header("Location: login.php");
            exit();

        } catch (PDOException $e) {
            // Error handling
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <title>Register - Laboratory Inventory System</title>
    <style>
        body { 
            background: url('https://static.vecteezy.com/system/resources/thumbnails/033/524/184/small/a-dark-warehouse-full-of-boxes-free-photo.jpg') no-repeat center center fixed; 
            background-size: cover;
            font-family: sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; color: #fff; margin: 0; position: relative;
        }
        body::before { content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.75); z-index: 1; }
        .card { background: rgba(26, 26, 36, 0.9); padding: 30px; border-radius: 8px; border: 1px solid #435dd8; width: 100%; max-width: 350px; z-index: 2; box-shadow: 0 8px 16px rgba(0,0,0,0.5); }
        .form-group { display: flex; flex-direction: column; gap: 10px; margin-top: 15px; }
        input[type="text"], input[type="email"] { padding: 10px; background: #202030; border: 1px solid #435dd8; color: #fff; border-radius: 5px; width: 100%; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px; background: #435dd8; color: #fff; border: none; border-radius: 5px; width: 100%; cursor: pointer; font-weight: bold; }
        button:hover { background: #3249b5; }
        .radio-group { display: flex; justify-content: space-around; margin-bottom: 15px; cursor: pointer; }
        .login-link { text-align: center; margin-top: 15px; font-size: 13px; }
        .login-link a { color: #435dd8; text-decoration: none; }
    </style>
</head>
<body>
<div class="card">
    <h3 style="text-align:center; margin-top:0;">REGISTER</h3>

    <form method="POST" action="">
        <div class="radio-group">
            <label><input type="radio" name="value-radio" value="Student" checked onclick="updateForm('Student')"> Student</label>
            <label><input type="radio" name="value-radio" value="Teacher" onclick="updateForm('Teacher')"> Teacher</label>
        </div>

        <div class="form-group">
            <!-- Student Number / ID Field na idinagdag -->
            <input type="text" name="student_id" id="field_id" placeholder="Student Number / ID" required>
            
            <input type="text" name="full_name" id="field_name" placeholder="Full Name" required>
            <input type="text" name="department" id="field_dept" placeholder="Course & Section" required>
            <input type="email" name="email" placeholder="Email Address" required>
        </div>

        <button type="submit">Register</button>
    </form>

    <div class="login-link">
        May account na ba? <a href="login.php">Log in here</a>
    </div>
</div>

<script>
function updateForm(role) {
    const idInput   = document.getElementById('field_id');
    const nameInput = document.getElementById('field_name');
    const deptInput = document.getElementById('field_dept');

    if (role === 'Student') {
        idInput.placeholder   = "Student Number / ID";
        nameInput.placeholder = "Full Name";
        deptInput.placeholder = "Course & Section";
    } else {
        idInput.placeholder   = "Teacher ID / Employee No.";
        nameInput.placeholder = "Teacher's Name";
        deptInput.placeholder = "Department Name";
    }
}
</script>
</body>
</html>