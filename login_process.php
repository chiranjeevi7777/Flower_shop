<?php
session_start();
include 'db_connect.php'; // Connect to database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Check if user exists
    $sql = "SELECT user_id, first_name, password FROM Users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $first_name, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $user_id;
            $_SESSION["first_name"] = $first_name;
            
            echo "Login successful! Redirecting...";
            header("refresh:2; url=dashboard.php"); // Redirect after 2 seconds
        } else {
            echo "Invalid password Try Again!";
            header("refresh:2; url=login.php");
        }
    } else {
        echo "No user found!";
    }

    $stmt->close();
    $conn->close();
}
?>
