<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Add new category
if (isset($_POST['name'])) {
    $name = $_POST['name'];

    $sql = "INSERT INTO categories (name) VALUES (?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $name);
    mysqli_stmt_execute($stmt);

    header("Location: categories.php");
    exit();
}

// Delete category
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $sql = "DELETE FROM categories WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    mysqli_stmt_execute($stmt);

    header("Location: categories.php");
    exit();
}

// Get all categories
$sql = "SELECT * FROM categories";
$result = mysqli_query($conn, $sql);
require '../includes/header.php';
?>
<h2 class="mb-4">Manage Categories</h2>

<form action="categories.php" method="POST" class="row g-2 mb-4">
    <div class="col-auto">
        <input type="text" name="name" class="form-control" placeholder="Category name" required>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Add Category</button>
    </div>
</form>

<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Name</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><a href="categories.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<br>
<a href="dashboard.php">Back to Dashboard</a>

<?php require '../includes/footer.php'; ?>