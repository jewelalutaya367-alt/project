<?php
session_start();
require_once 'db.php';

try {
    $studentCount = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $teacherCount = $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
    $itemCount = $pdo->query("SELECT SUM(TotalQuantity) FROM items")->fetchColumn();
    
    $stmtItems = $pdo->query("SELECT * FROM items ORDER BY Category ASC, ItemName ASC");
    $allItems = $stmtItems->fetchAll();
} catch (\Exception $e) {
    $studentCount = 0;
    $teacherCount = 0;
    $itemCount = 0;
    $allItems = [];
}
?>

<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InventoryLABS - Laboratory Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html {
            scroll-behavior: smooth;
        }
        
        body { 
            background-color: #07090f;
            color: #fff; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            overflow-x: hidden;
        }

        /* Floating Navigation Bar */
        .top-nav-container {
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

        .nav-brand {
            font-weight: bold;
            color: #bce8f1;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 16px;
            letter-spacing: 1.5px;
            text-shadow: 0 0 10px rgba(0, 240, 255, 0.4);
        }

        .nav-brand img {
            height: 34px;
            width: auto;
            object-fit: contain;
        }

        .nav-right-group {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .nav-links a {
            color: #bce8f1;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1.5px;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: #38bdf8;
        }

        .profile-icon-link {
            color: #fff;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .profile-icon-link:hover {
            color: #38bdf8;
        }

        /* Full Screen Sections */
        .page-section {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 90px 20px;
            max-width: 1050px;
            margin: 0 auto;
        }

        #section1 {
            background: linear-gradient(rgba(7, 9, 15, 0.88), rgba(7, 9, 15, 0.92)), 
                        url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQUV8oO3pkOyztkocwbwlgwuROO0PtMRm-5qQKMC7pZ9w&s=10');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            max-width: 100% !important;
            width: 100%;
        }

        .section-content-wrapper {
            width: 100%;
            max-width: 1050px;
            text-align: center;
        }

        .inventory-heading {
            text-align: center;
            margin-bottom: 40px;
        }

        .inventory-heading .title-top {
            font-size: 26px;
            font-weight: 600;
            letter-spacing: 3px;
            color: #bce8f1;
            text-transform: uppercase;
            margin-bottom: -5px;
        }

        .inventory-heading .title-bottom {
            font-size: 42px;
            font-weight: 800;
            letter-spacing: 4px;
            color: #ffffff;
            text-transform: uppercase;
            text-shadow: 0 0 15px rgba(0, 240, 255, 0.4);
        }

        .glow-card {
            background: linear-gradient(135deg, rgba(20, 30, 48, 0.85), rgba(11, 15, 25, 0.95));
            border: 1.5px solid #00f0ff;
            border-radius: 20px;
            padding: 30px 20px;
            width: 100%;
            text-align: center;
            color: #ffffff;
            cursor: pointer;
            box-shadow: 0 0 15px rgba(0, 240, 255, 0.3), inset 0 0 15px rgba(0, 240, 255, 0.1);
            transition: all 0.3s ease-in-out;
            text-decoration: none;
            display: block;
            backdrop-filter: blur(5px);
        }

        .glow-card:hover {
            transform: translateY(-5px);
            border-color: #38bdf8;
            box-shadow: 0 0 25px rgba(0, 240, 255, 0.6), 
                        0 0 50px rgba(0, 240, 255, 0.2), 
                        inset 0 0 20px rgba(0, 240, 255, 0.3);
        }

        .icon-container {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 60px;
            height: 60px;
            border: 1px solid #00f0ff;
            border-radius: 14px;
            margin-bottom: 15px;
            color: #00f0ff;
            box-shadow: 0 0 10px rgba(0, 240, 255, 0.2);
            background-color: rgba(0, 240, 255, 0.05);
        }

        .card-value {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
            letter-spacing: 1px;
            color: #ffffff;
        }

        .card-label {
            font-size: 0.95rem;
            color: #94a3b8;
            letter-spacing: 0.5px;
        }

        #section2 {
            background: linear-gradient(rgba(11, 15, 25, 0.85), rgba(7, 9, 15, 0.92)), 
                        url('https://static.vecteezy.com/system/resources/thumbnails/056/637/487/small/a-long-hallway-with-many-shelves-and-lights-photo.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            max-width: 100% !important;
            width: 100%;
        }

        .content-panel {
            background: rgba(13, 18, 30, 0.88);
            width: 100%;
            max-width: 1050px;
            border-radius: 20px;
            padding: 40px 30px;
            border: 1px solid rgba(0, 240, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0,0,0,0.8);
            backdrop-filter: blur(8px);
            margin-bottom: 20px;
        }

        .course-glow-card {
            background: linear-gradient(135deg, rgba(20, 30, 48, 0.85), rgba(11, 15, 25, 0.95));
            border: 1.5px solid #00f0ff;
            border-radius: 20px;
            padding: 25px;
            color: #ffffff;
            box-shadow: 0 0 15px rgba(0, 240, 255, 0.3), inset 0 0 15px rgba(0, 240, 255, 0.1);
            transition: all 0.3s ease-in-out;
            backdrop-filter: blur(5px);
            text-decoration: none;
            display: block;
            cursor: pointer;
        }

        .course-glow-card:hover {
            transform: translateY(-5px);
            border-color: #38bdf8;
            box-shadow: 0 0 25px rgba(0, 240, 255, 0.6), 
                        0 0 50px rgba(0, 240, 255, 0.2), 
                        inset 0 0 20px rgba(0, 240, 255, 0.3);
            color: #fff;
        }

        .glass-card-standard {
            background-color: rgba(18, 25, 41, 0.9);
            border: 1px solid #2a3d7d;
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 6px 15px rgba(0,0,0,0.4);
        }

        .table-dark-custom { 
            background-color: rgba(15, 22, 36, 0.95); 
            color: #fff; 
            border-color: #2a3d7d; 
            width: 100%;
            margin-bottom: 0;
        }
        .table-dark-custom th, .table-dark-custom td { 
            border-color: #2a3d7d; 
            vertical-align: middle; 
            padding: 10px;
            font-size: 11px;
        }
        .table-dark-custom th {
            background-color: #07090f;
            color: #fff;
            text-transform: uppercase;
        }
        .table-dark-custom td {
            background-color: rgba(15, 22, 36, 0.7);
            color: #fff;
        }
        .category-text {
            text-transform: lowercase;
            font-weight: 600;
            color: #38bdf8;
        }
        #section3 {
        background: linear-gradient(rgba(7, 9, 15, 0.88), rgba(7, 9, 15, 0.92)), 
                url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQUV8oO3pkOyztkocwbwlgwuROO0PtMRm-5qQKMC7pZ9w&s=10');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        max-width: 100% !important;
        width: 100%;
}
    </style>
</head>
<body>

    <!-- Floating Navigation Bar -->
    <div class="top-nav-container">
        <a href="#section1" class="nav-brand">
            <img src="logo.png" alt="InventoryLABS Logo">
            InventoryLABS
        </a>
        <div class="nav-right-group">
            <div class="nav-links">
                <a href="#section1">HOME</a>
                <a href="#section3">ADMIN</a>
                <a href="#section3">INVENTORY</a>
                <a href="#section4">BORROW</a>
            </div>
            <a href="profile.php" class="profile-icon-link" title="Profile">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                </svg>
            </a>
        </div>
    </div>

    <!-- ================= SEKSYON 1: HOME ================= -->
    <div id="section1" class="page-section">
        <div class="section-content-wrapper">
            <div class="inventory-heading">
                <div class="title-top">LABORATORY</div>
                <div class="title-bottom">INVENTORY</div>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; width: 100%;">
                <div class="glow-card">
                    <div class="icon-container">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <div class="card-value"><?= $studentCount ?></div>
                    <div class="card-label">Number of students</div>
                </div>

                <div class="glow-card">
                    <div class="icon-container">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                    <div class="card-value"><?= $teacherCount ?></div>
                    <div class="card-label">Number of teachers</div>
                </div>

                <div class="glow-card">
                    <div class="icon-container">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                    <div class="card-value"><?= $itemCount ?? 0 ?></div>
                    <div class="card-label">Number of items</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= SEKSYON 2: LABORATORY COURSES ================= -->
    <div id="section2" class="page-section">
        <div class="content-panel">
            <div class="inventory-heading" style="margin-bottom: 25px;">
                <div class="title-bottom" style="font-size: 28px;">LABORATORY COURSES</div>
            </div>

            <div style="display: flex; gap: 20px; width: 100%; margin-bottom: 20px;">
                <div class="course-glow-card" style="flex: 2; text-align: left; padding: 25px 30px; cursor: default;">
                    <h4 style="color: #bce8f1; font-weight: bold; text-align: center; margin-bottom: 12px; font-size: 16px;">COMPUTER ENGINEERING</h4>
                    <p style="text-align: center; color: #ced4da; font-size: 12px; line-height: 1.6; margin: 0;">
                        A discipline that combines Electrical Engineering and Computer Science. It focuses on the design, development, and maintenance of both the hardware and software of computer systems.
                    </p>
                </div>
                <a href="cpe_items.php" class="course-glow-card" style="flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; text-decoration: none;">
                    <div class="icon-container" style="margin-bottom: 10px;">
                        <span style="font-size: 24px;">💻</span>
                    </div>
                    <h5 style="color: #fff; font-weight: bold; font-size: 13px; margin: 0; letter-spacing: 1px;">CPE LABORATORY</h5>
                    <span style="font-size: 10px; color: #38bdf8; margin-top: 5px;">(Click to view items)</span>
                </a>
            </div>

            <div style="display: flex; gap: 20px; width: 100%;">
                <a href="bliss_items.php" class="course-glow-card" style="flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; text-decoration: none;">
                    <div class="icon-container" style="margin-bottom: 10px;">
                        <span style="font-size: 24px;">📚</span>
                    </div>
                    <h5 style="color: #fff; font-weight: bold; font-size: 13px; margin: 0; letter-spacing: 1px;">BLIS LABORATORY</h5>
                    <span style="font-size: 10px; color: #38bdf8; margin-top: 5px;">(Click to view items)</span>
                </a>
                <div class="course-glow-card" style="flex: 2; text-align: left; padding: 25px 30px; cursor: default;">
                    <h4 style="color: #bce8f1; font-weight: bold; text-align: center; margin-bottom: 12px; font-size: 16px;">LIBRARY AND INFORMATION SCIENCE</h4>
                    <p style="text-align: center; color: #ced4da; font-size: 12px; line-height: 1.6; margin: 0;">
                        It focuses on the effective collection, organization, preservation, and distribution of information and records—whether private or public.
                    </p>
                </div>
            </div>
        </div>
    </div>
<div id="section3" class="page-section" style="background: linear-gradient(rgba(7, 9, 15, 0.88), rgba(7, 9, 15, 0.92)), url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQUV8oO3pkOyztkocwbwlgwuROO0PtMRm-5qQKMC7pZ9w&s=10'); background-size: cover; background-position: center; background-repeat: no-repeat; width: 100%; max-width: 100% !important;">
    <div class="content-panel">
        
        <!-- Ümiiral mong Buttons sa Itaas -->
        <div style="display: flex; flex-direction: column; gap: 20px; width: 100%; margin-bottom: 20px;">
            <div style="display: flex; gap: 20px; width: 100%;">
                <a href="students.php" class="glow-card text-decoration-none py-4 flex-fill" style="padding: 20px !important; text-align: center;">
                    <div class="icon-container mx-auto" style="width: 45px; height: 45px; margin-bottom: 8px;">
                        <span style="font-size: 20px;">🎓</span>
                    </div>
                    <div style="font-weight: bold; color: #fff; font-size: 12px; letter-spacing: 1px;">STUDENTS USERS</div>
                </a>
                <a href="admin.php" class="glow-card text-decoration-none py-4 flex-fill" style="padding: 20px !important; text-align: center;">
                    <div class="icon-container mx-auto" style="width: 45px; height: 45px; margin-bottom: 8px;">
                        <span style="font-size: 20px;">⚙️</span>
                    </div>
                    <div style="font-weight: bold; color: #fff; font-size: 12px; letter-spacing: 1px;">ADMIN</div>
                </a>
            </div>
        </div>

        <!-- IDINAGDAG NA REPORT & ANALYTICS SECTION SA BABA -->
        <div class="glass-card-standard w-100 p-4" style="text-align: left;">
            <h6 class="fw-bold mb-4" style="color: #00f0ff; font-size: 14px; letter-spacing: 1.5px; text-transform: uppercase;">
                📊 REPORT & ANALYTICS OVERVIEW
            </h6>

            <!-- 1. Executive Overview KPIs -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div style="background: rgba(15, 22, 36, 0.8); border: 1px solid rgba(0, 240, 255, 0.3); border-radius: 12px; padding: 15px; text-align: center;">
                        <div style="font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Total Items</div>
                        <div style="font-size: 22px; font-weight: 800; color: #ffffff; margin-top: 5px;"><?= $totalItems ?? 0 ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div style="background: rgba(15, 22, 36, 0.8); border: 1px solid rgba(0, 240, 255, 0.3); border-radius: 12px; padding: 15px; text-align: center;">
                        <div style="font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Active Borrowers</div>
                        <div style="font-size: 22px; font-weight: 800; color: #ffffff; margin-top: 5px;"><?= $totalStudents ?? 0 ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div style="background: rgba(15, 22, 36, 0.8); border: 1px solid rgba(0, 240, 255, 0.3); border-radius: 12px; padding: 15px; text-align: center;">
                        <div style="font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Currently Borrowed</div>
                        <div style="font-size: 22px; font-weight: 800; color: #ffffff; margin-top: 5px;"><?= $totalBorrowed ?? 0 ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div style="background: rgba(15, 22, 36, 0.8); border: 1px solid rgba(0, 240, 255, 0.3); border-radius: 12px; padding: 15px; text-align: center;">
                        <div style="font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Damaged / Waste</div>
                        <div style="font-size: 22px; font-weight: 800; color: #ff6b6b; margin-top: 5px;"><?= $totalDamaged ?? 0 ?></div>
                    </div>
                </div>
            </div>

            <!-- 2. Visualizations (Graphs) -->
            <div class="row g-3 mb-4">
                <div class="col-md-7">
                    <div style="background: rgba(15, 22, 36, 0.8); border: 1px solid rgba(0, 240, 255, 0.3); border-radius: 12px; padding: 15px;">
                        <h6 style="color: #bce8f1; font-size: 12px; font-weight: bold; margin-bottom: 10px;">Weekly Usage Trend</h6>
                        <canvas id="dashboardUsageChart" height="120"></canvas>
                    </div>
                </div>
                <div class="col-md-5">
                    <div style="background: rgba(15, 22, 36, 0.8); border: 1px solid rgba(0, 240, 255, 0.3); border-radius: 12px; padding: 15px;">
                        <h6 style="color: #bce8f1; font-size: 12px; font-weight: bold; margin-bottom: 10px;">Item Distribution (CPE vs BLIS)</h6>
                        <canvas id="dashboardCategoryChart" height="120"></canvas>
                    </div>
                </div>
            </div>

            <!-- 3. Insights & Action Plan -->
            <div style="background: rgba(15, 22, 36, 0.8); border: 1px solid rgba(0, 240, 255, 0.3); border-radius: 12px; padding: 15px;">
                <h6 style="color: #bce8f1; font-size: 12px; font-weight: bold; margin-bottom: 10px;">Insights & Actionable Next Steps</h6>
                <div style="font-size: 11px; color: #cbd5e1; line-height: 1.5;">
                    <p class="mb-1"><strong>🔍 Data Overview:</strong> Aktibong tumaas ang sirkulasyon ng mga kagamitan sa lab ngayong linggo dahil sa mga sunud-sunod na eksperimento.</p>
                    <p class="mb-0"><strong>🚀 Rekomendasyon:</strong> I-monitor ang mga nasirang kagamitan para sa agarang pag-replenish o pag-aayos.</p>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Chart.js Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxDashTrend = document.getElementById('dashboardUsageChart').getContext('2d');
    new Chart(ctxDashTrend, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            datasets: [{
                label: 'Hiram na Gamit',
                data: [10, 18, 14, 24, 20, 8],
                borderColor: '#00f0ff',
                backgroundColor: 'rgba(0, 240, 255, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            plugins: { legend: { labels: { color: '#fff', font: { size: 10 } } } },
            scales: {
                x: { ticks: { color: '#94a3b8', font: { size: 9 } }, grid: { color: 'rgba(255,255,255,0.05)' } },
                y: { ticks: { color: '#94a3b8', font: { size: 9 } }, grid: { color: 'rgba(255,255,255,0.05)' } }
            }
        }
    });

    const ctxDashCat = document.getElementById('dashboardCategoryChart').getContext('2d');
    new Chart(ctxDashCat, {
        type: 'doughnut',
        data: {
            labels: ['CPE', 'BLIS'],
            datasets: [{
                data: [60, 40],
                backgroundColor: ['#00f0ff', '#3b82f6']
            }]
        },
        options: {
            plugins: { legend: { labels: { color: '#fff', font: { size: 9 } } } }
        }
    });
</script>

    <!-- ================= SEKSYON 4: CONTACT & FOOTER ================= -->
    <div id="section4" class="page-section" style="min-height: 70vh;">
        <div class="content-panel">
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; gap: 30px; margin-bottom: 35px;">
                <div style="flex: 1.5;">
                    <div class="nav-brand mb-2" style="font-size: 20px;">
                        <img src="logo.png" alt="InventoryLABS Logo">
                        InventoryLABS
                    </div>
                    <p style="color: #adb5bd; font-size: 13px; line-height: 1.6; margin: 0;">
                        The inventory lab system was developed to allow students to directly borrow the equipment they need for their studies.
                    </p>
                </div>

                <div style="flex: 1; text-align: center;">
                    <h5 style="color: #bce8f1; font-weight: bold; font-size: 14px; margin-bottom: 15px; letter-spacing: 1px;">MESSAGE US;</h5>
                    <div style="display: flex; justify-content: center; gap: 15px;">
                        <div class="glow-card" style="padding: 0; border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 16px; color: #38bdf8;">@</span>
                        </div>
                        <div class="glow-card" style="padding: 0; border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 16px; color: #38bdf8;">f</span>
                        </div>
                    </div>
                </div>
            </div>

            <div style="text-align: center; border-top: 1px solid rgba(31, 49, 102, 0.5); width: 100%; padding-top: 20px;">
                <div style="color: #bce8f1; font-weight: bold; letter-spacing: 1px; font-size: 13px;">
                    Thank you for visiting our inventory System.
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterLabItems(labName) {
            const rows = document.querySelectorAll('#inventory-table-body tr');
            const titleEl = document.getElementById('inventory-title');
            
            if (labName === 'ALL') {
                titleEl.innerText = "INVENTORY LIST (ALL)";
                rows.forEach(row => row.style.display = '');
                return;
            }

            title_text = `INVENTORY LIST (${labName} LABORATORY)`;
            titleEl.innerText = title_text;
            
            rows.forEach(row => {
                const category = row.getAttribute('data-category');
                if (category && category.includes(labName)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>