<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Retrieve user details
    $stmt = $conn->prepare("SELECT user_id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $name, $hashed_password, $role);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        // Store session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['name'] = $name;
        $_SESSION['role'] = $role;

        echo "<script>alert('Login successful!');</script>";

        // Redirect based on role
        if ($role === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: customer_dashboard.php");
        }
    } else {
        echo "<script>alert('Invalid email or password.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
