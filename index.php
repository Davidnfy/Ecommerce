
<?php
require_once 'config.php';
require_once 'functions.php';

$featured_products = mysqli_query($conn, "SELECT * FROM products WHERE featured = 1 LIMIT 4");
$categories = getAllCategories();
$new_arrivals = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC LIMIT 8");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Online Shopping</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'templates/header.php'; ?>
    
    <div class="hero">
        <div class="hero-content">
            <h1>Welcome to <?php echo SITE_NAME; ?></h1>
            <p>Your one-stop online shopping destination</p>
            <a href="products.php" class="btn">Shop Now</a>
        </div>
    </div>
    
    <section class="categories container">
        <h2>Shop by Category</h2>
        <div class="category-grid">
            <?php foreach ($categories as $category): ?>
                <a href="products.php?category=<?php echo $category['id']; ?>" class="category-card">
                    <h3><?php echo $category['name']; ?></h3>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    
    <section class="featured container">
        <h2>Featured Products</h2>
        <div class="product-grid">
            <?php while ($product = mysqli_fetch_assoc($featured_products)): ?>
                <div class="product-card">
                    <div class="product-img">
                        <img src="<?php echo UPLOAD_PATH . $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    </div>
                    <div class="product-info">
                        <h3><?php echo $product['name']; ?></h3>
                        <p class="price"><?php echo CURRENCY . ' ' . number_format($product['price'], 0, ',', '.'); ?></p>
                        <div class="product-actions">
                            <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn-small">Details</a>
                            <form action="cart_process.php" method="post">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="action" value="add">
                                <button type="submit" class="btn-small btn-primary">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
    
    <section class="new-arrivals container">
        <h2>New Arrivals</h2>
        <div class="product-grid">
            <?php while ($product = mysqli_fetch_assoc($new_arrivals)): ?>
                <div class="product-card">
                    <div class="product-img">
                        <img src="<?php echo UPLOAD_PATH . $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    </div>
                    <div class="product-info">
                        <h3><?php echo $product['name']; ?></h3>
                        <p class="price"><?php echo CURRENCY . ' ' . number_format($product['price'], 0, ',', '.'); ?></p>
                        <div class="product-actions">
                            <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn-small">Details</a>
                            <form action="cart_process.php" method="post">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="action" value="add">
                                <button type="submit" class="btn-small btn-primary">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
    
    <?php include 'templates/footer.php'; ?>
</body>
</html>
