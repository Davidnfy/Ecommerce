<?php
require_once '../config.php';
require_once '../functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    redirect('../login.php');
}

$id = $_GET['id'] ?? null;
$product = null;
$error = '';
$success = '';

if ($id) {
    $id = (int)$id;
    $product = getProductById($id);
    if (!$product) {
        redirect('products.php?message=Product not found');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $featured = isset($_POST['featured']) ? 1 : 0;

    if (empty($name) || empty($price) || empty($stock) || empty($category_id)) {
        $error = 'Please fill in all required fields';
    } else {
        // Handle image upload
        $image_name = $product['image'] ?? '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../' . UPLOAD_PATH;
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $tmp_name = $_FILES['image']['tmp_name'];
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_image_name = uniqid() . '.' . $ext;
            $destination = $upload_dir . $new_image_name;
            if (move_uploaded_file($tmp_name, $destination)) {
                // Delete old image
                if (!empty($image_name) && file_exists($upload_dir . $image_name)) {
                    unlink($upload_dir . $image_name);
                }
                $image_name = $new_image_name;
            } else {
                $error = 'Failed to upload image';
            }
        }

        if (!$error) {
            $name = clean($name);
            $price = (float)$price;
            $stock = (int)$stock;
            $category_id = (int)$category_id;

            if ($id) {
                // Update product
                $query = "UPDATE products SET name='$name', price=$price, stock=$stock, category_id=$category_id, featured=$featured, image='$image_name' WHERE id=$id";
                if (mysqli_query($conn, $query)) {
                    $success = 'Product updated successfully';
                    $product = getProductById($id);
                } else {
                    $error = 'Failed to update product';
                }
            } else {
                // Insert new product
                $query = "INSERT INTO products (name, price, stock, category_id, featured, image) VALUES ('$name', $price, $stock, $category_id, $featured, '$image_name')";
                if (mysqli_query($conn, $query)) {
                    $success = 'Product added successfully';
                    $id = mysqli_insert_id($conn);
                    $product = getProductById($id);
                } else {
                    $error = 'Failed to add product';
                }
            }
        }
    }
}

// Get categories for select dropdown
$categories = getAllCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo $id ? 'Edit' : 'Add'; ?> Product - Admin | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="css/admin.css" />
</head>
<body>
    <div class="admin-container">
        <?php include 'templates/sidebar.php'; ?>

        <main class="admin-content">
            <header class="admin-header">
                <h1><?php echo $id ? 'Edit' : 'Add'; ?> Product</h1>
                <div class="admin-user">
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                    <a href="../logout.php" class="btn-small">Logout</a>
                </div>
            </header>

            <?php if ($error): ?>
                <div class="form-message error"><?php echo $error; ?></div>
            <?php elseif ($success): ?>
                <div class="form-message success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post" enctype="multipart/form-data" class="admin-form">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" name="name" id="name" class="form-control" required value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="price">Price *</label>
                    <input type="number" step="0.01" name="price" id="price" class="form-control" required value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="stock">Stock *</label>
                    <input type="number" name="stock" id="stock" class="form-control" required value="<?php echo htmlspecialchars($product['stock'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select name="category_id" id="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo (isset($product['category_id']) && $product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group checkbox-group">
                    <input type="checkbox" name="featured" id="featured" <?php echo (!empty($product['featured'])) ? 'checked' : ''; ?>>
                    <label for="featured">Featured</label>
                </div>

                <div class="form-group">
                    <label for="image">Image <?php if (!empty($product['image'])): ?><br><img src="../<?php echo UPLOAD_PATH . $product['image']; ?>" alt="Product Image" class="image-preview"><?php endif; ?></label>
                    <input type="file" name="image" id="image" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary"><?php echo $id ? 'Update' : 'Add'; ?> Product</button>
                <a href="products.php" class="btn btn-secondary">Cancel</a>
            </form>
        </main>
    </div>
</body>
</html>
