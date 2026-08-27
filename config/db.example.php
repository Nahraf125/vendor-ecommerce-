<?php
$server = 'localhost';
$username = 'root';
$password = '';
$db_name = 'ecommerce_db';

$conn = mysqli_connect($server, $username, $password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>