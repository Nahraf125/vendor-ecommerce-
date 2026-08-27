<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vendor') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Vendor ki apni vendor_id nikalo
$sql = "SELECT id FROM vendors WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$vendor = mysqli_fetch_assoc($result);
$vendor_id = $vendor['id'];

// Delete product (agar delete link click hua ho)
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $sql = "DELETE FROM products WHERE id = ? AND vendor_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $delete_id, $vendor_id);
    mysqli_stmt_execute($stmt);

    header("Location: my_product.php");
    exit();
}

// Sirf isi vendor ke products nikalo
$sql = "SELECT * FROM products WHERE vendor_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $vendor_id);
mysqli_stmt_execute($stmt);
$products_result = mysqli_stmt_get_result($stmt);

?>
<h2>My Products</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Image</th>
        <th>Name</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Action</th>
    </tr>

    <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
    <tr>
        <td><img src="../uploads/<?php echo $product['image']; ?>" width="60"></td>
        <td><?php echo $product['name']; ?></td>
        <td><?php echo $product['price']; ?></td>
        <td><?php echo $product['stock']; ?></td>
        <td>
            <a href="edit_product.php?id=<?php echo $product['id']; ?>">Edit</a> |
            <a href="my_product.php?delete_id=<?php echo $product['id']; ?>">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<br>
<a href="dashboard.php">Back to Dashboard</a>