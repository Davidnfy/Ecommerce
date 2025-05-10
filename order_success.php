<?php
require_once 'config.php';
require_once 'functions.php';

if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit;
}

$order_id = (int)$_GET['order_id'];

// Fetch order details
$order_query = "SELECT o.*, u.name as user_name, u.email as user_email 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.id = $order_id";
$order_result = mysqli_query($conn, $order_query);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    // Order not found
    header('Location: index.php');
    exit;
}

// Fetch order items
$order_items = getOrderItems($order_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Order Success - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <?php include 'templates/header.php'; ?>

    <div class="container py-4">
        <h1>Order Successful</h1>
        <p>Thank you, <?php echo htmlspecialchars($order['user_name']); ?>, for your order!</p>
        <p>Your order ID is <strong>#<?php echo $order['id']; ?></strong>.</p>
        <p>We have sent a confirmation email to <strong><?php echo htmlspecialchars($order['user_email']); ?></strong>.</p>

        <h2>Order Summary</h2>
        <div class="order-summary">
            <?php foreach ($order_items as $item): ?>
                <div class="order-item">
                    <div class="order-item-details">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                        <p><?php echo CURRENCY . ' ' . number_format($item['price'], 0, ',', '.'); ?> each</p>
                    </div>
                    <div class="order-item-price">
                        <?php echo CURRENCY . ' ' . number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="order-total-row total">
                <span>Total Paid</span>
                <span><?php echo CURRENCY . ' ' . number_format($order['total'], 0, ',', '.'); ?></span>
            </div>
        </div>

        <a href="index.php" class="btn btn-primary mt-3">Back to Home</a>
        <a href="orders.php" class="btn btn-secondary mt-3">View My Orders</a>
    </div>

    <?php include 'templates/footer.php'; ?>
</body>
</html>
