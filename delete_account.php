<?php
require_once 'config.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete user account
    $query = "DELETE FROM users WHERE id = $user_id";
    if (mysqli_query($conn, $query)) {
        // Logout user after deletion
        session_unset();
        session_destroy();
        header('Location: login.php?message=Your account has been deleted.');
        exit;
    } else {
        $error = 'Failed to delete account. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Delete Account - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <?php include 'templates/header.php'; ?>

    <div class="container py-4">
        <h1>Delete Account</h1>

        <?php if ($error): ?>
            <div class="form-message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <p>Are you sure you want to delete your account? This action cannot be undone.</p>

        <form method="post" action="delete_account.php">
            <button type="submit" class="btn btn-danger">Delete My Account</button>
            <a href="profile.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <?php include 'templates/footer.php'; ?>
</body>
</html>
