<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: ../auth/login.php");
    exit();
}
?>
<h2>Customer Dashboard</h2>
<p>Welcome, <?php echo $_SESSION['name']; ?>!</p>
<a href="my_order.php">My Order</a>
<a href="../auth/logout.php">Logout</a>