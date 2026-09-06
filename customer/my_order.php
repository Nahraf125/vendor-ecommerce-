<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];

$sql = "SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$orders_result = mysqli_stmt_get_result($stmt);

require '../includes/header.php';
?>

<h2 class="mb-4">My Orders</h2>

<?php while ($order = mysqli_fetch_assoc($orders_result)): ?>

    <div class="card mb-3">
        <div class="card-body">

            <h5 class="card-title">Order #<?php echo $order['id']; ?></h5>
            <p class="mb-1"><strong>Date:</strong> <?php echo $order['created_at']; ?></p>
            <p class="mb-1"><strong>Address:</strong> <?php echo $order['address']; ?></p>

            <?php
            $sql2 = "SELECT order_items.quantity, order_items.price, order_items.status, products.name
                     FROM order_items
                     JOIN products ON order_items.product_id = products.id
                     WHERE order_items.order_id = ?";
            $stmt2 = mysqli_prepare($conn, $sql2);
            mysqli_stmt_bind_param($stmt2, "i", $order['id']);
            mysqli_stmt_execute($stmt2);
            $items_result = mysqli_stmt_get_result($stmt2);
            ?>

            <table class="table table-sm table-bordered mt-3">
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
                <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                <tr>
                    <td><?php echo $item['name']; ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>Rs. <?php echo $item['price']; ?></td>
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

            <p class="fw-bold">Total: Rs. <?php echo $order['total_amount']; ?></p>

        </div>
    </div>

<?php endwhile; ?>

<?php require '../includes/footer.php'; ?>