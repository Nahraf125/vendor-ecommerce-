<?php
session_start();
require '../config/db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require '../includes/header.php';

$customer_id = $_SESSION['user_id'];

// Cart items nikalo (order.php jaisa hi)
$sql = "SELECT cart.id, cart.quantity, products.id AS product_id, products.name, products.price
        FROM cart
        JOIN products ON cart.product_id = products.id
        WHERE cart.customer_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$cart_items = [];
$total = 0;

while ($item = mysqli_fetch_assoc($result)) {
    $cart_items[] = $item;
    $total += $item['price'] * $item['quantity'];
}

if (count($cart_items) == 0) {
    die("Your cart is empty.");
}
?>

<h2 class="mb-4">Checkout</h2>

<h4>Order Summary</h4>
<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($cart_items as $item): ?>
        <tr>
            <td><?php echo $item['name']; ?></td>
            <td><?php echo $item['quantity']; ?></td>
            <td>Rs. <?php echo $item['price'] * $item['quantity']; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h4>Total: Rs. <?php echo $total; ?></h4>

<form action="place_order.php" method="POST" style="max-width: 500px;" class="mt-4">
    <div class="mb-3">
        <label class="form-label">Delivery Address</label>
        <textarea name="address" class="form-control" required></textarea>
    </div>
    <div class="mb-3">
    <label class="form-label">Payment Method</label>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="payment_method" value="cod" id="cod" checked>
        <label class="form-check-label" for="cod">Cash on Delivery</label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="payment_method" value="online" id="online">
        <label class="form-check-label" for="online">Online Payment (Card)</label>
    </div>
</div>
    <button type="submit" class="btn btn-success btn-lg">Place Order</button>
</form>

<?php require '../includes/footer.php'; ?>