<?php
session_start();
include "../includes/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$tour_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$duration = isset($_GET['duration']) ? (int)$_GET['duration'] : 1;
$people = isset($_GET['people']) ? (int)$_GET['people'] : 1;
$booking_date = $_GET['booking_date'] ?? date('Y-m-d');
$travel_date = $_GET['travel_date'] ?? date('Y-m-d');
$status = "Pending";
$user_id = $_SESSION['user_id'];

// Validate booking_date
if (!$booking_date || !DateTime::createFromFormat('Y-m-d', $booking_date)) {
    exit("Error: Invalid or missing booking date.");
}

// Validate travel_date
if (!$travel_date || !DateTime::createFromFormat('Y-m-d', $travel_date)) {
    exit("Error: Invalid or missing travel date.");
}

// Validate other inputs
if ($tour_id <= 0 || $duration <= 0 || $people <= 0) {
    exit("Error: Invalid input values for tour ID, duration, or people.");
}

// Fetch tour base price
$stmt = $conn->prepare("SELECT price FROM tours WHERE tour_id = ?");
$stmt->bind_param("i", $tour_id);
$stmt->execute();
$result = $stmt->get_result();

if ($tour = $result->fetch_assoc()) {
    $base_price = (float)$tour['price'];
} else {
    $stmt->close();
    exit("Error: Tour not found.");
}
$stmt->close();

// Calculate total price
$total_price = $base_price * $duration * $people;

// Insert into bookings table
$stmt = $conn->prepare("
    INSERT INTO bookings (user_id, tour_id, travel_date, booking_date, duration, people, price, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    exit("Error preparing statement: " . $conn->error);
}

$stmt->bind_param(
    "iissiiis",
    $user_id,
    $tour_id,
    $travel_date,
    $booking_date,
    $duration,
    $people,
    $total_price,
    $status
);

if ($stmt->execute()) {
    echo "<script>
        alert('Tour booked successfully!');
        window.location.href = 'index.php';
    </script>";
} else {
    exit("Error executing query: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>