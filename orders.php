
<?php
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect('login.php?message=Please login to view your orders');
}

$user_id = $_SESSION['user_id'];
$orders = getUserOrders($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'templates/header.php'; ?>
    
    <div class="container py-4">
        <h1>My Orders</h1>
        
        <?php if (empty($orders)): ?>
            <div class="text-center py-5">
                <p>You haven't placed any orders yet.</p>
                <a href="products.php" class="btn mt-3">Shop Now</a>
            </div>
        <?php else: ?>
            <div class="orders-container">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <h3>Order #<?php echo $order['id']; ?></h3>
                                <p>Placed on <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                            </div>
                            <div>
                                <span class="status-<?php echo $order['status']; ?> order-status">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                                <p><strong>Total:</strong> <?php echo CURRENCY . ' ' . number_format($order['total'], 0, ',', '.'); ?></p>
                            </div>
                        </div>
                        <div class="order-body">
                            <div class="order-items">
                                <?php 
                                $items = getOrderItems($order['id']);
                                foreach ($items as $item): 
                                ?>
                                    <div class="order-item">
                                        <img src="<?php echo UPLOAD_PATH . $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="order-item-image">
                                        <div class="order-item-details">
                                            <h4><?php echo $item['name']; ?></h4>
                                            <p>Quantity: <?php echo $item['quantity']; ?></p>
                                            <p>Price: <?php echo CURRENCY . ' ' . number_format($item['price'], 0, ',', '.'); ?> each</p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="order-address">
                                <h4>Shipping Address</h4>
                                <p><?php echo $order['address']; ?></p>
                            </div>
                            <div class="order-payment">
                                <h4>Payment Method</h4>
                                <p><?php echo ucfirst($order['payment_method']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include 'templates/footer.php'; ?>
</body>
</html>
