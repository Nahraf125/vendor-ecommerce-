<?php

session_start();
require '../config/db.php';

// Agar referral link se aaya hai, cookie set karo
if (isset($_GET['ref'])) {
    $ref_code = $_GET['ref'];

    // Confirm karo ye valid, approved affiliate hai
    $sql_ref = "SELECT id FROM affiliates WHERE referral_code = ? AND status = 'approved'";
    $stmt_ref = mysqli_prepare($conn, $sql_ref);
    mysqli_stmt_bind_param($stmt_ref, "s", $ref_code);
    mysqli_stmt_execute($stmt_ref);
    $result_ref = mysqli_stmt_get_result($stmt_ref);

    if (mysqli_fetch_assoc($result_ref)) {
        // Cookie set karo 30 din ke liye
        setcookie("affiliate_ref", $ref_code, time() + (30 * 24 * 60 * 60), "/");

        // Click count badhao
        $sql_click = "UPDATE affiliates SET clicks = clicks + 1 WHERE referral_code = ?";
        $stmt_click = mysqli_prepare($conn, $sql_click);
        mysqli_stmt_bind_param($stmt_click, "s", $ref_code);
        mysqli_stmt_execute($stmt_click);
    }
}


require '../includes/header.php';

$product_id = $_GET['id'];

// Review submit hua ho to
if (isset($_POST['submit_review'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../auth/login.php");
        exit();
    }

    $customer_id = $_SESSION['user_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $sql = "INSERT INTO reviews (product_id, customer_id, rating, comment) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiis", $product_id, $customer_id, $rating, $comment);
    mysqli_stmt_execute($stmt);

    header("Location: product.php?id=" . $product_id);
    exit();
}

// Product ki detail nikalo
$sql = "SELECT products.*, vendors.store_name, categories.name AS category_name
        FROM products
        JOIN vendors ON products.vendor_id = vendors.id
        JOIN categories ON products.category_id = categories.id
        WHERE products.id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    die("Product not found.");
}

// Is product ke reviews nikalo, customer ka naam bhi sath
$sql2 = "SELECT reviews.*, users.name AS customer_name
         FROM reviews
         JOIN users ON reviews.customer_id = users.id
         WHERE reviews.product_id = ?
         ORDER BY reviews.created_at DESC";
$stmt2 = mysqli_prepare($conn, $sql2);
mysqli_stmt_bind_param($stmt2, "i", $product_id);
mysqli_stmt_execute($stmt2);
$reviews_result = mysqli_stmt_get_result($stmt2);

// Average rating nikalo
$sql3 = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews FROM reviews WHERE product_id = ?";
$stmt3 = mysqli_prepare($conn, $sql3);
mysqli_stmt_bind_param($stmt3, "i", $product_id);
mysqli_stmt_execute($stmt3);
$avg_result = mysqli_stmt_get_result($stmt3);
$avg_data = mysqli_fetch_assoc($avg_result);
?>

<div class="row">

    <div class="col-md-5">
        <img src="<?php echo (strpos($product['image'], 'http') === 0) ? $product['image'] : '../uploads/' . $product['image']; ?>" class="img-fluid rounded" style="max-height: 400px; width: 100%; object-fit: cover;">
    </div>

    <div class="col-md-7">
        <h2><?php echo $product['name']; ?></h2>
        <p class="text-muted">Sold by: <?php echo $product['store_name']; ?> | Category: <?php echo $product['category_name']; ?></p>
        <h3 class="text-primary">Rs. <?php echo $product['price']; ?></h3>
        <p><?php echo $product['stock']; ?> in stock</p>
        <p><?php echo $product['description']; ?></p>

        <?php if ($avg_data['total_reviews'] > 0): ?>
            <p><span class="badge bg-warning text-dark">★ <?php echo round($avg_data['avg_rating'], 1); ?> / 5</span>
            <small class="text-muted"> (<?php echo $avg_data['total_reviews']; ?> reviews)</small></p>
        <?php else: ?>
            <p class="text-muted">No reviews yet.</p>
        <?php endif; ?>

        <?php if ($product['stock'] > 0): ?>
            <form action="add_to_cart.php" method="POST" class="d-flex gap-2 align-items-end">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <div>
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" value="1" min="1" max="<?php echo $product['stock']; ?>">
                </div>
                <button type="submit" class="btn btn-success">Add to Cart</button>
            </form>
        <?php else: ?>
            <p class="text-danger fw-bold">Out of Stock</p>
        <?php endif; ?>
    </div>

</div>

<hr class="my-4">

<?php
// Agar user login hai aur approved affiliate hai, to share link dikhao
if (isset($_SESSION['user_id'])) {
    $sql_check_aff = "SELECT referral_code FROM affiliates WHERE user_id = ? AND status = 'approved'";
    $stmt_check_aff = mysqli_prepare($conn, $sql_check_aff);
    mysqli_stmt_bind_param($stmt_check_aff, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt_check_aff);
    $result_check_aff = mysqli_stmt_get_result($stmt_check_aff);
    $my_affiliate = mysqli_fetch_assoc($result_check_aff);

    if ($my_affiliate) {
        
            // Localhost ho to http, warna https use karo
            $protocol = ($_SERVER['HTTP_HOST'] == 'localhost') ? "http://" : "https://";

            // Agar local XAMPP hai, folder naam add karo. Live server pe (InfinityFree) ye khali rahega.
            $base_folder = ($_SERVER['HTTP_HOST'] == 'localhost') ? "/vendorwaala" : "";

            $current_url = $protocol . $_SERVER['HTTP_HOST'] . $base_folder . "/customer/product.php?id=" . $product_id;
            $share_link = $current_url . "&ref=" . $my_affiliate['referral_code'];
        ?>
        <div class="card mb-4" style="background-color: var(--light-bg); border: 2px dashed var(--primary-color);">
            <div class="card-body">
                <h6 class="mb-2">💰 Share this product & earn 5% commission!</h6>
                <div class="input-group">
                    <input type="text" class="form-control" value="<?php echo $share_link; ?>" id="shareLink" readonly>
                    <button class="btn btn-primary" onclick="copyShareLink()">Copy Link</button>
                </div>
            </div>
        </div>
        <script>
            function copyShareLink() {
                const input = document.getElementById('shareLink');
                navigator.clipboard.writeText(input.value);
                event.target.innerText = 'Copied!';
                setTimeout(() => { event.target.innerText = 'Copy Link'; }, 2000);
            }
        </script>
        <?php
    }
}
?>


<h4>Write a Review</h4>
<form action="product.php?id=<?php echo $product_id; ?>" method="POST" class="mb-4" style="max-width: 500px;">
    <div class="mb-3">
        <label class="form-label">Rating</label>
        <select name="rating" class="form-select" required>
            <option value="5">5 - Excellent</option>
            <option value="4">4 - Good</option>
            <option value="3">3 - Average</option>
            <option value="2">2 - Poor</option>
            <option value="1">1 - Bad</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Comment</label>
        <textarea name="comment" class="form-control" required></textarea>
    </div>
    <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
</form>

<h4>Customer Reviews</h4>
<?php while ($review = mysqli_fetch_assoc($reviews_result)): ?>
    <div class="border-bottom py-2">
        <strong><?php echo $review['customer_name']; ?></strong>
        <span class="badge bg-warning text-dark">★ <?php echo $review['rating']; ?>/5</span>
        <p class="mb-1"><?php echo $review['comment']; ?></p>
        <small class="text-muted"><?php echo $review['created_at']; ?></small>
    </div>
<?php endwhile; ?>


<?php require '../includes/footer.php'; ?>