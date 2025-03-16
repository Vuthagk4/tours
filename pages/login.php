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
<style>
    /* @import url("https://fonts.googleapis.com/css2?family=Open+Sans:wght@200;300;400;500;600;700&display=swap"); */

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Open Sans", sans-serif;
}

body {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  width: 100%;
  padding: 0 10px;
  /* background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%); */
}

body::before {
  content: "";
  position: absolute;
  width: 100%;
  height: 100%;
  background: url("../assets/images/log.jpg"), #000;
  background-position: center;
  background-size: cover;
  z-index: -1;
}

.wrapper {
  width: 400px;
  border-radius: 15px;
  padding: 40px;
  text-align: center;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.37);
  transition: all 0.3s ease;
}

.wrapper:hover {
  box-shadow: 0 12px 48px rgba(0, 0, 0, 0.5);
}

form {
  display: flex;
  flex-direction: column;
}

h2 {
  font-size: 2.4rem;
  margin-bottom: 25px;
  color: #ffffff;
  letter-spacing: 1px;
}

.input-field {
  position: relative;
  border-bottom: 2px solid rgba(255, 255, 255, 0.3);
  margin: 20px 0;
}

.input-field label {
  position: absolute;
  top: 50%;
  left: 0;
  transform: translateY(-50%);
  color: #ffffff;
  font-size: 1.4rem;
  pointer-events: none;
  transition: 0.3s ease;
}

.input-field input {
  width: 100%;
  height: 40px;
  background: transparent;
  border: none;
  outline: none;
  font-size: 17px;
  color: #ffffff;
  padding: 0 10px;
}

.input-field input:focus~label,
.input-field input:valid~label {
  font-size: 0.9rem;
  top: 10px;
  transform: translateY(-150%);
  color: #ffdde1;
}

.forget {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin: 25px 0 35px 0;
  color: #ffffff;
}

#remember {
  accent-color: #ffdde1;
}

.forget label {
  display: flex;
  align-items: center;
}

.forget label p {
  margin-left: 8px;
}

.wrapper a {
  color: #ffdde1;
  text-decoration: none;
}

.wrapper a:hover {
  text-decoration: underline;
}

button {
  background-color: #271930;
  color: #ffffff;
  font-weight: 600;
  border: none;
  padding: 15px 20px;
  cursor: pointer;
  border-radius: 25px;
  font-size: 17px;
  border: 2px solid transparent;
  transition: all 0.3s ease;
}

button:hover {
  color: #000000;
  background: rgba(255, 255, 255, 0.2);
  border-color: #ffffff;
}

.register {
  text-align: center;
  margin-top: 30px;
  color: #ffffff;
}
                                         
                                                     
            
</style>

<div class="wrapper">
    <form action="login.php" method="POST">
      <h2>Login Form</h2>
      <div class="input-field">
        <input type="email" name="email" required>
        <label>Enter your email</label>
      </div>
      <div class="input-field">
        <input type="password" name="password"  required>
        <label>Enter your password</label>
      </div>
      <div class="forget">
        <label for="remember">
          <input type="checkbox" id="remember">
          <p>Remember me</p>
        </label>
        <a href="#">Forgot password?</a>
      </div>
      <button type="submit">Log In</button>
      <div class="register">
        <p>Don't have an account? <a href="register.php" onclick="return true;">Register</a></p>
      </div>
    </form>
  </div>