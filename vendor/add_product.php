<?php
session_start();
require '../config/db.php';

require '../includes/vendor_check.php';;

require '../includes/header.php';
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