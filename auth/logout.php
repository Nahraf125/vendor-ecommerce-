<?php
session_start();
session_destroy();
header("Location: /vendorwaala/customer/index.php");
exit();
?>