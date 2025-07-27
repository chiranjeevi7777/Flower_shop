<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $color = $_POST["color"];
    $price = $_POST["price"];
    $description = $_POST["description"];

    // Image Upload Handling
    $targetDir = "uploads/";  // Folder to store images
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $imageFileName = basename($_FILES["flower_image"]["name"]);
    $targetFilePath = $targetDir . $imageFileName;
    $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Allowed file formats
    $allowedFormats = ["jpg", "jpeg", "png", "gif"];
    if (in_array($imageFileType, $allowedFormats)) {
        if (move_uploaded_file($_FILES["flower_image"]["tmp_name"], $targetFilePath)) {
            // Insert flower details into database
            $sql = "INSERT INTO Flowers (name, color, price, description, image_path) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdss", $name, $color, $price, $description, $targetFilePath);

            if ($stmt->execute()) {
                echo "Flower added successfully!";
                header("Location: flowers.php"); // Redirect back to flower list
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            echo "Error uploading image!";
        }
    } else {
        echo "Only JPG, JPEG, PNG, and GIF files are allowed!";
    }
}
?>
