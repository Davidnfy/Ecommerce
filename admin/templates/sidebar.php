
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <h2><?php echo SITE_NAME; ?> Admin</h2>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">Products</a></li>
            <li><a href="categories.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">Categories</a></li>
            <li><a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">Orders</a></li>
            <li><a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">Users</a></li>
            <li><a href="../index.php" target="_blank">View Site</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>
</aside>