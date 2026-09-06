<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$order_id = $_GET['order_id'];

// Agar "Pay Now" click hua
if (isset($_POST['pay_now'])) {
    $sql = "UPDATE orders SET payment_status = 'paid' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);

    header("Location: order_success.php?order_id=" . $order_id);
    exit();
}

require '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">💳 Payment Details</h4>
                <p class="text-muted">This is a demo payment gateway (sandbox). No real transaction will occur.</p>

                <form action="simulate_payment.php?order_id=<?php echo $order_id; ?>" method="POST">

                    <div class="mb-3">
                        <label class="form-label">Card Number</label>
                        <input type="text" class="form-control" placeholder="4242 4242 4242 4242" required>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Expiry</label>
                            <input type="text" class="form-control" placeholder="MM/YY" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">CVV</label>
                            <input type="text" class="form-control" placeholder="123" required>
                        </div>
                    </div>

                    <button type="submit" name="pay_now" class="btn btn-success w-100">Pay Now</button>

                </form>
            </div>
        </div>

    </div>
</div>

<?php require '../includes/footer.php'; ?>