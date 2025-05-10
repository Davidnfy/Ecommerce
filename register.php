
<?php
require_once 'config.php';
require_once 'functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate input
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        // Check if email already exists
        $query = "SELECT id FROM users WHERE email = '" . clean($email) . "'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $error = 'Email already exists';
        } else {
            // Register user
            if (registerUser($name, $email, $password)) {
                $success = 'Registration successful! You can now login';
            } else {
                $error = 'Something went wrong. Please try again';
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
    <title>Register - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'templates/header.php'; ?>
    
    <div class="container">
        <div class="form-container">
            <h2 class="form-title">Create an Account</h2>
            
            <?php if (!empty($error)): ?>
                <div class="form-message error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="form-message success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form action="register.php" method="post">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary form-submit">Register</button>
            </form>
            
            <p class="text-center mt-3">
                Already have an account? <a href="login.php">Login</a>
            </p>
        </div>
    </div>
    
    <?php include 'templates/footer.php'; ?>
</body>
</html>
