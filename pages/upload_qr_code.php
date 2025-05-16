<?php
session_start();
include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if request is POST and has required data
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['booking_id']) || !isset($_FILES['qrCode'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$booking_id = (int) $_POST['booking_id'];
$file = $_FILES['qrCode'];

// Validate file
$allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
$max_size = 2 * 1024 * 1024; // 2MB
$upload_dir = '../Uploads/qr_codes/';
$file_name = uniqid('qr_') . '_' . basename($file['name']);
$file_path = $upload_dir . $file_name;

if ($file['error'] !== UPLOAD_ERR_OK) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'File upload error']);
    exit;
}

if (!in_array($file['type'], $allowed_types)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Only PNG, JPG, and JPEG files are allowed']);
    exit;
}

if ($file['size'] > $max_size) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'File size exceeds 2MB']);
    exit;
}

// Verify booking belongs to the user
$stmt = $conn->prepare("SELECT user_id FROM bookings WHERE booking_id = ?");
$stmt->bind_param('i', $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking || $booking['user_id'] !== $_SESSION['user_id']) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized booking']);
    exit;
}

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $file_path)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to upload QR code']);
    exit;
}

// Update database
$stmt = $conn->prepare("UPDATE bookings SET qr_code_image = ? WHERE booking_id = ?");
$stmt->bind_param('si', $file_name, $booking_id);
$success = $stmt->execute();
$stmt->close();

header('Content-Type: application/json');
if ($success) {
    echo json_encode(['success' => true, 'message' => 'QR code uploaded successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database update failed']);
}
exit;
?>