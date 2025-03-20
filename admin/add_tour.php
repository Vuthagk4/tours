<?php
include '../includes/header.php';
include '../includes/config.php';

// Check if the admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch destinations
$destinations = $conn->query("SELECT * FROM destinations");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $destination_id = intval($_POST["destination_id"]);
    $title = htmlspecialchars(trim($_POST["title"]));
    $description = htmlspecialchars(trim($_POST["description"]));
    $price = floatval($_POST["price"]);
    $duration = htmlspecialchars(trim($_POST["duration"]));

    // Check if destination exists
    $check_destination = $conn->prepare("SELECT COUNT(*) FROM destinations WHERE destination_id = ?");
    $check_destination->bind_param("i", $destination_id);
    $check_destination->execute();
    $check_destination->bind_result($count);
    $check_destination->fetch();
    $check_destination->close();

    if ($count == 0) {
        die("<script>alert('Invalid destination selected!');</script>");
    }

    // Image Upload Validation
    $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
    $image = $_FILES["image"]["name"];
    $image_tmp = $_FILES["image"]["tmp_name"];
    $image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));

    if (!in_array($image_ext, $allowed_extensions)) {
        die("<script>alert('Invalid file type! Only JPG, JPEG, PNG, and GIF are allowed.');</script>");
    }

    $image_new_name = uniqid() . "." . $image_ext;
    $upload_dir = "../uploads/"; // Set upload directory

    // Create directory if it does not exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create the folder with full permissions
    }

    // Move uploaded file
    if (!move_uploaded_file($image_tmp, $upload_dir . $image_new_name)) {
        die("<script>alert('File upload failed!');</script>");
    }

    // Insert tour
    $stmt = $conn->prepare("INSERT INTO tours (destination_id, title, description, price, duration, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $destination_id, $title, $description, $price, $duration, $image_new_name);
    
    if ($stmt->execute()) {
        echo "<script>alert('Tour added successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error adding tour!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0px;
        }

        /* Form Container */
        .form-add {
            max-width: 500px;
            background: white;
            padding: 20px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Headings */
        h2 {
            text-align: center;
            color: #333;
        }

        /* Labels */
        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }

        /* Inputs & Textarea */
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
            font-size: 16px;
        }

        textarea {
            height: 100px;
            resize: none;
        }

        /* File Input */
        input[type="file"] {
            border: none;
        }

        /* Submit Button */
        .button {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Responsive */
        @media (max-width: 600px) {
            form {
                width: 90%;
            }
        }

    </style>
</head>
<body>

    <h2>Add New Tour</h2>
    <form class="form-add" action="" method="post" enctype="multipart/form-data">
        <label>Destination:</label>
        <select name="destination_id" required>
            <option value="">Select a Destination</option>
            <?php while ($row = $destinations->fetch_assoc()): ?>
                <option value="<?= $row['destination_id']; ?>"><?= $row['name']; ?></option>
            <?php endwhile; ?>
        </select>

        <label>Title:</label>
        <input type="text" name="title" required><br>

        <label>Description:</label>
        <textarea name="description"></textarea><br>

        <label>Price ($):</label>
        <input type="number" name="price" step="0.01" required><br>

        <label>Duration:</label>
        <input type="text" name="duration" required><br>

        <label>Image:</label>
        <input type="file" name="image" required><br>

        <button class="btn btn-primary" type="submit">Add Tour</button>
    </form>

</body>
</html>
