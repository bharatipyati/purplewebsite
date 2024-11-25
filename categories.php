<?php
session_start(); // Start session to store cart items

// Database connection
$conn = new mysqli('localhost', 'root', '', 'login_register');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Add to Cart Function
function addToCart($productName, $productPrice) {
    // Check if the cart already exists in session, if not create it
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if the item already exists in the cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['name'] === $productName) {
            $item['quantity'] += 1; // Increase the quantity
            $found = true;
            break;
        }
    }

    // If the item was not found, add it to the cart
    if (!$found) {
        $_SESSION['cart'][] = [
            'name' => $productName,
            'price' => $productPrice,
            'quantity' => 1
        ];
    }
}

// Handle add to cart action when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the product information from the form
    $productName = $_POST['product_name'];
    $productPrice = $_POST['product_price'];

    // Add the item to the cart using the function defined above
    addToCart($productName, $productPrice);

    // Redirect to the same page after adding to cart
    header("Location: categories.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
    <style>
        /* Styling for cart container */
        .cart-container {
            display: flex;
            justify-content: flex-end;
            align-items: flex-start;
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #f8f8f8;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Add some space for the cart details */
        .cart-details {
            margin-left: 20px;
        }

        /* Styling for products listing */
        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: flex-start;
            padding: 20px;
        }

        .product-item {
            width: 200px;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            border-radius: 8px;
            background-color: #fff;
        }

        .product-item img {
            width: 100%;
            height: auto;
            border-radius: 4px;
        }

        .product-item h3 {
            font-size: 16px;
            margin: 10px 0;
        }

        .product-item p {
            font-size: 14px;
        }

        .product-item button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .product-item button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>

    <h1>Product Categories</h1>

    <!-- Cart Information in Right Corner -->
    <div class="cart-container">
        <div class="cart-details">
            <h2>Your Cart</h2>
            <?php
            if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                foreach ($_SESSION['cart'] as $item) {
                    echo "Product: " . $item['name'] . " - Price: $" . $item['price'] . " - Quantity: " . $item['quantity'] . "<br>";
                }
                echo '<a href="payment.php"><button>Proceed to Payment</button></a>';
            } else {
                echo "Your cart is empty.";
            }
            ?>
        </div>
    </div>

    <!-- Products Listing -->
    <div class="product-container">
        <?php
        // Display products from the database
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Get product details
                $productName = $row['name'];
                $productPrice = $row['price'];
                $productImage = $row['image']; // Assuming image is stored in the database
        ?>
                <div class="product-item">
                    <img src="uploads/<?php echo $productImage; ?>" alt="<?php echo $productName; ?>">
                    <h3><?php echo $productName; ?></h3>
                    <p>Price: $<?php echo $productPrice; ?></p>
                    <form method="POST" action="categories.php">
                        <input type="hidden" name="product_name" value="<?php echo $productName; ?>">
                        <input type="hidden" name="product_price" value="<?php echo $productPrice; ?>">
                        <button type="submit">Add to Cart</button>
                    </form>
                </div>
        <?php
            }
        } else {
            echo "No products available.";
        }
        ?>
    </div>

</body>

</html>
