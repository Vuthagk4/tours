
<?php
include "../includes/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST["name"]);
  $email = trim($_POST["email"]);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);


  // **Check if Email Already Exists**
  $stmt = $conn->prepare("SELECT email FROM admins WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
      echo "<script>alert('Email already registered!'); window.location.href='register.php';</script>";
      exit();
  }
  $stmt->close();

  // **Insert New User**
  $created_at = date("Y-m-d H:i:s");
  $stmt = $conn->prepare("INSERT INTO admins (name, email, password, created_at) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $name, $email, $password, $created_at);
  

  if ($stmt->execute()) {
      echo "<script>alert('Registration successful!'); window.location.href='admin_login.php';</script>";
      exit();
  } else {
      echo "<script>alert('Error during registration!'); window.location.href='admin_register.php';</script>";
      exit();
  }

  $stmt->close();
}






?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Registration</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <style>
    body {
      background: #f3f4f6;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      font-family: 'Segoe UI', sans-serif;
    }

    .register-container {
      background: #fff;
      border-radius: 16px;
      padding: 40px 30px;
      max-width: 450px;
      width: 100%;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .register-container h2 {
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

    .btn-register {
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

    .btn-register:hover {
      background: linear-gradient(to right, #6a48c7, #8a4cec);
    }
  </style>
</head>
<body>
  <div class="register-container">
    <h2>Admin Registration</h2>
    <p>Create your admin account</p>

    <form action="admin_register.php" method="POST">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="name" placeholder="Enter your name" required />
      </div>

      <div class="form-group">
        <label>Email address</label>
        <input type="email" name="email" placeholder="Enter your email" required />
      </div>

      <div class="form-group form-group-relative">
        <label>Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required />
        <i class="fa-solid fa-eye toggle-password" onclick="togglePassword()"></i>
      </div>

      <button class="btn-register" type="submit">Register</button>
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
</body>
</html>

<?php if (isset($swal)) echo $swal; ?>
