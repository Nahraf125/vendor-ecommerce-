<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

require '../includes/header.php';
$order_id = $_GET['order_id'];
?>

<div class="text-center py-5">
    <h2 class="text-success">✓ Order Placed Successfully!</h2>
    <p class="lead">Your order #<?php echo $order_id; ?> has been placed.</p>
    <a href="index.php" class="btn btn-primary mt-3">Continue Shopping</a>
</div>

<?php require '../includes/footer.php'; ?>