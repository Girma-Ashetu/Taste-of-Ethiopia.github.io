<?php
session_start();
require_once 'DB/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: contact.php");
    exit;
}

// Get & sanitize input
$first_name = trim($_POST['first_name'] ?? '');
$last_name  = trim($_POST['last_name'] ?? '');
$email      = trim($_POST['email'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$subject    = trim($_POST['subject'] ?? '');
$message    = trim($_POST['message'] ?? '');

// Validate required fields
$errors = [];
if ($first_name === '') $errors[] = "First name is required.";
if ($last_name  === '') $errors[] = "Last name is required.";
if ($email      === '') $errors[] = "Email is required.";
if ($subject    === '') $errors[] = "Subject is required.";
if ($message    === '') $errors[] = "Message is required.";

// Validate email format
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

if (!empty($errors)) {
    $_SESSION['contact_errors'] = $errors;
    $_SESSION['contact_old'] = $_POST;
    header("Location: contact.php");
    exit;
}

try {
    $db = getDB();

    $stmt = $db->prepare(
        "INSERT INTO contacts (first_name, last_name, email, phone, subject, message)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->execute([
        $first_name,
        $last_name,
        $email,
        $phone,
        $subject,
        $message
    ]);

    $_SESSION['contact_success'] = "Thank you! Your message has been sent.";
    header("Location: contact.php");
    exit;
} catch (PDOException $e) {
    $_SESSION['contact_errors'] = ["Database Error: " . $e->getMessage()];
    $_SESSION['contact_old'] = $_POST;
    header("Location: contact.php");
    exit;
}
