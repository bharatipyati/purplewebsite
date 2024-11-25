<?php
session_start();

require_once "dbconnection.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"]; // User-entered password

    // Admin login
    $sql = "SELECT * FROM admin WHERE username = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $admin = mysqli_fetch_assoc($result);

        if ($admin) {
            // Direct password comparison
            if ($password === $admin["password"]) {
                $_SESSION["admin"] = $admin["id"];
                $_SESSION["role"] = 'admin';
                header("Location: admin_dashboard.php");
                exit();
            } else {
                echo "<div class='alert alert-danger'>Incorrect password.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>No admin found with this email.</div>";
        }
    } else {
        die("SQL Error: " . mysqli_error($conn));
    }
}
?>
<div class="admin-login-form">
    <h2>Admin Login</h2>
    <form action="admin_login.php" method="POST">
        <div class="input-group">
            <input type="email" name="email" placeholder="Email" required />
        </div>
        <div class="input-group">
            <input type="password" name="password" placeholder="Password" required />
        </div>
        <button type="submit" name="admin_login" class="btn">Login as Admin</button>
    </form>
</div>
