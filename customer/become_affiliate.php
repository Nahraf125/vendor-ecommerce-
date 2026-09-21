<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check karo pehle se apply kiya hai ya nahi
$sql = "SELECT * FROM affiliates WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$existing = mysqli_fetch_assoc($result);

// Agar form submit hua (aur pehle se apply nahi kiya)
if (isset($_POST['apply']) && !$existing) {
    // Unique referral code banao
    $referral_code = strtoupper(substr(md5(uniqid()), 0, 8));

    $sql2 = "INSERT INTO affiliates (user_id, referral_code) VALUES (?, ?)";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "is", $user_id, $referral_code);
    mysqli_stmt_execute($stmt2);

    header("Location: become_affiliate.php");
    exit();
}

require '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 text-center">

        <h2 class="mb-4">Affiliate Program</h2>

        <?php if ($existing): ?>

            <?php if ($existing['status'] == 'pending'): ?>
                <div class="alert alert-warning">Your affiliate application is pending admin approval.</div>

            <?php elseif ($existing['status'] == 'rejected'): ?>
                <div class="alert alert-danger">Your affiliate application was rejected.</div>

            <?php else: ?>
                <div class="alert alert-success">You're an approved affiliate! 🎉</div>
                <a href="affiliate_dashboard.php" class="btn btn-primary">Go to Affiliate Dashboard</a>
            <?php endif; ?>

        <?php else: ?>
            <p class="text-muted mb-4">Earn commission by sharing product links with your friends and followers. Every purchase made through your link earns you a reward.</p>
            <form action="become_affiliate.php" method="POST">
                <button type="submit" name="apply" class="btn btn-primary btn-lg">Apply to Become an Affiliate</button>
            </form>
        <?php endif; ?>

    </div>
</div>

<?php require '../includes/footer.php'; ?>