<?php
session_start(); // Start session to access cart

// Check if cart is empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    echo "Your cart is empty. Please add products before proceeding to payment.";
    exit;
}

$total = 0; // Initialize total price
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
</head>

<body>

    <h1>Your Cart</h1>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($_SESSION['cart'] as $item) {
                $itemTotal = $item['price'] * $item['quantity'];
                $total += $itemTotal;
                echo "<tr>
                        <td>" . $item['name'] . "</td>
                        <td>$" . $item['price'] . "</td>
                        <td>" . $item['quantity'] . "</td>
                        <td>$" . $itemTotal . "</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>

    <h3>Total: $<?php echo $total; ?></h3>

    <!-- Payment form (Example, you can integrate a payment gateway) -->
    <form method="POST" action="process_payment.php">
        <button type="submit">Proceed to Payment</button>
    </form>

</body>

</html>
