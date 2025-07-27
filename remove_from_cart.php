<?php
session_start();
include "db_connect.php";

if (isset($_POST['flower_id']) && isset($_SESSION['customer_id'])) {
    $flower_id = $_POST['flower_id'];
    $customer_id = $_SESSION['customer_id'];

    $deleteItem = $conn->prepare("DELETE FROM Cart WHERE customer_id = ? AND flower_id = ?");
    $deleteItem->bind_param("ii", $customer_id, $flower_id);
    $deleteItem->execute();

    echo "Removed from cart.";
}
?>
