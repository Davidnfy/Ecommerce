<?php
require_once '../config.php';
require_once '../functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    redirect('../login.php');
}

// Delete order
function reorderTableIds($conn, $tableName) {
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");
    mysqli_query($conn, "SET @count = 0");
    mysqli_query($conn, "UPDATE $tableName SET id = (@count:=@count+1) ORDER BY id");
    // Reset AUTO_INCREMENT to max(id) + 1 instead of 1 to avoid conflicts
    $result = mysqli_query($conn, "SELECT MAX(id) AS max_id FROM $tableName");
    $row = mysqli_fetch_assoc($result);
    $next_id = $row['max_id'] + 1;
    mysqli_query($conn, "ALTER TABLE $tableName AUTO_INCREMENT = $next_id");
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM orders WHERE id = $id");
    reorderTableIds($conn, 'orders');
    redirect('orders.php?message=Order deleted successfully');
}

// Edit order
$edit_order = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM orders WHERE id = $id");
    $edit_order = mysqli_fetch_assoc($result);
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'] ?? '';
    $address = $_POST['address'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';

    if (empty($status) || empty($address) || empty($payment_method)) {
        $error = 'Please fill in all fields';
    } else {
        $status = clean($status);
        $address = clean($address);
        $payment_method = clean($payment_method);

        $query = "UPDATE orders SET status='$status', address='$address', payment_method='$payment_method' WHERE id=$order_id";
        if (mysqli_query($conn, $query)) {
            $success = 'Order updated successfully';
            $edit_order = null;
        } else {
            $error = 'Failed to update order';
        }
    }
}

// Get all orders with user info
$orders = mysqli_query($conn, "SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");

$message = $_GET['message'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Orders - Admin | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="css/admin.css" />
</head>
<body>
    <div class="admin-container">
        <?php include 'templates/sidebar.php'; ?>

        <main class="admin-content">
            <header class="admin-header">
                <h1>Orders</h1>
                <div class="admin-user">
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                    <a href="../logout.php" class="btn-small">Logout</a>
                </div>
            </header>

            <?php if ($message): ?>
                <div class="form-message success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="form-message error"><?php echo $error; ?></div>
            <?php elseif ($success): ?>
                <div class="form-message success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if ($edit_order): ?>
                <section class="admin-section">
                    <h2>Edit Order #<?php echo $edit_order['id']; ?></h2>
                    <form method="post" action="orders.php" class="admin-form">
                        <input type="hidden" name="order_id" value="<?php echo $edit_order['id']; ?>" />
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending" <?php echo $edit_order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo $edit_order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="completed" <?php echo $edit_order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo $edit_order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" class="form-control" required><?php echo htmlspecialchars($edit_order['address']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="payment_method">Payment Method</label>
                            <input type="text" name="payment_method" id="payment_method" class="form-control" required value="<?php echo htmlspecialchars($edit_order['payment_method']); ?>" />
                        </div>
                        <button type="submit" class="btn btn-primary">Update Order</button>
                        <a href="orders.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </section>
            <?php else: ?>
                <section class="admin-section">
                    <h2>Order History</h2>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = mysqli_fetch_assoc($orders)): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                        <td><?php echo CURRENCY . ' ' . number_format($order['total'], 0, ',', '.'); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <a href="orders.php?edit=<?php echo $order['id']; ?>" class="btn-small">Edit</a>
                                            <a href="orders.php?delete=<?php echo $order['id']; ?>" class="btn-small btn-secondary" onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                <?php if (mysqli_num_rows($orders) === 0): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No orders found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
