<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if the admin is logged in
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'login_register');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize messages
$message = "";

// Fetch products
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Check for form submission for adding a product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['product_image'])) {
    $productName = htmlspecialchars(trim($_POST['product_name']));
    $productPrice = floatval($_POST['product_price']);
    $productImage = $_FILES['product_image'];

    // Validate input
    if ($productName && $productPrice > 0 && $productImage['size'] > 0) {
        // Validate image file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($productImage['type'], $allowedTypes)) {
            // Limit file size to 2MB
            if ($productImage['size'] <= 2 * 1024 * 1024) {
                // Handle image upload
                $imageName = time() . '_' . basename($productImage['name']);
                $targetDir = 'uploads/';
                $targetFile = $targetDir . $imageName;

                if (move_uploaded_file($productImage['tmp_name'], $targetFile)) {
                    // Insert new product into the database
                    $stmt = $conn->prepare("INSERT INTO products (name, price, image) VALUES (?, ?, ?)");
                    $stmt->bind_param("sds", $productName, $productPrice, $imageName);

                    if ($stmt->execute()) {
                        $message = "Product added successfully!";
                    } else {
                        $message = "Error adding product: " . $conn->error;
                    }

                    $stmt->close();
                } else {
                    $message = "Failed to upload image.";
                }
            } else {
                $message = "Image file size exceeds 2MB limit.";
            }
        } else {
            $message = "Invalid file type. Only JPEG, PNG, and GIF allowed.";
        }
    } else {
        $message = "Invalid input. Please fill all fields correctly.";
    }
}

// Check for form submission for deleting a product
if (isset($_POST['delete_product'])) {
    $productId = intval($_POST['product_id']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);

    if ($stmt->execute()) {
        $message = "Product deleted successfully!";
    } else {
        $message = "Error deleting product: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to the Admin Dashboard</h1>

        <!-- Display Messages -->
        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Add Product Form -->
        <h2>Add a New Product</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="text" name="product_name" placeholder="Product Name" required>
            <input type="number" step="0.01" name="product_price" placeholder="Product Price" required>
            <input type="file" name="product_image" required>
            <button type="submit">Add Product</button>
        </form>

        <!-- Product List -->
        <h2>Product List</h2>
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['price']; ?></td>
                            <td><img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" width="50"></td>
                            <td>
                                <form action="" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete_product">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No products found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
