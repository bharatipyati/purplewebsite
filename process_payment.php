<?php
session_start();

// In a real scenario, you would integrate payment gateway API here

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    echo "Your cart is empty.";
    exit;
}

// Process the payment (placeholder)
echo "Payment processed successfully!";

// Clear the cart after payment
unset($_SESSION['cart']);
?>
