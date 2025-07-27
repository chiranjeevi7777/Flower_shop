<?php
session_start();
include "db_connect.php";  // Database connection

// Check if order_id is passed
if (!isset($_GET['order_id'])) {
    echo "Error: Order ID not found!";
    exit();
}

$order_id = $_GET['order_id'];

// Fetch Order Details
$query = $conn->prepare("SELECT * FROM Orders WHERE order_id = ?");
$query->bind_param("i", $order_id);
$query->execute();
$order_result = $query->get_result();

if ($order_result->num_rows == 0) {
    echo "Error: Order not found!";
    exit();
}

$order = $order_result->fetch_assoc();

// Fetch Order Items
$query = $conn->prepare("SELECT oi.*, f.name FROM Order_Items oi 
                         JOIN Flowers f ON oi.flower_id = f.flower_id 
                         WHERE oi.order_id = ?");
$query->bind_param("i", $order_id);
$query->execute();
$order_items_result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Flower Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Order Confirmation</h2>
<p>Thank you for your order! Your order has been successfully placed.</p>

<h3>Order Details:</h3>
<p><strong>Order ID:</strong> <?php echo $order['order_id']; ?></p>
<p><strong>Total Amount:</strong> ₹<?php echo $order['total_amount']; ?></p>
<p><strong>Shipping Address:</strong> <?php echo $order['shipping_address']; ?></p>
<p><strong>Payment Method:</strong> <?php echo $order['payment_method']; ?></p>

<h3>Ordered Items:</h3>
<table border="1">
    <tr>
        <th>Flower Name</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Total</th>
    </tr>
    <?php while ($item = $order_items_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $item['name']; ?></td>
            <td><?php echo $item['quantity']; ?></td>
            <td>₹<?php echo $item['price']; ?></td>
            <td>₹<?php echo $item['quantity'] * $item['price']; ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<p><a href="dashboard.php">Return to Home</a></p>

</body>
</html>
