<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
</head>

<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['customer_name']); ?> 👋</h1>

    <a href="logout.php">Logout</a>
</body>

</html>