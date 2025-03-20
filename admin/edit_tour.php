<?php
session_start();
include '../includes/config.php';

// Check if the admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$tour_id = $_GET['edit'];
$stmt = $conn->prepare("SELECT * FROM tours WHERE tour_id = ?");
$stmt->bind_param("i", $tour_id);
$stmt->execute();
$tour = $stmt->get_result()->fetch_assoc();

// Fetch destinations
$destinations = $conn->query("SELECT * FROM destinations");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $destination_id = $_POST["destination_id"];
    $title = $_POST["title"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $duration = $_POST["duration"];

    // Check if a new image is uploaded
    if ($_FILES["image"]["name"]) {
        $image = $_FILES["image"]["name"];
        $image_tmp = $_FILES["image"]["tmp_name"];
        move_uploaded_file($image_tmp, "../uploads/" . $image);
    } else {
        $image = $tour['image'];
    }

    // Update tour
    $stmt = $conn->prepare("UPDATE tours SET destination_id=?, title=?, description=?, price=?, duration=?, image=? WHERE tour_id=?");
    $stmt->bind_param("isssssi", $destination_id, $title, $description, $price, $duration, $image, $tour_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Tour updated successfully!'); window.location.href='admin_tours.php';</script>";
    } else {
        echo "<script>alert('Error updating tour!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Tour</title>
</head>
<body>

    <h2>Edit Tour</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <label>Title:</label>
        <input type="text" name="title" value="<?= $tour['title']; ?>" required><br>

        <label>Description:</label>
        <textarea name="description" required><?= $tour['description']; ?></textarea><br>

        <label>Image:</label>
        <input type="file" name="image"><br>
        <img src="../uploads/<?= $tour['image']; ?>" width="50"><br>

        <button type="submit">Update Tour</button>
    </form>

</body>
</html>
