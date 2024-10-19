<?php
session_start();
require_once 'db_connect.php';

// Fetch admin ID and password from form
$admin_id = $_POST['admin_id'];
$password = $_POST['password'];

// Query to check if the admin ID and password are valid
$query = "SELECT * FROM admin WHERE admin_id = ? AND password = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $admin_id, $password);
$stmt->execute();
$result = $stmt->get_result();

// If the admin credentials are correct
if ($result->num_rows > 0) {
    $_SESSION['admin_id'] = $admin_id; // Start session
    header("Location: admin_dashboard.php"); // Redirect to admin dashboard
} else {
    $_SESSION['error'] = "Invalid Admin ID or Password!";
    header("Location: admin_login.php"); // Redirect back to login with error
}

$stmt->close();
$conn->close();
?>
