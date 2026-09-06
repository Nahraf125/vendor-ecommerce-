<?php
session_start();
require '../config/db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {

    if ($user['role'] == 'vendor') {
        $sql_v = "SELECT status, rejection_reason FROM vendors WHERE user_id = ?";
        $stmt_v = mysqli_prepare($conn, $sql_v);
        mysqli_stmt_bind_param($stmt_v, "i", $user['id']);
        mysqli_stmt_execute($stmt_v);
        $result_v = mysqli_stmt_get_result($stmt_v);
        $vendor_data = mysqli_fetch_assoc($result_v);

        if ($vendor_data['status'] == 'rejected') {
            header("Location: /vendorwaala/customer/index.php?error=rejected&reason=" . urlencode($vendor_data['rejection_reason']));
            exit();
        }
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] == 'admin') {
        header("Location: /vendorwaala/admin/dashboard.php");
    } elseif ($user['role'] == 'vendor') {
        header("Location: /vendorwaala/vendor/dashboard.php");
    } else {
        header("Location: /vendorwaala/customer/index.php");
    }
    exit();

} else {
    header("Location: /vendorwaala/customer/index.php?error=invalid");
    exit();
}
?>