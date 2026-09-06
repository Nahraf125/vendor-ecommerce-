<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}


$customer_id = $_SESSION['user_id'];

// Remove item (agar remove link click hua ho)
if (isset($_GET['remove_id'])) {
    $remove_id = $_GET['remove_id'];

    $sql = "DELETE FROM cart WHERE id = ? AND customer_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $remove_id, $customer_id);
    mysqli_stmt_execute($stmt);

    header("Location: cart.php");
    exit();
}

// Cart items nikalo, product details ke sath
$sql = "SELECT cart.id, cart.quantity, products.name, products.price, products.image
        FROM cart
        JOIN products ON cart.product_id = products.id
        WHERE cart.customer_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$total = 0;

require '../includes/header.php';
?>

<h2 class="mb-4">My Cart</h2>

<div class="table-responsive">
<table class="table table-striped table-bordered align-middle">
    <thead class="table-dark">
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($item = mysqli_fetch_assoc($result)): ?>
            <?php $subtotal = $item['price'] * $item['quantity']; ?>
            <?php $total += $subtotal; ?>
            <tr>
                <td><img src="<?php echo (strpos($product['image'], 'http') === 0) ? $product['image'] : '../uploads/' . $product['image']; ?>" ...></td>
                <td><?php echo $item['name']; ?></td>
                <td>Rs. <?php echo $item['price']; ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>Rs. <?php echo $subtotal; ?></td>
                <td><a href="cart.php?remove_id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger">Remove</a></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
    <h4 class="mb-0">Total: Rs. <?php echo $total; ?></h4>
    <a href="checkout.php" class="btn btn-success btn-lg w-100 w-md-auto">Proceed to Checkout</a>
</div>



<?php require '../includes/footer.php'; ?>