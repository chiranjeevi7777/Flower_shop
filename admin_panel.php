<?php
session_start();
include "db_connect.php";

// Handle all actions first
// Login Handling
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = $conn->prepare("SELECT admin_id, password FROM Admins WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['admin_id'];
            header("Location: admin_panel.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid password!";
        }
    } else {
        $_SESSION['error'] = "Admin not found!";
    }
}

// Logout Handling
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_panel.php");
    exit();
}

// Status Update Handling
if (isset($_POST['update_status'])) {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: admin_panel.php");
        exit();
    }

    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $updateQuery = $conn->prepare("UPDATE Orders SET status = ? WHERE order_id = ?");
    $updateQuery->bind_param("si", $status, $order_id);
    
    if ($updateQuery->execute()) {
        $_SESSION['message'] = "Status updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating status: " . $conn->error;
    }
    
    header("Location: admin_panel.php");
    exit();
}

// Authentication Check
if (!isset($_SESSION['admin_id'])) {
    // Show login form if not authenticated
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin Login</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h2>Admin Login</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <form method="post">
            <label>Username:</label>
            <input type="text" name="username" required>
            <br><br>
            <label>Password:</label>
            <input type="password" name="password" required>
            <br><br>
            <button type="submit" name="login">Login</button>
        </form>
    </body>
    </html>
    <?php
    exit();
}

// Show Admin Panel
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>
<body>
    <h2>Admin Dashboard</h2>
    <a href="?logout=1">Logout</a>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="success"><?= $_SESSION['message'] ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <h3>Order Management</h3>
    <?php
    $query = $conn->query("SELECT * FROM Orders ORDER BY order_id DESC");
    
    if ($query->num_rows > 0) {
        echo '<table border="1">
                <tr>
                    <th>Order ID</th>
                    <th>User ID</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>';
    
        while ($row = $query->fetch_assoc()) {
            echo '<tr>
                    <td>' . htmlspecialchars($row['order_id']) . '</td>
                    <td>' . htmlspecialchars($row['user_id']) . '</td>
                    <td>₹' . number_format($row['total_amount'], 2) . '</td>
                    <td>' . htmlspecialchars($row['status']) . '</td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="order_id" value="' . $row['order_id'] . '">
                            <select name="status">
                                <option value="Pending"' . ($row['status'] == 'Pending' ? ' selected' : '') . '>Pending</option>
                                <option value="Completed"' . ($row['status'] == 'Completed' ? ' selected' : '') . '>Completed</option>
                                <option value="Canceled"' . ($row['status'] == 'Canceled' ? ' selected' : '') . '>Canceled</option>
                            </select>
                            <button type="submit" name="update_status">Update</button>
                        </form>
                    </td>
                  </tr>';
        }
    
        echo '</table>';
    } else {
        echo '<p>No orders found.</p>';
    }
    ?>
</body>
</html>