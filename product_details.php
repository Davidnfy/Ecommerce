
<?php
require_once 'config.php';
require_once 'functions.php';

// Get product ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    redirect('products.php');
}

// Get product details
$product = getProductById($id);

if (!$product) {
    redirect('products.php');
}

// Get related products
$category_id = $product['category_id'];
$related_products = mysqli_query($conn, 
    "SELECT * FROM products 
    WHERE category_id = $category_id AND id != $id 
    ORDER BY RAND() LIMIT 4"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'templates/header.php'; ?>
    
    <div class="container py-4">
        <div class="breadcrumb">
            <a href="index.php">Home</a> &raquo;
            <a href="products.php?category=<?php echo $product['category_id']; ?>"><?php echo $product['category_name']; ?></a> &raquo;
            <span><?php echo $product['name']; ?></span>
        </div>
        
        <div class="product-details">
            <div class="product-image">
                <img src="<?php echo UPLOAD_PATH . $product['image']; ?>" alt="<?php echo $product['name']; ?>">
            </div>
            
            <div class="product-info-details">
                <h1><?php echo $product['name']; ?></h1>
                <p class="product-price"><?php echo CURRENCY . ' ' . number_format($product['price'], 0, ',', '.'); ?></p>
                
                <div class="product-description">
                    <h3>Description</h3>
                    <p><?php echo nl2br($product['description']); ?></p>
                </div>
                
                <div class="product-stock">
                    <?php if ($product['stock'] > 0): ?>
                        <span class="in-stock">In Stock (<?php echo $product['stock']; ?> available)</span>
                    <?php else: ?>
                        <span class="out-of-stock">Out of Stock</span>
                    <?php endif; ?>
                </div>
                
                <form action="cart_process.php" method="post" class="product-form">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="quantity-selector">
                        <label for="quantity">Quantity:</label>
                        <div class="cart-quantity">
                            <button type="button" class="quantity-btn decrease">-</button>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="quantity-input">
                            <button type="button" class="quantity-btn increase">+</button>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary add-to-cart" <?php echo ($product['stock'] <= 0) ? 'disabled' : ''; ?>>
                        Add to Cart
                    </button>
                </form>
            </div>
        </div>
        
        <?php if (mysqli_num_rows($related_products) > 0): ?>
            <section class="related-products">
                <h2>Related Products</h2>
                <div class="product-grid">
                    <?php while ($related = mysqli_fetch_assoc($related_products)): ?>
                        <div class="product-card">
                            <div class="product-img">
                                <a href="product_details.php?id=<?php echo $related['id']; ?>">
                                    <img src="<?php echo UPLOAD_PATH . $related['image']; ?>" alt="<?php echo $related['name']; ?>">
                                </a>
                            </div>
                            <div class="product-info">
                                <a href="product_details.php?id=<?php echo $related['id']; ?>" class="product-name">
                                    <h3><?php echo $related['name']; ?></h3>
                                </a>
                                <p class="price"><?php echo CURRENCY . ' ' . number_format($related['price'], 0, ',', '.'); ?></p>
                                <div class="product-actions">
                                    <a href="product_details.php?id=<?php echo $related['id']; ?>" class="btn-small">Details</a>
                                    <form action="cart_process.php" method="post">
                                        <input type="hidden" name="product_id" value="<?php echo $related['id']; ?>">
                                        <input type="hidden" name="action" value="add">
                                        <button type="submit" class="btn-small btn-primary">Add to Cart</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
    
    <?php include 'templates/footer.php'; ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity buttons
        const decreaseBtn = document.querySelector('.decrease');
        const increaseBtn = document.querySelector('.increase');
        const quantityInput = document.querySelector('#quantity');
        const maxStock = <?php echo $product['stock']; ?>;
        
        decreaseBtn.addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            if (value > 1) {
                quantityInput.value = value - 1;
            }
        });
        
        increaseBtn.addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            if (value < maxStock) {
                quantityInput.value = value + 1;
            }
        });
    });
    </script>
</body>
</html>
