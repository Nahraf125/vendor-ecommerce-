<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

require '../includes/header.php';
?>

<h2 class="mb-4">Contact Messages</h2>

<?php while ($msg = mysqli_fetch_assoc($result)): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">
                    <?php echo $msg['name']; ?>
                    <?php if ($msg['sender_type'] == 'vendor'): ?>
                        <span class="badge bg-warning text-dark">Vendor</span>
                    <?php elseif ($msg['sender_type'] == 'customer'): ?>
                        <span class="badge bg-info text-dark">Customer</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Guest</span>
                    <?php endif; ?>
            </h5>
            <p class="text-muted mb-2"><?php echo $msg['email']; ?> • <?php echo $msg['created_at']; ?></p>
            <p class="card-text"><?php echo $msg['message']; ?></p>
        </div>
    </div>
<?php endwhile; ?>

<?php require '../includes/footer.php'; ?>