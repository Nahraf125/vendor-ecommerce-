<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Saare orders nikalo, customer ka naam bhi sath
$sql = "SELECT orders.*, users.name AS customer_name
        FROM orders
        JOIN users ON orders.customer_id = users.id
        ORDER BY orders.created_at DESC";
$result = mysqli_query($conn, $sql);

require '../includes/header.php';
?>

<h2 class="mb-4">All Orders (Overview)</h2>

<?php while ($order = mysqli_fetch_assoc($result)): ?>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Order #<?php echo $order['id']; ?> — <?php echo $order['customer_name']; ?></h5>
            <p class="mb-1"><strong>Date:</strong> <?php echo $order['created_at']; ?></p>
            <p class="mb-1"><strong>Address:</strong> <?php echo $order['address']; ?></p>
            <p class="mb-1"><strong>Total:</strong> Rs. <?php echo $order['total_amount']; ?></p>

            <?php
            $sql2 = "SELECT order_items.quantity, order_items.status, products.name AS product_name, vendors.store_name
                     FROM order_items
                     JOIN products ON order_items.product_id = products.id
                     JOIN vendors ON products.vendor_id = vendors.id
                     WHERE order_items.order_id = ?";
            $stmt2 = mysqli_prepare($conn, $sql2);
            mysqli_stmt_bind_param($stmt2, "i", $order['id']);
            mysqli_stmt_execute($stmt2);
            $items_result = mysqli_stmt_get_result($stmt2);
            ?>

            <table class="table table-sm table-bordered mt-2">
                <tr>
                    <th>Product</th>
                    <th>Vendor</th>
                    <th>Qty</th>
                    <th>Status</th>
                </tr>
                <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                <tr>
                    <td><?php echo $item['product_name']; ?></td>
                    <td><?php echo $item['store_name']; ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>
                        <?php if ($item['status'] == 'delivered'): ?>
                            <span class="badge bg-success">Delivered</span>
                        <?php elseif ($item['status'] == 'shipped'): ?>
                            <span class="badge bg-info text-dark">Shipped</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>

        </div>
    </div>

<?php endwhile; ?>

<?php require '../includes/footer.php'; ?>