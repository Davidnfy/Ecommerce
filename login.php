
<?php
require_once 'config.php';
require_once 'functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        // Check fixed admin credentials first
        if ($email === 'admin@gmail.com' && $password === 'admin123') {
            $_SESSION['user_id'] = 0; // dummy id for admin
            $_SESSION['user_name'] = 'Admin';
            $_SESSION['is_admin'] = 1;
            redirect('admin/index.php');
        } else {
            if (loginUser($email, $password)) {
                if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
                    redirect('admin/index.php');
                } else {
                    redirect('index.php');
                }
            } else {
                $error = 'Invalid email or password';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'templates/header.php'; ?>
    
    <div class="container">
        <div class="form-container">
            <h2 class="form-title">Login to Your Account</h2>
            
            <?php if (!empty($error)): ?>
                <div class="form-message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary form-submit">Login</button>
            </form>
            
            <p class="text-center mt-3">
                Don't have an account? <a href="register.php">Register</a>
            </p>
        </div>
    </div>
    
    <?php include 'templates/footer.php'; ?>
</body>
</html>
