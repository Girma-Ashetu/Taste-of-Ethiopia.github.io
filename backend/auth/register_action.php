<?php
session_start();
require_once "../DB/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // 1️⃣ Validate input
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['register_error'] = "All fields are required!";
        header("Location: register.php");
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['register_error'] = "Passwords do not match!";
        header("Location: register.php");
        exit;
    }

    $db = getDB();

    // 2️⃣ Check if email already exists (CUSTOMERS only)
    $stmt = $db->prepare("SELECT id FROM customers WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $_SESSION['register_error'] = "Email already registered!";
        header("Location: register.php");
        exit;
    }

    // 3️⃣ Insert customer
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db->prepare(
        "INSERT INTO customers (name, email, password) VALUES (?, ?, ?)"
    );

    if ($stmt->execute([$name, $email, $hashed_password])) {
        $_SESSION['register_success'] = "Registration successful! Please login.";
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['register_error'] = "Registration failed. Try again.";
        header("Location: register.php");
        exit;
    }
} else {
    header("Location: register.php");
    exit;
}
