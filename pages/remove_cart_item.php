<?php
include '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $itemId = $data['id'] ?? null;
    $userId = $_SESSION['user_id'] ?? null;

    if (!$itemId || !$userId) {
        echo json_encode(['success' => false, 'message' => 'Invalid booking ID or user not logged in']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $itemId, $userId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Booking not found or not authorized']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>