<?php
$page_title = "Checkout - E-Commerce Store";
require_once 'config/database.php';
require_once 'config/session.php';

// Require login
requireLogin();

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$error = '';
$success = false;

// Process checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_address = trim($_POST['shipping_address']);
    $city = trim($_POST['city']);
    $postal_code = trim($_POST['postal_code']);
    $phone = trim($_POST['phone']);
    
    // Validation
    if (empty($shipping_address) || empty($city) || empty($postal_code) || empty($phone)) {
        $error = "All fields are required.";
    } else {
        // Calculate total and prepare order
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
                    'product_id' => $product['id'],
                    'quantity' => $quantity,
                    'price' => $product['price']
                ];
            }
        }
        
        // Create full shipping address
        $full_shipping_address = $shipping_address . "\n" . $city . ", " . $postal_code . "\nPhone: " . $phone;
        
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // Insert order
            $current_user = getCurrentUser();
            $order_sql = "INSERT INTO orders (user_id, total_amount, shipping_address) VALUES (?, ?, ?)";
            $order_stmt = $conn->prepare($order_sql);
            $order_stmt->bind_param("ids", $current_user['id'], $total, $full_shipping_address);
            $order_stmt->execute();
            
            $order_id = $conn->insert_id;
            
            // Insert order items
            $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $item_stmt = $conn->prepare($item_sql);
            
            foreach ($cart_items as $item) {
                $item_stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                $item_stmt->execute();
            }
            
            // Update product stock
            $stock_sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?";
            $stock_stmt = $conn->prepare($stock_sql);
            
            foreach ($cart_items as $item) {
                $stock_stmt->bind_param("ii", $item['quantity'], $item['product_id']);
                $stock_stmt->execute();
            }
            
            // Commit transaction
            $conn->commit();
            
            // Clear cart
            clearCart();
            
            $success = true;
            $order_number = $order_id;
            
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Order processing failed. Please try again.";
        }
    }
}

// Get cart items for display
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

require_once 'includes/header.php';
?>

<div class="container">
    <h1>Checkout</h1>
    
    <?php if ($success): ?>
        <div class="card" style="text-align: center; padding: 40px;">
            <div style="color: #27ae60; font-size: 64px; margin-bottom: 20px;">✓</div>
            <h2 style="color: #27ae60; margin-bottom: 15px;">Order Placed Successfully!</h2>
            <p style="font-size: 18px; margin-bottom: 10px;">
                <strong>Order Number: #<?php echo $order_number; ?></strong>
            </p>
            <p style="color: #666; margin-bottom: 30px;">
                Thank you for your purchase! We'll process your order soon.
            </p>
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="message message-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: 1fr 400px; gap: 30px;">
            <!-- Shipping Information Form -->
            <div class="card">
                <h3>Shipping Information</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="shipping_address" class="form-label">Street Address</label>
                        <textarea id="shipping_address" name="shipping_address" class="form-control" 
                                  rows="3" placeholder="Enter your full street address" required><?php echo htmlspecialchars($_POST['shipping_address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="city" class="form-label">City</label>
                        <input type="text" id="city" name="city" class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="postal_code" class="form-label">Postal Code</label>
                        <input type="text" id="postal_code" name="postal_code" class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['postal_code'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" 
                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn btn-success btn-block" style="font-size: 18px; padding: 15px;">
                            Place Order ($<?php echo number_format($total, 2); ?>)
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Order Summary -->
            <div>
                <div class="card">
                    <h3>Order Summary</h3>
                    
                    <div style="max-height: 300px; overflow-y: auto; margin-bottom: 20px;">
                        <?php foreach ($cart_items as $item): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; 
                                        padding: 10px 0; border-bottom: 1px solid #eee;">
                                <div>
                                    <strong style="font-size: 14px;">
                                        <?php echo htmlspecialchars($item['product']['name']); ?>
                                    </strong>
                                    <div style="color: #666; font-size: 12px;">
                                        Qty: <?php echo $item['quantity']; ?> × $<?php echo number_format($item['product']['price'], 2); ?>
                                    </div>
                                </div>
                                <div style="font-weight: bold;">
                                    $<?php echo number_format($item['subtotal'], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div style="border-top: 2px solid #ddd; padding-top: 15px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: bold;">
                            <span>Total:</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 20px; text-align: center;">
                    <a href="cart.php" class="btn btn-primary">← Back to Cart</a>
                </div>
                
                <!-- Payment Note -->
                <div class="card" style="margin-top: 20px; background-color: #f8f9fa;">
                    <h4 style="color: #495057;">Payment Information</h4>
                    <p style="color: #666; font-size: 14px; margin: 0;">
                        This is a demo e-commerce site. No actual payment will be processed. 
                        Your order will be recorded for demonstration purposes only.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
@media (max-width: 768px) {
    .container > div:not(.card) {
        grid-template-columns: 1fr !important;
        gap: 20px !important;
    }
}
</style>

<?php
require_once 'includes/footer.php';
$conn->close();
?>