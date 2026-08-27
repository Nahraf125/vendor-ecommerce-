<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vendor') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT status FROM vendors WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$vendor = mysqli_fetch_assoc($result);

if ($vendor['status'] != 'approved') {
    die("Your vendor account is pending approval. Please wait for admin to approve.");
}
?>

<h2>Vendor Dashboard</h2>
<p>Welcome, <?php echo $_SESSION['name']; ?>!</p>

<hr>

<h3>Menu</h3>
<ul>
    <li><a href="add_product.php">Add Product</a></li>
    <li><a href="my_product.php">My Products</a></li>
</ul>

<hr>

<a href="../auth/logout.php">Logout</a>