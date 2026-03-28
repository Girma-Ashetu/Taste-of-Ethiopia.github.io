<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['role']);
$role = $_SESSION['role'] ?? '';
?>
<style>
    /* LOGO */
    .logo {
        width: 120px;
        height: 120px;
        border-radius: 60px;
        margin-bottom: 20px;
    }

    h1 {
        padding-right: 40px;
    }

    /* HAMBURGER */
    #hamburger {
        display: none;
        cursor: pointer;
    }

    #hamburger .bar {
        width: 25px;
        height: 3px;
        background-color: black;
        margin: 5px 0;
    }

    /* MOBILE VIEW */
    @media (max-width: 768px) {
        #nav-menu {
            display: none;
            flex-direction: column;
        }

        #nav-menu.active {
            display: flex;
        }


        #hamburger {
            display: block;
        }
    }
</style>

<header class="main-header">
    <img class="logo" src="../asset/image/image.png" alt="Logo">

    <h1 class="brand-title">
        Taste of <span>Ethiopia</span>
    </h1>

    <!-- NAV -->
    <nav id="nav-menu">
        <a href="../frontend/index.php">Home</a>
        <a href="../frontend/about.php">About</a>
        <a href="../frontend/blog.php">Blog</a>
        <a href="../backend/dishes.php">Menu</a>
        <a href="../backend/contact.php">Contact</a>

        <a href="../backend/cart.php">
            🛒 Cart
            <span id="cart-count" class="cart-count">0</span>
        </a>

        <?php if ($isLoggedIn && $role === 'admin'): ?>
            <a href="../backend/admin/admin.php">Admin</a>
        <?php endif; ?>

        <?php if ($isLoggedIn): ?>
            <a href="../backend/auth/logout.php">Logout</a>
        <?php else: ?>
            <a href="../backend/auth/login.php">Login</a>
        <?php endif; ?>
    </nav>

    <!-- HAMBURGER -->
    <div id="hamburger">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </div>
</header>