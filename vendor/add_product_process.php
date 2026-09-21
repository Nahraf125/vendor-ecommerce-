<?php
session_start();
require '../config/db.php';

require '../includes/vendor_check.php';

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


