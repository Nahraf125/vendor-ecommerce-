<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];
$address = $_POST['address'];
$payment_method = $_POST['payment_method'];

$sql = "SELECT cart.quantity, products.id AS product_id, products.price
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

// Order banao (payment_status abhi 'unpaid' hi rahega dono cases mein, jab tak payment confirm na ho)
$sql1 = "INSERT INTO orders (customer_id, total_amount, address, payment_method) VALUES (?, ?, ?, ?)";
$stmt1 = mysqli_prepare($conn, $sql1);
mysqli_stmt_bind_param($stmt1, "idss", $customer_id, $total, $address, $payment_method);
mysqli_stmt_execute($stmt1);

$order_id = mysqli_insert_id($conn);

foreach ($cart_items as $item) {
    $sql2 = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
    mysqli_stmt_execute($stmt2);
}

// Agar affiliate cookie hai, to commission record karo
if (isset($_COOKIE['affiliate_ref'])) {
    $ref_code = $_COOKIE['affiliate_ref'];

    $sql_aff = "SELECT id FROM affiliates WHERE referral_code = ? AND status = 'approved'";
    $stmt_aff = mysqli_prepare($conn, $sql_aff);
    mysqli_stmt_bind_param($stmt_aff, "s", $ref_code);
    mysqli_stmt_execute($stmt_aff);
    $result_aff = mysqli_stmt_get_result($stmt_aff);
    $affiliate = mysqli_fetch_assoc($result_aff);

    if ($affiliate) {
        $commission_rate = 0.05; // 5%
        $commission_amount = $total * $commission_rate;

        $sql_comm = "INSERT INTO affiliate_commissions (affiliate_id, order_id, amount) VALUES (?, ?, ?)";
        $stmt_comm = mysqli_prepare($conn, $sql_comm);
        mysqli_stmt_bind_param($stmt_comm, "iid", $affiliate['id'], $order_id, $commission_amount);
        mysqli_stmt_execute($stmt_comm);

        // Cookie clear kar do (ek order pe ek hi commission mile)
        setcookie("affiliate_ref", "", time() - 3600, "/");
    }
}

$sql3 = "DELETE FROM cart WHERE customer_id = ?";
$stmt3 = mysqli_prepare($conn, $sql3);
mysqli_stmt_bind_param($stmt3, "i", $customer_id);
mysqli_stmt_execute($stmt3);

// Agar Online payment chuna, to payment page pe bhejo, warna seedha success
if ($payment_method == 'online') {
    header("Location: simulate_payment.php?order_id=" . $order_id);
} else {
    header("Location: order_success.php?order_id=" . $order_id);
}
exit();
?>