<?php
session_start();

// Get old values if form submission failed
$old = $_SESSION['contact_old'] ?? [];
$errors = $_SESSION['contact_errors'] ?? [];
$success = $_SESSION['contact_success'] ?? '';

// Clear session messages
unset($_SESSION['contact_old'], $_SESSION['contact_errors'], $_SESSION['contact_success']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us | Taste of Ethiopia</title>
  <link rel="stylesheet" href="../asset/css/web.css" />
</head>

<body class="contact-page">
  <?php include '../backend/header.php'; ?>

  <section class="map-form-section">
    <div class="contact-form">
      <h2>Send Us a Message</h2>

      <!-- Show errors -->
      <?php if (!empty($errors)): ?>
        <div style="color:#dc2626; margin-bottom:15px;">
          <ul>
            <?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <!-- Show success -->
      <?php if ($success): ?>
        <p style="color: green; font-weight: bold; margin-bottom:15px;">
          <?= htmlspecialchars($success) ?>
        </p>
      <?php endif; ?>

      <form action="contact_submit.php" method="POST">
        <div class="form-row">
          <div class="form-group">
            <label>First Name *</label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" required>
          </div>

          <div class="form-group">
            <label>Last Name *</label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label>Email *</label>
          <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
        </div>

        <div class="form-group">
          <label>Phone</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label>Subject *</label>
          <input type="text" name="subject" value="<?= htmlspecialchars($old['subject'] ?? '') ?>" required>
        </div>

        <div class="form-group">
          <label>Message *</label>
          <textarea name="message" required><?= htmlspecialchars($old['message'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="submit-btn">Send Message</button>
      </form>
    </div>
  </section>


</body>

</html>