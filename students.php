<?php
session_start();
require_once 'db.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE Role_ENUM = ? ORDER BY FullName ASC");
    $stmt->execute(['student']);
    $students = $stmt->fetchAll();
} catch (\Exception $e) {
    $students = [];
}
?>

<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Users - Laboratory Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #07090f; 
            color: #fff; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            overflow-x: hidden;
            padding-bottom: 50px;
        }

        /* Floating Navigation Bar (Uiverse Style) */
        .top-navbar { 
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 85%;
            max-width: 950px;
            background-color: rgba(15, 22, 36, 0.95);
            border: 1px solid rgba(0, 240, 255, 0.3);
            border-radius: 40px;
            padding: 8px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.6);
        }

        /* Main Container */
        .main-container {
            max-width: 1050px;
            margin: 110px auto 30px auto;
            padding: 0 20px;
        }

        /* Glassmorphism Content Panel */
        .content-panel {
            background: linear-gradient(135deg, rgba(20, 30, 48, 0.85), rgba(11, 15, 25, 0.95));
            border: 1.5px solid #00f0ff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0, 240, 255, 0.2);
            backdrop-filter: blur(5px);
        }

        .title-text {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: #ffffff;
            text-transform: uppercase;
        }

        /* Custom Glowing Grid Table Styling */
        .table-custom {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #00f0ff;
            background-color: rgba(15, 22, 36, 0.8);
        }

        .table-custom th, .table-custom td {
            border: 1px solid rgba(0, 240, 255, 0.3);
            padding: 16px 20px;
            vertical-align: middle;
            color: #ffffff;
            font-size: 14px;
        }

        .table-custom th {
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            background-color: rgba(7, 9, 15, 0.9);
            color: #bce8f1;
        }

        .table-custom td {
            font-weight: 600;
        }

        .status-active {
            color: #38bdf8;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }

        /* Uiverse Capsule Drop Button */
        .btn-drop {
            background-color: transparent;
            border: 1.5px solid #00f0ff;
            color: #00f0ff;
            font-weight: bold;
            font-size: 12px;
            padding: 6px 22px;
            border-radius: 20px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            letter-spacing: 1px;
        }

        .btn-drop:hover {
            background-color: #00f0ff;
            color: #07090f;
            box-shadow: 0 0 15px rgba(0, 240, 255, 0.6);
        }
    </style>
</head>
<body>

    <!-- Floating Top Navbar -->
    <nav class="navbar top-navbar px-4">
        <div>
            <a href="dashboard.php" class="text-white text-decoration-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-house-door-fill" viewBox="0 0 16 16">
                    <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"/>
                </svg>
            </a>
        </div>
        <div class="navbar-brand text-white fw-bold" style="letter-spacing: 1.5px; font-size: 15px;">STUDENT USERS MANAGEMENT</div>
        <div>
            <a href="profile.php" class="text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                </svg>
            </a>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="main-container">
        <div class="content-panel">
            <div class="mb-4">
                <h2 class="title-text m-0">LIST OF STUDENTS</h2>
            </div>

            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th style="width: 15%;">STUDENT NO.</th>
                            <th style="width: 25%;">NAME</th>
                            <th style="width: 30%;">EMAIL</th>
                            <th style="width: 15%;">STATUS</th>
                            <th style="width: 15%;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($students)): ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td class="text-center"><?= htmlspecialchars($student['UserID']) ?></td>
                                    <td class="text-uppercase"><?= htmlspecialchars($student['FullName']) ?></td>
                                    <td><?= htmlspecialchars($student['Email']) ?></td>
                                    <td class="status-active">ACTIVE</td>
                                    <td class="text-center">
                                        <a href="delete_student.php?id=<?= $student['UserID'] ?>" 
                                           class="btn-drop" 
                                           onclick="return confirm('Sigurado ka bang gusto mong i-drop ang estudyanteng ito?');">
                                           DROP
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    Walang nakitang student users sa database.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>