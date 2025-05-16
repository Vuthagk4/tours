<?php
session_start();
include "../includes/config.php";

$errors = [];
$loginSuccess = false;
$adminName = "";
$showSignup = false;
$signupSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Handle Login
  if (isset($_POST['login'])) {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
      echo "<script>setTimeout(() => swal('Error', 'Email and password are required!', 'error'), 100);</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo "<script>setTimeout(() => swal('Error', 'Invalid email format!', 'error'), 100);</script>";
    } else {
      $stmt = $conn->prepare("SELECT admin_id, name, password FROM admins WHERE email = ?");
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $stmt->store_result();

      if ($stmt->num_rows > 0) {
        $stmt->bind_result($admin_id, $name, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
          session_regenerate_id();
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
  }
  // Handle Signup
  elseif (isset($_POST['signup'])) {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($name))
      $errors[] = "Name is required!";
    if (empty($email))
      $errors[] = "Email is required!";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
      $errors[] = "Invalid email format!";
    if (empty($password))
      $errors[] = "Password is required!";
    elseif (strlen($password) < 8)
      $errors[] = "Password must be at least 8 characters!";
    if ($password !== $confirm_password)
      $errors[] = "Passwords do not match!";

    if (empty($errors)) {
      $stmt = $conn->prepare("SELECT email FROM admins WHERE email = ?");
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $stmt->store_result();
      if ($stmt->num_rows > 0)
        $errors[] = "Email already registered!";
      $stmt->close();
    }

    if (empty($errors)) {
      $password = password_hash($password, PASSWORD_DEFAULT);
      $created_at = date("Y-m-d H:i:s");
      $stmt = $conn->prepare("INSERT INTO admins (name, email, password, created_at) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssss", $name, $email, $password, $created_at);
      if ($stmt->execute()) {
        $signupSuccess = true;
      } else {
        $errors[] = "Error during registration!";
      }
      $stmt->close();
    } else {
      $showSignup = true;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Authentication</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
  <style>
    @import url('https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap');

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    html,
    body {
      display: grid;
      height: 100%;
      width: 100%;
      place-items: center;
      background: -webkit-linear-gradient(left, #003366, #004080, #0059b3, #0073e6);
    }

    ::selection {
      background: #1a75ff;
      color: #fff;
    }

    .wrapper {
      overflow: hidden;
      max-width: 390px;
      background: #fff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.1);
    }

    .wrapper .title-text {
      display: flex;
      width: 200%;
    }

    .wrapper .title {
      width: 50%;
      font-size: 35px;
      font-weight: 600;
      text-align: center;
      transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    .wrapper .slide-controls {
      position: relative;
      display: flex;
      height: 50px;
      width: 100%;
      overflow: hidden;
      margin: 30px 0 10px 0;
      justify-content: space-between;
      border: 1px solid lightgrey;
      border-radius: 15px;
    }

    .slide-controls .slide {
      height: 100%;
      width: 100%;
      color: #fff;
      font-size: 18px;
      font-weight: 500;
      text-align: center;
      line-height: 48px;
      cursor: pointer;
      z-index: 1;
      transition: all 0.6s ease;
    }

    .slide-controls label.signup {
      color: #000;
    }

    .slide-controls .slider-tab {
      position: absolute;
      height: 100%;
      width: 50%;
      left: 0;
      z-index: 0;
      border-radius: 15px;
      background: -webkit-linear-gradient(left, #003366, #004080, #0059b3, #0073e6);
      transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    input[type="radio"] {
      display: none;
    }

    #signup:checked~.slider-tab {
      left: 50%;
    }

    #signup:checked~label.signup {
      color: #fff;
      cursor: default;
      user-select: none;
    }

    #signup:checked~label.login {
      color: #000;
    }

    #login:checked~label.signup {
      color: #000;
    }

    #login:checked~label.login {
      cursor: default;
      user-select: none;
    }

    .wrapper .form-container {
      width: 100%;
      overflow: hidden;
    }

    .form-container .form-inner {
      display: flex;
      width: 200%;
    }

    .form-container .form-inner form {
      width: 50%;
      transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    .form-inner form .field {
      height: 50px;
      width: 100%;
      margin-top: 20px;
      position: relative;
    }

    .form-inner form .field input {
      height: 100%;
      width: 100%;
      outline: none;
      padding-left: 15px;
      border-radius: 15px;
      border: 1px solid lightgrey;
      border-bottom-width: 2px;
      font-size: 17px;
      transition: all 0.3s ease;
    }

    .form-inner form .field input:focus {
      border-color: #1a75ff;
    }

    .form-inner form .field input::placeholder {
      color: #999;
      transition: all 0.3s ease;
    }

    form .field input:focus::placeholder {
      color: #1a75ff;
    }

    .form-inner form .pass-link {
      margin-top: 5px;
      text-align: left;
    }

    .form-inner form .signup-link {
      text-align: center;
      margin-top: 30px;
    }

    .form-inner form .pass-link a,
    .form-inner form .signup-link a {
      color: #1a75ff;
      text-decoration: none;
    }

    .form-inner form .pass-link a:hover,
    .form-inner form .signup-link a:hover {
      text-decoration: underline;
    }

    form .btn {
      height: 50px;
      width: 100%;
      border-radius: 15px;
      position: relative;
      overflow: hidden;
    }

    form .btn .btn-layer {
      height: 100%;
      width: 300%;
      position: absolute;
      left: -100%;
      background: -webkit-linear-gradient(right, #003366, #004080, #0059b3, #0073e6);
      border-radius: 15px;
      transition: all 0.4s ease;
    }

    form .btn:hover .btn-layer {
      left: 0;
    }

    form .btn input[type="submit"] {
      height: 100%;
      width: 100%;
      z-index: 1;
      position: relative;
      background: none;
      border: none;
      color: #fff;
      padding-left: 0;
      border-radius: 15px;
      font-size: 20px;
      font-weight: 500;
      cursor: pointer;
    }

    .field .toggle-password {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #aaa;
    }

    .error-messages {
      color: red;
      margin-bottom: 10px;
      text-align: left;
    }
  </style>
</head>

<body>
  <div class="wrapper">
    <div class="title-text">
      <div class="title login">Admin Login</div>
      <div class="title signup">Admin Signup</div>
    </div>
    <div class="form-container">
      <div class="slide-controls">
        <input type="radio" name="slide" id="login" checked>
        <input type="radio" name="slide" id="signup">
        <label for="login" class="slide login">Login</label>
        <label for="signup" class="slide signup">Signup</label>
        <div class="slider-tab"></div>
      </div>
      <div class="form-inner">
        <!-- Login Form -->
        <form action="" method="POST" class="login">
          <div class="field">
            <input type="email" name="email" placeholder="Email Address" required>
          </div>
          <div class="field">
            <input type="password" id="password-login" name="password" placeholder="Password" required>
            <i class="fa-solid fa-eye toggle-password" onclick="togglePassword('password-login')"></i>
          </div>
          <div class="pass-link"><a href="#">Forgot password?</a></div>
          <div class="field btn">
            <div class="btn-layer"></div>
            <input type="submit" name="login" value="Login">
          </div>
        </form>
        <!-- Signup Form -->
        <form action="" method="POST" class="signup">
          <?php if (!empty($errors)): ?>
            <div class="error-messages">
              <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <div class="field">
            <input type="text" name="name" placeholder="Name" required
              value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
          </div>
          <div class="field">
            <input type="email" name="email" placeholder="Email Address" required
              value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
          </div>
          <div class="field">
            <input type="password" id="password-signup" name="password" placeholder="Password" required>
            <i class="fa-solid fa-eye toggle-password" onclick="togglePassword('password-signup')"></i>
          </div>
          <div class="field">
            <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm password"
              required>
          </div>
          <div class="field btn">
            <div class="btn-layer"></div>
            <input type="submit" name="signup" value="Signup">
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    const loginText = document.querySelector(".title-text .login");
    const loginForm = document.querySelector("form.login");
    const loginBtn = document.querySelector("label.login");
    const signupBtn = document.querySelector("label.signup");

    signupBtn.onclick = () => {
      loginForm.style.marginLeft = "-50%";
      loginText.style.marginLeft = "-50%";
    };
    loginBtn.onclick = () => {
      loginForm.style.marginLeft = "0%";
      loginText.style.marginLeft = "0%";
    };

    function togglePassword(id) {
      const input = document.getElementById(id);
      const icon = input.nextElementSibling;
      if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
      } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
      }
    }

    document.querySelector("form.signup").addEventListener("submit", function (e) {
      const password = document.getElementById("password-signup").value;
      const confirmPassword = document.getElementById("confirm-password").value;
      if (password !== confirmPassword) {
        e.preventDefault();
        swal("Error", "Passwords do not match!", "error");
      }
    });

    <?php if ($loginSuccess): ?>
      document.addEventListener("DOMContentLoaded", function () {
        swal("Login Successful!", "Welcome back, <?= htmlspecialchars($adminName) ?>!", "success")
          .then(() => window.location.href = "index.php");
      });
    <?php endif; ?>

    <?php if ($signupSuccess): ?>
      document.addEventListener("DOMContentLoaded", function () {
        loginBtn.click();
        swal("Success", "Registration successful! Please log in.", "success");
      });
    <?php endif; ?>

    <?php if ($showSignup): ?>
      document.addEventListener("DOMContentLoaded", function () {
        signupBtn.click();
      });
    <?php endif; ?>
  </script>
</body>

</html>