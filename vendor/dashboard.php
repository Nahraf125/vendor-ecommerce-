<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vendor') {
    header("Location: ../auth/login.php");
    exit();
}
require '../includes/header.php';


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

<h2 class="mb-4">Vendor Dashboard</h2>
<p class="lead">Welcome, <?php echo $_SESSION['name']; ?>!</p>

<div class="list-group mt-4" style="max-width: 300px;">
    <a href="add_product.php" class="list-group-item list-group-item-action">Add Product</a>
    <a href="my_product.php" class="list-group-item list-group-item-action">My Product</a>
    <a href="orders.php" class="list-group-item list-group-item-action">My Orders</a>
</div>

<?php require '../includes/footer.php'; ?>