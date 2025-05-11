 
<?php
require_once '../config.php';
require_once '../functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    redirect('../login.php');
}

// Delete product
function reorderTableIds($conn, $tableName) {
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");
    mysqli_query($conn, "SET @count = 0");
    mysqli_query($conn, "UPDATE $tableName SET id = (@count:=@count+1) ORDER BY id");
    mysqli_query($conn, "ALTER TABLE $tableName AUTO_INCREMENT = 1");
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Get product image
    $product = getProductById($id);
    if ($product && !empty($product['image'])) {
        // Delete image file
        $image_path = "../" . UPLOAD_PATH . $product['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Delete product
    mysqli_query($conn, "DELETE FROM products WHERE id = $id");
    reorderTableIds($conn, 'products');
    redirect('products.php?message=Product deleted successfully');
}

// Messages
$message = $_GET['message'] ?? '';

// Get all products
$products = getAllProducts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'templates/sidebar.php'; ?>
        
        <main class="admin-content">
            <header class="admin-header">
                <h1>Products</h1>
                <div class="admin-user">
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                    <a href="../logout.php" class="btn-small">Logout</a>
                </div>
            </header>
            
            <?php if (!empty($message)): ?>
                <div class="form-message success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <div class="admin-action">
                <a href="product_form.php" class="btn btn-primary">Add New Product</a>
            </div>
            
            <section class="admin-section">
                <h2>Manage Products</h2>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Category</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td>
                                        <?php if (!empty($product['image'])): ?>
                                            <img src="<?php echo '../' . UPLOAD_PATH . $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="image-preview">
                                        <?php else: ?>
                                            No Image
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $product['name']; ?></td>
                                    <td><?php echo CURRENCY . ' ' . number_format($product['price'], 0, ',', '.'); ?></td>
                                    <td><?php echo $product['stock']; ?></td>
                                    <td>
                                        <?php
                                        $category = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM categories WHERE id = " . $product['category_id']));
                                        echo $category ? $category['name'] : 'N/A';
                                        ?>
                                    </td>
                                    <td><?php echo $product['featured'] ? 'Yes' : 'No'; ?></td>
                                    <td class="action-buttons">
                                        <a href="product_form.php?id=<?php echo $product['id']; ?>" class="btn-small">Edit</a>
                                        <a href="products.php?delete=<?php echo $product['id']; ?>" class="btn-small btn-secondary" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <?php if (count($products) === 0): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No products found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>