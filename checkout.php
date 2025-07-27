<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "db_connect.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login
    exit();
}
$user_id = $_SESSION['user_id'];

// Fetch cart items
$cartQuery = $conn->prepare("
    SELECT C.flower_id, C.quantity, C.price, F.name AS flower_name 
    FROM Cart C 
    JOIN Flowers F ON C.flower_id = F.flower_id 
    WHERE C.user_id = ?
");
$cartQuery->bind_param("i", $user_id);
$cartQuery->execute();
$cartResult = $cartQuery->get_result();
$cart_items = $cartResult->fetch_all(MYSQLI_ASSOC);

// Handle empty cart
if (empty($cart_items)) {
    die("Your cart is empty! <a href='cart.php'>Return to Cart</a>");
}

// Calculate total
$total = array_sum(array_column($cart_items, 'price'));

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $shipping_address = $_POST['shipping_address'];
    $payment_method = $_POST['payment_method'];

    // Insert order
    $orderQuery = $conn->prepare("
        INSERT INTO Orders (user_id, total_amount, shipping_address, payment_method) 
        VALUES (?, ?, ?, ?)
    ");
    $orderQuery->bind_param("idss", $user_id, $total, $shipping_address, $payment_method);
    $orderQuery->execute();
    $order_id = $conn->insert_id;

    // Insert order items
    foreach ($cart_items as $item) {
        $itemQuery = $conn->prepare("
            INSERT INTO Order_Items (order_id, flower_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        $itemQuery->bind_param("iiid", $order_id, $item['flower_id'], $item['quantity'], $item['price']);
        $itemQuery->execute();
    }

    // Clear cart
    $clearCart = $conn->prepare("DELETE FROM Cart WHERE user_id = ?");
    $clearCart->bind_param("i", $user_id);
    $clearCart->execute();

    // Redirect to confirmation
    header("Location: order_confirmation.php?order_id=$order_id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Checkout</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Checkout</h1>
  
  <!-- Order Summary Table -->
  <table>
    <tr>
      <th>Flower</th>
      <th>Quantity</th>
      <th>Price</th>
    </tr>
    <?php foreach ($cart_items as $item): ?>
    <tr>
      <td><?= htmlspecialchars($item['flower_name']) ?></td>
      <td><?= $item['quantity'] ?></td>
      <td>₹<?= $item['price'] ?></td>
    </tr>
    <?php endforeach; ?>
  </table>

  <p><strong>Total: ₹<?= $total ?></strong></p>

  <!-- Checkout Form -->
  <form method="POST">
    <label>Shipping Address:</label>
    <textarea name="shipping_address" required></textarea>

    <label>Payment Method:</label>
    <select name="payment_method" required>
      <option value="Cash">Cash on Delivery</option>
      <option value="Card">Credit Card</option>
    </select>

    <button type="submit" name="place_order">Place Order</button>
  </form>
</body>
</html>