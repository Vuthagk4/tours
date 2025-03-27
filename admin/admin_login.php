<?php
session_start();
include '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch admin user from the admins table
    $stmt = $conn->prepare("SELECT admin_id, name, password FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['name'] = $row['name'];
            echo "<script>alert('Admin Login Successful!'); window.location.href='../admin/index.php';</script>";
            exit();
        } else {
            echo "<script>alert('Invalid Password!'); window.location.href='admin_login.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid Email!'); window.location.href='admin_login.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }
        .wrapper {
            width: 380px;
            padding: 35px;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }
        .wrapper h2 {
            font-size: 24px;
            color: #fff;
            margin-bottom: 20px;
        }
        .input-field {
            position: relative;
            margin: 20px 0;
        }
        .input-field input {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.3);
            color: #fff;
            font-size: 16px;
            outline: none;
            padding-right: 40px;
        }
        .input-field input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #fff;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: #ff6f61;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn:hover {
            background: #ff8a80;
        }
        .forgot-password {
            margin-top: 10px;
        }
        .forgot-password a {
            color: #fff;
            text-decoration: none;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Admin Login</h2>
        <form action="admin_login.php" method="POST">
            <div class="input-field">
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-field">
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
                <span class="toggle-password" onclick="togglePassword()">
                    <i class="fas fa-eye"></i>
                </span>
            </div>

            <button type="submit" class="btn">Login</button>
            <div class="forgot-password">
                <a href="#">Forgot password?</a>
            </div>
        </form>
    </div>

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
</body>
</html>
