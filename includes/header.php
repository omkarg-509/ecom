<?php
require_once 'config/session.php';
$current_user = getCurrentUser();
$cart_count = getCartCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'E-Commerce Store'; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-container">
                <a href="index.php" class="nav-brand">E-Store</a>
                
                <div class="nav-menu">
                    <a href="index.php" class="nav-link">Home</a>
                    
                    <?php if ($current_user): ?>
                        <a href="cart.php" class="nav-link">
                            Cart (<?php echo $cart_count; ?>)
                        </a>
                        <span class="nav-link">Hello, <?php echo htmlspecialchars($current_user['username']); ?></span>
                        <a href="logout.php" class="nav-link">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="nav-link">Login</a>
                        <a href="register.php" class="nav-link">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>
    
    <main class="main-content">