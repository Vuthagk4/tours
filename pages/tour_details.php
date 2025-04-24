<?php
session_start();
include "../includes/config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch booking details from GET
$tour_id = $_GET['id'] ?? null;
$duration = $_GET['duration'] ?? 1;
$people = $_GET['people'] ?? 1;
$price = $_GET['price'] ?? 0;
$travel_date = $_GET['travel_date'] ?? date('Y-m-d'); // Default today if not set
$status = "Pending";
$user_id = $_SESSION['user_id'];

// Insert into bookings table
$stmt = $conn->prepare("INSERT INTO bookings (user_id, tour_id, travel_date, duration, people, price, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iisiids", $user_id, $tour_id, $travel_date, $duration, $people, $price, $status);

if ($stmt->execute()) {
    echo "<script>
        alert('✅ Tour booked successfully!');
        window.location.href = 'my_bookings.php';
    </script>";
} else {
    echo "Error: " . $stmt->error;
}
?>
