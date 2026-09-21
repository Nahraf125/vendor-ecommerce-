<?php

session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();


}

require '../includes/header.php';
if (isset($_GET['action']) && $_GET['action'] == 'approve' && isset($_GET['id'])) {
    $vendor_id = $_GET['id'];

    $sql = "UPDATE vendors SET status = 'approved', rejection_reason = NULL WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $vendor_id);
    mysqli_stmt_execute($stmt);

    header("Location: vendors.php");
    exit();
}
if (isset($_POST['reject_id'])) {
    $vendor_id = $_POST['reject_id'];
    $reason = $_POST['reason'];

    $sql = "UPDATE vendors SET status = 'rejected', rejection_reason = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $reason, $vendor_id);
    mysqli_stmt_execute($stmt);

    header("Location: vendors.php");
    exit();
}


$sql = "SELECT vendors.*, users.name, users.email 
        FROM vendors 
        JOIN users ON vendors.user_id = users.id";
        
$result = mysqli_query($conn, $sql);

?>
<h2 class="mb-4">Manage Vendors</h2>

<?php while ($row = mysqli_fetch_assoc($result)): ?>

    <div class="card mb-3">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="card-title"><?php echo $row['store_name']; ?></h5>
                    <p class="mb-1"><strong>Owner:</strong> <?php echo $row['name']; ?> (<?php echo $row['email']; ?>)</p>
                    <p class="mb-1"><strong>Business Type:</strong> <?php echo ucfirst(str_replace('_', ' ', $row['business_type'])); ?></p>
                    <p class="mb-1"><strong>Phone:</strong> <?php echo $row['phone']; ?></p>
                    <p class="mb-1"><strong>CNIC:</strong> <?php echo $row['cnic']; ?></p>
                    <p class="mb-1"><strong>Address:</strong> <?php echo $row['business_address']; ?></p>
                </div>
                <div>
                    <?php if ($row['status'] == 'approved'): ?>
                        <span class="badge bg-success">Approved</span>
                    <?php elseif ($row['status'] == 'rejected'): ?>
                        <span class="badge bg-danger">Rejected</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Pending</span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($row['status'] == 'pending'): ?>
                <div class="mt-3">
                    <a href="vendors.php?action=approve&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">Approve</a>

                    <button type="button" class="btn btn-sm btn-danger" onclick="document.getElementById('reject-form-<?php echo $row['id']; ?>').style.display='block'">Reject</button>

                    <div id="reject-form-<?php echo $row['id']; ?>" style="display:none; margin-top:8px;">
                        <form action="vendors.php" method="POST">
                            <input type="hidden" name="reject_id" value="<?php echo $row['id']; ?>">
                            <textarea name="reason" class="form-control form-control-sm mb-1" placeholder="Reason for rejection" required></textarea>
                            <button type="submit" class="btn btn-sm btn-outline-danger">Confirm Reject</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

<?php endwhile; ?>
<?php require '../includes/footer.php'; ?>