<?php
// Set custom session save path
session_save_path('C:/Users/TECH POINT/Desktop/flower_shop/sessions');

// Ensure the 'sessions' folder exists
if (!is_dir('C:/Users/TECH POINT/Desktop/flower_shop/sessions')) {
    mkdir('C:/Users/TECH POINT/Desktop/flower_shop/sessions', 0777, true);
}

// Start the session
session_start();

// Store some session data
$_SESSION['customer_id'] = 1;
$_SESSION['username'] = 'JohnDoe';
$_SESSION['cart'] = array('flower_id' => 1, 'quantity' => 2);

// Check if session variables are set
if (isset($_SESSION['customer_id'])) {
    echo 'Customer ID: ' . $_SESSION['customer_id'] . '<br>';
    echo 'Username: ' . $_SESSION['username'] . '<br>';
    echo 'Cart: ' . print_r($_SESSION['cart'], true) . '<br>';
} else {
    echo 'Session data is not set.';
}
?>
