<?php
require_once '../auth/check_admin.php';
require_once '../DB/db.php';

$db = getDB();
$message = $_SESSION['success'] ?? $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// --- Handle single or bulk delete ---
if (!empty($_GET['delete_id']) || !empty($_POST['bulk_delete'])) {
    $ids = !empty($_GET['delete_id']) ? [intval($_GET['delete_id'])] : ($_POST['selected_customers'] ?? []);
    if ($ids) {
        try {
            $stmt = $db->prepare("DELETE FROM customers WHERE id IN (" . implode(',', array_fill(0, count($ids), '?')) . ")");
            $stmt->execute($ids);
            $_SESSION['success'] = count($ids) . " customer(s) deleted!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error deleting: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "No customers selected.";
    }
    header("Location: customers.php");
    exit;
}

// --- Fetch customers ---
$customers = $db->query("SELECT id, name, email, created_at FROM customers ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// --- Customer stats ---
$stats = $db->query("
    SELECT COUNT(*) total, COUNT(DISTINCT DATE(created_at)) unique_days, MAX(created_at) latest_signup 
    FROM customers
")->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'unique_days' => 0, 'latest_signup' => null];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Customers | Taste of Ethiopia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
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
            transition: all 0.3s ease;
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

        .message {
            background: #d4edda;
            color: #155724;
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #f4f4f4;
        }

        tr:hover {
            background: #f1f5f9;
        }

        .customer-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 10px;
        }

        .btn-action {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            margin-right: 5px;
            cursor: pointer;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        .btn-email {
            background: #3b82f6;
            color: white;
        }

        .btn-email:hover {
            background: #2563eb;
        }

        .bulk-actions {
            display: flex;
            gap: 10px;
            padding: 15px;
            background: #f3f4f6;
            border-bottom: 1px solid #e5e7eb;
            align-items: center;
        }

        .date-badge {
            background: #f3f4f6;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            color: #6b7280;
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
                transition: 0.3s;
            }

            .sidebar.active {
                transform: translateX(0);
            }
        }
    </style>
</head>

<body>
    <header class="admin-header">
        <h2>🍽️ Taste of Ethiopia — Admin</h2>
        <div><a href="../../frontend/index.php">Back</a> | <a href="../auth/logout.php">Logout</a></div>
    </header>
    <div class="admin-container">
        <aside class="sidebar">
            <ul>
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="customers.php" class="active">Customers</a></li>
                <li><a href="view_dishes.php">All Dishes</a></li>
                <li><a href="add_dish.php">Add Dish</a></li>
                <li><a href="edit_dishes.php">Manage Dish</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="view_contacts.php">Feedback</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <h2>👥 Customers</h2>

            <?php if ($message): ?>
                <div class="message"><?= htmlspecialchars($message) ?></div>
                <script>
                    setTimeout(() => {
                        document.querySelector('.message').style.display = 'none';
                    }, 2000);
                </script>
            <?php endif; ?>

            <?php if ($customers): ?>
                <form method="POST" class="bulk-actions">
                    <input type="checkbox" id="selectAll"> <label for="selectAll">Select All</label>
                    <button type="submit" name="bulk_delete" class="btn-delete" onclick="return confirm('Delete selected customers?')">Delete Selected</button>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $i => $c): ?>
                            <tr>
                                <td><input type="checkbox" name="selected_customers[]" value="<?= $c['id'] ?>"><?= $i + 1 ?></td>
                                <td style="display:flex;align-items:center;">
                                    <div class="customer-avatar"><?= strtoupper($c['name'][0]) ?></div><?= htmlspecialchars($c['name']) ?>
                                </td>
                                <td><?= htmlspecialchars($c['email']) ?></td>
                                <td><span class="date-badge"><?= date('M d, Y', strtotime($c['created_at'])) ?></span></td>
                                <td>
                                    <button class="btn-email" onclick="window.location='mailto:<?= htmlspecialchars($c['email']) ?>'">Email</button>
                                    <button class="btn-delete" onclick="if(confirm('Delete <?= $c['name'] ?>?'))window.location='?delete_id=<?= $c['id'] ?>'">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No customers found.</p>
            <?php endif; ?>
        </main>
    </div>

    <script>
        const selectAll = document.getElementById('selectAll');
        selectAll?.addEventListener('change', () => {
            document.querySelectorAll('input[name="selected_customers[]"]').forEach(cb => cb.checked = selectAll.checked);
        });
    </script>
</body>

</html>