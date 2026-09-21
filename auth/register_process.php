<?php
session_start();
require '../config/db.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role'];
$terms_accepted = isset($_POST['terms']) ? 1 : 0;
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check karo ye email pehle se hai kya
$check_sql = "SELECT id, is_verified FROM users WHERE email = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "s", $email);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);
$existing_user = mysqli_fetch_assoc($check_result);

if ($existing_user && $existing_user['is_verified'] == 1) {
    // Already verified account hai isi email se — register nahi hone denge
    header("Location: register.php?error=exists");
    exit();
}

$otp = rand(100000, 999999);
$expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

if ($existing_user && $existing_user['is_verified'] == 0) {
    // Purana adhoora account tha — usay UPDATE karo (overwrite)
    $user_id = $existing_user['id'];

    $sql = "UPDATE users SET name=?, password=?, role=?, terms_accepted=?, otp_code=?, otp_expires_at=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssissi", $name, $hashed_password, $role, $terms_accepted, $otp, $expires, $user_id);
    mysqli_stmt_execute($stmt);

    // Agar vendor hai, purani vendor entry hatao (agar thi), nayi banao
    mysqli_query($conn, "DELETE FROM vendors WHERE user_id = $user_id");

} else {
    // Bilkul naya user — insert karo
    $sql = "INSERT INTO users (name, email, password, role, terms_accepted, otp_code, otp_expires_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssisss", $name, $email, $hashed_password, $role, $terms_accepted, $otp, $expires);
    mysqli_stmt_execute($stmt);

    $user_id = mysqli_insert_id($conn);
}

// Agar vendor role hai, vendor details daalo
if ($role == 'vendor') {
    $store_name = $_POST['store_name'];
    $business_type = $_POST['business_type'];
    $phone = $_POST['phone'];
    $cnic = $_POST['cnic'];
    $business_address = $_POST['business_address'];

    $sql2 = "INSERT INTO vendors (user_id, store_name, business_type, phone, cnic, business_address, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "isssss", $user_id, $store_name, $business_type, $phone, $cnic, $business_address);
    mysqli_stmt_execute($stmt2);
}

require '../config/mailer.php';
sendOTPEmail($email, $otp);

$_SESSION['pending_verification_user'] = $user_id;
header("Location: verify_otp.php");
exit();
?>