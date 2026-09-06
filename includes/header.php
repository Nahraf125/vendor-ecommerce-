<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>vendorwaala</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/vendorwaala/assets/css/style.css">
</head>
<body>

<?php
// Cart item count nikalo (sirf customer ke liye, navbar mein dikhane ke liye)
$cart_count = 0;
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'customer') {
    $cc_sql = "SELECT SUM(quantity) AS total_qty FROM cart WHERE customer_id = ?";
    $cc_stmt = mysqli_prepare($conn, $cc_sql);
    mysqli_stmt_bind_param($cc_stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($cc_stmt);
    $cc_result = mysqli_stmt_get_result($cc_stmt);
    $cc_data = mysqli_fetch_assoc($cc_result);
    $cart_count = $cc_data['total_qty'] ? $cc_data['total_qty'] : 0;
}
?>

<!-- TOP BAR: Logo + Search + Account + Cart -->
<div class="top-navbar py-2">
    <div class="container d-flex align-items-center gap-3 flex-wrap">

        <a class="navbar-brand d-flex align-items-center gap-2 text-white text-decoration-none" href="/vendorwaala/customer/index.php">
            <i class="bi bi-shop fs-3"></i>
            <span class="fw-bold">Vendorwaala</span>
        </a>

        <form action="/vendorwaala/customer/index.php" method="GET" class="flex-grow-1 d-none d-md-flex">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search everything...">
                <button class="btn btn-search" type="submit"><i class="bi bi-search"></i></button>
            </div>
        </form>

        <div class="ms-auto d-flex align-items-center gap-3">

            <?php if (isset($_SESSION['user_id'])): ?>

               <div class="dropdown">
                        <a class="text-white text-decoration-none dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-4"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Hi, <?php echo $_SESSION['name']; ?></h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <li><a class="dropdown-item" href="/vendorwaala/admin/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                            <?php elseif ($_SESSION['role'] == 'vendor'): ?>
                                <li><a class="dropdown-item" href="/vendorwaala/vendor/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="/vendorwaala/customer/my_orders.php"><i class="bi bi-box-seam me-2"></i>My Orders</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/vendorwaala/auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
               </div>

                <?php if ($_SESSION['role'] == 'customer'): ?>
                    <a href="/vendorwaala/customer/cart.php" class="text-white text-decoration-none position-relative">
                        <i class="bi bi-cart3 fs-4"></i>
                        <?php if ($cart_count > 0): ?>
                            <span class="cart-badge"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>

            <?php else: ?>
               <a href="#" class="text-white text-decoration-none" data-bs-toggle="modal" data-bs-target="#authModal">
                  <i class="bi bi-person"></i> Sign In
               </a>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- SECOND BAR: Categories -->
<!-- SECOND BAR: Role Links -->
<div class="category-navbar">
    <div class="container d-flex align-items-center gap-4 flex-wrap py-2">

        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin'): ?>
            <a href="/vendorwaala/admin/dashboard.php" class="text-white text-decoration-none small">Dashboard</a>
            <a href="/vendorwaala/admin/vendors.php" class="text-white text-decoration-none small">Vendors</a>
            <a href="/vendorwaala/admin/categories.php" class="text-white text-decoration-none small">Categories</a>
            <a href="/vendorwaala/admin/orders.php" class="text-white text-decoration-none small">Orders</a>
        <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] == 'vendor'): ?>
            <a href="/vendorwaala/vendor/dashboard.php" class="text-white text-decoration-none small">Dashboard</a>
            <a href="/vendorwaala/vendor/add_product.php" class="text-white text-decoration-none small">Add Product</a>
            <a href="/vendorwaala/vendor/my_product.php" class="text-white text-decoration-none small">My Product</a>
            <a href="/vendorwaala/vendor/orders.php" class="text-white text-decoration-none small">Orders</a>
        <?php else: ?>
            <a href="/vendorwaala/customer/index.php" class="text-white text-decoration-none small">Shop</a>
            <a href="/vendorwaala/customer/index.php" class="text-white text-decoration-none small">All Categories</a>
        <?php endif; ?>

    </div>
</div>

<!-- LOGIN / REGISTER MODAL -->
<div class="modal fade" id="authModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header border-0">
                <ul class="nav nav-tabs card-header-tabs" id="authTabs">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#loginTab">Login</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#registerTab">Register</button>
                    </li>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="tab-content">

                    <!-- LOGIN FORM -->
                    <div class="tab-pane fade show active" id="loginTab">
                        <form action="/vendorwaala/auth/login_process.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>

                    <!-- REGISTER FORM -->
                    <div class="tab-pane fade" id="registerTab">
                        <form action="/vendorwaala/auth/register_process.php" method="POST">
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
                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<div class="container mt-4">
    <?php if (isset($_GET['error'])): ?>
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show">
            <?php if ($_GET['error'] == 'rejected'): ?>
                Your vendor account was rejected.<br>
                <strong>Reason:</strong> <?php echo htmlspecialchars($_GET['reason']); ?>
            <?php elseif ($_GET['error'] == 'invalid'): ?>
                Invalid email or password.
            <?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>