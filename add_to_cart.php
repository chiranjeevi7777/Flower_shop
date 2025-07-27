<?php
session_start();
include "db_connect.php";

// Fix: Check for the correct session variable 'customer_id'
if (!isset($_SESSION['customer_id'])) {
    echo "Error: Customer not logged in.";
    exit();
}

$customer_id = $_SESSION['customer_id'];  // Access the correct session variable
echo "Customer ID: " . $customer_id;  // Debugging to check if it's working


$flower_id = $_POST['flower_id'];
$customer_id = $_SESSION['customer_id'];

if (isset($_POST['flower_id']) && isset($_SESSION['customer_id'])) {
    $flower_id = $_POST['flower_id'];
    $customer_id = $_SESSION['customer_id'];

    // Check if the flower is already in the cart
    $checkCart = $conn->prepare("SELECT * FROM Cart WHERE customer_id = ? AND flower_id = ?");
    $checkCart->bind_param("ii", $customer_id, $flower_id);
    $checkCart->execute();
    $result = $checkCart->get_result();

    if ($result->num_rows > 0) {
        // If flower is already in cart, update quantity
        $updateCart = $conn->prepare("UPDATE Cart SET quantity = quantity + 1 WHERE customer_id = ? AND flower_id = ?");
        $updateCart->bind_param("ii", $customer_id, $flower_id);
        $updateCart->execute();
    } else {
        // Insert new item into cart
        $insertCart = $conn->prepare("INSERT INTO Cart (customer_id, flower_id, quantity) VALUES (?, ?, 1)");
        $insertCart->bind_param("ii", $customer_id, $flower_id);
        $insertCart->execute();
    }

    echo "Added to cart!";
} else {
    echo "Error: Customer not logged in.";
}
?>
