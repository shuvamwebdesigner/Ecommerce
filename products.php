<?php
// products.php - More stylish product listing
session_start();
include 'config.php';

// Handle search and category filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// Build query with filters
$query = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $query .= " AND name LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= 's';
}

if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= 's';
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$result->free();
$stmt->close();

// Optional: Fetch categories for filter dropdown (if you have a categories table)
$categories = [];
$result = $conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL");
if ($result) {
    $categories = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products - Ecommerce Site</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="products-container">
        <div class="products-bg">
            <div class="products-overlay"></div>
        </div>
        <div class="products-content">
            <div class="products-header">
                <h1>Our Products</h1>
                <p>Discover amazing products at great prices</p>
            </div>
            
            <!-- Search and Filter Form -->
            <div class="filters">
                <form method="GET" action="products.php" class="filter-form">
                    <div class="filter-group">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="filter-group">
                        <i class="fas fa-filter"></i>
                        <select name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['category']); ?>" <?php if ($category == $cat['category']) echo 'selected'; ?>><?php echo htmlspecialchars($cat['category']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="filter-btn">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </form>
            </div>
            
            <!-- Products Grid -->
            <div class="products-grid">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="admin/assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php else: ?>
                                    <img src="admin/assets/images/placeholder.jpg" alt="No Image">
                                <?php endif; ?>
                                <div class="product-overlay">
                                    <a href="product.php?id=<?php echo $product['id']; ?>" class="view-btn">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                </div>
                            </div>
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars(substr($product['name'], 0, 25)); ?>...</h3>
                                <p class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 70)); ?>...</p>
                                <div class="product-meta">
                                    <span class="price">$<?php echo number_format($product['price'], 2); ?></span>
                                    <span class="stock">Stock: <?php echo htmlspecialchars($product['stock']); ?></span>
                                </div>
                                <div class="product-actions">
                                    <button class="add-to-cart-btn" onclick="addToCart(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-products">
                        <i class="fas fa-search"></i>
                        <h3>No products found</h3>
                        <p>Try adjusting your search or filter criteria.</p>
                        <a href="products.php" class="reset-btn">Show All Products</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        function addToCart(productId) {
            // Simple add to cart logic (you can enhance this)
            alert('Product added to cart!');
            // Here you can make an AJAX call to add to cart
        }
    </script>
</body>
</html>
