<?php
session_start();
require '../config/db.php';

require '../includes/vendor_check.php';

if ($vendor['status'] != 'approved') {
    require '../includes/header.php';
    ?>
    <div class="text-center py-5">
        <?php if ($vendor['status'] == 'rejected'): ?>
            <h2 class="text-danger">Application Rejected</h2>
            <p class="lead">Unfortunately, your vendor application was not approved.</p>
        <?php else: ?>
            <h2 class="text-warning">⏳ Pending Approval</h2>
            <p class="lead">Your vendor account is under review. We'll notify you once approved.</p>
        <?php endif; ?>
        <a href="/vendorwaala/customer/index.php" class="btn btn-primary mt-3">Back to Home</a>
    </div>
    <?php
    require '../includes/footer.php';
    exit();
}

$vendor_id = $vendor['id'];

// Stat 1: My Products
$sql = "SELECT COUNT(*) AS cnt FROM products WHERE vendor_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $vendor_id);
mysqli_stmt_execute($stmt);
$total_products = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['cnt'];

// Stat 2: My Orders (items) + Stat 3: My Revenue
$sql = "SELECT COUNT(*) AS cnt, SUM(order_items.price * order_items.quantity) AS revenue
        FROM order_items
        JOIN products ON order_items.product_id = products.id
        WHERE products.vendor_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $vendor_id);
mysqli_stmt_execute($stmt);
$order_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$total_orders = $order_data['cnt'];
$total_revenue = $order_data['revenue'] ? $order_data['revenue'] : 0;

// Chart Data: Top 5 selling products (by quantity sold)
$sql = "SELECT products.name, SUM(order_items.quantity) AS total_sold
        FROM order_items
        JOIN products ON order_items.product_id = products.id
        WHERE products.vendor_id = ?
        GROUP BY products.id
        ORDER BY total_sold DESC
        LIMIT 5";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $vendor_id);
mysqli_stmt_execute($stmt);
$top_result = mysqli_stmt_get_result($stmt);

$product_labels = [];
$product_sales = [];
while ($row = mysqli_fetch_assoc($top_result)) {
    $product_labels[] = $row['name'];
    $product_sales[] = $row['total_sold'];
}

require '../includes/header.php';
?>

<h2 class="mb-4">Vendor Dashboard</h2>
<p class="lead">Welcome, <?php echo $_SESSION['name']; ?>!</p>

<!-- STAT CARDS -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card stat-card text-white bg-primary">
            <div class="card-body">
                <h6>My Products</h6>
                <h2><?php echo $total_products; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card stat-card text-white" style="background-color: #fdcb6e;">
            <div class="card-body">
                <h6>Items Sold</h6>
                <h2><?php echo $total_orders; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card stat-card text-white" style="background-color: #00b894;">
            <div class="card-body">
                <h6>Revenue</h6>
                <h2>Rs. <?php echo number_format($total_revenue); ?></h2>
            </div>
        </div>
    </div>
</div>

<!-- CHART -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Top Selling Products</h5>
        <?php if (count($product_labels) > 0): ?>
            <canvas id="productsChart" height="80"></canvas>
        <?php else: ?>
            <p class="text-muted">No sales data yet.</p>
        <?php endif; ?>
    </div>
</div>

<div class="list-group mt-4" style="max-width: 300px;">
    <a href="add_product.php" class="list-group-item list-group-item-action">Add Product</a>
    <a href="my_product.php" class="list-group-item list-group-item-action">My Product</a>
    <a href="orders.php" class="list-group-item list-group-item-action">My Orders</a>
</div>

<?php if (count($product_labels) > 0): ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('productsChart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($product_labels); ?>,
            datasets: [{
                label: 'Units Sold',
                data: <?php echo json_encode($product_sales); ?>,
                backgroundColor: '#a29bfe',
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
</script>
<?php endif; ?>

<?php require '../includes/footer.php'; ?>