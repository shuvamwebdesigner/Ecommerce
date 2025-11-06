<?php
// cart.php - Shopping cart with enhancements
session_start();
include 'config.php';
include 'includes/functions.php';

// Handle updates, remove, and clear cart
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_cart'])) {
        $productId = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        if ($quantity > 0) {
            $_SESSION['cart'][$productId] = $quantity;
            $message = "Cart updated!";
        } else {
            unset($_SESSION['cart'][$productId]);
            $message = "Item removed from cart!";
        }
    } elseif (isset($_POST['remove_item'])) {
        $productId = (int)$_POST['product_id'];
        unset($_SESSION['cart'][$productId]);
        $message = "Item removed from cart!";
    } elseif (isset($_POST['clear_cart'])) {
        unset($_SESSION['cart']);
        $message = "Cart cleared!";
    }
}

// Fetch cart items
$cartItems = [];
$total = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $productIds = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
    $stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE id IN ($placeholders)");
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
            'image' => $product['image'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart - Ecommerce Site</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/cart.css">  <!-- New CSS file for cart -->
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h1>Your Shopping Cart</h1>
        <?php if (isset($message)): ?>
            <div class="cart-message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if (empty($cartItems)): ?>
            <div class="empty-cart">
                <p>Your cart is empty. <a href="products.php">Continue shopping</a></p>
            </div>
        <?php else: ?>
            <div class="cart-actions">
                <form method="POST" style="display:inline;">
                    <button type="submit" name="clear_cart" class="btn clear-btn">Clear Cart</button>
                </form>
            </div>
            
            <div class="cart-items">
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">
                        <div class="item-image">
                            <?php if (!empty($item['image'])): ?>
                                <img src="admin/assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <?php else: ?>
                                <img src="admin/assets/images/placeholder.jpg" alt="No Image">
                            <?php endif; ?>
                        </div>
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="price">$<?php echo number_format($item['price'], 2); ?> each</p>
                            <form method="POST" class="quantity-form">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <label>Quantity:</label>
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" required>
                                <button type="submit" name="update_cart" class="btn update-btn">Update</button>
                            </form>
                            <p class="subtotal">Subtotal: $<?php echo number_format($item['subtotal'], 2); ?></p>
                        </div>
                        <div class="item-actions">
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="remove_item" class="btn remove-btn">Remove</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-total">
                <h3>Total: $<?php echo number_format($total, 2); ?></h3>
                <a href="checkout.php" class="btn checkout-btn">Proceed to Checkout</a>
            </div>
        <?php endif; ?>
        
        <a href="products.php" class="continue-shopping">Continue Shopping</a>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>