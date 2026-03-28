<?php
require_once '../auth/check_admin.php';
require_once '../DB/db.php';

$db = getDB();
$admin_id = $_SESSION['admin_id']; // logged-in admin

// FETCH DISHES FOR LOGGED-IN ADMIN
$stmt = $db->prepare("
    SELECT dish_id, name, category, region, status, image, price 
    FROM dishes 
    WHERE admin_id = ?
    ORDER BY name ASC
");
$stmt->execute([$admin_id]);
$dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>All Dishes | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* ===== Global ===== */
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #f9fafb;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1f2933;
            color: #fff;
            padding: 15px 25px;
            position: relative;
        }

        .admin-header h2 {
            font-size: 18px;
            color: #fff;
        }

        .admin-header a {
            margin-left: 15px;
            color: #f9fafb;
        }

        .admin-container {
            display: flex;
            min-height: calc(100vh - 60px);
        }

        .sidebar {
            width: 230px;
            background: #111827;
            padding-top: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .sidebar ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .sidebar ul li a {
            display: block;
            padding: 14px 20px;
            color: #e5e7eb;
            font-size: 15px;
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: #374151;
            color: #f59e0b;
            border-left: 4px solid #f59e0b;
        }

        .main-content {
            flex: 1;
            padding: 25px;
        }

        h2 {
            color: #111827;
            margin-bottom: 20px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
        }

        th {
            background: #1f2933;
            color: #fff;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background: #f3f4f6;
        }

        tr:hover {
            background: #f59e0b;
            color: #fff;
            cursor: pointer;
        }

        img {
            max-width: 80px;
            border-radius: 6px;
        }

        .sidebar-toggle {
            display: none;
            position: absolute;
            top: 15px;
            left: 15px;
            background: #f59e0b;
            color: #111827;
            border: none;
            padding: 10px 12px;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1100;
        }

        /* Responsive */
        @media screen and (max-width:560px) {
            .admin-container {
                flex-direction: column;
            }

            .sidebar {
                position: fixed;
                top: 60px;
                left: 0;
                height: calc(100%-60px);
                transform: translateX(-100%);
                z-index: 1000;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .sidebar-toggle {
                display: block;
            }

            .main-content {
                padding: 15px;
            }

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            tr {
                margin-bottom: 15px;
                border-radius: 8px;
                overflow: hidden;
                background: #fff;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
                padding: 10px;
            }

            th {
                display: none;
            }

            td {
                display: flex;
                justify-content: space-between;
                padding: 10px;
                border: none;
            }

            td img {
                max-width: 60px;
            }

            td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #374151;
            }
        }
    </style>
</head>

<body>
    <header class="admin-header">
        <button class="sidebar-toggle">☰</button>
        <h2>🍽️ Taste of Ethiopia — Admin</h2>
        <div><a href="../../frontend/index.php">Back</a> | <a href="../../auth/logout.php">Logout</a></div>
    </header>

    <div class="admin-container">
        <aside class="sidebar">
            <ul>
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="customers.php">Customers</a></li>
                <li><a href="view_dishes.php" class="active">All Dishes</a></li>
                <li><a href="add_dish.php">Add Dish</a></li>
                <li><a href="edit_dishes.php">Manage Dish</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="view_contacts.php">Feedback</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h2>All Dishes</h2>
            <?php if (empty($dishes)): ?>
                <p>No dishes found.</p>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Region</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dishes as $dish): ?>
                                <tr>
                                    <td data-label="Image"><?php if ($dish['image']): ?><img src="../admin/uploads/<?= htmlspecialchars($dish['image']) ?>" alt="<?= htmlspecialchars($dish['name']) ?>"><?php endif; ?></td>
                                    <td data-label="Name"><?= htmlspecialchars($dish['name']) ?></td>
                                    <td data-label="Category"><?= htmlspecialchars($dish['category']) ?></td>
                                    <td data-label="Region"><?= htmlspecialchars($dish['region']) ?></td>
                                    <td data-label="Price">$<?= number_format($dish['price'], 2) ?></td>
                                    <td data-label="Status"><?= htmlspecialchars($dish['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        const toggleBtn = document.querySelector('.sidebar-toggle');
        const sidebar = document.querySelector('.sidebar');
        toggleBtn.addEventListener('click', () => sidebar.classList.toggle('active'));
    </script>
</body>

</html>