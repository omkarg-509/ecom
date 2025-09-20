<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple E-commerce</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <a href="index.php">E-Shop</a>
            </div>
            <div class="nav-menu">
                <a href="index.php">Home</a>
                <a href="cart.php">Cart (<?php echo getCartItemsCount(); ?>)</a>
                <?php if (isLoggedIn()): ?>
                    <span>Welcome, <?php echo sanitizeInput($_SESSION['username']); ?>!</span>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main>