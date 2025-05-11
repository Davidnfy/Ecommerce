<?php
require_once '../config.php';
require_once '../functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    redirect('../login.php');
}

$error = '';
$success = '';

// Handle add/edit category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? '';

    if (empty($name)) {
        $error = 'Category name is required';
    } else {
        $name = clean($name);
        if ($id) {
            $id = (int)$id;
            $query = "UPDATE categories SET name='$name' WHERE id=$id";
            if (mysqli_query($conn, $query)) {
                $success = 'Category updated successfully';
            } else {
                $error = 'Failed to update category';
            }
        } else {
            $query = "INSERT INTO categories (name) VALUES ('$name')";
            if (mysqli_query($conn, $query)) {
                $success = 'Category added successfully';
            } else {
                $error = 'Failed to add category';
            }
        }
    }
}

// Handle delete category
function reorderTableIds($conn, $tableName) {
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");
    mysqli_query($conn, "SET @count = 0");
    mysqli_query($conn, "UPDATE $tableName SET id = (@count:=@count+1) ORDER BY id");
    mysqli_query($conn, "ALTER TABLE $tableName AUTO_INCREMENT = 1");
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM categories WHERE id = $id");
    reorderTableIds($conn, 'categories');
    redirect('categories.php?message=Category deleted successfully');
}

// Get all categories
$categories = getAllCategories();
$message = $_GET['message'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Categories - Admin | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="css/admin.css" />
</head>
<body>
    <div class="admin-container">
        <?php include 'templates/sidebar.php'; ?>

        <main class="admin-content">
            <header class="admin-header">
                <h1>Categories</h1>
                <div class="admin-user">
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                    <a href="../logout.php" class="btn-small">Logout</a>
                </div>
            </header>

            <?php if ($error): ?>
                <div class="form-message error"><?php echo $error; ?></div>
            <?php elseif ($success): ?>
                <div class="form-message success"><?php echo $success; ?></div>
            <?php elseif ($message): ?>
                <div class="form-message success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <section class="admin-section">
                <h2>Add / Edit Category</h2>
                <form action="categories.php" method="post" class="admin-form">
                    <input type="hidden" name="id" id="category_id" value="">
                    <div class="form-group">
                        <label for="name">Category Name *</label>
                        <input type="text" name="name" id="category_name" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </form>
            </section>

            <section class="admin-section">
                <h2>Manage Categories</h2>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo $category['id']; ?></td>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td class="action-buttons">
                                        <button class="btn-small btn-edit" data-id="<?php echo $category['id']; ?>" data-name="<?php echo htmlspecialchars($category['name']); ?>">Edit</button>
                                        <a href="categories.php?delete=<?php echo $category['id']; ?>" class="btn-small btn-secondary" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($categories) === 0): ?>
                                <tr>
                                    <td colspan="3" class="text-center">No categories found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script>
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                document.getElementById('category_id').value = id;
                document.getElementById('category_name').value = name;
                window.scrollTo(0, 0);
            });
        });
    </script>
</body>
</html>
