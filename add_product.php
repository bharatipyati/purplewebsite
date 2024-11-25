<?php
// Database connection
$conn = new mysqli('localhost', 'username', 'password', 'your_database_name');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize response messages
$message = "";

// Validate form data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['product_image'])) {
    $product_name = trim($_POST['product_name']);
    $product_price = floatval($_POST['product_price']);
    $product_image = $_FILES['product_image'];

    // Validate product name and price
    if (empty($product_name) || $product_price <= 0) {
        $message = "Invalid product name or price.";
    } else {
        // Validate image file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($product_image['type'], $allowed_types) && $product_image['size'] <= 2 * 1024 * 1024) {
            $target_dir = "uploads/";
            $image_name = time() . '_' . basename($product_image['name']);
            $target_file = $target_dir . $image_name;

            // Upload image
            if (move_uploaded_file($product_image['tmp_name'], $target_file)) {
                // Use prepared statements to insert product
                $stmt = $conn->prepare("INSERT INTO products (name, price, image) VALUES (?, ?, ?)");
                $stmt->bind_param("sds", $product_name, $product_price, $image_name);

                if ($stmt->execute()) {
                    $message = "Product added successfully.";
                } else {
                    $message = "Database error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = "Failed to upload image.";
            }
        } else {
            $message = "Invalid file type or size. Only JPEG, PNG, and GIF files under 2MB are allowed.";
        }
    }
} else {
    $message = "Invalid request.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Upload</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="message">
        <p><?php echo htmlspecialchars($message); ?></p>
        <a href="admin_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
