// fetch_users.php
<?php
include '../includes/config.php';
header('Content-Type: application/json');
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 0;
$query = $limit ? "SELECT user_id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT $limit" : "SELECT user_id, name, email, role, created_at FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $query) or die(json_encode(['error' => mysqli_error($conn)]));
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
echo json_encode($users);
?>