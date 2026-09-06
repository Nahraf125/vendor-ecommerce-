<?php
require '../config/db.php';

// Search aur filter ki values URL se lo (agar hain)
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';

// Base query
$sql = "SELECT products.*, vendors.store_name, categories.name AS category_name
        FROM products
        JOIN vendors ON products.vendor_id = vendors.id
        JOIN categories ON products.category_id = categories.id
        WHERE products.name LIKE ?";

$search_param = "%" . $search . "%";

// Agar category filter bhi select hui hai, to query mein add karo
if ($category_id != '') {
    $sql .= " AND products.category_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $search_param, $category_id);
} else {
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $search_param);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Categories nikalo dropdown ke liye
$cat_result = mysqli_query($conn, "SELECT * FROM categories");
?>

<?php
session_start();
require '../config/db.php';
require '../includes/header.php';
?>

<div class="row">

    <!-- LEFT SIDEBAR -->
    <div class="col-md-3 mb-4">
        <div class="card category-sidebar">
            <div class="card-header bg-primary text-white fw-bold">
                📂 All Categories
            </div>
            <div class="list-group list-group-flush">
                <a href="index.php" class="list-group-item list-group-item-action">All Products</a>
                <?php
                $sidebar_cats = mysqli_query($conn, "SELECT * FROM categories");
                while ($cat = mysqli_fetch_assoc($sidebar_cats)):
                ?>
                    <a href="index.php?category_id=<?php echo $cat['id']; ?>" class="list-group-item list-group-item-action">
                        <?php echo $cat['name']; ?>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="col-md-9">

        <!-- SEARCH BAR -->
        <form action="index.php" method="GET" class="d-flex gap-2 mb-4">
            <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?php echo $search; ?>">
            <button type="submit" class="btn btn-primary px-4">Search</button>
        </form>

        <!-- HERO BANNER -->
        <div class="hero-banner mb-4 p-5 rounded text-white">
            <h6 class="text-uppercase mb-1">🔥 Popular Products</h6>
            <h1 class="fw-bold">Summer Collection 2026</h1>
            <p class="lead mb-3">Discount up to 30% off this week</p>
            <a href="#products" class="btn btn-light fw-bold">Shop Now</a>
        </div>

        <!-- PROMO TILES -->
        <div class="row mb-4">
            <div class="col-md-3 col-6 mb-3">
                <a href="index.php" class="promo-tile tile-1">
                    <small>UP TO 30% OFF</small>
                    <h6>Electronics</h6>
                </a>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <a href="index.php" class="promo-tile tile-2">
                    <small>NEW ARRIVALS</small>
                    <h6>Fashion</h6>
                </a>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <a href="index.php" class="promo-tile tile-3">
                    <small>BEST DEALS</small>
                    <h6>Home & Living</h6>
                </a>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <a href="index.php" class="promo-tile tile-4">
                    <small>LIMITED TIME</small>
                    <h6>Accessories</h6>
                </a>
            </div>
        </div>

        <!-- PRODUCTS GRID -->
        <h4 id="products" class="mb-3">All Products</h4>

        <div class="row">
            <?php while ($product = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?php echo (strpos($product['image'], 'http') === 0) ? $product['image'] : '../uploads/' . $product['image']; ?>" ...>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo $product['name']; ?></h5>
                            <p class="card-text mb-1"><small class="text-muted">By: <?php echo $product['store_name']; ?></small></p>
                            <p class="card-text mb-1"><small class="text-muted"><?php echo $product['category_name']; ?></small></p>
                            <p class="card-text fw-bold text-primary">Rs. <?php echo $product['price']; ?></p>
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary mt-auto">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    </div>
</div>

<?php require '../includes/footer.php'; ?>