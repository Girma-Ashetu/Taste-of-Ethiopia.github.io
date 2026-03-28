<?php
require_once '../auth/check_admin.php';
require_once '../DB/db.php';

$db = getDB();

/* ===============================
   DELETE CONTACT MESSAGE
================================ */
if (isset($_GET['delete_id'])) {
    $delete_id = (int) $_GET['delete_id'];
    $stmt = $db->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->execute([$delete_id]);
    header("Location: view_contacts.php");
    exit;
}

/* ===============================
   FETCH CONTACT MESSAGES
================================ */
$stmt = $db->prepare("SELECT id, first_name, last_name, email, message FROM contacts ORDER BY id DESC");
$stmt->execute();
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
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
            color: #fff;
        }

        .admin-header h2 {
            font-size: 18px;
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
        }

        th {
            background: #1f2933;
            color: #fff;
            text-align: left;
        }

        tr:nth-child(even) {
            background: #f3f4f6;
        }

        tr:hover {
            background: #f59e0b;
            color: #fff;
        }

        .delete-btn {
            color: #dc2626;
            font-weight: bold;
            transition: color 0.3s;
        }

        .delete-btn:hover {
            text-decoration: underline;
            color: #b91c1c;
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

            .main-content {
                padding: 15px;
            }

            table {
                font-size: 14px;
            }

            .sidebar-toggle {
                display: block;
            }
        }
    </style>
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
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="customers.php">Customers</a></li>
                <li><a href="view_dishes.php">All Dishes</a></li>
                <li><a href="add_dish.php">Add Dish</a></li>
                <li><a href="edit_dishes.php">Manage Dish</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="view_contacts.php" class="active">Feedback</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h2>Contact Messages</h2>
            <?php if (empty($contacts)): ?>
                <p>No messages found.</p>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $c): ?>
                                <tr>
                                    <td><?= htmlspecialchars($c['first_name']) ?></td>
                                    <td><?= htmlspecialchars($c['last_name']) ?></td>
                                    <td><?= htmlspecialchars($c['email']) ?></td>
                                    <td><?= nl2br(htmlspecialchars($c['message'])) ?></td>
                                    <td>
                                        <a href="view_contacts.php?delete_id=<?= $c['id'] ?>" onclick="return confirm('Are you sure you want to delete this message?')" class="delete-btn">❌ Delete</a>
                                    </td>
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