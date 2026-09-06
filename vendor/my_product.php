<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vendor') {
    header("Location: ../auth/login.php");
    exit();
}

require '../includes/header.php';

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
<h2 class="mb-4">My Products</h2>

<div class="table-responsive">
<table class="table table-striped table-bordered align-middle">
    <thead class="table-dark">
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
        <tr>
            <td><img src="<?php echo (strpos($product['image'], 'http') === 0) ? $product['image'] : '../uploads/' . $product['image']; ?>" ...></td>
            <td><?php echo $product['name']; ?></td>
            <td>Rs. <?php echo $product['price']; ?></td>
            <td><?php echo $product['stock']; ?></td>
            <td>
                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                <a href="my_products.php?delete_id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>



<?php require '../includes/footer.php'; ?>