<?php
session_start();
include '../includes/config.php';

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
            header("Location: index.php");
        }
    } else {
        echo "<script>alert('Invalid email or password.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<script>
function togglePassword() {
    var passwordField = document.getElementById("password");
    var icon = document.querySelector(".toggle-password i");
    if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        passwordField.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>
<style>* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.login-container {
    background: #fff;
    padding: 25px;
    width: 350px;
    border-radius: 12px;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
    text-align: center;
}

h2 {
    margin-bottom: 15px;
    color: #333;
}

.input-group {
    position: relative;
    margin: 20px 0;
}

.input-group input {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 2px solid #aaa;
    border-radius: 6px;
    outline: none;
    transition: 0.3s;
}

.input-group label {
    position: absolute;
    top: 50%;
    left: 10px;
    transform: translateY(-50%);
    font-size: 16px;
    color: #888;
    transition: 0.3s;
}

.input-group input:focus + label,
.input-group input:valid + label {
    top: 5px;
    font-size: 12px;
    color: #667eea;
}

.input-group .toggle-password {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #888;
}

button {
    width: 100%;
    padding: 10px;
    border: none;
    background: #667eea;
    color: white;
    font-size: 18px;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #5643a3;
}</style>
<form action="login.php" method="POST">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
</form>
