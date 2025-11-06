<?php
// includes/header.php - More stylish header
// Assumes session is started in the including file

// Calculate cart item count for display
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $quantity) {
        $cartCount += $quantity;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Ecommerce Site'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header.css">  <!-- New CSS file for header -->
</head>
<body>
    <header class="site-header">
        <div class="header-overlay"></div>
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    <img src="assets/images/Logo.png" alt="Ecommerce Store Logo" class="logo-img">
                </a>
            </div>
            
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">Products</a></li>
                    <li><a href="cart.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : ''; ?>">Cart (<?php echo $cartCount; ?>)</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">My Orders</a></li>
                        <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>">Login</a></li>
                        <li><a href="register.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            
            <!-- Enhanced Search Form -->
            <form action="products.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" required>
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </header>
    
    <!-- Page content starts here -->