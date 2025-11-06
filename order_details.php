<?php
// order_details.php - Displays detailed information for a specific order
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($orderId > 0) {
    // Fetch order details (updated to include payment_method)
    $stmt = $conn->prepare("SELECT id, total, status, created_at, payment_method FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $orderId, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $result->free();
    $stmt->close();
    
    if (!$order) {
        die("Order not found or access denied.");
    }
    
    // Fetch order items
    $stmt = $conn->prepare("
        SELECT oi.quantity, oi.price, p.name, p.image 
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
    <title>Order Details - Ecommerce Site</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/order_details.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <h1>Order Details</h1>
        
        <div class="order-summary-card">
            <h2>Order Summary</h2>
            <div class="summary-details">
                <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id']); ?></p>
                <p><strong>Total Amount:</strong> <span class="total-price">$<?php echo number_format($order['total'], 2); ?></span></p>
                <p><strong>Status:</strong> <span class="status <?php echo strtolower($order['status']); ?>"><?php echo htmlspecialchars($order['status']); ?></span></p>
                <p><strong>Payment Method:</strong> <?php echo htmlspecialchars(formatPaymentMethod($order['payment_method'])); ?></p>
                <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
            </div>
        </div>
        
        <div class="order-items-section">
            <h2>Items in Order</h2>
            <?php if (empty($orderItems)): ?>
                <p class="no-items">No items found for this order.</p>
            <?php else: ?>
                <div class="items-grid">
                    <?php foreach ($orderItems as $item): ?>
                        <div class="item-card">
                            <div class="item-image">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="admin/assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <?php else: ?>
                                    <img src="admin/assets/images/placeholder.jpg" alt="No Image">
                                <?php endif; ?>
                            </div>
                            <div class="item-info">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="quantity">Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                                <p class="price">Price: $<?php echo number_format($item['price'], 2); ?> each</p>
                                <p class="subtotal">Subtotal: $<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="actions">
            <a href="orders.php" class="btn back-btn">Back to My Orders</a>
            <a href="products.php" class="btn shop-btn">Continue Shopping</a>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
