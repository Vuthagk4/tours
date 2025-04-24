<?php
session_start();
include "../includes/config.php";

$loginSuccess = false;
$adminName = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST["email"]);
  $password = $_POST["password"];

  $stmt = $conn->prepare("SELECT admin_id, name, password FROM admins WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->bind_result($admin_id, $name, $hashed_password);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
      session_regenerate_id(); // for security
      $_SESSION["admin_id"] = $admin_id;
      $_SESSION["admin_name"] = $name;

      $loginSuccess = true;
      $adminName = $name;
    } else {
      echo "<script>setTimeout(() => swal('Error', 'Invalid password!', 'error'), 100);</script>";
    }
  } else {
    echo "<script>setTimeout(() => swal('Error', 'No admin found with that email!', 'error'), 100);</script>";
  }

  $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    body {
      background: #f3f4f6;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      font-family: 'Segoe UI', sans-serif;
    }

    .login-container {
      background: #fff;
      border-radius: 16px;
      padding: 40px 30px;
      max-width: 450px;
      width: 100%;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .login-container h2 {
      font-size: 24px;
      font-weight: 600;
      margin-bottom: 6px;
    }

    .form-group {
      text-align: left;
      margin-bottom: 15px;
    }

    .form-group label {
      font-size: 14px;
      color: #555;
      display: block;
      margin-bottom: 5px;
    }

    .form-group input {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 14px;
    }

    .form-group-relative {
      position: relative;
    }

    .toggle-password {
      position: absolute;
      right: 15px;
      top: 38px;
      cursor: pointer;
      color: #aaa;
    }

    .btn-login {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 10px;
      background: linear-gradient(to right, #7f56d9, #9f5dfd);
      color: white;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn-login:hover {
      background: linear-gradient(to right, #6a48c7, #8a4cec);
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Admin Login</h2>
    <p>Welcome back! Please login.</p>

    <form action="admin_login.php" method="POST">
      <div class="form-group">
        <label>Email address</label>
        <input type="email" name="email" placeholder="Enter your email" required />
      </div>

      <div class="form-group form-group-relative">
        <label>Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required />
        <i class="fa-solid fa-eye toggle-password" onclick="togglePassword()"></i>
      </div>

      <button class="btn-login" type="submit">Login</button>
    </form>
  </div>

  <script>
    function togglePassword() {
      const input = document.getElementById('password');
      const icon = document.querySelector('.toggle-password');
      if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
      } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
      }
    }
  </script>

  <?php if ($loginSuccess): ?>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      swal("Login Successful!", "Welcome back, <?= $adminName ?>!", "success")
      .then(() => window.location.href = "index.php");
    });
  </script>
  <?php endif; ?>
</body>
</html>
