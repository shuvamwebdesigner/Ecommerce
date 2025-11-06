<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}
include 'config.php';

// Fetch stats
$result = $conn->query("SELECT COUNT(*) FROM products");
$productCount = $result->fetch_row()[0];
$result->free();

$result = $conn->query("SELECT COUNT(*) FROM orders");
$orderCount = $result->fetch_row()[0];
$result->free();

$result = $conn->query("SELECT SUM(total) FROM orders WHERE status = 'completed'");
$totalSales = $result->fetch_row()[0] ?? 0;
$result->free();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="admin-dashboard">
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="admin_products.php">Manage Products</a> |
            <a href="admin_orders.php">Manage Orders</a> |
            <a href="admin_logout.php">Logout</a>
        </nav>
        
        <div class="stats">
            <div class="stat-card">
                <h3>Total Products</h3>
                <p><?php echo $productCount; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Orders</h3>
                <p><?php echo $orderCount; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Sales</h3>
                <p>$<?php echo number_format($totalSales, 2); ?></p>
            </div>
        </div>
    </div>
</body>
</html>
