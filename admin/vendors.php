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


$sql = "SELECT vendors.id , vendors.store_name , vendors.status , users.name , users.email FROM vendors
        JOIN users ON vendors.user_id = users.id ";
$result = mysqli_query($conn, $sql);

?>
<h2 class="mb-4">Manage Vendors</h2>

<div class="table-responsive">
<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Store Name</th>
            <th>Owner Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $row['store_name']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td>
                <?php if ($row['status'] == 'approved'): ?>
                    <span class="badge bg-success">Approved</span>
                <?php elseif ($row['status'] == 'rejected'): ?>
                    <span class="badge bg-danger">Rejected</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark">Pending</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($row['status'] == 'pending'): ?>
                    <a href="vendors.php?action=approve&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">Approve</a>

                    <button type="button" class="btn btn-sm btn-danger" onclick="document.getElementById('reject-form-<?php echo $row['id']; ?>').style.display='block'">Reject</button>

                    <div id="reject-form-<?php echo $row['id']; ?>" style="display:none; margin-top:8px;">
                        <form action="vendors.php" method="POST">
                            <input type="hidden" name="reject_id" value="<?php echo $row['id']; ?>">
                            <textarea name="reason" class="form-control form-control-sm mb-1" placeholder="Reason for rejection" required></textarea>
                            <button type="submit" class="btn btn-sm btn-outline-danger">Confirm Reject</button>
                        </form>
                    </div>

                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>
<?php require '../includes/footer.php'; ?>