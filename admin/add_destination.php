<?php
include '../includes/header.php';
include '../includes/config.php'; // Database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];

    // Upload Image
    $target_dir = "../assets/uploads/";
    $target_file = $target_dir . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

    // Insert into database
    $sql = "INSERT INTO destinations (name, location, description, image) 
            VALUES ('$name', '$location', '$description', '$target_file')";

    if (mysqli_query($conn, $sql)) {
        echo "Destination added successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>


<style>
    /* Form Styling */
.form {
    width: 400px;
    margin: 50px auto;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Form Heading */
h3 {
    text-align: center;
    color: #333;
}

/* Labels */
label {
    display: block;
    margin: 10px 0 5px;
    font-weight: bold;
    color: #555;
}

/* Inputs & Textarea */
input[type="text"], textarea, input[type="file"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
}
.btn {
    width: 100%;
    padding: 10px;
    /* background: #007bff; */
    color: white;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 15px;
}
.btn:hover {
    background: #0056b3;
}
input:focus, textarea:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

</style>
<form action="add_destination.php" class="form" method="POST" enctype="multipart/form-data">
    <h3>Add Destination</h3>

    <label for="name">Destination Name:</label>
    <input type="text" name="name" id="name" required>

    <label for="location">Location:</label>
    <input type="text" name="location" id="location" required>

    <label for="description">Description:</label>
    <textarea name="description" id="description"></textarea>

    <label for="image">Upload Image:</label>
    <input type="file" name="image" id="image" accept="image/*">

    <button type="submit" class="btn">Add Destination</button>
</form>
