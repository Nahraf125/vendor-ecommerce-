<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<h2>Admin Dashboard</h2>
<p>Welcome, <?php echo $_SESSION['name']; ?>!</p>

<hr>

<h3>Menu</h3>
<ul>
    <li><a href="vendors.php">Manage Vendors</a></li>
    <li><a href="categories.php">Manage Categories</a></li>
</ul>

<hr>

<a href="../auth/logout.php">Logout</a>