<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vendor') {
    header("Location: ../auth/login.php");
    exit();
}

$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price'];
$stock = $_POST['stock'];
$category_id = $_POST['category_id'];
$vendor_id = $_POST['vendor_id'];

// Image upload handle karna
$image_name = time() . '_' . $_FILES['image']['name'];
$target_path = '../uploads/' . $image_name;
move_uploaded_file($_FILES['image']['tmp_name'], $target_path);

$sql = "INSERT INTO products (vendor_id, category_id, name, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iissdis", $vendor_id, $category_id, $name, $description, $price, $stock, $image_name);
mysqli_stmt_execute($stmt);

header("Location: dashboard.php");
exit();
?>