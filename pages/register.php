<?php
include 'db_connection.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $role = 'customer'; // Default role

    // Check if the email already exists
    $check_email = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        echo "<script>alert('Email already exists!');</script>";
    } else {
        // Insert user into the database
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $role);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!');</script>";
            header("Location: login.php"); // Redirect to login page
        } else {
            echo "<script>alert('Error registering user.');</script>";
        }
        $stmt->close();
    }
    $check_email->close();
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
    * {
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

.register-container {
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
}

p {
    margin-top: 10px;
}

p a {
    color: #667eea;
    text-decoration: none;
    font-weight: bold;
}

p a:hover {
    text-decoration: underline;
}

</style>
<form action="register.php" method="POST">
    <input type="text" name="name" placeholder="Full Name" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Register</button>
</form>
