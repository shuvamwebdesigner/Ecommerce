<?php
// index.php - Homepage with enhancements
session_start();
include 'config.php';
include 'includes/functions.php';

// Fetch featured products
$result = $conn->query("SELECT id, name, price, image FROM products ORDER BY created_at DESC LIMIT 8");
$featuredProducts = $result->fetch_all(MYSQLI_ASSOC);
$result->free();

// Fetch stats for display
$result = $conn->query("SELECT COUNT(*) FROM products");
$totalProducts = $result->fetch_row()[0];
$result->free();

$result = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'completed'");
$totalOrders = $result->fetch_row()[0];
$result->free();

// Fetch categories
$result = $conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL LIMIT 4");
$categories = $result->fetch_all(MYSQLI_ASSOC);
$result->free();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce Site - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/index.css">  <!-- New CSS file for homepage -->
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <section class="hero">
            <div class="hero-content">
                <h1>Welcome to Our Ecommerce Store</h1>
                <p>Discover amazing products at great prices. Shop now and enjoy fast delivery!</p>
                <a href="products.php" class="btn hero-btn">Browse Products</a>
            </div>
            <div class="hero-image">
                <img src="assets/images/banner.png" alt="Shopping">  <!-- Add a hero image -->
            </div>
        </section>
        
        <section class="stats">
            <div class="stat-card">
                <h3><?php echo $totalProducts; ?></h3>
                <p>Products Available</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $totalOrders; ?></h3>
                <p>Orders Completed</p>
            </div>
            <div class="stat-card">
                <h3>100%</h3>
                <p>Customer Satisfaction</p>
            </div>
        </section>
        
        <section class="featured-products">
            <h2>Featured Products</h2>
            <div class="products-grid">
                <?php if (!empty($featuredProducts)): ?>
                    <?php foreach ($featuredProducts as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="admin/assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php else: ?>
                                    <img src="admin/assets/images/placeholder.jpg" alt="No Image">
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars(substr($product['name'], 0, 18)); ?>...</h3>
                                <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No products available at the moment.</p>
                <?php endif; ?>
            </div>
        </section>
        
        <section class="categories">
            <h2>Shop by Category</h2>
            <div class="categories-grid">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <div class="category-card">
                            <h3><?php echo htmlspecialchars($category['category']); ?></h3>
                            <a href="products.php?category=<?php echo urlencode($category['category']); ?>" class="btn">Explore</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No categories available.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
