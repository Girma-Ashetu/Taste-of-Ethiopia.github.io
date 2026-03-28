<?php
session_start();
require_once '../auth/check_admin.php';
require_once '../DB/db.php';

$db = getDB();
$admin_id = $_SESSION['admin_id'];

// --- Fetch stats ---
$stmt = $db->prepare("SELECT COUNT(*) as total_dishes FROM dishes WHERE admin_id = ?");
$stmt->execute([$admin_id]);
$total_dishes = $stmt->fetch(PDO::FETCH_ASSOC)['total_dishes'] ?? 0;

$stmt = $db->prepare("SELECT COUNT(*) as total_orders FROM orderfood WHERE admin_id = ?");
$stmt->execute([$admin_id]);
$total_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'] ?? 0;

$stmt = $db->prepare("SELECT COUNT(*) as pending_orders FROM orderfood WHERE admin_id = ? AND status = 'Pending'");
$stmt->execute([$admin_id]);
$pending_orders = $stmt->fetch(PDO::FETCH_ASSOC)['pending_orders'] ?? 0;

$stmt = $db->prepare("SELECT AVG(rating) as avg_rating FROM dishes WHERE admin_id = ?");
$stmt->execute([$admin_id]);
$avg_rating = round($stmt->fetch(PDO::FETCH_ASSOC)['avg_rating'] ?? 0, 1);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Taste of Ethiopia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../asset/css/admin.css">
    <link rel="stylesheet" href="../../asset/css/website.css">
</head>

<body>

    <header class="admin-header">
        <button class="sidebar-toggle">☰</button>
        <h2>🍽️ Taste of Ethiopia — Admin</h2>
        <div><a href="../../frontend/index.php">Back</a> | <a href="../auth/logout.php">Logout</a></div>
    </header>

    <div class="admin-container">
        <aside class="sidebar">
            <ul>
                <li><a href="admin.php" class="active">Dashboard</a></li>
                <li><a href="customers.php">Customers</a></li>
                <li><a href="view_dishes.php">All Dishes</a></li>
                <li><a href="add_dish.php">Add Dish</a></li>
                <li><a href="edit_dishes.php">Manage Dish</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="view_contacts.php">Feedback</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h1>Welcome, Admin 👋</h1>

            <!-- Stats Cards -->
            <div class="stats">
                <div class="card">
                    <h3>Total Dishes</h3>
                    <p><?= $total_dishes ?></p>
                </div>
                <div class="card">
                    <h3>Total Orders</h3>
                    <p><?= $total_orders ?></p>
                </div>
                <div class="card">
                    <h3>Pending Orders</h3>
                    <p><?= $pending_orders ?></p>
                </div>
                <div class="card">
                    <h3>Average Rating</h3>
                    <p><?= $avg_rating ?> ⭐</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <ul>
                    <li><a href="add_dish.php">➕ Add New Dish</a></li>
                    <li><a href="orders.php">📦 View Orders</a></li>
                    <li><a href="view_contacts.php">💬 View Feedback</a></li>
                </ul>
            </div>
        </main>
    </div>

    <script>
        const toggleBtn = document.querySelector('.sidebar-toggle');
        const sidebar = document.querySelector('.sidebar');
        toggleBtn.addEventListener('click', () => sidebar.classList.toggle('active'));
    </script>

</body>

</html>