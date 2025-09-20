<?php
$page_title = "Home - E-Commerce Store";
require_once 'config/database.php';
require_once 'includes/header.php';

// Fetch products from database
$sql = "SELECT * FROM products WHERE stock_quantity > 0 ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="container">
    <h1>Welcome to E-Store</h1>
    <p>Discover our amazing collection of products at great prices!</p>
    
    <?php if (isset($_GET['message'])): ?>
        <div class="message message-success">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>
    
    <div class="product-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($product = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if ($product['image'] && $product['image'] != 'placeholder.jpg'): ?>
                            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <div style="color: #999; text-align: center;">
                                <div style="font-size: 48px;">📦</div>
                                <div>No Image Available</div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                        <p class="product-description">
                            <?php 
                            $description = $product['description'];
                            echo htmlspecialchars(strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description); 
                            ?>
                        </p>
                        
                        <div style="margin-top: 15px;">
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">
                                View Details
                            </a>
                            
                            <?php if (isLoggedIn()): ?>
                                <button class="btn btn-success add-to-cart-btn" 
                                        data-product-id="<?php echo $product['id']; ?>"
                                        style="margin-left: 10px;">
                                    Add to Cart
                                </button>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-success" style="margin-left: 10px;">
                                    Login to Buy
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <small style="color: #666; margin-top: 10px; display: block;">
                            Stock: <?php echo $product['stock_quantity']; ?> available
                        </small>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                <h3>No products available</h3>
                <p>Please check back later for new products!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'includes/footer.php';
$conn->close();
?>