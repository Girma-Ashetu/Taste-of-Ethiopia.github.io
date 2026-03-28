<?php
require_once '../auth/check_admin.php';
require_once '../DB/db.php';

$db = getDB();
$admin_id = $_SESSION['admin_id'];
$message = "";

// --- Handle status update ---
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    $allowed_status = ['Pending', 'Processing', 'Completed', 'Cancelled'];

    if (in_array($new_status, $allowed_status)) {
        $stmt = $db->prepare("
            UPDATE orderfood 
            SET status = ? 
            WHERE order_id = ? 
            AND admin_id = ?
        ");
        if ($stmt->execute([$new_status, $order_id, $admin_id])) {
            $message = "✅ Order #$order_id status updated to $new_status!";
        } else {
            $message = "❌ Failed to update order status.";
        }
    }
}

// --- Handle remove order action ---
if (isset($_POST['delete_order'])) {
    $order_id = (int)$_POST['order_id'];
    $stmt = $db->prepare("
        DELETE FROM orderfood 
        WHERE order_id = ? AND admin_id = ?
    ");
    if ($stmt->execute([$order_id, $admin_id])) {
        $message = "🗑️ Order #$order_id removed successfully!";
    } else {
        $message = "❌ Failed to remove order.";
    }
}

// --- Fetch orders ---
$stmt = $db->prepare("
    SELECT * 
    FROM orderfood 
    WHERE admin_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$admin_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders | Taste of Ethiopia</title>
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

        .sidebar ul li {
            margin-bottom: 5px;
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

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            color: #fff;
            font-weight: 600;
            text-align: center;
            display: inline-block;
        }

        .Pending {
            background: #f0ad4e;
        }

        .Processing {
            background: #5bc0de;
        }

        .Completed {
            background: #28a745;
        }

        .Cancelled {
            background: #dc3545;
        }

        button,
        select {
            padding: 6px 10px;
            border-radius: 6px;
        }

        button {
            cursor: pointer;
            background: #f59e0b;
            color: #111827;
            border: none;
            font-weight: 600;
        }

        .delete-btn {
            background: #dc3545;
            color: #fff;
        }

        img.food-img {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 5px;
            vertical-align: middle;
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
        <div>
            <a href="../../frontend/index.php">Back</a> |
            <a href="../auth/logout.php">Logout</a>
        </div>
    </header>

    <div class="admin-container">
        <aside class="sidebar">
            <ul>
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="customers.php" Customers</a></li>
                <li><a href="view_dishes.php">All Dishes</a></li>
                <li><a href="add_dish.php">Add Dish</a></li>
                <li><a href="edit_dishes.php">Manage Dish</a></li>
                <li><a href="orders.php" class="active">Orders</a></li>
                <li><a href="view_contacts.php">Feedback</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h2>📦 Orders</h2>

            <?php if ($message): ?>
                <div class="message"><?= htmlspecialchars($message) ?></div>
                <script>
                    setTimeout(() => {
                        document.querySelector('.message').style.display = 'none';
                    }, 2000);
                </script>
            <?php endif; ?>

            <?php if ($orders): ?>
                <table>
                    <tr>
                        <th>Customer Name</th>
                        <th>Customer Address</th>
                        <th>Phone Number</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Update</th>
                        <th>Food</th>
                        <th>Remove</th>
                    </tr>
                    <?php foreach ($orders as $order):
                        $items = json_decode($order['order_items'], true) ?: [];
                        $total_price = array_sum(array_column($items, 'subtotal'));
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($order['customer_name']) ?></td>
                            <td><?= nl2br(htmlspecialchars($order['customer_address'])) ?></td>
                            <td><?= htmlspecialchars($order['customer_phone']) ?></td>
                            <td>$<?= number_format($total_price, 2) ?></td>
                            <td><span class="status-badge <?= $order['status'] ?>"><?= $order['status'] ?></span></td>
                            <td>
                                <form method="post" style="display:flex; gap:5px; flex-wrap:wrap;">
                                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                    <select name="status">
                                        <?php foreach (['Pending', 'Processing', 'Completed', 'Cancelled'] as $s): ?>
                                            <option value="<?= $s ?>" <?= $order['status'] == $s ? 'selected' : '' ?>><?= $s ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="update_status">Update</button>
                                </form>
                            </td>
                            <td>
                                <?php if (!empty($items)): ?>
                                    <ul style="list-style:none; padding:0; margin:0;">
                                        <?php foreach ($items as $it): ?>
                                            <li style="margin-bottom:5px;">
                                                <img src="uploads/<?= htmlspecialchars($it['image']) ?>" class="food-img" alt="<?= htmlspecialchars($it['name']) ?>">
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <span>No items</span>
                                <?php endif; ?>
                            </td>
                            <!-- Remove order button -->
                            <td>
                                <form method="post" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                    <button type="submit" name="delete_order" class="delete-btn">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No orders found.</p>
            <?php endif; ?>

        </main>
    </div>

</body>

</html>