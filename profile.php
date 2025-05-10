<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    // User not found, logout and redirect to login
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Profile - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <?php include 'templates/header.php'; ?>

    <div class="container">
        <h2>My Profile</h2>
        <div class="profile-info">
            <p><strong>Nama:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        <a href="logout.php" class="btn btn-primary">Logout</a>
    </div>

    <?php include 'templates/footer.php'; ?>
</body>
</html>
