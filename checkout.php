<?php
// checkout.php - Checkout process with enhancements
session_start();
include 'config.php';
include 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch cart items and calculate total
$cartItems = [];
$total = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $productIds = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
    $stmt = $conn->prepare("SELECT id, name, price FROM products WHERE id IN ($placeholders)");
    $types = str_repeat('i', count($productIds));
    $stmt->bind_param($types, ...$productIds);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
    $stmt->close();
    
    foreach ($products as $product) {
        $id = $product['id'];
        $quantity = $_SESSION['cart'][$id];
        $subtotal = $product['price'] * $quantity;
        $total += $subtotal;
        $cartItems[] = [
            'id' => $id,
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
} else {
    header('Location: cart.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shippingAddress = trim($_POST['shipping_address']);
    $billingAddress = trim($_POST['billing_address']);
    $paymentMethod = $_POST['payment_method'];
    
    if (empty($shippingAddress) || empty($billingAddress) || empty($paymentMethod)) {
        $error = "Please fill in all required fields.";
    } else {
        $paymentSuccess = true;  // Simulate payment
        
        if ($paymentSuccess) {
            // Insert order
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status, payment_method) VALUES (?, ?, 'completed', ?)");
            $stmt->bind_param("ids", $_SESSION['user_id'], $total, $paymentMethod);
            $stmt->execute();
            $orderId = $stmt->insert_id;
            $stmt->close();
            
            // Insert order items
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cartItems as $item) {
                $stmt->bind_param("iiid", $orderId, $item['id'], $item['quantity'], $item['price']);
                $stmt->execute();
            }
            $stmt->close();
            
            unset($_SESSION['cart']);
            header('Location: order_success.php?order_id=' . $orderId);
            exit;
        } else {
            $error = "Payment failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Ecommerce Site</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/checkout.css">  <!-- New CSS file for checkout -->
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <h1>Checkout</h1>
        
        <div class="checkout-layout">
            <!-- Checkout Form -->
            <div class="checkout-form-section">
                <?php if (isset($error)): ?>
                    <div class="checkout-message error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="checkout.php" class="checkout-form">
                    <div class="form-section">
                        <h2>Shipping Information</h2>
                        <label for="shipping_address">Shipping Address:</label>
                        <textarea name="shipping_address" id="shipping_address" required><?php echo isset($_POST['shipping_address']) ? htmlspecialchars($_POST['shipping_address']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-section">
                        <h2>Billing Information</h2>
                        <label for="billing_address">Billing Address:</label>
                        <textarea name="billing_address" id="billing_address" required><?php echo isset($_POST['billing_address']) ? htmlspecialchars($_POST['billing_address']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-section">
                        <h2>Payment Method</h2>
                        <select name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="credit_card" <?php if (isset($_POST['payment_method']) && $_POST['payment_method'] == 'credit_card') echo 'selected'; ?>>Credit Card</option>
                            <option value="paypal" <?php if (isset($_POST['payment_method']) && $_POST['payment_method'] == 'paypal') echo 'selected'; ?>>PayPal</option>
                            <option value="cod" <?php if (isset($_POST['payment_method']) && $_POST['payment_method'] == 'cod') echo 'selected'; ?>>Cash On Delivery</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn complete-order-btn">Complete Order</button>
                </form>
            </div>
            
            <!-- Order Summary Sidebar -->
            <div class="order-summary-sidebar">
                <h2>Order Summary</h2>
                <div class="summary-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="summary-item">
                            <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                            <span>$<?php echo number_format($item['subtotal'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="summary-total">
                    <strong>Total: $<?php echo number_format($total, 2); ?></strong>
                </div>
                <a href="cart.php" class="edit-cart-link">Edit Cart</a>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>