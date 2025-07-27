<?php
session_start();
include "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = $conn->prepare("SELECT customer_id, first_name, password FROM Customers WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            // Correct session variable name for customer_id
            $_SESSION['customer_id'] = $row['customer_id'];  
            $_SESSION['first_name'] = $row['first_name'];  // Optional: Store first name too
            header("Location: flowers.php");  // Redirect to home or flowers page
            exit();
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Flower Shop</title>
    <link rel="stylesheet" href="style.css"> <!-- Same styling as registration -->
</head>
<body>
    <h2>User Login</h2>
    <form action="login_process.php" method="POST">
        <label>Email:</label>
        <input type="email" name="email" required>
        
        <label>Password:</label>
        <input type="password" name="password" required>
        
        <button type="submit">Login</button>
    </form>
    <p style = "text-align: center;">Don't have an account?<a href="register.php">Register Here</a> </p>

    <p style = "text-align: center;">Click Below to login as Admin... </p><a href="admin_panel.php">Admin Panel</a></p>

</body>
</html>

