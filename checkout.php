
<?php
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    redirect('login.php?message=Please login to proceed with checkout');
}

// Check if cart is empty
$cart_items = getCartItems();
if (empty($cart_items)) {
    redirect('cart.php');
}

// Process checkout
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $_POST['address'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    
    if (empty($address)) {
        $error = 'Please enter your shipping address';
    } elseif (empty($payment_method)) {
        $error = 'Please select a payment method';
    } else {
        $order_id = createOrder($_SESSION['user_id'], $address, $payment_method);
        
        if ($order_id) {
            redirect('order_success.php?order_id=' . $order_id);
        } else {
            $error = 'Something went wrong. Please try again.';
        }
    }
}

// Get user details
$user_id = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id"));

$total = getCartTotal();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'templates/header.php'; ?>
    
    <div class="container py-4">
        <h1>Checkout</h1>
        
        <?php if (!empty($error)): ?>
            <div class="form-message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="checkout-container">
            <div class="checkout-form">
                <h2>Shopping Information</h2>
                <form action="checkout.php" method="post">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" class="form-control" value="<?php echo $user['name']; ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" class="form-control" value="<?php echo $user['email']; ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="address"> Address</label>
                        <textarea name="address" id="address" class="form-control" rows="3" required><?php echo $user['address'] ?? ''; ?></textarea>
                    </div>
                    
                    <h2>Payment Method</h2>
                    <div class="form-group">
                        <div class="payment-methods">
                            <div class="payment-method">
                                <input type="radio" name="payment_method" id="cod" value="cod" checked>
                                <label for="cod">Cash on Delivery</label>
                            </div>
                            <div class="payment-method">
                                <input type="radio" name="payment_method" id="bank_transfer" value="bank_transfer">
                                <label for="bank_transfer">Bank Transfer</label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary form-submit">Place Order</button>
                </form>
            </div>
            
            <div class="order-summary">
                <h2>Order Summary</h2>
                <div class="order-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="order-item">
                            <div class="order-item-details">
                                <h3><?php echo $item['name']; ?></h3>
                                <p>Quantity: <?php echo $item['quantity']; ?></p>
                                <p><?php echo CURRENCY . ' ' . number_format($item['price'], 0, ',', '.'); ?> each</p>
                            </div>
                            <div class="order-item-price">
                                <?php echo CURRENCY . ' ' . number_format($item['subtotal'], 0, ',', '.'); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="order-totals">
                    <div class="order-total-row">
                        <span>Subtotal</span>
                        <span><?php echo CURRENCY . ' ' . number_format($total, 0, ',', '.'); ?></span>
                    </div>
                    <div class="order-total-row">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <div class="order-total-row total">
                        <span>Total</span>
                        <span><?php echo CURRENCY . ' ' . number_format($total, 0, ',', '.'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'templates/footer.php'; ?>
</body>
</html>
