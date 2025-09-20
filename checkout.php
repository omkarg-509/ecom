<?php
include 'includes/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

$error = '';
$success = '';

// Process order
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $total_amount = getCartTotal();
    
    if ($total_amount > 0) {
        $conn = getDBConnection();
        $conn->autocommit(false); // Start transaction
        
        try {
            // Insert order
            $sql = "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("id", $user_id, $total_amount);
            $stmt->execute();
            $order_id = $conn->insert_id;
            $stmt->close();
            
            // Insert order items
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                // Get product info
                $sql = "SELECT price FROM products WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $product = $result->fetch_assoc();
                $stmt->close();
                
                if ($product) {
                    $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $product['price']);
                    $stmt->execute();
                    $stmt->close();
                    
                    // Update product stock
                    $sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $quantity, $product_id);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            
            $conn->commit(); // Commit transaction
            
            // Clear cart
            unset($_SESSION['cart']);
            
            $success = "Order placed successfully! Order ID: #$order_id";
            
        } catch (Exception $e) {
            $conn->rollback(); // Rollback transaction
            $error = 'Error placing order. Please try again.';
        }
        
        $conn->close();
    }
}

// Get cart items for display
$cart_items = [];
$total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $conn = getDBConnection();
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
    
    $sql = "SELECT * FROM products WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
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
    
    $stmt->close();
    $conn->close();
}
?>

<div class="container" style="max-width: 800px;">
    <h1 class="page-title">Checkout</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
            <div style="margin-top: 15px;">
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        </div>
    <?php elseif (!empty($cart_items)): ?>
        <div style="background: white; border-radius: 8px; padding: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h2 style="color: #2c3e50; margin-bottom: 20px;">Order Summary</h2>
            
            <div class="cart-table" style="margin-bottom: 20px;">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div style="width: 50px; height: 50px; background: #f5f5f5; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                            <?php if ($item['product']['image']): ?>
                                                <img src="images/<?php echo $item['product']['image']; ?>" 
                                                     alt="<?php echo htmlspecialchars($item['product']['name']); ?>"
                                                     style="max-width: 100%; max-height: 100%; object-fit: cover; border-radius: 4px;">
                                            <?php else: ?>
                                                <span style="font-size: 10px; color: #666;">No Image</span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <h4 style="margin: 0; color: #2c3e50;"><?php echo htmlspecialchars($item['product']['name']); ?></h4>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo formatPrice($item['product']['price']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><strong><?php echo formatPrice($item['subtotal']); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="text-align: right; padding: 20px 0; border-top: 2px solid #eee;">
                <h3 style="color: #2c3e50; margin-bottom: 10px;">Total Amount: <span style="color: #e74c3c;"><?php echo formatPrice($total); ?></span></h3>
            </div>
            
            <div style="margin-top: 30px;">
                <h3 style="color: #2c3e50; margin-bottom: 15px;">Customer Information</h3>
                <?php $user = getCurrentUser(); ?>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 4px;">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>
            
            <div style="margin-top: 30px;">
                <h3 style="color: #2c3e50; margin-bottom: 15px;">Payment Information</h3>
                <div class="alert alert-info">
                    <strong>Note:</strong> This is a demo e-commerce site. No actual payment will be processed. 
                    Click "Place Order" to complete your order simulation.
                </div>
            </div>
            
            <div style="margin-top: 30px; display: flex; gap: 15px; justify-content: center;">
                <a href="cart.php" class="btn btn-primary">Back to Cart</a>
                <form method="POST" style="display: inline;">
                    <button type="submit" class="btn btn-success" style="padding: 12px 30px; font-size: 16px;">
                        Place Order
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>