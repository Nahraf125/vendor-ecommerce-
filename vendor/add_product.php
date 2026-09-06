<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'vendor') {
    header("Location: ../auth/login.php");
    exit();
}

require '../includes/header.php';

// Vendor ki apni vendor_id nikalni hai (users table se link hai)
$user_id = $_SESSION['user_id'];
$sql = "SELECT id FROM vendors WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$vendor = mysqli_fetch_assoc($result);
$vendor_id = $vendor['id'];

// Categories list karo dropdown ke liye
$cat_result = mysqli_query($conn, "SELECT * FROM categories");
?>

<div class="row justify-content-center">
    <div class="col-md-6">

        <h2 class="mb-4">Add Product</h2>

        <form action="add_product_process.php" method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select" required>
                    <?php while ($cat = mysqli_fetch_assoc($cat_result)): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Product Image</label>
                <input type="file" name="image" class="form-control" accept="image/*" required>
            </div>

            <input type="hidden" name="vendor_id" value="<?php echo $vendor_id; ?>">

            <button type="submit" class="btn btn-primary">Add Product</button>

        </form>

    </div>
</div>
<?php require '../includes/footer.php'; ?>