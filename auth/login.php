
<?php
session_start();
require '../config/db.php';


require '../includes/header.php';
?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger" style="max-width: 500px; margin: auto;">
        <?php if ($_GET['error'] == 'rejected'): ?>
            Your vendor account was rejected.<br>
            <strong>Reason:</strong> <?php echo htmlspecialchars($_GET['reason']); ?>
        <?php elseif ($_GET['error'] == 'invalid'): ?>
            Invalid email or password.
        <?php endif; ?>
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

   <div class="row justify-content-center">
    <div class="col-md-5">

        <h2 class="mb-4">Login</h2>

        <form action="login_process.php" method="POST">

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>

        </form>

        <p class="mt-3">Account nahi hai? <a href="register.php">Sign Up</a></p>

    </div>
</div>

</body>
</html>


<?php require '../includes/footer.php'; ?>