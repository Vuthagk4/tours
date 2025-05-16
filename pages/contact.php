<?php
include '../includes/header.php'; // Include Header
include '../includes/config.php'; // Include Database Config

// Initialize variables
$name = $email = $message = "";
$success_message = $error_message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    // Additional validation
    if (empty($name) || empty($email) || empty($message)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // Prepare and bind to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            $success_message = "Your message has been sent successfully!";
            // Clear form fields
            $name = $email = $message = "";
        } else {
            $error_message = "Error: " . $stmt->error;
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
    <title>Contact Us - Wanderlust Travel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .contact {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80') no-repeat center center/cover;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .contact::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .contact h3 {
            font-size: 2.5em;
            text-align: center;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .contact-info {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            z-index: 2;
        }

        .contact-info p {
            font-size: 1.1em;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .contact-info ul {
            list-style: none;
            padding: 0;
        }

        .contact-info ul li {
            font-size: 1em;
            margin: 10px 0;
        }

        .contact-info ul li i {
            margin-right: 10px;
            color: #00d4ff;
        }

        .contact-info ul li a {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s;
        }

        .contact-info ul li a:hover {
            color: #00d4ff;
        }

        .form {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            background: rgba(255, 255, 255, 0.9);
            transition: background 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            background: #fff;
            outline: 2px solid #00d4ff;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 150px;
        }

        .form button {
            display: block;
            width: 100%;
            padding: 12px;
            background: #00d4ff;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 1.1em;
            cursor: pointer;
            transition: background 0.3s;
        }

        .form button:hover {
            background: #0098b3;
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            position: relative;
            z-index: 2;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .contact {
                margin: 20px;
                padding: 15px;
            }

            .contact h3 {
                font-size: 2em;
            }

            .form {
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <section id="contact" class="contact">
        <h3>Contact Wanderlust Travel</h3>
        <?php if ($success_message): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php elseif ($error_message): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <div class="contact-info">
            <p>
                Ready to explore the world? Fill out the form below to plan your dream vacation or get in touch with our
                travel experts!
            </p>
            <ul>
                <li>
                    <i class="fas fa-map-marker-alt"></i>123 Wanderlust Way, Adventure City, USA
                </li>
                <li>
                    <i class="fas fa-envelope"></i><a
                        href="mailto:info@wanderlusttravel.com">info@wanderlusttravel.com</a>
                </li>
                <li>
                    <i class="fas fa-phone-alt"></i><a href="tel:555-987-6543">555-987-6543</a>
                </li>
            </ul>
        </div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="form">
            <div class="form-group">
                <input type="text" name="name" id="name" placeholder="Your Name"
                    value="<?php echo htmlspecialchars($name); ?>" required />
            </div>
            <div class="form-group">
                <input type="email" name="email" id="email" placeholder="Your Email"
                    value="<?php echo htmlspecialchars($email); ?>" required />
            </div>
            <div class="form-group">
                <textarea name="message" id="message" placeholder="Tell us about your travel plans"
                    required><?php echo htmlspecialchars($message); ?></textarea>
            </div>
            <button type="submit">Send Message</button>
        </form>
    </section>
</body>

</html>