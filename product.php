<?php
require_once 'config/database.php';
require_once 'config/session.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    header("Location: index.php");
    exit();
}

// Fetch product details
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$product = $result->fetch_assoc();
$page_title = $product['name'] . " - E-Commerce Store";

require_once 'includes/header.php';
?>

<div class="container">
    <div style="margin-bottom: 20px;">
        <a href="index.php" class="btn btn-primary">← Back to Products</a>
    </div>
    
    <div class="card" style="padding: 30px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
            <div class="product-image-large">
                <?php if ($product['image'] && $product['image'] != 'placeholder.jpg'): ?>
                    <img src="images/<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         style="width: 100%; max-width: 400px; border-radius: 8px; border: 1px solid #ddd;">
                <?php else: ?>
                    <div style="width: 100%; max-width: 400px; height: 300px; background-color: #f0f0f0; 
                                border-radius: 8px; display: flex; align-items: center; justify-content: center;
                                border: 1px solid #ddd; color: #666;">
                        <div style="text-align: center;">
                            <div style="font-size: 64px; margin-bottom: 10px;">📦</div>
                            <div>No Image Available</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="product-details">
                <h1 style="color: #2c3e50; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($product['name']); ?>
                </h1>
                
                <div class="product-price" style="font-size: 28px; color: #e74c3c; font-weight: bold; margin-bottom: 20px;">
                    $<?php echo number_format($product['price'], 2); ?>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <strong>Stock Available:</strong> 
                    <span style="color: <?php echo $product['stock_quantity'] > 0 ? '#27ae60' : '#e74c3c'; ?>">
                        <?php echo $product['stock_quantity']; ?> units
                    </span>
                </div>
                
                <div style="margin-bottom: 25px;">
                    <h3 style="margin-bottom: 10px;">Description</h3>
                    <p style="line-height: 1.6; color: #555;">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </p>
                </div>
                
                <?php if ($product['stock_quantity'] > 0): ?>
                    <?php if (isLoggedIn()): ?>
                        <div style="margin-bottom: 20px;">
                            <label for="quantity" style="display: block; margin-bottom: 5px; font-weight: bold;">
                                Quantity:
                            </label>
                            <select id="quantity" class="form-control" style="width: 100px; display: inline-block;">
                                <?php for ($i = 1; $i <= min(10, $product['stock_quantity']); $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <button class="btn btn-success btn-block add-to-cart-btn" 
                                data-product-id="<?php echo $product['id']; ?>"
                                onclick="addToCartWithQuantity(<?php echo $product['id']; ?>)"
                                style="font-size: 18px; padding: 15px;">
                            Add to Cart
                        </button>
                    <?php else: ?>
                        <div class="message" style="text-align: center; padding: 20px; background-color: #fff3cd; 
                                                   border: 1px solid #ffeaa7; color: #856404; border-radius: 4px;">
                            <p style="margin-bottom: 15px;">Please login to add items to your cart</p>
                            <a href="login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" 
                               class="btn btn-primary">Login</a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="message message-error" style="text-align: center;">
                        <strong>Out of Stock</strong><br>
                        This product is currently unavailable.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Related Products Section (Optional enhancement) -->
    <div style="margin-top: 40px;">
        <h2>Other Products You Might Like</h2>
        <div class="product-grid">
            <?php
            // Fetch 3 other random products
            $related_sql = "SELECT * FROM products WHERE id != ? AND stock_quantity > 0 ORDER BY RAND() LIMIT 3";
            $related_stmt = $conn->prepare($related_sql);
            $related_stmt->bind_param("i", $product_id);
            $related_stmt->execute();
            $related_result = $related_stmt->get_result();
            
            while ($related_product = $related_result->fetch_assoc()):
            ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if ($related_product['image'] && $related_product['image'] != 'placeholder.jpg'): ?>
                            <img src="images/<?php echo htmlspecialchars($related_product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($related_product['name']); ?>"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <div style="color: #999; text-align: center;">
                                <div style="font-size: 48px;">📦</div>
                                <div>No Image Available</div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($related_product['name']); ?></h3>
                        <p class="product-price">$<?php echo number_format($related_product['price'], 2); ?></p>
                        <a href="product.php?id=<?php echo $related_product['id']; ?>" class="btn btn-primary">
                            View Details
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<script>
function addToCartWithQuantity(productId) {
    const quantity = document.getElementById('quantity').value;
    addToCart(productId, quantity);
}
</script>

<style>
@media (max-width: 768px) {
    .card > div:first-child {
        grid-template-columns: 1fr !important;
        gap: 20px !important;
    }
}
</style>

<?php
require_once 'includes/footer.php';
$stmt->close();
$related_stmt->close();
$conn->close();
?>