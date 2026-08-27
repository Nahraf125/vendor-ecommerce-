<?php

session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();


}


if (isset($_GET['action']) && isset($_GET['id'])){
    $vendor_id = $_GET['id'];
    $action = $_GET['action'] == 'approve' ?  'approved'  : 'rejected';
    

    $sql = "UPDATE vendors SET status = ? where id = ? "; 
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si" , $action , $vendor_id);
    mysqli_stmt_execute($stmt);

    header("Location: vendors.php");
    exit();
}


$sql = "SELECT vendors.id , vendors.store_name , vendors.status , users.name , users.email FROM vendors
        JOIN users ON vendors.user_id = users.id ";
$result = mysqli_query($conn, $sql);

?>
<h2>Manage Vendors</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Store Name</th>
        <th>Owner Name</th>
        <th>Email</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?php echo $row['store_name']; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['email']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td>
            <?php if ($row['status'] == 'pending'): ?>
                <a href="vendors.php?action=approve&id=<?php echo $row['id']; ?>">Approve</a> |
                <a href="vendors.php?action=reject&id=<?php echo $row['id']; ?>">Reject</a>
            <?php else: ?>
                —
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
