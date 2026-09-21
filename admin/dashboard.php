<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Stat 1: Total Approved Vendors
$total_vendors = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM vendors WHERE status = 'approved'"))['cnt'];

// Stat 2: Total Products
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM products"))['cnt'];

// Stat 3: Total Orders
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM orders"))['cnt'];

// Stat 4: Total Revenue
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) AS total FROM orders"))['total'];
$total_revenue = $total_revenue ? $total_revenue : 0;

// Chart Data: Orders per day, pichle 7 din
$sql = "SELECT DATE(created_at) AS day, COUNT(*) AS cnt FROM orders
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)";
$result = mysqli_query($conn, $sql);

// Database se jo mila usay ek array mein daalo (date => count)
$orders_by_date = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders_by_date[$row['day']] = $row['cnt'];
}

// Pichle 7 din ki poori list banao (chahe order ho ya na ho, 0 dikhna chahiye)
$chart_labels = [];
$chart_values = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chart_labels[] = date('D', strtotime($date));
    $chart_values[] = isset($orders_by_date[$date]) ? $orders_by_date[$date] : 0;
}

require '../includes/header.php';
?>

<h2 class="mb-4">Admin Dashboard</h2>
<p class="lead">Welcome, <?php echo $_SESSION['name']; ?>!</p>

<!-- STAT CARDS -->
<div class="row mb-4">
    <div class="col-md-3 col-6 mb-3">
        <div class="card stat-card text-white bg-primary">
            <div class="card-body">
                <h6>Vendors</h6>
                <h2><?php echo $total_vendors; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card stat-card text-white" style="background-color: #00b894;">
            <div class="card-body">
                <h6>Products</h6>
                <h2><?php echo $total_products; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card stat-card text-white" style="background-color: #fdcb6e;">
            <div class="card-body">
                <h6>Orders</h6>
                <h2><?php echo $total_orders; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="card stat-card text-white" style="background-color: #e17055;">
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
        <h5 class="card-title">Orders — Last 7 Days</h5>
        <canvas id="ordersChart" height="80"></canvas>
    </div>
</div>

<div class="list-group mt-4" style="max-width: 300px;">
    <a href="vendors.php" class="list-group-item list-group-item-action">Manage Vendors</a>
    <a href="categories.php" class="list-group-item list-group-item-action">Manage Categories</a>
    <a href="orders.php" class="list-group-item list-group-item-action">Manage Orders</a>
    <a href="messages.php" class="list-group-item list-group-item-action">Contact Messages</a>
    <a href="affiliates.php" class="list-group-item list-group-item-action">Manage Affiliates</a>
    <a href="users.php" class="list-group-item list-group-item-action">Manage Users</a>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('ordersChart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'Orders',
                data: <?php echo json_encode($chart_values); ?>,
                backgroundColor: '#6C5CE7',
                borderRadius: 6
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
</script>

<?php require '../includes/footer.php'; ?>