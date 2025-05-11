<?php
require_once '../config.php';
require_once '../functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    redirect('../login.php');
}

// Get all users except admin
$query = "SELECT id, name, email FROM users WHERE is_admin = 0 ORDER BY name";
$result = mysqli_query($conn, $query);

$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

$message = $_GET['message'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Users - Admin | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="css/admin.css" />
</head>
<body>
    <div class="admin-container">
        <?php include 'templates/sidebar.php'; ?>

        <main class="admin-content">
            <header class="admin-header">
                <h1>Users</h1>
                <div class="admin-user">
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                    <a href="../logout.php" class="btn-small">Logout</a>
                </div>
            </header>

            <?php if ($message): ?>
                <div class="form-message success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <section class="admin-section">
                <h2>Registered Users</h2>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($users) === 0): ?>
                                <tr>
                                    <td colspan="3" class="text-center">No users found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
