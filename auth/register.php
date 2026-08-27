<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>

    <h2>Register</h2>

    <form action="register_process.php" method="POST">
        
        <label>Name:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Register as:</label><br>
        <select name="role">
            <option value="customer">Customer</option>
            <option value="vendor">Vendor</option>
        </select><br><br>

        <label>Store name (only if Vendor)</label><br><br>
        <input type="text" name= "store_name">

        <button type="submit">Register</button>

    </form>

</body>
</html>