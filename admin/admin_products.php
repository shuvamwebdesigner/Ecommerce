<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}
include 'config.php';

// Define search variables at the top
$searchName = isset($_GET['name']) ? trim($_GET['name']) : '';
$searchCategory = isset($_GET['category']) ? trim($_GET['category']) : '';


// Handle add/edit/delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $category = trim($_POST['category']);
        $imagePath = '';
        
        // Handle main image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'assets/images/';
            $fileName = basename($_FILES['image']['name']);
            $fileTmp = $_FILES['image']['tmp_name'];
            $fileSize = $_FILES['image']['size'];
            $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            $maxSize = 2 * 1024 * 1024;
            
            if (in_array($fileType, $allowedTypes) && $fileSize <= $maxSize) {
                $newFileName = time() . '_' . rand(1000, 9999) . '.' . $fileType;
                $targetFile = $uploadDir . $newFileName;
                
                if (move_uploaded_file($fileTmp, $targetFile)) {
                    $imagePath = $newFileName;
                } else {
                    $error = "Failed to upload main image.";
                }
            } else {
                $error = "Invalid main image file.";
            }
        }
        
        if (!isset($error)) {
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, category, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdsss", $name, $description, $price, $stock, $category, $imagePath);
            $stmt->execute();
            $productId = $stmt->insert_id;
            $stmt->close();
            
            // Handle gallery images
            if (isset($_FILES['gallery_images'])) {
                $sortOrder = 1; // Start from 1, main image is 0
                foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmpName) {
                    if ($_FILES['gallery_images']['error'][$key] == UPLOAD_ERR_OK) {
                        $fileName = basename($_FILES['gallery_images']['name'][$key]);
                        $fileSize = $_FILES['gallery_images']['size'][$key];
                        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        
                        if (in_array($fileType, $allowedTypes) && $fileSize <= $maxSize) {
                            $newFileName = time() . '_' . rand(1000, 9999) . '_' . $sortOrder . '.' . $fileType;
                            $targetFile = $uploadDir . $newFileName;
                            
                            if (move_uploaded_file($tmpName, $targetFile)) {
                                $stmt = $conn->prepare("INSERT INTO product_images (product_id, image_path, alt_text, sort_order) VALUES (?, ?, ?, ?)");
                                $stmt->bind_param("issi", $productId, $newFileName, $name, $sortOrder);
                                $stmt->execute();
                                $stmt->close();
                                $sortOrder++;
                            }
                        }
                    }
                }
            }
            
            $message = "Product added with images.";
        }
    } elseif (isset($_POST['edit_product'])) {
        $id = (int)$_POST['id'];
        $name = trim($_POST['edit_name']);
        $description = trim($_POST['edit_description']);
        $price = (float)$_POST['edit_price'];
        $stock = (int)$_POST['edit_stock'];
        $category = trim($_POST['edit_category']);
        $imagePath = trim($_POST['existing_image']);
        
        // Handle new main image upload
        if (isset($_FILES['edit_image']) && $_FILES['edit_image']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'assets/images/';
            $fileName = basename($_FILES['edit_image']['name']);
            $fileTmp = $_FILES['edit_image']['tmp_name'];
            $fileSize = $_FILES['image']['size'];
            $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            $maxSize = 2 * 1024 * 1024;
            
            if (in_array($fileType, $allowedTypes) && $fileSize <= $maxSize) {
                $newFileName = time() . '_' . rand(1000, 9999) . '.' . $fileType;
                $targetFile = $uploadDir . $newFileName;
                
                if (move_uploaded_file($fileTmp, $targetFile)) {
                    if (!empty($_POST['existing_image'])) {
                        unlink($uploadDir . $_POST['existing_image']);
                    }
                    $imagePath = $newFileName;
                } else {
                    $error = "Failed to upload new main image.";
                }
            } else {
                $error = "Invalid new main image file.";
            }
        }
        
        if (!isset($error)) {
            $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category = ?, image = ? WHERE id = ?");
            $stmt->bind_param("ssdsssi", $name, $description, $price, $stock, $category, $imagePath, $id);
            $stmt->execute();
            $stmt->close();
            $message = "Product updated.";
        }
    } elseif (isset($_POST['delete_product'])) {
        $id = (int)$_POST['id'];
        // Delete main image
        $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        if ($product && !empty($product['image'])) {
            unlink('assets/images/' . $product['image']);
        }
        $result->free();
        
        // Delete gallery images
        $stmt = $conn->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($image = $result->fetch_assoc()) {
            unlink('assets/images/' . $image['image_path']);
        }
        $result->free();
        
        $stmt = $conn->prepare("DELETE FROM product_images WHERE product_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $message = "Product and images deleted.";
    }
}

// Fetch products
$query = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = '';

if (!empty($searchName)) {
    $query .= " AND name LIKE ?";
    $params[] = '%' . $searchName . '%';
    $types .= 's';
}

if (!empty($searchCategory)) {
    $query .= " AND category LIKE ?";
    $params[] = '%' . $searchCategory . '%';
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin_products.css">
</head>
<body>
    <div class="container">
        <h1>Manage Products</h1>
        <a href="index.php">Back to Dashboard</a>
        <?php if (isset($message)): ?>
            <div class="admin-message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="admin-message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <!-- Search Filters -->
        <div class="filters">
            <form method="GET" action="admin_products.php" class="filter-form">
                <input type="text" name="name" placeholder="Product Name" value="<?php echo htmlspecialchars($searchName); ?>">
                <input type="text" name="category" placeholder="Category" value="<?php echo htmlspecialchars($searchCategory); ?>">
                <button type="submit">Search</button>
                <?php if (!empty($searchName) || !empty($searchCategory)): ?>
                    <a href="admin_products.php" class="clear-filter">Clear Filters</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Add Product Form -->
        <div class="add-product-form">
            <h2>Add Product</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Name" required>
                <textarea name="description" placeholder="Description"></textarea>
                <input type="number" step="0.01" name="price" placeholder="Price" required>
                <input type="number" name="stock" placeholder="Stock" required>
                <input type="text" name="category" placeholder="Category">
                <input type="file" name="image" accept="image/*" required> <!-- Main image -->
                <input type="file" name="gallery_images[]" accept="image/*" multiple> <!-- Gallery images -->
                <button type="submit" name="add_product">Add Product</button>
            </form>
        </div>
        
        <!-- Products Table -->
        <div class="products-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Category</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="7" class="no-products">No products found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['id']); ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($product['stock']); ?></td>
                                <td><?php echo htmlspecialchars($product['category'] ?? 'N/A'); ?></td>
                                <td><?php if (!empty($product['image'])): ?><img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="Image" style="width: 50px; height: auto;"><?php else: ?>No Image<?php endif; ?></td>
                                <td>
                                    <button class="edit-btn" onclick="toggleEdit(<?php echo $product['id']; ?>)">Edit</button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" name="delete_product" class="delete-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <tr id="edit-row-<?php echo $product['id']; ?>" class="edit-form">
                                <td colspan="7">
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($product['image']); ?>">
                                        <input type="text" name="edit_name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                        <textarea name="edit_description"><?php echo htmlspecialchars($product['description']); ?></textarea>
                                        <input type="number" step="0.01" name="edit_price" value="<?php echo $product['price']; ?>" required>
                                        <input type="number" name="edit_stock" value="<?php echo $product['stock']; ?>" required>
                                        <input type="text" name="edit_category" value="<?php echo htmlspecialchars($product['category']); ?>">
                                        <input type="file" name="edit_image" accept="image/*">
                                        <button type="submit" name="edit_product">Update Product</button>
                                        <button type="button" onclick="toggleEdit(<?php echo $product['id']; ?>)">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        function toggleEdit(id) {
            const row = document.getElementById('edit-row-' + id);
            row.style.display = row.style.display === 'table-row' ? 'none' : 'table-row';
        }
    </script>
</body>
</html>
