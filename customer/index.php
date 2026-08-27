<?php
require '../config/db.php';

// Saare products nikalo, vendor aur category ka naam bhi sath
$sql = "SELECT products.*, vendors.store_name, categories.name AS category_name
        FROM products
        JOIN vendors ON products.vendor_id = vendors.id
        JOIN categories ON products.category_id = categories.id";
$result = mysqli_query($conn, $sql);
?>

<h2>All Products</h2>

<div style="display: flex; flex-wrap: wrap; gap: 20px;">

    <?php while ($product = mysqli_fetch_assoc($result)): ?>
        <div style="border: 1px solid #ccc; padding: 10px; width: 200px;">
            <img src="../uploads/<?php echo $product['image']; ?>" width="180"><br>
            <strong><?php echo $product['name']; ?></strong><br>
            <small>By: <?php echo $product['store_name']; ?></small><br>
            <small>Category: <?php echo $product['category_name']; ?></small><br>
            <p>Rs. <?php echo $product['price']; ?></p>
            <a href="product.php?id=<?php echo $product['id']; ?>">View Details</a>
        </div>
    <?php endwhile; ?>

</div>