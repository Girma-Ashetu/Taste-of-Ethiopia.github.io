<?php
// ================= SESSION & AUTH CHECK =================
session_start();
require_once 'DB/db.php'; // getDB() function
require_once '../includes/auth.php';

// Only customers (and admins) can access menu
protectPage('customer', true); // true = show alert if guest

$db = getDB();
$message = "";

// ================= ADD TO CART =================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['menu_id'])) {
    $menu_id = (int)$_POST['menu_id'];
    $qty = max(1, (int)$_POST['quantity']);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $_SESSION['cart'][$menu_id] = ($_SESSION['cart'][$menu_id] ?? 0) + $qty;
    $message = "✅ Added to cart!";
}

// ================= FETCH DISHES =================
$stmt = $db->query("
    SELECT dish_id, name, description, price, image 
    FROM dishes 
    WHERE status = 'Available' 
    ORDER BY name ASC
");
$dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Menu | Taste of Ethiopia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../asset/css/web.css" />

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f4f4f4;
            margin: 0;
        }

        .menu-wrapper {
            max-width: 1200px;
            margin: auto;
            padding: 50px 20px;
        }

        .menu-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .menu-header h1 {
            font-size: 2.4rem;
            color: #111827;
        }

        .menu-header h1 span {
            background: linear-gradient(90deg, #f59e0b, #dc2626, #16a34a);
    
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        .menu-header p {
            color: #6b7280;
            max-width: 500px;
        }

        .cart-link {
            background: #f59e0b;
            color: #fff;
            padding: 12px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }

        .cart-link:hover {
            background: #d97706;
        }

        .message {
            background: #dcfce7;
            color: #166534;
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: inline-block;
            font-weight: 600;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 25px;
        }

        .menu-card {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            display: flex;
            flex-direction: column;
        }

        .menu-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        .menu-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .menu-content {
            padding: 18px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .menu-content h3 {
            margin: 0 0 8px;
            font-size: 1.25rem;
            color: #111827;
        }

        .menu-content p {
            font-size: 0.95rem;
            color: #6b7280;
            flex: 1;
        }

        .price {
            background: #111827;
            color: #fff;
            display: inline-block;
            padding: 6px 12px;
            border-radius: 999px;
            font-weight: 600;
            margin: 12px 0;
            width: fit-content;
        }

        .menu-card form {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .menu-card input[type="number"] {
            width: 65px;
            padding: 7px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            text-align: center;
        }

        .menu-card button {
            flex: 1;
            background: #3b82f6;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
        }

        .menu-card button:hover {
            background: #2563eb;
        }

        @media (max-width: 600px) {
            .menu-header h1 {
                font-size: 1.9rem;
            }
        }
    </style>
</head>

<body>

    <?php include 'header.php'; ?>

    <section class="menu-wrapper">
        <div class="menu-header">
            <div>
                <h1>🍽️ Taste of <span>Ethiopia</span> Menu</h1>
                <p>Discover authentic Ethiopian dishes prepared with tradition, culture, and love.</p>
            </div>
            <a href="cart.php" class="cart-link">🛒 View Cart</a>
        </div>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
            <script>
                setTimeout(() => {
                    document.querySelector('.message').style.display = 'none';
                }, 2000);
            </script>
        <?php endif; ?>

        <div class="menu-grid">
            <?php foreach ($dishes as $dish): ?>
                <div class="menu-card">
                    <img src="admin/uploads/<?= htmlspecialchars($dish['image'] ?? 'default.png') ?>" alt="">
                    <div class="menu-content">
                        <h3><?= htmlspecialchars($dish['name']) ?></h3>
                        <p><?= htmlspecialchars($dish['description']) ?></p>
                        <span class="price">$<?= number_format($dish['price'], 2) ?></span>

                        <form method="post">
                            <input type="hidden" name="menu_id" value="<?= $dish['dish_id'] ?>">
                            <input type="number" name="quantity" value="1" min="1">
                            <button type="submit">Add to Cart</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <script src="../asset/script/app.js" defer></script>
</body>

</html>