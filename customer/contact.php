<?php
session_start();
require '../config/db.php';

// Form submit hua ho to
if (isset($_POST['send_message'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

   
    if (isset($_SESSION['role'])) {
        $sender_type = $_SESSION['role'];
    } else {
        $sender_type = $_POST['sender_type'];
    }

    $sql = "INSERT INTO contact_messages (name, email, message, sender_type) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $message, $sender_type);
    mysqli_stmt_execute($stmt);

    header("Location: contact.php?sent=1");
    exit();
}
require '../includes/header.php';
?>

<?php if (isset($_GET['sent'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        Your message has been sent! We'll get back to you soon.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-6">

        <h2 class="mb-4">Contact Us</h2>
        <p class="text-muted mb-4">Have a question or feedback? Send us a message and we'll respond as soon as possible.</p>

        <form action="contact.php" method="POST">

            <div class="mb-3">
                <label class="form-label">Your Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Your Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="mb-3">
                        <label class="form-label">I am a</label>
                        <select name="sender_type" class="form-select">
                            <option value="customer">Customer</option>
                            <option value="vendor">Vendor</option>
                            <option value="guest">Just Visiting / Other</option>
                        </select>
                    </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea name="message" class="form-control" rows="5" required></textarea>
            </div>

            <button type="submit" name="send_message" class="btn btn-primary">Send Message</button>

        </form>

    </div>
</div>

<?php require '../includes/footer.php'; ?>