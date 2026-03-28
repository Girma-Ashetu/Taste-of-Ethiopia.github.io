<?php
session_start();
require_once 'DB/db.php'; // getDB() function
require_once '../includes/auth.php';

// Only customers (and admins) can access menu/cart
protectPage('customer', true); // true = show alert if guest
$db = getDB();
$message = "";

// --- Handle quantity update ---
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $dish_id => $qty) {
        $qty = max(1, (int)$qty);
        if (isset($_SESSION['cart'][$dish_id])) {
            $_SESSION['cart'][$dish_id] = $qty;
        }
    }
    $message = "✅ Cart updated successfully!";
}

// --- Handle remove item ---
if (isset($_POST['remove_item'])) {
    $dish_id = (int)$_POST['dish_id'];
    if (isset($_SESSION['cart'][$dish_id])) {
        unset($_SESSION['cart'][$dish_id]);
        $message = "❌ Item removed from cart.";
    }
}

// --- Handle checkout ---
if (isset($_POST['checkout'])) {
    if (empty($_SESSION['cart'])) {
        $message = "❌ Your cart is empty!";
    } else {
        // Customer info
        $customer_name = trim($_POST['customer_name']);
        $customer_email = trim($_POST['customer_email']);
        $customer_phone = trim($_POST['customer_phone']);
        $customer_address = trim($_POST['customer_address']);

        // Build full cart details (with image, name, price, quantity, subtotal)
        $full_cart = [];
        $ids = implode(',', array_keys($_SESSION['cart']));
        $stmt = $db->query("SELECT dish_id, name, price, image FROM dishes WHERE dish_id IN ($ids)");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dish_id = $row['dish_id'];
            $full_cart[] = [
                'dish_id' => $dish_id,
                'name' => $row['name'],
                'price' => $row['price'],
                'image' => $row['image'],
                'quantity' => $_SESSION['cart'][$dish_id],
                'subtotal' => $row['price'] * $_SESSION['cart'][$dish_id]
            ];
        }

        // Convert to JSON to store in orderfood table
        $order_items = json_encode($full_cart);

        // Optional: assign admin_id (first admin)
        $stmt = $db->query("SELECT admin_id FROM admins LIMIT 1");
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        $admin_id = $admin ? $admin['admin_id'] : null;

        // Insert order into database
        $stmt = $db->prepare("
            INSERT INTO orderfood
            (admin_id, customer_name, customer_email, customer_phone, customer_address, order_items, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())
        ");
        if ($stmt->execute([$admin_id, $customer_name, $customer_email, $customer_phone, $customer_address, $order_items])) {
            $message = "✅ Your order has been placed!";
            unset($_SESSION['cart']); // clear cart
        } else {
            $message = "❌ Failed to place order. Try again.";
        }
    }
}

// --- Fetch cart items for display ---
$cart_items = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $db->query("SELECT dish_id, name, price, image FROM dishes WHERE dish_id IN ($ids)");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row['quantity'] = $_SESSION['cart'][$row['dish_id']];
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $total += $row['subtotal'];
        $cart_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart | Taste of Ethiopia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            background: #f4f4f4;
        }

        .header {
            background: #1f2933;
            color: #fff;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header a {
            text-decoration: none;
            color: #fff;
            background: #3b82f6;
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 600;
        }

        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .message {
            margin: 15px 0;
            padding: 10px 15px;
            background: #d4edda;
            color: #155724;
            border-radius: 6px;
            font-weight: 600;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .cart-table th,
        .cart-table td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
        }

        .cart-table th {
            background: #3b82f6;
            color: #fff;
        }

        .cart-table img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }

        .quantity-input {
            width: 60px;
            padding: 5px;
            text-align: center;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }

        .update-btn {
            background: #4CAF50;
            color: #fff;
        }

        .update-btn:hover {
            background: #218838;
        }

        .remove-btn {
            background: #dc2626;
            color: #fff;
        }

        .remove-btn:hover {
            background: #c82333;
        }

        .checkout-section {
            margin-top: 30px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }

        .checkout-form,
        .summary {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            flex: 1 1 300px;
        }

        .checkout-form input,
        .checkout-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .checkout-btn {
            background: #f59e0b;
            color: #111827;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            margin-top: 10px;
            cursor: pointer;
        }

        .checkout-btn:hover {
            background: #d97706;
        }

        .total-label {
            font-weight: 600;
            font-size: 18px;
        }

        @media screen and (max-width:768px) {

            .cart-table th,
            .cart-table td {
                font-size: 14px;
                padding: 10px;
            }

            .checkout-section {
                flex-direction: column;
            }
        }
    </style>
    <link rel="stylesheet" href="../asset/css/web.css" />
</head>
<header>
    <?php include 'header.php'; ?>
</header>

<body>

    <div class="header">
        <h2>🛒 Your Cart</h2>
        <a href="dishes.php">⬅️ Back to Menu</a>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if (!empty($cart_items)) { ?>
            <form method="post">
                <table class="cart-table">
                    <tr>
                        <th>Dish</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($cart_items as $item) { ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><img src="admin/uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>"></td>
                            <td>$<?= number_format($item['price'], 2) ?></td>
                            <td><input type="number" name="quantities[<?= $item['dish_id'] ?>]" value="<?= $item['quantity'] ?>" min="1" class="quantity-input"></td>
                            <td>$<?= number_format($item['subtotal'], 2) ?></td>
                            <td>
                                <button type="submit" name="remove_item" class="btn remove-btn">Remove</button>
                                <input type="hidden" name="dish_id" value="<?= $item['dish_id'] ?>">
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="4" class="total-label" style="text-align:right;">Total:</td>
                        <td colspan="2" class="total-label">$<?= number_format($total, 2) ?></td>
                    </tr>
                </table>
                <br>
                <button type="submit" name="update_cart" class="btn update-btn">Update Cart</button>
            </form>

            <div class="checkout-section">
                <div class="summary">
                    <h3>Order Summary</h3>
                    <p>Total Items: <?= count($cart_items) ?></p>
                    <p class="total-label">Total: $<?= number_format($total, 2) ?></p>
                </div>

                <div class="checkout-form">
                    <h3>Complete Your Order</h3>
                    <form method="post">
                        <input type="text" name="customer_name" placeholder="Your Name" required>
                        <input type="email" name="customer_email" placeholder="Your Email" required>
                        <input type="text" name="customer_phone" placeholder="Phone Number" required>
                        <textarea name="customer_address" placeholder="Delivery Address" required></textarea>
                        <button type="submit" name="checkout" class="checkout-btn">Submit Order</button>
                    </form>
                </div>
            </div>

        <?php } else { ?>
            <p>Your cart is empty. 🛒</p>
        <?php } ?>
    </div>

</body>

</html>