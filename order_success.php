<?php
// order_success.php - Order confirmation with enhancements
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($orderId > 0) {
    // Fetch order details
    $stmt = $conn->prepare("SELECT id, total, status, created_at, payment_method FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $orderId, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $result->free();
    $stmt->close();
    
    if (!$order) {
        die("Order not found.");
    }
    
    // Fetch order items for summary
    $stmt = $conn->prepare("
        SELECT oi.quantity, oi.price, p.name 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?
    ");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $orderItems = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
    $stmt->close();
} else {
    die("Invalid order ID.");
}

// Function to format payment method
function formatPaymentMethod($method) {
    $methods = [
        'credit_card' => 'Credit Card',
        'paypal' => 'PayPal',
        'cod' => 'Cash On Delivery'
    ];
    return $methods[$method] ?? ucfirst(str_replace('_', ' ', $method));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success - Ecommerce Site</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/order_success.css">  <!-- New CSS file for order success -->
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="success-message">
            <div class="success-icon">✓</div>
            <h1>Order Placed Successfully!</h1>
            <p>Thank you for your purchase. Your order has been confirmed and is being processed.</p>
        </div>
        
        <div class="order-summary-card">
            <h2>Order Summary</h2>
            <div class="summary-details">
                <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id']); ?></p>
                <p><strong>Total Amount:</strong> <span class="total-price">$<?php echo number_format($order['total'], 2); ?></span></p>
                <p><strong>Payment Method:</strong> <?php echo htmlspecialchars(formatPaymentMethod($order['payment_method'])); ?></p>
                <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
                <p><strong>Status:</strong> <span class="status completed"><?php echo htmlspecialchars($order['status']); ?></span></p>
            </div>
            
            <h3>Items Ordered</h3>
            <ul class="order-items-list">
                <?php foreach ($orderItems as $item): ?>
                    <li><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>) - $<?php echo number_format($item['price'] * $item['quantity'], 2); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="next-steps">
            <p>You will receive an email confirmation shortly with tracking details.</p>
            <div class="action-buttons">
                <a href="orders.php" class="btn view-orders-btn">View My Orders</a>
                <a href="products.php" class="btn continue-shopping-btn">Continue Shopping</a>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>