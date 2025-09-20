<?php
include 'includes/header.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    
    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    switch ($action) {
        case 'add':
            if ($product_id > 0) {
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id] += $quantity;
                } else {
                    $_SESSION['cart'][$product_id] = $quantity;
                }
                echo json_encode(['success' => true]);
                exit();
            }
            break;
            
        case 'update':
            if ($product_id > 0 && $quantity > 0) {
                $_SESSION['cart'][$product_id] = $quantity;
                echo json_encode(['success' => true]);
                exit();
            }
            break;
            
        case 'remove':
            if ($product_id > 0) {
                unset($_SESSION['cart'][$product_id]);
                echo json_encode(['success' => true]);
                exit();
            }
            break;
    }
    
    echo json_encode(['success' => false]);
    exit();
}

// Handle count request
if (isset($_GET['action']) && $_GET['action'] == 'count') {
    echo json_encode(['count' => getCartItemsCount()]);
    exit();
}

// Get cart items
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

<div class="container">
    <h1 class="page-title">Shopping Cart</h1>
    
    <?php if (empty($cart_items)): ?>
        <div class="empty-state">
            <h3>Your cart is empty</h3>
            <p>Add some products to your cart to get started!</p>
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-table">
            <table>
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
                                    <div style="width: 60px; height: 60px; background: #f5f5f5; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
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
                                        <small style="color: #666;">Stock: <?php echo $item['product']['stock']; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo formatPrice($item['product']['price']); ?></td>
                            <td>
                                <div class="quantity-controls">
                                    <input type="number" 
                                           value="<?php echo $item['quantity']; ?>" 
                                           min="1" 
                                           max="<?php echo $item['product']['stock']; ?>"
                                           class="quantity-input"
                                           data-product-id="<?php echo $item['product']['id']; ?>"
                                           style="width: 70px; padding: 5px; text-align: center; border: 1px solid #ddd; border-radius: 4px;">
                                </div>
                            </td>
                            <td><strong><?php echo formatPrice($item['subtotal']); ?></strong></td>
                            <td>
                                <button class="btn btn-danger remove-btn" 
                                        data-product-id="<?php echo $item['product']['id']; ?>"
                                        style="padding: 5px 10px; font-size: 12px;">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="cart-total">
            <h3>Cart Total</h3>
            <div class="total-amount"><?php echo formatPrice($total); ?></div>
            <div style="margin-top: 20px; display: flex; gap: 15px; justify-content: flex-end;">
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                <?php if (isLoggedIn()): ?>
                    <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
                <?php else: ?>
                    <div>
                        <p style="margin-bottom: 10px; color: #666;">Please login to checkout</p>
                        <a href="login.php" class="btn btn-success">Login to Checkout</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>