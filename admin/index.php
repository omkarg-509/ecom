<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Simple admin authentication (in a real app, this would be more secure)
if (!isLoggedIn() || $_SESSION['username'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$conn = getDBConnection();

// Get statistics
$sql = "SELECT COUNT(*) as total_products FROM products";
$products_count = $conn->query($sql)->fetch_assoc()['total_products'];

$sql = "SELECT COUNT(*) as total_users FROM users";
$users_count = $conn->query($sql)->fetch_assoc()['total_users'];

$sql = "SELECT COUNT(*) as total_orders FROM orders";
$orders_count = $conn->query($sql)->fetch_assoc()['total_orders'];

$sql = "SELECT SUM(total_amount) as total_revenue FROM orders WHERE status = 'pending'";
$result = $conn->query($sql);
$total_revenue = $result->fetch_assoc()['total_revenue'] ?? 0;

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - E-Shop</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-nav {
            background: #34495e;
            padding: 15px 0;
        }
        .admin-nav .nav-menu a {
            margin: 0 15px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #3498db;
        }
        .stat-label {
            color: #666;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <a href="../index.php">E-Shop Admin</a>
            </div>
            <div class="nav-menu">
                <a href="index.php">Dashboard</a>
                <a href="products.php">Products</a>
                <a href="orders.php">Orders</a>
                <a href="../index.php">View Site</a>
                <a href="../logout.php">Logout</a>
            </div>
        </nav>
    </header>

    <main>
        <h1 class="page-title">Admin Dashboard</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $products_count; ?></div>
                <div class="stat-label">Total Products</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $users_count; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $orders_count; ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo formatPrice($total_revenue); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h2 style="color: #2c3e50; margin-bottom: 20px;">Quick Actions</h2>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <a href="products.php?action=add" class="btn btn-success">Add New Product</a>
                <a href="products.php" class="btn btn-primary">Manage Products</a>
                <a href="orders.php" class="btn btn-primary">View Orders</a>
            </div>
        </div>
        
        <div class="alert alert-info" style="margin-top: 20px;">
            <strong>Admin Note:</strong> This is a simple admin panel. In a production environment, 
            you would implement proper admin authentication, role-based access control, and more advanced features.
        </div>
    </main>
    
    <footer>
        <div class="footer-content">
            <p>&copy; 2025 E-Shop Admin Panel</p>
        </div>
    </footer>
</body>
</html>