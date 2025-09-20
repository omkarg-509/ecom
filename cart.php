<?php
$page_title = "Shopping Cart - E-Commerce Store";
require_once 'config/database.php';
require_once 'config/session.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    switch ($action) {
        case 'add':
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            addToCart($product_id, $quantity);
            echo json_encode([
                'success' => true,
                'message' => 'Item added to cart',
                'cart_count' => getCartCount()
            ]);
            break;
            
        case 'remove':
            removeFromCart($product_id);
            echo json_encode([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart_count' => getCartCount()
            ]);
            break;
            
        case 'update':
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            updateCartQuantity($product_id, $quantity);
            
            // Calculate new total
            $total = 0;
            if (!empty($_SESSION['cart'])) {
                $product_ids = implode(',', array_keys($_SESSION['cart']));
                $sql = "SELECT id, price FROM products WHERE id IN ($product_ids)";
                $result = $conn->query($sql);
                
                while ($product = $result->fetch_assoc()) {
                    $total += $product['price'] * $_SESSION['cart'][$product['id']];
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Cart updated',
                'cart_count' => getCartCount(),
                'cart_total' => $total
            ]);
            break;
    }
    exit();
}

// Regular page load - display cart
require_once 'includes/header.php';

$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = implode(',', array_keys($_SESSION['cart']));
    $sql = "SELECT * FROM products WHERE id IN ($product_ids)";
    $result = $conn->query($sql);
    
    while ($product = $result->fetch_assoc()) {
        $quantity = $_SESSION['cart'][$product['id']];
        $subtotal = $product['price'] * $quantity;
        $total += $subtotal;
        
        $cart_items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}
?>

<div class="container">
    <h1>Shopping Cart</h1>
    
    <?php if (empty($cart_items)): ?>
        <div class="card" style="text-align: center; padding: 40px;">
            <h3>Your cart is empty</h3>
            <p>Add some products to your cart to see them here.</p>
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="card">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div style="width: 60px; height: 60px; background-color: #f0f0f0; 
                                                border-radius: 4px; display: flex; align-items: center; 
                                                justify-content: center; color: #666;">
                                        <?php if ($item['product']['image'] && $item['product']['image'] != 'placeholder.jpg'): ?>
                                            <img src="images/<?php echo htmlspecialchars($item['product']['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['product']['name']); ?>"
                                                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            📦
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($item['product']['name']); ?></strong>
                                        <br>
                                        <small style="color: #666;">
                                            <?php echo htmlspecialchars(substr($item['product']['description'], 0, 50)) . '...'; ?>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>$<?php echo number_format($item['product']['price'], 2); ?></td>
                            <td>
                                <input type="number" min="1" max="<?php echo $item['product']['stock_quantity']; ?>"
                                       value="<?php echo $item['quantity']; ?>" 
                                       class="quantity-input"
                                       data-product-id="<?php echo $item['product']['id']; ?>"
                                       style="width: 70px; padding: 5px; text-align: center;">
                            </td>
                            <td><strong>$<?php echo number_format($item['subtotal'], 2); ?></strong></td>
                            <td>
                                <button class="btn btn-danger remove-from-cart-btn" 
                                        data-product-id="<?php echo $item['product']['id']; ?>">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="cart-total">
                <h3>Total: $<span class="cart-total-amount"><?php echo number_format($total, 2); ?></span></h3>
            </div>
            
            <div style="margin-top: 20px; text-align: right;">
                <a href="index.php" class="btn btn-primary" style="margin-right: 15px;">
                    Continue Shopping
                </a>
                
                <?php if (isLoggedIn()): ?>
                    <a href="checkout.php" class="btn btn-success">
                        Proceed to Checkout
                    </a>
                <?php else: ?>
                    <a href="login.php?redirect=checkout.php" class="btn btn-success">
                        Login to Checkout
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Order Summary Card -->
        <div class="card" style="margin-top: 20px;">
            <h3>Order Summary</h3>
            <div style="border-top: 1px solid #ddd; padding-top: 15px; margin-top: 15px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Items (<?php echo getCartCount(); ?>):</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Shipping:</span>
                    <span>Free</span>
                </div>
                <hr>
                <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: bold;">
                    <span>Total:</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Override the default quantity update function for cart page
document.addEventListener('DOMContentLoaded', function() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            const quantity = parseInt(this.value);
            const row = this.closest('tr');
            const priceCell = row.cells[1];
            const subtotalCell = row.cells[3];
            
            if (quantity > 0) {
                // Update quantity via AJAX
                fetch('cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=update&product_id=${productId}&quantity=${quantity}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update subtotal
                        const price = parseFloat(priceCell.textContent.replace('$', ''));
                        const newSubtotal = price * quantity;
                        subtotalCell.innerHTML = '<strong>$' + newSubtotal.toFixed(2) + '</strong>';
                        
                        // Update total
                        document.querySelector('.cart-total-amount').textContent = data.cart_total.toFixed(2);
                        
                        // Update cart count in navigation
                        updateCartCount(data.cart_count);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Reset to original value on error
                    this.value = this.getAttribute('data-original-value') || 1;
                });
            } else {
                this.value = 1;
            }
        });
        
        // Store original value
        input.setAttribute('data-original-value', input.value);
    });
});
</script>

<?php
require_once 'includes/footer.php';
$conn->close();
?>