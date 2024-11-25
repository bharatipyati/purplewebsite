<?php
// Include your database connection file
include 'dbconnection.php';

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST["reset_password"])) {
    $email = $_POST["email"];
    $new_password = $_POST["new_password"];

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update the password in the database
    $sql = "UPDATE admins SET password = ? WHERE email = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $hashed_password, $email);
        if (mysqli_stmt_execute($stmt)) {
            echo "<div class='alert alert-success'>Password has been reset successfully.</div>";
            header("Location: admin_login.php"); // Redirect to login page
            exit();
        }
        
    }
}
?>
