<?php
// orders.php - Displays a user's past orders with enhancements
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle status filter
$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : '';

// Build query with filter
$query = "SELECT id, total, status, created_at FROM orders WHERE user_id = ?";
$params = [$_SESSION['user_id']];
$types = 'i';

if (!empty($statusFilter)) {
    $query .= " AND status = ?";
    $params[] = $statusFilter;
    $types .= 's';
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$result->free();
$stmt->close();

// Function to format status
function formatStatus($status) {
    $statuses = [
        'pending' => 'Pending',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled'
    ];
    return $statuses[$status] ?? ucfirst($status);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - Ecommerce Site</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/orders.css">  <!-- New CSS file for orders -->
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <h1>My Orders</h1>
        
        <!-- Filter Form -->
        <div class="filters">
            <form method="GET" action="orders.php" class="filter-form">
                <label for="status">Filter by Status:</label>
                <select name="status" id="status">
                    <option value="">All Orders</option>
                    <option value="pending" <?php if ($statusFilter == 'pending') echo 'selected'; ?>>Pending</option>
                    <option value="completed" <?php if ($statusFilter == 'completed') echo 'selected'; ?>>Completed</option>
                    <option value="cancelled" <?php if ($statusFilter == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                </select>
                <button type="submit">Filter</button>
                <?php if (!empty($statusFilter)): ?>
                    <a href="orders.php" class="clear-filter">Clear Filter</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Orders Grid -->
        <div class="orders-grid">
            <?php if (empty($orders)): ?>
                <div class="no-orders">
                    <p>You have no orders yet. <a href="products.php">Start shopping</a></p>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <h3>Order #<?php echo htmlspecialchars($order['id']); ?></h3>
                            <span class="status <?php echo strtolower($order['status']); ?>"><?php echo formatStatus($order['status']); ?></span>
                        </div>
                        <div class="order-details">
                            <p><strong>Total:</strong> <span class="total">$<?php echo number_format($order['total'], 2); ?></span></p>
                            <p><strong>Date:</strong> <?php echo htmlspecialchars(date('M d, Y', strtotime($order['created_at']))); ?></p>
                        </div>
                        <div class="order-actions">
                            <a href="order_details.php?order_id=<?php echo $order['id']; ?>" class="btn view-btn">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <a href="index.php" class="back-link">Back to Home</a>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>