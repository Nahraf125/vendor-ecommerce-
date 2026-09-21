 <?php
require 'config/mailer.php';

$result = sendOTPEmail('apna_koi_bhi_email@gmail.com', '123456');

if ($result) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email.";
}
?> 