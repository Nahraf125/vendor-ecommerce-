<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
require '../includes/header.php';

?>

<h2 class="mb-4">Admin Dashboard</h2>
<p class="lead">Welcome, <?php echo $_SESSION['name']; ?>!</p>

<div class="list-group mt-4" style="max-width: 300px;">
    <a href="vendors.php" class="list-group-item list-group-item-action">Manage Vendors</a>
    <a href="categories.php" class="list-group-item list-group-item-action">Manage Categories</a>
    <a href="orders.php" class="list-group-item list-group-item-action">Manage Orders</a>
</div>


<?php require '../includes/footer.php'; ?>