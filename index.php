<?php
include 'includes/header.php';

// Get all products from database
$conn = getDBConnection();
$sql = "SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="container">
    <h1 class="page-title">Welcome to E-Shop</h1>
    <p style="text-align: center; margin-bottom: 30px; color: #666;">
        Discover our amazing products at great prices!
    </p>
    
    <?php if ($result && $result->num_rows > 0): ?>
        <div class="products-grid">
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if ($product['image']): ?>
                            <img src="images/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <span>No Image Available</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                        <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                            <button onclick="addToCart(<?php echo $product['id']; ?>)" class="btn btn-success">Add to Cart</button>
                        </div>
                        <small style="color: #666; margin-top: 10px; display: block;">
                            Stock: <?php echo $product['stock']; ?> available
                        </small>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <h3>No Products Available</h3>
            <p>Check back later for new products!</p>
        </div>
    <?php endif; ?>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>