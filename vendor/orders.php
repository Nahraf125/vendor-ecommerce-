<?php
session_start();
require '../config/db.php';

require '../includes/vendor_check.php';

// Status update (agar dropdown se update hua)
if (isset($_POST['update_status'])) {
    $item_id = $_POST['item_id'];
    $new_status = $_POST['status'];

    // Security: sirf apne product ka item update kar sake
    $sql_check = "SELECT order_items.id FROM order_items
                  JOIN products ON order_items.product_id = products.id
                  WHERE order_items.id = ? AND products.vendor_id = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "ii", $item_id, $vendor_id);
    mysqli_stmt_execute($stmt_check);
    $check_result = mysqli_stmt_get_result($stmt_check);

    if (mysqli_fetch_assoc($check_result)) {
        $sql_update = "UPDATE order_items SET status = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "si", $new_status, $item_id);
        mysqli_stmt_execute($stmt_update);
    }

    header("Location: orders.php");
    exit();
}

// Is vendor ke saare order_items nikalo, product + customer + order info ke sath
$sql = "SELECT order_items.id, order_items.quantity, order_items.price, order_items.status,
               products.name AS product_name,
               orders.id AS order_id, orders.address, orders.created_at,
               users.name AS customer_name
        FROM order_items
        JOIN products ON order_items.product_id = products.id
        JOIN orders ON order_items.order_id = orders.id
        JOIN users ON orders.customer_id = users.id
        WHERE products.vendor_id = ?
        ORDER BY orders.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $vendor_id);
mysqli_stmt_execute($stmt);
$items_result = mysqli_stmt_get_result($stmt);

require '../includes/header.php';
?>

<h2 class="mb-4">My Orders</h2>

<div class="table-responsive">
<table class="table table-striped table-bordered align-middle">
    <thead class="table-dark">
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Address</th>
            <th>Status</th>
            <th>Update</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
        <tr>
            <td><?php echo $item['order_id']; ?></td>
            <td><?php echo $item['customer_name']; ?></td>
            <td><?php echo $item['product_name']; ?></td>
            <td><?php echo $item['quantity']; ?></td>
            <td><?php echo $item['address']; ?></td>
            <td>
                <?php if ($item['status'] == 'delivered'): ?>
                    <span class="badge bg-success">Delivered</span>
                <?php elseif ($item['status'] == 'shipped'): ?>
                    <span class="badge bg-info text-dark">Shipped</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark">Pending</span>
                <?php endif; ?>
            </td>
            <td>
                <form action="orders.php" method="POST" class="d-flex gap-2">
                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    <select name="status" class="form-select form-select-sm">
                        <option value="pending" <?php if ($item['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                        <option value="shipped" <?php if ($item['status'] == 'shipped') echo 'selected'; ?>>Shipped</option>
                        <option value="delivered" <?php if ($item['status'] == 'delivered') echo 'selected'; ?>>Delivered</option>
                    </select>
                    <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>
<?php require '../includes/footer.php'; ?>