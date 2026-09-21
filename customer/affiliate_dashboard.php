<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM affiliates WHERE user_id = ? AND status = 'approved'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$affiliate = mysqli_fetch_assoc($result);

if (!$affiliate) {
    header("Location: become_affiliate.php");
    exit();
}

// Total earnings nikalo
$sql2 = "SELECT SUM(amount) AS total FROM affiliate_commissions WHERE affiliate_id = ?";
$stmt2 = mysqli_prepare($conn, $sql2);
mysqli_stmt_bind_param($stmt2, "i", $affiliate['id']);
mysqli_stmt_execute($stmt2);
$earnings = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2))['total'];
$earnings = $earnings ? $earnings : 0;

// Total conversions (orders) count
$sql3 = "SELECT COUNT(*) AS cnt FROM affiliate_commissions WHERE affiliate_id = ?";
$stmt3 = mysqli_prepare($conn, $sql3);
mysqli_stmt_bind_param($stmt3, "i", $affiliate['id']);
mysqli_stmt_execute($stmt3);
$conversions = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt3))['cnt'];

require '../includes/header.php';
?>

<h2 class="mb-4">Affiliate Dashboard</h2>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card stat-card text-white bg-primary">
            <div class="card-body">
                <h6>Total Clicks</h6>
                <h2><?php echo $affiliate['clicks']; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card stat-card text-white" style="background-color: #00b894;">
            <div class="card-body">
                <h6>Conversions</h6>
                <h2><?php echo $conversions; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card stat-card text-white" style="background-color: #fdcb6e;">
            <div class="card-body">
                <h6>Total Earnings</h6>
                <h2>Rs. <?php echo number_format($earnings); ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Your Referral Code</h5>
        <p>Share this code with your friends, or add <code>?ref=<?php echo $affiliate['referral_code']; ?></code> to any product link.</p>
        <div class="input-group mb-3">
            <input type="text" class="form-control" value="<?php echo $affiliate['referral_code']; ?>" id="refCode" readonly>
            <button class="btn btn-primary" onclick="navigator.clipboard.writeText(document.getElementById('refCode').value)">Copy</button>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>