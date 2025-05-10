
<?php
require_once 'config.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $action = $_POST['action'] ?? '';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if ($product_id > 0) {
        switch ($action) {
            case 'add':
                addToCart($product_id, $quantity);
                break;
                
            case 'update':
                updateCartQuantity($product_id, $quantity);
                break;
                
            case 'remove':
                removeFromCart($product_id);
                break;
        }
    }
}

// Redirect back to referring page or cart
$redirect = $_SERVER['HTTP_REFERER'] ?? 'cart.php';
redirect($redirect);
?>
