<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}
include 'config.php';

// Handle search and status update
$searchOrderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : '';
$searchUserId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $orderId);
    $stmt->execute();
    $stmt->close();
    $message = "Order status updated.";
}

// Build query with filters
$query = "SELECT o.id, o.user_id, o.total, o.status, o.created_at, u.username FROM orders o JOIN users u ON o.user_id = u.id WHERE 1=1";
$params = [];
$types = '';

if (!empty($searchOrderId)) {
    $query .= " AND o.id = ?";
    $params[] = $searchOrderId;
    $types .= 'i';
}

if (!empty($searchUserId)) {
    $query .= " AND o.user_id = ?";
    $params[] = $searchUserId;
    $types .= 'i';
}

$query .= " ORDER BY o.created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$result->free();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin_orders.css">  <!-- New CSS file for admin orders -->
</head>
<body>
    <div class="container">
        <h1>Manage Orders</h1>
        <a href="index.php">Back to Dashboard</a>
        <?php if (isset($message)): ?>
            <div class="admin-message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <!-- Search Filters -->
        <div class="filters">
            <form method="GET" action="admin_orders.php" class="filter-form">
                <input type="number" name="order_id" placeholder="Order ID" value="<?php echo htmlspecialchars($searchOrderId); ?>">
                <input type="number" name="user_id" placeholder="User ID" value="<?php echo htmlspecialchars($searchUserId); ?>">
                <button type="submit">Search</button>
                <?php if (!empty($searchOrderId) || !empty($searchUserId)): ?>
                    <a href="admin_orders.php" class="clear-filter">Clear Filters</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Orders Table -->
        <div class="orders-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="no-orders">No orders found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo htmlspecialchars($order['username']); ?> (ID: <?php echo htmlspecialchars($order['user_id']); ?>)</td>
                                <td>$<?php echo number_format($order['total'], 2); ?></td>
                                <td><span class="status <?php echo strtolower($order['status']); ?>"><?php echo htmlspecialchars($order['status']); ?></span></td>
                                <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="status">
                                            <option value="pending" <?php if ($order['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                            <option value="completed" <?php if ($order['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                                            <option value="cancelled" <?php if ($order['status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" name="update_status" class="update-btn">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>