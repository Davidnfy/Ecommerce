
<?php
require_once 'config.php';

// Product functions
function getAllProducts() {
    global $conn;
    $query = "SELECT * FROM products ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    return $products;
}

function getProductById($id) {
    global $conn;
    $id = (int)$id;
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              JOIN categories c ON p.category_id = c.id
              WHERE p.id = $id";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

function getProductsByCategory($category_id) {
    global $conn;
    $category_id = (int)$category_id;
    $query = "SELECT * FROM products WHERE category_id = $category_id ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    return $products;
}

function searchProducts($term) {
    global $conn;
    $term = clean($term);
    $query = "SELECT * FROM products WHERE name LIKE '%$term%' OR description LIKE '%$term%'";
    $result = mysqli_query($conn, $query);
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    return $products;
}

// Category functions
function getAllCategories() {
    global $conn;
    $query = "SELECT * FROM categories ORDER BY id ASC";
    $result = mysqli_query($conn, $query);
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    return $categories;
}

// User functions
function registerUser($name, $email, $password) {
    global $conn;
    $name = clean($name);
    $email = clean($email);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $query = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')";
    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    }
    return false;
}

function loginUser($email, $password) {
    global $conn;
    $email = clean($email);
    
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['is_admin'] = $user['is_admin'];
            return true;
        }
    }
    return false;
}

// Cart functions
function addToCart($product_id, $quantity = 1) {
    $product_id = (int)$product_id;
    $quantity = (int)$quantity;
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

function removeFromCart($product_id) {
    $product_id = (int)$product_id;
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

function updateCartQuantity($product_id, $quantity) {
    $product_id = (int)$product_id;
    $quantity = (int)$quantity;
    
    if ($quantity <= 0) {
        removeFromCart($product_id);
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

function getCartItems() {
    global $conn;
    $items = [];
    
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $product = getProductById($product_id);
            if ($product) {
                $items[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image' => $product['image'],
                    'quantity' => $quantity,
                    'subtotal' => $product['price'] * $quantity
                ];
            }
        }
    }
    
    return $items;
}

function getCartTotal() {
    $total = 0;
    $items = getCartItems();
    
    foreach ($items as $item) {
        $total += $item['subtotal'];
    }
    
    return $total;
}

// Order functions
function createOrder($user_id, $address, $payment_method) {
    global $conn;
    
    $user_id = (int)$user_id;
    $address = clean($address);
    $payment_method = clean($payment_method);
    $total = getCartTotal();
    
    $query = "INSERT INTO orders (user_id, total, address, payment_method, status)
              VALUES ($user_id, $total, '$address', '$payment_method', 'pending')";
              
    if (mysqli_query($conn, $query)) {
        $order_id = mysqli_insert_id($conn);
        $items = getCartItems();
        
        foreach ($items as $item) {
            $product_id = $item['id'];
            $price = $item['price'];
            $quantity = $item['quantity'];
            
            $query = "INSERT INTO order_items (order_id, product_id, price, quantity) 
                     VALUES ($order_id, $product_id, $price, $quantity)";
            mysqli_query($conn, $query);
        }
        
        // Clear cart after order is created
        $_SESSION['cart'] = [];
        
        return $order_id;
    }
    
    return false;
}

function getUserOrders($user_id) {
    global $conn;
    $user_id = (int)$user_id;
    
    $query = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    
    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    
    return $orders;
}

function getOrderItems($order_id) {
    global $conn;
    $order_id = (int)$order_id;
    
    $query = "SELECT oi.*, p.name, p.image 
              FROM order_items oi
              JOIN products p ON oi.product_id = p.id
              WHERE oi.order_id = $order_id";
    $result = mysqli_query($conn, $query);
    
    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    
    return $items;
}
?>
