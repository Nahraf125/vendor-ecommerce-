<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Status update
if (isset($_GET['action']) && isset($_GET['id'])) {
    $target_id = $_GET['id'];
    $action = $_GET['action'];

    if (in_array($action, ['active', 'suspended', 'blocked'])) {
        $sql = "UPDATE users SET account_status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $action, $target_id);
        mysqli_stmt_execute($stmt);
    }

    header("Location: users.php");
    exit();
}

// Saare users nikalo (admin ke ilawa)
$sql = "SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

require '../includes/header.php';
?>

<h2 class="mb-4">Manage Users</h2>

<div class="table-responsive">
<table class="table table-striped table-bordered align-middle">
    <thead class="table-dark">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($u = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $u['name']; ?></td>
            <td><?php echo $u['email']; ?></td>
            <td><span class="badge bg-secondary"><?php echo ucfirst($u['role']); ?></span></td>
            <td>
                <?php if ($u['account_status'] == 'active'): ?>
                    <span class="badge bg-success">Active</span>
                <?php elseif ($u['account_status'] == 'suspended'): ?>
                    <span class="badge bg-warning text-dark">Suspended</span>
                <?php else: ?>
                    <span class="badge bg-danger">Blocked</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($u['account_status'] != 'active'): ?>
                    <a href="users.php?action=active&id=<?php echo $u['id']; ?>" class="btn btn-sm btn-success">Activate</a>
                <?php endif; ?>
                <?php if ($u['account_status'] != 'suspended'): ?>
                    <a href="users.php?action=suspended&id=<?php echo $u['id']; ?>" class="btn btn-sm btn-warning">Suspend</a>
                <?php endif; ?>
                <?php if ($u['account_status'] != 'blocked'): ?>
                    <a href="users.php?action=blocked&id=<?php echo $u['id']; ?>" class="btn btn-sm btn-danger">Block</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>

<?php require '../includes/footer.php'; ?>