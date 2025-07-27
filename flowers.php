<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Fetch all flowers
$sql = "SELECT * FROM Flowers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Manage Flowers</title>
</head>
<body>
    <h2>Flower Management</h2>

    <h3>Add New Flower</h3>
<form action="add_flower.php" method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Flower Name" required>
    <input type="text" name="color" placeholder="Color">
    <input type="number" step="0.01" name="price" placeholder="Price" required>
    <textarea name="description" placeholder="Description"></textarea>
    <input type="file" name="flower_image" accept="image/*" required> 
    <button type="submit">Add Flower</button>
</form>


    <h3>Flower List</h3>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Image</th>
        <th>Name</th>
        <th>Color</th>
        <th>Price</th>
        <th>Description</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row["flower_id"] ?></td>
        <td>
            <?php if (!empty($row["image_path"])): ?>
                <img src="<?= $row["image_path"] ?>" alt="Flower Image" width="100">
            <?php else: ?>
                No Image
            <?php endif; ?>
        </td>
        <td><?= $row["name"] ?></td>
        <td><?= $row["color"] ?></td>
        <td>₹<?= $row["price"] ?></td>
        <td><?= $row["description"] ?></td>
        <td>
            <a href="edit_flower.php?id=<?= $row['flower_id'] ?>">Edit</a>
            <a href="delete_flower.php?id=<?= $row['flower_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

    <form action="cart.php" method="POST">
    <input type="hidden" name="flower_id" value="<?php echo $flower['flower_id']; ?>">
    <button type="submit">Add to Cart</button>
</form>

    <a href="dashboard.php">Back to Dashboard</a>
    <a href="logout.php">Logout</a>

</body>
</html>
