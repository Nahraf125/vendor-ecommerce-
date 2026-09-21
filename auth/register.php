<?php
session_start();
require '../includes/header.php';
?>



<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>

    <div class="row justify-content-center">
    <div class="col-md-5">

        <h2 class="mb-4">Register</h2>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'exists'): ?>
            <div class="alert alert-danger">This email is already registered. Please login instead.</div>
        <?php endif; ?>

        <form action="register_process.php" method="POST">

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Register as</label>
                <select name="role" class="form-select">
                    <option value="customer">Customer</option>
                    <option value="vendor">Vendor</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Store Name (only if Vendor)</label>
                <input type="text" name="store_name" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Register</button>

        </form>

        <p class="mt-3">Pehle se account hai? <a href="login.php">Login</a></p>

    </div>
</div>
</body>
</html>


<?php require '../includes/footer.php'; ?>