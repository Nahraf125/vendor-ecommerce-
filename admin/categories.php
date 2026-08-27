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

?>
<h2>Manage Categories</h2>

<form action="categories.php" method="POST">
    <input type="text" name="name" placeholder="Category name" required>
    <button type="submit">Add Category</button>
</form>

<hr>

<table border="1" cellpadding="8">
    <tr>
        <th>Name</th>
        <th>Action</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?php echo $row['name']; ?></td>
        <td><a href="categories.php?delete_id=<?php echo $row['id']; ?>">Delete</a></td>
    </tr>
    <?php endwhile; ?>
</table>

<br>
<a href="dashboard.php">Back to Dashboard</a>