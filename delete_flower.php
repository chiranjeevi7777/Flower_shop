<?php
include 'db_connect.php';

if (!isset($_GET["id"])) {
    die("Invalid request.");
}

$flower_id = $_GET["id"];

// 1. Delete from dependent tables first
try {
    // Delete from Cart (if flower exists in carts)
    $stmt = $conn->prepare("DELETE FROM Cart WHERE flower_id = ?");
    $stmt->bind_param("i", $flower_id);
    $stmt->execute();

    // Delete from Order_Items
    $stmt = $conn->prepare("DELETE FROM Order_Items WHERE flower_id = ?");
    $stmt->bind_param("i", $flower_id);
    $stmt->execute();

    // Now delete from Flowers
    $stmt = $conn->prepare("DELETE FROM Flowers WHERE flower_id = ?");
    $stmt->bind_param("i", $flower_id);
    
    if ($stmt->execute()) {
        echo "Flower deleted successfully!";
        header("refresh:2; url=flowers.php");
    } else {
        echo "Error deleting flower.";
    }
} catch (mysqli_sql_exception $e) {
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>