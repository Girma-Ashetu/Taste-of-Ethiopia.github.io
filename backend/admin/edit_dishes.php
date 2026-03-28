<?php
require_once '../auth/check_admin.php';
require_once '../DB/db.php';

$db = getDB();
$admin_id = $_SESSION['admin_id']; // logged-in admin

// Fetch dishes only for this admin
$stmt = $db->prepare("SELECT name FROM dishes WHERE admin_id = ? ORDER BY name ASC");
$stmt->execute([$admin_id]);
$dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle update
if (isset($_POST['update_dish'])) {
    $old_name = $_POST['selected_dish'];
    $new_name = $_POST['name'];
    $category = $_POST['category'];
    $region = $_POST['region'];
    $description = $_POST['description']; // Added description
    $status = $_POST['status'];

    $image = $_POST['current_image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
        $image = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $image);
    }

    // Update query with description
    $stmt = $db->prepare("UPDATE dishes SET name=?, category=?, region=?, description=?, status=?, image=? WHERE name=? AND admin_id=?");
    $stmt->execute([$new_name, $category, $region, $description, $status, $image, $old_name, $admin_id]);

    // Redirect to scroll to updated dish
    header("Location: edit_dishes.php?updated=" . urlencode($new_name) . "#dish-" . urlencode($new_name));
    exit;
}

// Handle delete
if (isset($_POST['delete_dish'])) {
    $name_to_delete = $_POST['selected_dish'];
    $stmt = $db->prepare("DELETE FROM dishes WHERE name=? AND admin_id=?");
    $stmt->execute([$name_to_delete, $admin_id]);
    header("Location: edit_dishes.php?deleted=" . urlencode($name_to_delete));
    exit;
}

// Fetch selected dish details
$selected_dish = null;
if (isset($_POST['selected_dish'])) {
    $stmt = $db->prepare("SELECT * FROM dishes WHERE name=? AND admin_id=?");
    $stmt->execute([$_POST['selected_dish'], $admin_id]);
    $selected_dish = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Dishes | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #f9fafb;
        }

        a {
            text-decoration: none;
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
            margin: 0;
            font-size: 18px;
        }

        .admin-header a {
            color: #f9fafb;
            margin-left: 15px;
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
            padding: 0;
            margin: 0;
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

        .dish-form {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            max-width: 600px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .dish-form label {
            display: block;
            margin: 12px 0 6px;
            font-weight: 600;
            color: #374151;
        }

        .dish-form input[type="text"],
        .dish-form select,
        .dish-form textarea,
        .dish-form input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            font-size: 14px;
        }

        .dish-form button {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            margin-top: 10px;
        }

        .btn-update {
            background: #f59e0b;
            color: #111827;
            margin-right: 10px;
        }

        .btn-update:hover {
            background: #d97706;
            color: #fff;
        }

        .btn-delete {
            background: #dc2626;
            color: #fff;
        }

        .btn-delete:hover {
            background: #b91c1c;
        }

        .success {
            color: green;
            margin-bottom: 15px;
        }

        #images {
            display: block;
            max-width: 330px;
            margin-top: 10px;
            border-radius: 6px;
        }

        img#imagePreview {
            display: block;
            max-width: 330px;
            margin-top: 10px;
            border-radius: 6px;
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
                <li><a href="view_dishes.php">All Dishes</a></li>
                <li><a href="add_dish.php">Add Dish</a></li>
                <li><a href="edit_dishes.php" class="active">Manage Dishes</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="view_contacts.php">Feedback</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h2>Manage Dishes</h2>

            <?php if (isset($_GET['updated'])): ?>
                <p class="success"><?= htmlspecialchars($_GET['updated']) ?> updated successfully!</p>
            <?php elseif (isset($_GET['deleted'])): ?>
                <p class="success"><?= htmlspecialchars($_GET['deleted']) ?> deleted successfully!</p>
            <?php endif; ?>

            <!-- Select Dish -->
            <form method="POST">
                <label>Select Dish:</label>
                <select name="selected_dish" onchange="this.form.submit()" required>
                    <option value="">-- Select Dish --</option>
                    <?php foreach ($dishes as $dish): ?>
                        <option value="<?= htmlspecialchars($dish['name']) ?>" <?= (isset($_POST['selected_dish']) && $_POST['selected_dish'] == $dish['name']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dish['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <noscript><button type="submit">Select</button></noscript>
            </form>

            <?php if ($selected_dish): ?>
                <div id="dish-<?= htmlspecialchars($selected_dish['name']) ?>" class="dish-form">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="selected_dish" value="<?= htmlspecialchars($selected_dish['name']) ?>">
                        <input type="hidden" name="current_image" value="<?= $selected_dish['image'] ?>">

                        <label>Dish Name:</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($selected_dish['name']) ?>" required>

                        <label>Category:</label>
                        <input type="text" name="category" value="<?= htmlspecialchars($selected_dish['category']) ?>" required>

                        <label>Region:</label>
                        <input type="text" name="region" value="<?= htmlspecialchars($selected_dish['region']) ?>" required>

                        <label>Description:</label>
                        <textarea name="description" rows="4" required><?= htmlspecialchars($selected_dish['description']) ?></textarea>

                        <label>Status:</label>
                        <select name="status" required>
                            <option value="Available" <?= $selected_dish['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                            <option value="Unavailable" <?= $selected_dish['status'] == 'Unavailable' ? 'selected' : '' ?>>Unavailable</option>
                        </select>

                        <?php if ($selected_dish['image']): ?>
                            <label>Current Image:</label>
                            <img id="images" src="uploads/<?= htmlspecialchars($selected_dish['image']) ?>" alt="Dish Image">
                        <?php endif; ?>

                        <label>Change Image:</label>
                        <input type="file" name="image" id="imageInput" accept="image/*">
                        <img id="imagePreview" src="#" alt="Image Preview" style="display:none;">

                        <button type="submit" name="update_dish" class="btn-update">Update Dish</button>
                        <button type="submit" name="delete_dish" class="btn-delete" onclick="return confirm('Are you sure you want to delete this dish?')">Delete Dish</button>
                    </form>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        // Image preview
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        imageInput?.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function() {
                    imagePreview.src = reader.result;
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.display = 'none';
            }
        });
    </script>
</body>

</html>