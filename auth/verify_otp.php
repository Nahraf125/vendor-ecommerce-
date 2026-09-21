<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['pending_verification_user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['pending_verification_user'];

if (isset($_POST['otp'])) {
    $entered_otp = $_POST['otp'];

    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($entered_otp == $user['otp_code'] && strtotime($user['otp_expires_at']) > time()) {

        // Sahi OTP! Account verify karo
        $sql2 = "UPDATE users SET is_verified = 1, otp_code = NULL WHERE id = ?";
        $stmt2 = mysqli_prepare($conn, $sql2);
        mysqli_stmt_bind_param($stmt2, "i", $user_id);
        mysqli_stmt_execute($stmt2);

        unset($_SESSION['pending_verification_user']);

        header("Location: login.php?verified=1");
        exit();

    } else {
        $error = "Invalid or expired code. Please try again.";
    }
}

require '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5 text-center">
        <h2 class="mb-3">Verify Your Email</h2>
        <p class="text-muted mb-4">We've sent a 6-digit code to your email. Enter it below to activate your account.</p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="verify_otp.php" method="POST">
            <input type="text" name="otp" class="form-control form-control-lg text-center mb-3" placeholder="Enter 6-digit code" maxlength="6" required>
            <button type="submit" class="btn btn-primary w-100">Verify</button>
        </form>
        <?php if (isset($_GET['resent'])): ?>
            <div class="alert alert-success mt-3">A new code has been sent to your email!</div>
        <?php endif; ?>

        <p class="mt-3">
            Didn't receive the code? <a href="resend_otp.php">Resend Code</a>
        </p>
    </div>
</div>

<?php require '../includes/footer.php'; ?>