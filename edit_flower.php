<?php
include 'db_connect.php';

if (!isset($_GET["id"])) {
    die("Invalid request.");
}

$flower_id = $_GET["id"];

// Fetch existing details
$sql = "SELECT * FROM Flowers WHERE flower_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $flower_id);
$stmt->execute();
$result = $stmt->get_result();
$flower = $result->fetch_assoc();

if (!$flower) {
    die("Flower not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $color = $_POST["color"];
    $price = $_POST["price"];
    $description = $_POST["description"];
    
    // Handle file upload
    if (!empty($_FILES["flower_image"]["name"])) {
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES["flower_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if it's an actual image
        $check = getimagesize($_FILES["flower_image"]["tmp_name"]);
        if ($check === false) {
            die("File is not an image.");
        }

        // Allow only certain file formats
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowed_types)) {
            die("Only JPG, JPEG, PNG & GIF files are allowed.");
        }

        // Move the uploaded file
        if (move_uploaded_file($_FILES["flower_image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        } else {
            die("Error uploading image.");
        }
    } else {
        // If no new image is uploaded, keep the existing one
        $image_path = $flower['image_path'];
    }

    $sql = "UPDATE Flowers SET name=?, color=?, price=?, description=?, image_path=? WHERE flower_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdssi", $name, $color, $price, $description, $image_path, $flower_id);

    if ($stmt->execute()) {
        echo "Flower updated successfully!";
        header("refresh:2; url=flowers.php");
    } else {
        echo "Error updating flower.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Flower</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Edit Flower</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" value="<?= $flower['name'] ?>" required>
        <input type="text" name="color" value="<?= $flower['color'] ?>">
        <input type="number" step="0.01" name="price" value="<?= $flower['price'] ?>" required>
        <textarea name="description"><?= $flower['description'] ?></textarea>
        <input type="file" name="flower_image">
        <p>Current Image: <img src="<?= $flower['image_path'] ?>" width="100"></p>
        <button type="submit">Update</button>
    </form>
    <a href="flowers.php">Back to Flowers</a>
</body>
</html>