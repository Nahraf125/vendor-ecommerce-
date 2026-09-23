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


<nav class="navbar-dark sticky-top" style="padding: 0;">

    <!-- TOP BAR -->
    <div class="top-navbar py-2 w-100">
        <div class="container px-4 d-flex align-items-center">

            <a class="navbar-brand d-flex align-items-center gap-2 text-white text-decoration-none me-3" href="/vendorwaala/customer/index.php">
                <i class="bi bi-shop fs-3"></i>
                <span class="fw-bold" style="font-size: 0.95rem;">Vendorwaala</span>
            </a>

            <div class="text-white d-none d-lg-flex flex-column justify-content-center me-3" style="line-height: 1.1;">
                <span style="opacity: 0.7; font-size: 0.7rem;">Deliver to</span>
                <span class="fw-semibold"><i class="bi bi-geo-alt"></i> Pakistan</span>
            </div>

            <form action="/vendorwaala/customer/index.php" method="GET" class="d-none d-md-flex mx-3" style="flex: 1 1 auto; min-width: 200px; max-width: 600px;">
                <div class="input-group">
                    <select name="category_id" class="form-select category-select">
                        <option value="">All</option>
                        <?php
                        $nav_cats = mysqli_query($conn, "SELECT * FROM categories");
                        while ($ncat = mysqli_fetch_assoc($nav_cats)):
                        ?>
                            <option value="<?php echo $ncat['id']; ?>"><?php echo $ncat['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                    <input type="text" name="search" class="form-control" placeholder="Search everything...">
                    <button class="btn btn-search" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>

            <div class="ms-auto d-flex align-items-center gap-3 flex-shrink-0">

                <div class="text-white d-none d-lg-flex flex-column justify-content-center" style="line-height: 1.1;">
                    <span style="opacity: 0.7; font-size: 0.7rem;">Language</span>
                    <span class="fw-semibold">EN</span>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>

                    <div class="dropdown">
                        <a class="text-white text-decoration-none dropdown-toggle d-flex flex-column justify-content-center" href="#" data-bs-toggle="dropdown" style="line-height: 1.1;">
                            <span class="d-none d-lg-inline" style="opacity: 0.7; font-size: 0.7rem;">Hello, <?php echo explode(' ', $_SESSION['name'])[0]; ?></span>
                            <span class="fw-semibold"><i class="bi bi-person-circle d-lg-none"></i><span class="d-none d-lg-inline">Account & Lists</span></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
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
                        <a href="/vendorwaala/customer/my_order.php" class="text-white text-decoration-none d-none d-lg-flex flex-column justify-content-center" style="line-height: 1.1;">
                            <span style="opacity: 0.7; font-size: 0.7rem;">Returns</span>
                            <span class="fw-semibold">& Orders</span>
                        </a>

                        <a href="/vendorwaala/customer/cart.php" class="text-white text-decoration-none position-relative">
                            <i class="bi bi-cart3 fs-4"></i>
                            <?php if ($cart_count > 0): ?>
                                <span class="cart-badge"><?php echo $cart_count; ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>

                <?php else: ?>
                    <a class="text-white text-decoration-none d-flex flex-column justify-content-center" href="#" data-bs-toggle="modal" data-bs-target="#authModal" style="line-height: 1.1;">
                        <span class="d-none d-lg-inline" style="opacity: 0.7; font-size: 0.7rem;">Hello, Sign in</span>
                        <span class="fw-semibold"><i class="bi bi-person d-lg-none"></i><span class="d-none d-lg-inline">Account & Lists</span></span>
                    </a>
                <?php endif; ?>

                <button class="navbar-toggler border-0 text-white p-0 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#categoryMenu">
                    <i class="bi bi-list fs-2"></i>
                </button>

            </div>
        </div>
    </div>

    <!-- MOBILE SEARCH (only visible on small screens) -->
    <div class="d-md-none w-100 px-3 py-2" style="background-color: var(--secondary-color);">
        <form action="/vendorwaala/customer/index.php" method="GET">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search everything...">
                <button class="btn btn-search" type="submit"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>

    <!-- SECOND BAR: Role Links (collapsible on mobile) -->
        <div class="collapse w-100" id="categoryMenu">
        <div class="category-navbar w-100">
            <div class="container-fluid px-4 d-flex align-items-center gap-4 flex-wrap py-2">

                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin'): ?>
                    <a href="/vendorwaala/admin/dashboard.php" class="text-white text-decoration-none small">Dashboard</a>
                    <a href="/vendorwaala/admin/vendors.php" class="text-white text-decoration-none small">Vendors</a>
                    <a href="/vendorwaala/admin/categories.php" class="text-white text-decoration-none small">Categories</a>
                    <a href="/vendorwaala/admin/orders.php" class="text-white text-decoration-none small">Orders</a>
                    <a href="/vendorwaala/admin/affiliates.php" class="text-white text-decoration-none small">Affiliates</a>
                    <a href="/vendorwaala/admin/users.php" class="text-white text-decoration-none small">Users</a>
                    <a href="/vendorwaala/admin/messages.php" class="text-white text-decoration-none small">Messages</a>
                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] == 'vendor'): ?>
                    <a href="/vendorwaala/vendor/dashboard.php" class="text-white text-decoration-none small">Dashboard</a>
                    <a href="/vendorwaala/vendor/add_product.php" class="text-white text-decoration-none small">Add Product</a>
                    <a href="/vendorwaala/vendor/my_products.php" class="text-white text-decoration-none small">My Products</a>
                    <a href="/vendorwaala/vendor/orders.php" class="text-white text-decoration-none small">Orders</a>
                <?php else: ?>
                    <a href="/vendorwaala/customer/index.php" class="text-white text-decoration-none small">Shop</a>
                    <a href="/vendorwaala/customer/index.php" class="text-white text-decoration-none small">All Categories</a>
                    <a href="/vendorwaala/customer/contact.php" class="text-white text-decoration-none small">Contact</a>
                    <a href="/vendorwaala/customer/become_affiliate.php" class="text-white text-decoration-none small">Become Affiliate</a>
                <?php endif; ?>

            </div>
        </div>
    </div>

</nav>

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
                                <select name="role" class="form-select" id="roleSelect" onchange="toggleVendorFields()">
                                    <option value="customer">Customer</option>
                                    <option value="vendor">Vendor</option>
                                </select>
                            </div>

                            <!-- Vendor-only fields, hidden by default -->
                            <div id="vendorFields" style="display: none;">

                                <div class="mb-3">
                                    <label class="form-label">Store Name</label>
                                    <input type="text" name="store_name" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Business Type</label>
                                    <select name="business_type" class="form-select">
                                        <option value="individual">Individual Seller</option>
                                        <option value="registered">Registered Business</option>
                                        <option value="home_based">Home-based Business</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" placeholder="03XX-XXXXXXX">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">CNIC Number</label>
                                    <input type="text" name="cnic" class="form-control" placeholder="XXXXX-XXXXXXX-X">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Business Address</label>
                                    <textarea name="business_address" class="form-control"></textarea>
                                </div>

                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="terms" id="termsCheck" required>
                                <label class="form-check-label" for="termsCheck">
                                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms & Conditions</a>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Register</button>

                        </form>
                    </div>

                    <script>
                    function toggleVendorFields() {
                        const role = document.getElementById('roleSelect').value;
                        const vendorFields = document.getElementById('vendorFields');

                        if (role === 'vendor') {
                            vendorFields.style.display = 'block';
                        } else {
                            vendorFields.style.display = 'none';
                        }
                    }
                    </script>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- TERMS & CONDITIONS MODAL -->
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terms & Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>1. Account Responsibility</h6>
                <p>You are responsible for maintaining the confidentiality of your account and password.</p>

                <h6>2. Vendor Conduct</h6>
                <p>Vendors must provide accurate product information and honor orders placed by customers. Fraudulent listings will result in account suspension.</p>

                <h6>3. Prohibited Activities</h6>
                <p>Users may not engage in fraud, harassment, or any activity that violates applicable laws. Vendorwaala reserves the right to suspend or terminate accounts found in violation.</p>

                <h6>4. Affiliate Program</h6>
                <p>Commissions are only valid for genuine referrals. Self-referrals or fraudulent click activity will result in forfeiture of earnings and account termination.</p>

                <h6>5. Changes to Terms</h6>
                <p>Vendorwaala may update these terms at any time. Continued use of the platform constitutes acceptance of the updated terms.</p>
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
            <?php elseif ($_GET['error'] == 'blocked'): ?>
                 Your account has been blocked by the admin. <a href="/vendorwaala/customer/contact.php" class="alert-link">Contact support</a> if you think this is a mistake.
            <?php elseif ($_GET['error'] == 'suspended'): ?>
                  Your account has been temporarily suspended. <a href="/vendorwaala/customer/contact.php" class="alert-link">Contact support</a> if you think this is a mistake.
            <?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>