<?php
include('db_connect.php'); // Include the database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}
$user_id = $_SESSION['user_id'];

// Handle Removing Item from Cart
if (isset($_POST['remove_from_cart'])) {
    $flower_id = $_POST['flower_id'];
    
    // Delete the cart entry for this user and flower
    $deleteQuery = $conn->prepare("DELETE FROM Cart WHERE user_id = ? AND flower_id = ?");
    $deleteQuery->bind_param("ii", $user_id, $flower_id);
    $deleteQuery->execute();
    // Optionally you can add a message: echo "Item removed from cart!";
}

// Handle Checkout
if (isset($_POST['checkout'])) {
    // Redirect to the checkout page (which will process the order)
    header("Location: checkout.php");
    exit();
}

// Fetch Cart Items for this user with flower details
$query = $conn->prepare("SELECT C.flower_id, C.quantity, C.total_price, F.name AS flower_name, F.price AS unit_price 
                         FROM Cart C 
                         JOIN Flowers F ON C.flower_id = F.flower_id 
                         WHERE C.user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();

$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flower Shop - Cart</title>
  <link rel="stylesheet" href="style.css">  <!-- Link your CSS file here -->
</head>
<body>
  <h2>Your Shopping Cart</h2>
  
  <?php if (!empty($cart_items)): ?>
    <table border="1">
      <thead>
         <tr>
           <th>Flower ID</th>
           <th>Flower Name</th>
           <th>Quantity</th>
           <th>Unit Price</th>
           <th>Total Price</th>
           <th>Action</th>
         </tr>
      </thead>
      <tbody>
         <?php foreach ($cart_items as $item): ?>
         <tr>
           <td><?php echo $item['flower_id']; ?></td>
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
         <?php endforeach; ?>
      </tbody>
    </table>
    
    <form method="post">
       <button type="submit" name="checkout">Proceed to Checkout</button>
    </form>
  <?php else: ?>
     <p>Your cart is empty.</p>
  <?php endif; ?>

  <h3>Available Flowers:</h3>
  <?php
    // Fetch available flowers to add to the cart.
    // This form posts to your cart management file (e.g., cart.php) which should handle adding items to the Cart table.
    $query = $conn->prepare("SELECT * FROM Flowers");
    $query->execute();
    $result = $query->get_result();
    while ($row = $result->fetch_assoc()):
  ?>
    <div>
      <p>Flower Name: <?php echo $row['name']; ?></p>
      <p>Price: ₹<?php echo $row['price']; ?></p>
      <form method="POST" action="cart.php">
         <label>Quantity: </label>
         <input type="number" name="quantity" value="1" min="1" required>
         <input type="hidden" name="flower_id" value="<?php echo $row['flower_id']; ?>">
         <button type="submit" name="add_to_cart">Add to Cart</button>
      </form>
    </div>
  <?php endwhile; ?>
  
  <p><a href="index.php">Continue Shopping</a></p>
</body>
</html>
<?php
$conn->close();
?>
