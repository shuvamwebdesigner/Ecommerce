<?php
// product.php - More stylish product detail
session_start();
include 'config.php';
include 'includes/functions.php';

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId > 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $result->free();
    $stmt->close();
    
    if (!$product) {
        die("Product not found.");
    }
    
    // Fetch product images
    $stmt = $conn->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $productImages = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
    $stmt->close();
    
    // If no images in gallery, use the main product image
    if (empty($productImages) && !empty($product['image'])) {
        $productImages[] = ['image_path' => $product['image'], 'alt_text' => $product['name']];
    }
    
    // Fetch related products
    $relatedProducts = [];
    if (!empty($product['category'])) {
        $stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE category = ? AND id != ? LIMIT 4");
        $stmt->bind_param("si", $product['category'], $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $relatedProducts = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();
        $stmt->close();
    }
} else {
    die("Invalid product ID.");
}

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $quantity = (int)$_POST['quantity'];
    if ($quantity > 0 && $quantity <= $product['stock']) {
        addToCart($productId, $quantity);
        $message = "Product added to cart!";
    } else {
        $error = "Invalid quantity or out of stock.";
    }
}

// Handle add to wishlist
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_wishlist'])) {
    if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];
    if (!in_array($productId, $_SESSION['wishlist'])) {
        $_SESSION['wishlist'][] = $productId;
        $message = "Added to wishlist!";
    } else {
        $error = "Already in wishlist.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?> - Ecommerce Site</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/product.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="product-container">
        <div class="product-bg">
            <div class="product-overlay"></div>
        </div>
        <div class="product-content">
            <div class="product-detail">
                <div class="product-gallery">
                    <div class="main-image">
                        <img src="admin/assets/images/<?php echo htmlspecialchars($productImages[0]['image_path']); ?>" alt="<?php echo htmlspecialchars($productImages[0]['alt_text']); ?>" id="main-img">
                    </div>
                    <?php if (count($productImages) > 1): ?>
                    <div class="thumbnail-gallery">
                        <?php foreach ($productImages as $index => $image): ?>
                            <img src="admin/assets/images/<?php echo htmlspecialchars($image['image_path']); ?>" alt="<?php echo htmlspecialchars($image['alt_text']); ?>" class="thumbnail" onclick="changeImage('<?php echo htmlspecialchars($image['image_path']); ?>', '<?php echo htmlspecialchars($image['alt_text']); ?>')">
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="product-info">
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    <div class="product-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                        <span>(4.5)</span>
                    </div>
                    <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                    <p class="stock"><i class="fas fa-check-circle"></i> In Stock: <?php echo htmlspecialchars($product['stock']); ?></p>
                    
                    <?php if (isset($message)): ?>
                        <div class="product-message success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($error)): ?>
                        <div class="product-message error">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="product.php?id=<?php echo $productId; ?>" class="add-to-cart-form">
                        <div class="quantity-group">
                            <label for="quantity"><i class="fas fa-minus"></i></label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" required>
                            <label for="quantity"><i class="fas fa-plus"></i></label>
                        </div>
                        <button type="submit" name="add_to_cart" class="btn add-cart-btn">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                        <button type="submit" name="add_to_wishlist" class="btn wishlist-btn">
                            <i class="fas fa-heart"></i> Add to Wishlist
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="product-description">
                <h2><i class="fas fa-info-circle"></i> Description</h2>
                <ul>
                    <?php
                    $descriptionLines = explode("\n", trim($product['description']));
                    foreach ($descriptionLines as $line) {
                        $line = trim($line);
                        if (!empty($line)) {
                            echo '<li>' . htmlspecialchars($line) . '</li>';
                        }
                    }
                    ?>
                </ul>
            </div>
            
            <?php if (!empty($relatedProducts)): ?>
                <div class="related-products">
                    <h2><i class="fas fa-th-large"></i> Related Products</h2>
                    <div class="related-grid">
                        <?php foreach ($relatedProducts as $related): ?>
                            <div class="related-card">
                                <div class="related-image">
                                    <?php if (!empty($related['image'])): ?>
                                        <img src="admin/assets/images/<?php echo htmlspecialchars($related['image']); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>">
                                    <?php else: ?>
                                        <img src="admin/assets/images/placeholder.jpg" alt="No Image">
                                    <?php endif; ?>
                                </div>
                                <div class="related-info">
                                    <h4><?php echo htmlspecialchars($related['name']); ?></h4>
                                    <div class="product-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fa-regular fa-star"></i>
                                        <span>(4)</span>
                                    </div>
                                    <p class="price">$<?php echo number_format($related['price'], 2); ?></p>
                                    <a href="product.php?id=<?php echo $related['id']; ?>" class="btn">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="back-link">
                <a href="products.php"><i class="fas fa-arrow-left"></i> Back to Products</a>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        function changeImage(imagePath, altText) {
            document.getElementById('main-img').src = 'admin/assets/images/' + imagePath;
            document.getElementById('main-img').alt = altText;
        }
    </script>
</body>
</html>