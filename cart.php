
<?php
require_once 'config.php';
require_once 'functions.php';

// Process cart actions
if (isset($_POST['action'])) {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $action = $_POST['action'];
    
    switch ($action) {
        case 'update':
            $quantity = (int)($_POST['quantity'] ?? 1);
            updateCartQuantity($product_id, $quantity);
            break;
        case 'remove':
            removeFromCart($product_id);
            break;
    }
    
    // Redirect to prevent form resubmission
    redirect('cart.php');
}

// Get cart items
$cart_items = getCartItems();
$total = getCartTotal();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'templates/header.php'; ?>
    
    <div class="container py-4">
        <h1>Shopping Cart</h1>
        
        <?php if (empty($cart_items)): ?>
            <div class="text-center py-5">
                <p>Your cart is empty</p>
                <a href="products.php" class="btn mt-3">Shop Now</a>
            </div>
        <?php else: ?>
            <div class="cart-container">
                <div class="cart-items">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="product-info-cart">
                                            <img src="<?php echo UPLOAD_PATH . $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="cart-image">
                                            <div>
                                                <h3><?php echo $item['name']; ?></h3>
                                                <p><a href="product_details.php?id=<?php echo $item['id']; ?>">View details</a></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo CURRENCY . ' ' . number_format($item['price'], 0, ',', '.'); ?></td>
                                    <td>
                                        <form action="cart.php" method="post" class="cart-quantity-form">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="action" value="update">
                                            <div class="cart-quantity">
                                                <button type="button" class="quantity-btn decrease">-</button>
                                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input" onchange="this.form.submit()">
                                                <button type="button" class="quantity-btn increase">+</button>
                                            </div>
                                        </form>
                                    </td>
                                    <td><?php echo CURRENCY . ' ' . number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                    <td>
                                        <form action="cart.php" method="post">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="action" value="remove">
                                            <button type="submit" class="cart-remove">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="cart-summary">
                    <h2>Order Summary</h2>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span><?php echo CURRENCY . ' ' . number_format($total, 0, ',', '.'); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span><?php echo CURRENCY . ' ' . number_format($total, 0, ',', '.'); ?></span>
                    </div>
                    
                    <a href="checkout.php" class="btn btn-primary form-submit">Proceed to Checkout</a>
                    <a href="products.php" class="btn form-submit">Continue Shopping</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include 'templates/footer.php'; ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity buttons
        document.querySelectorAll('.decrease').forEach(function(button) {
            button.addEventListener('click', function() {
                const input = this.parentNode.querySelector('.quantity-input');
                const value = parseInt(input.value);
                if (value > 1) {
                    input.value = value - 1;
                    input.form.submit();
                }
            });
        });
        
        document.querySelectorAll('.increase').forEach(function(button) {
            button.addEventListener('click', function() {
                const input = this.parentNode.querySelector('.quantity-input');
                input.value = parseInt(input.value) + 1;
                input.form.submit();
            });
        });
    });
    </script>
</body>
</html>
