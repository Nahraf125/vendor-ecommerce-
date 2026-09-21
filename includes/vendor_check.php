<?php
// Ye file assume karti hai session_start() aur db.php pehle se ho chuke hain

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vendor') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT id, status FROM vendors WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$vendor = mysqli_fetch_assoc($result);

if ($vendor['status'] != 'approved') {
    require '../includes/header.php';
    ?>
    <div class="text-center py-5">
        <?php if ($vendor['status'] == 'rejected'): ?>
            <h2 class="text-danger">Application Rejected</h2>
            <p class="lead">Unfortunately, your vendor application was not approved.</p>
        <?php else: ?>
            <h2 class="text-warning">⏳ Pending Approval</h2>
            <p class="lead">Your vendor account is under review. We'll notify you once approved.</p>
        <?php endif; ?>
        <a href="/vendorwaala/customer/index.php" class="btn btn-primary mt-3">Back to Home</a>
    </div>
    <?php
    require '../includes/footer.php';
    exit();
}

$vendor_id = $vendor['id'];
?>