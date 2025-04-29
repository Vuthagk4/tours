<?php
session_start();
include "../includes/config.php";

// Check if user is logged in (optional check for admin)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if action and booking_id are set
if (isset($_POST['action']) && isset($_POST['booking_id'])) {
    $action = $_POST['action'];
    $booking_id = $_POST['booking_id'];

    // Sanitize the inputs to prevent SQL injection
    $action = $conn->real_escape_string($action);
    $booking_id = (int)$booking_id;

    // Determine the new status based on the action
    if ($action === 'confirm') {
        $new_status = 'Confirmed';
    } elseif ($action === 'reject') {
        $new_status = 'Rejected';
    } else {
        // If the action is not recognized, redirect to the manage bookings page
        header("Location: manage_bookings.php");
        exit();
    }

    // Update the booking status in the database
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
    $stmt->bind_param("si", $new_status, $booking_id);

    if ($stmt->execute()) {
        // Redirect to the manage bookings page after successful update
        echo "<script>
            alert('Booking status updated successfully!');
            window.location.href = 'manage_bookings.php';
        </script>";
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    // If action or booking_id is not set, redirect to the manage bookings page
    header("Location: manage_bookings.php");
    exit();
}
?>
