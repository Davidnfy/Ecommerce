
<?php
require_once 'config.php';
require_once 'functions.php';

// Get filter parameters
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'latest';

// Query building
$query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";

if ($category_id > 0) {
    $query .= " AND p.category_id = $category_id";
}

if (!empty($search)) {
    $search = clean($search);
    $query .= " AND (p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
}

// Sorting
switch ($sort) {
    case 'price_low':
        $query .= " ORDER BY p.price ASC";
        break;
    case 'price_high':
        $query .= " ORDER BY p.price DESC";
        break;
    case 'name':
        $query .= " ORDER BY p.name ASC";
        break;
    default:
        $query .= " ORDER BY p.created_at DESC";
}

$products = mysqli_query($conn, $query);
$categories = getAllCategories();

// Get active category name
$active_category_name = 'All Products';
if ($category_id > 0) {
    foreach ($categories as $category) {
        if ($category['id'] == $category_id) {
            $active_category_name = $category['name'];
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $active_category_name; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'templates/header.php'; ?>
    
    <div class="container py-4">
        <div class="product-page-header">
            <h1><?php echo $active_category_name; ?></h1>
            
            <?php if (!empty($search)): ?>
                <p>Search results for: <strong><?php echo $search; ?></strong></p>
            <?php endif; ?>
            
            <div class="product-filters">
                <div class="category-filter">
                    <select onchange="location = this.value;">
                        <option value="products.php">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="products.php?category=<?php echo $category['id']; ?>" <?php echo ($category_id == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo $category['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="sort-filter">
                    <select onchange="location = updateQueryStringParameter(window.location.href, 'sort', this.value);">
                        <option value="latest" <?php echo ($sort == 'latest') ? 'selected' : ''; ?>>Latest</option>
                        <option value="price_low" <?php echo ($sort == 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_high" <?php echo ($sort == 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="name" <?php echo ($sort == 'name') ? 'selected' : ''; ?>>Name</option>
                    </select>
                </div>
            </div>
        </div>
        
        <?php if (mysqli_num_rows($products) === 0): ?>
            <div class="text-center py-5">
                <p>No products found.</p>
                <a href="products.php" class="btn mt-3">View All Products</a>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php while ($product = mysqli_fetch_assoc($products)): ?>
                    <div class="product-card">
                        <div class="product-img">
                            <a href="product_details.php?id=<?php echo $product['id']; ?>">
                                <img src="<?php echo UPLOAD_PATH . $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                            </a>
                        </div>
                        <div class="product-info">
                            <a href="product_details.php?id=<?php echo $product['id']; ?>" class="product-name">
                                <h3><?php echo $product['name']; ?></h3>
                            </a>
                            <p class="price"><?php echo CURRENCY . ' ' . number_format($product['price'], 0, ',', '.'); ?></p>
                            <p class="category"><?php echo $product['category_name']; ?></p>
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
        <?php endif; ?>
    </div>
    
    <?php include 'templates/footer.php'; ?>
    
    <script>
    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            return uri + separator + key + "=" + value;
        }
    }
    </script>
</body>
</html>
