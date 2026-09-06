<?php
require '../config/db.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role'];

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hashed_password, $role);

if (mysqli_stmt_execute($stmt)) {

    $user_id = mysqli_insert_id($conn);

    if ($role == 'vendor') {
        $store_name = $_POST['store_name'];

        $sql2 = "INSERT INTO vendors (user_id, store_name, status) VALUES (?, ?, 'pending')";
        $stmt2 = mysqli_prepare($conn, $sql2);
        mysqli_stmt_bind_param($stmt2, "is", $user_id, $store_name);
        mysqli_stmt_execute($stmt2);
    }

    header("Location: /vendorwaala/customer/index.php");
    exit();

} else {
    echo "Error: " . mysqli_error($conn);
}
?>