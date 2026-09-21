<?php
session_start();
require '../config/db.php';
require '../config/mailer.php';

if (!isset($_SESSION['pending_verification_user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['pending_verification_user'];

// Naya OTP generate karo
$otp = rand(100000, 999999);
$expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

$sql = "UPDATE users SET otp_code = ?, otp_expires_at = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssi", $otp, $expires, $user_id);
mysqli_stmt_execute($stmt);

// User ka email nikalo
$sql2 = "SELECT email FROM users WHERE id = ?";
$stmt2 = mysqli_prepare($conn, $sql2);
mysqli_stmt_bind_param($stmt2, "i", $user_id);
mysqli_stmt_execute($stmt2);
$result2 = mysqli_stmt_get_result($stmt2);
$user = mysqli_fetch_assoc($result2);

sendOTPEmail($user['email'], $otp);

header("Location: verify_otp.php?resent=1");
exit();
?>