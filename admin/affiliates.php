<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $affiliate_id = $_GET['id'];
    $action = $_GET['action'] == 'approve' ? 'approved' : 'rejected';

    $sql = "UPDATE affiliates SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $action, $affiliate_id);
    mysqli_stmt_execute($stmt);

    header("Location: affiliates.php");
    exit();
}

$sql = "SELECT affiliates.id, affiliates.referral_code, affiliates.status, affiliates.clicks, users.name, users.email
        FROM affiliates
        JOIN users ON affiliates.user_id = users.id
        ORDER BY affiliates.created_at DESC";
$result = mysqli_query($conn, $sql);

require '../includes/header.php';
?>

<h2 class="mb-4">Manage Affiliates</h2>

<div class="table-responsive">
<table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Referral Code</th>
            <th>Clicks</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><code><?php echo $row['referral_code']; ?></code></td>
            <td><?php echo $row['clicks']; ?></td>
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
                    <a href="affiliates.php?action=approve&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">Approve</a>
                    <a href="affiliates.php?action=reject&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger">Reject</a>
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