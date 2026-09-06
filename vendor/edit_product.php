<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vendor') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Vendor ki apni vendor_id nikalo
$sql = "SELECT id FROM vendors WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$vendor = mysqli_fetch_assoc($result);
$vendor_id = $vendor['id'];

// Agar form submit hua hai (Update button dabaya)
if (isset($_POST['update'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $sql = "UPDATE products SET name=?, description=?, price=?, stock=? WHERE id=? AND vendor_id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdiii", $name, $description, $price, $stock, $product_id, $vendor_id);
    mysqli_stmt_execute($stmt);

    header("Location: my_product.php");
    exit();
}

// Product ka purana data nikalo (dikhane ke liye)
$product_id = $_GET['id'];
$sql = "SELECT * FROM products WHERE id = ? AND vendor_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $product_id, $vendor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

require '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">

        <h2 class="mb-4">Edit Product</h2>

        <form action="edit_product.php" method="POST">

            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

            <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo $product['name']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control"><?php echo $product['description']; ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $product['price']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control" value="<?php echo $product['stock']; ?>" required>
            </div>

            <button type="submit" name="update" class="btn btn-primary">Update Product</button>

        </form>

    </div>
</div>
<a href="my_products.php">Back to My Products</a>

<?php require '../includes/footer.php'; ?>