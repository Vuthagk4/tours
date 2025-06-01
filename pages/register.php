<?php
session_start();
include '../includes/config.php';

$errors = []; // Array to hold validation errors

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST['password'];
    $profile_image = $_FILES['profile_image'] ?? null;

    // Server-side validation
    if (empty($name)) {
        $errors[] = "Name is required!";
    }
    if (empty($email)) {
        $errors[] = "Email is required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format!";
    }
    if (empty($password)) {
        $errors[] = "Password is required!";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters!";
    }

    // Validate profile image
    if ($profile_image && $profile_image['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        if ($profile_image['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading image: " . $profile_image['error'];
        } elseif (!in_array($profile_image['type'], $allowed_types)) {
            $errors[] = "Only JPEG, PNG, or GIF images are allowed!";
        } elseif ($profile_image['size'] > $max_size) {
            $errors[] = "Image size must not exceed 2MB!";
        }
    }

    if (empty($errors)) {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Email already registered!";
        }
        $stmt->close();
    }

    if (empty($errors) && $profile_image && $profile_image['error'] === UPLOAD_ERR_OK) {
        // Ensure upload directory exists
        $upload_dir = dirname(__DIR__) . '/Uploads/users/';
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                $errors[] = "Failed to create upload directory!";
            }
        }

        // Handle image upload
        $image_name = uniqid() . '_' . basename($profile_image['name']);
        $image_path = $upload_dir . $image_name;

        if (!is_writable($upload_dir)) {
            $errors[] = "Upload directory is not writable!";
        } elseif (!move_uploaded_file($profile_image['tmp_name'], $image_path)) {
            $errors[] = "Failed to move uploaded image!";
        }
    } else {
        $image_name = null; // No image uploaded
    }

    if (empty($errors)) {
        // Hash password
        $password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'customer'; // Default role

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, profile_image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $password, $role, $image_name);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
            exit();
        } else {
            $errors[] = "Error during registration: " . $conn->error;
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f6f5f7;
        }

        .register-container {
            background: #fff;
            padding: 25px;
            width: 400px;
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25),
                0 10px 10px rgba(0, 0, 0, 0.22);
            text-align: center;
        }

        h2 {
            margin-bottom: 15px;
            color: #333;
        }

        .input-group {
            margin: 15px 0;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px;
            font-size: 14px;
            border: none;
            background: #eee;
            border-radius: 5px;
            outline: none;
        }

        .input-group input[type="file"] {
            padding: 8px;
            background: transparent;
            font-size: 12px;
        }

        button {
            width: 100%;
            padding: 12px;
            border: 1px solid #FF4B2B;
            background: #FF4B2B;
            color: white;
            font-size: 12px;
            font-weight: bold;
            border-radius: 20px;
            cursor: pointer;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
        }

        button:hover {
            background: #e03a1c;
        }

        button:active {
            transform: scale(0.95);
        }

        p {
            margin-top: 15px;
            font-size: 14px;
        }

        p a {
            color: #FF4B2B;
            text-decoration: none;
            font-weight: bold;
        }

        p a:hover {
            text-decoration: underline;
        }

        .error-messages {
            color: red;
            margin-bottom: 10px;
            text-align: left;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h2>Register</h2>
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form action="register.php" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <input type="text" name="name" placeholder="Name" required
                    value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="input-group">
                <input type="file" name="profile_image" accept="image/jpeg,image/png,image/gif">
            </div>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>

</html>