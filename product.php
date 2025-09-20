<?php
include 'includes/header.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id === 0) {
    header('Location: index.php');
    exit();
}

$conn = getDBConnection();
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header('Location: index.php');
    exit();
}

$stmt->close();
$conn->close();
?>

<div class="container" style="max-width: 800px; margin: 2rem auto;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        
        <!-- Product Image -->
        <div class="product-image-large" style="height: 400px; background: #f5f5f5; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
            <?php if ($product['image']): ?>
                <img src="images/<?php echo $product['image']; ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                     style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px;">
            <?php else: ?>
                <span style="color: #666; font-size: 18px;">No Image Available</span>
            <?php endif; ?>
        </div>
        
        <!-- Product Details -->
        <div class="product-details">
            <h1 style="color: #2c3e50; margin-bottom: 15px; font-size: 28px;"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div class="product-price" style="font-size: 32px; font-weight: bold; color: #e74c3c; margin-bottom: 20px;">
                <?php echo formatPrice($product['price']); ?>
            </div>
            
            <div style="margin-bottom: 25px;">
                <h3 style="color: #2c3e50; margin-bottom: 10px;">Description</h3>
                <p style="line-height: 1.6; color: #666;">
                    <?php echo htmlspecialchars($product['description']); ?>
                </p>
            </div>
            
            <div style="margin-bottom: 25px;">
                <strong style="color: #2c3e50;">Stock Available: </strong>
                <span style="color: <?php echo $product['stock'] > 5 ? '#2ecc71' : '#e74c3c'; ?>;">
                    <?php echo $product['stock']; ?> units
                </span>
            </div>
            
            <?php if ($product['stock'] > 0): ?>
                <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div class="quantity-controls">
                        <label for="quantity" style="margin-right: 10px; color: #2c3e50; font-weight: bold;">Quantity:</label>
                        <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" 
                               style="width: 80px; text-align: center; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                </div>
                
                <div style="display: flex; gap: 15px;">
                    <button onclick="addToCart(<?php echo $product['id']; ?>, document.getElementById('quantity').value)" 
                            class="btn btn-success" style="padding: 15px 30px; font-size: 16px;">
                        Add to Cart
                    </button>
                    <a href="index.php" class="btn btn-primary" style="padding: 15px 30px; font-size: 16px; text-decoration: none;">
                        Continue Shopping
                    </a>
                </div>
            <?php else: ?>
                <div class="alert alert-error">
                    This product is currently out of stock.
                </div>
                <a href="index.php" class="btn btn-primary" style="padding: 15px 30px; font-size: 16px; text-decoration: none;">
                    Continue Shopping
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Back to Products Link -->
    <div style="text-align: center; margin-top: 30px;">
        <a href="index.php" style="color: #3498db; text-decoration: none; font-size: 16px;">
            ← Back to All Products
        </a>
    </div>
</div>

<style>
/* Responsive design for product page */
@media (max-width: 768px) {
    .container > div {
        grid-template-columns: 1fr !important;
        gap: 20px !important;
        padding: 20px !important;
    }
    
    .product-image-large {
        height: 250px !important;
    }
    
    .product-details h1 {
        font-size: 24px !important;
    }
    
    .product-price {
        font-size: 28px !important;
    }
}
</style>

<?php include 'includes/footer.php'; ?>