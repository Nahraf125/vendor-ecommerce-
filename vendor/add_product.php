<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vendor') {
    header("Location: ../auth/login.php");
    exit();
}

// Vendor ki apni vendor_id nikalni hai (users table se link hai)
$user_id = $_SESSION['user_id'];
$sql = "SELECT id FROM vendors WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$vendor = mysqli_fetch_assoc($result);
$vendor_id = $vendor['id'];

// Categories list karo dropdown ke liye
$cat_result = mysqli_query($conn, "SELECT * FROM categories");
?>

<h2>Add Product</h2>

<form action="add_product_process.php" method="POST" enctype="multipart/form-data">

    <label>Product Name:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Description:</label><br>
    <textarea name="description"></textarea><br><br>

    <label>Price:</label><br>
    <input type="number" step="0.01" name="price" required><br><br>

    <label>Stock:</label><br>
    <input type="number" name="stock" required><br><br>

    <label>Category:</label><br>
    <select name="category_id" required>
        <?php while ($cat = mysqli_fetch_assoc($cat_result)): ?>
            <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Product Image:</label><br>
    <input type="file" name="image" accept="image/*" required><br><br>

    <input type="hidden" name="vendor_id" value="<?php echo $vendor_id; ?>">

    <button type="submit">Add Product</button>

</form>

<br>
<a href="dashboard.php">Back to Dashboard</a>