<?php
session_start();
require_once 'db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (!empty($email)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE Email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                $_SESSION['user_id'] = $user['UserID'] ?? null;
                $_SESSION['user_name'] = $user['FullName'];
                $_SESSION['user_role'] = $user['Role_ENUM'];

                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Walang nahanap na account sa email na ito.";
            }
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $error = "Mangyaring ilagay ang iyong email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Laboratory Inventory System</title>
    <style>
        body { 
            background: url('https://static.vecteezy.com/system/resources/thumbnails/033/524/184/small/a-dark-warehouse-full-of-boxes-free-photo.jpg') no-repeat center center fixed; 
            background-size: cover;
            font-family: sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            color: #fff; 
            margin: 0; 
            position: relative;
        }
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.75);
            z-index: 1;
        }
        .login-card { 
            background: rgba(26, 26, 36, 0.9); 
            padding: 30px; 
            border-radius: 8px; 
            border: 1px solid #435dd8; 
            width: 100%; 
            max-width: 320px; 
            z-index: 2;
            box-shadow: 0 8px 16px rgba(0,0,0,0.5);
        }
        input { width: 100%; padding: 10px; margin-bottom: 15px; background: #202030; border: 1px solid #435dd8; color: #fff; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #435dd8; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        button:hover { background: #3249b5; }
        .error { color: #ff6b6b; font-size: 13px; margin-bottom: 15px; text-align: center; }
        .register-link { text-align: center; margin-top: 15px; font-size: 13px; }
        .register-link a { color: #435dd8; text-decoration: none; }
        .register-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="login-card">
    <h3 style="text-align:center; margin-top:0;">SYSTEM LOGIN</h3>
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="I-type ang iyong Email" required>
        <button type="submit">Mag-log in</button>
    </form>
    <div class="register-link">
        Wala pang account? <a href="register.php">Register here</a>
    </div>
</div>
</body>
</html>