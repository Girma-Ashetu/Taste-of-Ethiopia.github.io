<?php
require_once '../auth/check_admin.php';
require_once '../DB/db.php';

$db = getDB();
$admin_id = $_SESSION['admin_id'];
$message = '';
$error = '';

if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $category = trim($_POST['category']);
    $region = trim($_POST['region']);
    $status = $_POST['status'];

    if (!$name || !$price || !$category) {
        $error = "Please fill required fields!";
    } else {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $image = '';
        if (!empty($_FILES['image']['name'])) {
            $imageName = time() . '_' . basename($_FILES['image']['name']);
            $imagePath = $uploadDir . $imageName;
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $error = "Invalid image format!";
            } elseif (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                $error = "Image upload failed!";
            } else $image = $imageName;
        }

        if (!$error) {
            $stmt = $db->prepare("
                INSERT INTO dishes 
                (admin_id,name,description,price,category,region,status,image)
                VALUES (?,?,?,?,?,?,?,?)
            ");
            $stmt->execute([$admin_id, $name, $description, $price, $category, $region, $status, $image]);
            $message = "✅ Dish added successfully!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Dish | Admin</title>
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

        .message {
            background: #d4edda;
            color: #155724;
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
        }

        .dish-form {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            max-width: 600px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .dish-form label {
            display: block;
            margin: 12px 0 6px;
            font-weight: 600;
            color: #374151;
        }

        .dish-form input[type="text"],
        .dish-form input[type="number"],
        .dish-form textarea,
        .dish-form select,
        .dish-form input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            font-size: 14px;
        }

        .dish-form textarea {
            resize: vertical;
        }

        .btn-submit {
            background: #f59e0b;
            color: #111827;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            margin-top: 15px;
        }

        .btn-submit:hover {
            background: #d97706;
            color: #fff;
        }

        #imagePreview {
            display: block;
            max-width: 200px;
            margin-top: 10px;
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

            .sidebar-toggle {
                display: block;
            }

            .main-content {
                padding: 15px;
            }

            .dish-form {
                width: 95%;
                padding: 15px;
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
                <li><a href="add_dish.php" class="active">Add Dish</a></li>
                <li><a href="edit_dishes.php">Manage Dish</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="view_contacts.php">Feedback</a></li>
            </ul>
        </aside>
        <main class="main-content">

            <?php if ($message): ?>
                <div class="message"><?= htmlspecialchars($message) ?></div>
                <script>
                    setTimeout(() => {
                        document.querySelector('.message').style.display = 'none';
                    }, 2000);
                </script>
            <?php elseif ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
                <script>
                    setTimeout(() => {
                        document.querySelector('.error').style.display = 'none';
                    }, 2000);
                </script>
            <?php endif; ?>

            <h2>➕ Add New Dish</h2>
            <form action="add_dish.php" method="POST" enctype="multipart/form-data" class="dish-form">
                <label>Dish Name:</label>
                <input type="text" name="name" required>

                <label>Description:</label>
                <textarea name="description" required></textarea>

                <label>Price:</label>
                <input type="number" name="price" step="0.01" required>

                <label>Category:</label>
                <input type="text" name="category" required>

                <label>Region:</label>
                <input type="text" name="region" required>

                <label>Status:</label>
                <select name="status" required>
                    <option value="Available">Available</option>
                    <option value="Unavailable">Unavailable</option>
                </select>

                <label>Image:</label>
                <input type="file" name="image" accept="image/*" id="imageInput">
                <img id="imagePreview" src="#" alt="Image Preview">

                <input type="submit" name="submit" value="Add Dish" class="btn-submit">
            </form>
        </main>
    </div>

    <script>
        const toggleBtn = document.querySelector('.sidebar-toggle');
        const sidebar = document.querySelector('.sidebar');
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.addEventListener('load', function() {
                    imagePreview.setAttribute('src', this.result);
                    imagePreview.style.display = 'block';
                });
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.display = 'none';
            }
        });
    </script>
</body>

</html>