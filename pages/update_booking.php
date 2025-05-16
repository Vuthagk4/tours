<?php
session_start();
include "../includes/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['action']) && $_POST['action'] === 'confirm') {
    $booking_id = $_POST['booking_id'];

    $stmt = $conn->prepare("UPDATE bookings SET status = 'paid' WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();

    header("Location: booking_history.php");
    exit();
} elseif (isset($_POST['action']) && $_POST['action'] === 'cancel') {
    $booking_id = $_POST['booking_id'];

    // Update the booking status to "cancelled" in the database (optional)
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
    $status = 'cancelled';
    $stmt->bind_param("si", $status, $booking_id);
    $stmt->execute();

    // Redirect back to the booking history page
    header("Location: booking_history.php");
    exit();
}
?>