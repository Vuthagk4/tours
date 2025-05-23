<?php
// Suppress any unexpected output
ob_start();
session_start();
include '../includes/config.php';

// Set JSON header
header('Content-Type: application/json');

// Initialize response
$response = ['success' => false, 'message' => ''];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'You must be logged in to remove a booking.';
    echo json_encode($response);
    ob_end_flush();
    exit;
}

// Check if booking ID is provided and valid
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    $response['message'] = 'Invalid booking ID.';
    echo json_encode($response);
    ob_end_flush();
    exit;
}

$userId = $_SESSION['user_id'];
$bookingId = (int) $_POST['id'];

try {
    // Prepare and execute deletion query
    $stmt = mysqli_prepare($conn, "DELETE FROM bookings WHERE booking_id = ? AND user_id = ?");
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "ii", $bookingId, $userId);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to execute statement: ' . mysqli_stmt_error($stmt));
    }

    // Check if any rows were affected
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $response['success'] = true;
        $response['message'] = 'Booking removed successfully.';
    } else {
        $response['message'] = 'Booking not found or you do not have permission to delete it.';
    }

    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    $response['message'] = 'Server error: ' . $e->getMessage();
}

mysqli_close($conn);
echo json_encode($response);
ob_end_flush();
?>