<?php
session_start();
require '../config/db.php';

// Agar login nahi hai, to login page pe bhej do
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$quantity = $_POST['quantity'];

// Check karo ye product pehle se cart mein hai ya nahi
$sql = "SELECT * FROM cart WHERE customer_id = ? AND product_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $customer_id, $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$existing = mysqli_fetch_assoc($result);

if ($existing) {
    // Pehle se hai, to quantity badha do
    $new_quantity = $existing['quantity'] + $quantity;

    $sql2 = "UPDATE cart SET quantity = ? WHERE id = ?";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "ii", $new_quantity, $existing['id']);
    mysqli_stmt_execute($stmt2);

} else {
    // Naya row banao
    $sql2 = "INSERT INTO cart (customer_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "iii", $customer_id, $product_id, $quantity);
    mysqli_stmt_execute($stmt2);
}

header("Location: cart.php");
exit();
?>