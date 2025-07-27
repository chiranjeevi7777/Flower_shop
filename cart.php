<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "db_connect.php"; // Database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in to use the cart.");
}
$user_id = $_SESSION['user_id'];

/*==============================
  HANDLE ADDING ITEM TO CART
==============================*/
if (isset($_POST['add_to_cart'])) {
    $flower_id = $_POST['flower_id'];
    $quantity = (int) $_POST['quantity'];

    // Get flower details (name & price) from the database
    $query = $conn->prepare("SELECT name, price FROM Flowers WHERE flower_id = ?");
    if (!$query) die("Prepare failed: " . $conn->error);
    $query->bind_param("i", $flower_id);
    if (!$query->execute()) die("Execute failed: " . $query->error);
    $result = $query->get_result();

    if ($row = $result->fetch_assoc()) {
        $flower_name = $row['name'];
        $price = $row['price'];
        $total_price = $price * $quantity;

        // Check if the item already exists in the Cart table for this user
        $checkQuery = $conn->prepare("SELECT quantity, total_price FROM Cart WHERE user_id = ? AND flower_id = ?");
        if (!$checkQuery) die("Prepare failed: " . $conn->error);
        $checkQuery->bind_param("ii", $user_id, $flower_id);
        if (!$checkQuery->execute()) die("Execute failed: " . $checkQuery->error);
        $existingItem = $checkQuery->get_result()->fetch_assoc();

        if ($existingItem) {
            // Update the item quantity and total price
            $newQuantity = $existingItem['quantity'] + $quantity;
            $newTotalPrice = $newQuantity * $price;

            $updateQuery = $conn->prepare("UPDATE Cart SET quantity = ?, total_price = ? WHERE user_id = ? AND flower_id = ?");
            if (!$updateQuery) die("Prepare failed: " . $conn->error);
            $updateQuery->bind_param("idii", $newQuantity, $newTotalPrice, $user_id, $flower_id);
            if (!$updateQuery->execute()) die("Execute failed: " . $updateQuery->error);
            echo "Item updated in cart!";
        } 
        else {
            // Insert the new item into the Cart table
            $insertQuery = $conn->prepare("INSERT INTO Cart (user_id, flower_id, quantity, price, total_price) VALUES (?, ?, ?, ?, ?)");
            if (!$insertQuery) die("Prepare failed: " . $conn->error);
            $insertQuery->bind_param("iiidd", $user_id, $flower_id, $quantity, $price, $total_price);
            if (!$insertQuery->execute()) die("Execute failed: " . $insertQuery->error);
            echo "Item added to cart!";
        }
    } else {
        echo "Error: Flower not found!";
    }
}

/*=================================
  HANDLE REMOVING ITEM FROM CART
=================================*/
if (isset($_POST['remove_from_cart'])) {
    $flower_id = $_POST['flower_id'];
    
    $deleteQuery = $conn->prepare("DELETE FROM Cart WHERE user_id = ? AND flower_id = ?");
    if (!$deleteQuery) die("Prepare failed: " . $conn->error);
    $deleteQuery->bind_param("ii", $user_id, $flower_id);
    if (!$deleteQuery->execute()) die("Execute failed: " . $deleteQuery->error);
    
    echo "Item removed from cart!";
}

/*=========================
  HANDLE CHECKOUT PROCESS
=========================*/
if (isset($_POST['checkout'])) {
    $total_amount = 0;

    // Get all cart items for this user
    $cartQuery = $conn->prepare("SELECT * FROM Cart WHERE user_id = ?");
    if (!$cartQuery) die("Prepare failed: " . $conn->error);
    $cartQuery->bind_param("i", $user_id);
    if (!$cartQuery->execute()) die("Execute failed: " . $cartQuery->error);
    $cartResult = $cartQuery->get_result();

    if ($cartResult->num_rows > 0) {
        // Insert a new order with initial total_amount = 0
        $insertOrder = $conn->prepare("INSERT INTO Orders (user_id, total_amount) VALUES (?, ?)");
        if (!$insertOrder) die("Prepare failed: " . $conn->error);
        $insertOrder->bind_param("id", $user_id, $total_amount);
        if (!$insertOrder->execute()) die("Execute failed: " . $insertOrder->error);
        $order_id = $conn->insert_id;

        // Process each cart item
        while ($item = $cartResult->fetch_assoc()) {
            $flower_id = $item['flower_id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $itemTotal = $item['total_price'];
            $total_amount += $itemTotal;

            $orderItemQuery = $conn->prepare("INSERT INTO Order_Items (order_id, flower_id, quantity, price) VALUES (?, ?, ?, ?)");
            if (!$orderItemQuery) die("Prepare failed: " . $conn->error);
            $orderItemQuery->bind_param("iiid", $order_id, $flower_id, $quantity, $price);
            if (!$orderItemQuery->execute()) die("Execute failed: " . $orderItemQuery->error);
        }

        // Update the order with the correct total amount
        $updateOrder = $conn->prepare("UPDATE Orders SET total_amount = ? WHERE order_id = ?");
        if (!$updateOrder) die("Prepare failed: " . $conn->error);
        $updateOrder->bind_param("di", $total_amount, $order_id);
        if (!$updateOrder->execute()) die("Execute failed: " . $updateOrder->error);

        // Clear the cart for this user
        $clearCart = $conn->prepare("DELETE FROM Cart WHERE user_id = ?");
        if (!$clearCart) die("Prepare failed: " . $conn->error);
        $clearCart->bind_param("i", $user_id);
        if (!$clearCart->execute()) die("Execute failed: " . $clearCart->error);

        echo "Order placed successfully!";
    }
    else 
    {
        echo "Your cart is empty!";
    }
}

/*===========================
  FETCH CART ITEMS FOR VIEW
===========================*/
$cartQuery = $conn->prepare("SELECT C.flower_id, C.quantity, C.total_price, F.name AS flower_name, F.price AS unit_price
                              FROM Cart C JOIN Flowers F ON C.flower_id = F.flower_id
                              WHERE C.user_id = ?");
if (!$cartQuery) die("Prepare failed: " . $conn->error);
$cartQuery->bind_param("i", $user_id);
if (!$cartQuery->execute()) die("Execute failed: " . $cartQuery->error);
$cartResult = $cartQuery->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flower Shop - Cart</title>
  <link rel="stylesheet" href="style.css">
  <style>
    /* Basic styles to improve widget appearance */
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    table, th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background-color: #f0f0f0; }
    .flower-item { border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; }
  </style>
</head>
<body>
  <h2>Your Shopping Cart</h2>
  
  <?php if ($cartResult->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Flower Name</th>
          <th>Quantity</th>
          <th>Unit Price</th>
          <th>Total Price</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($item = $cartResult->fetch_assoc()): ?>
          <tr>
            <td><?php echo $item['flower_name']; ?></td>
            <td><?php echo $item['quantity']; ?></td>
            <td>₹<?php echo $item['unit_price']; ?></td>
            <td>₹<?php echo $item['total_price']; ?></td>
            <td>
              <form method="post">
                <input type="hidden" name="flower_id" value="<?php echo $item['flower_id']; ?>">
                <button type="submit" name="remove_from_cart">Remove</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    
 
  <?php else: ?>
    <p>Your cart is empty.</p>
  <?php endif; ?>
  
  <h2>Available Flowers</h2>
  <?php
    // Fetch available flowers to add to cart.
    $flowerQuery = $conn->prepare("SELECT * FROM Flowers");
    if (!$flowerQuery) die("Prepare failed: " . $conn->error);
    if (!$flowerQuery->execute()) die("Execute failed: " . $flowerQuery->error);
    $flowerResult = $flowerQuery->get_result();
    
    while ($row = $flowerResult->fetch_assoc()):
  ?>
    <div class="flower-item">
      <p><strong><?php echo $row['name']; ?></strong></p>
      <p>Price: ₹<?php echo $row['price']; ?></p>
      <form method="POST">
        <label>Quantity: </label>
        <input type="number" name="quantity" value="1" min="1" required>
        <input type="hidden" name="flower_id" value="<?php echo $row['flower_id']; ?>">
        <button type="submit" name="add_to_cart">Add to Cart</button>
      </form>
    </div>
  <?php endwhile; ?>
  <!-- Replace the checkout form with a link -->
<a href="checkout.php" class="checkout-button">Proceed to Checkout</a>
  
  <p><a href="flowers.php">Continue Shopping</a></p>
</body>
</html>