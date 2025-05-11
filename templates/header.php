
<header class="main-header">
    <div class="container">
        <div class="header-top">
            <div class="logo">
                <a href="index.php"><?php echo SITE_NAME; ?></a>
            </div>
            <div class="search-form">
                <form action="products.php" method="get">
                    <input type="text" name="search" placeholder="Search products...">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <div class="user-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php"><?php echo $_SESSION['user_name']; ?></a>
                    <a href="orders.php">My Orders</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
                <a href="cart.php" class="cart-link">
                    Cart
                    <?php 
                    if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                        echo '<span class="cart-count">' . count($_SESSION['cart']) . '</span>';
                    }
                    ?>
                </a>
            </div>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php">Home</a></li>
                <?php 
                $categories = getAllCategories();
                foreach ($categories as $category): 
                ?>
                <li><a href="products.php?category=<?php echo $category['id']; ?>"><?php echo $category['name']; ?></a></li>
                <?php endforeach; ?>
                <li><a href="products.php">All Products</a></li>
            </ul>
        </nav>
    </div>
</header>
