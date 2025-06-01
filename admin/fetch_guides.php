// fetch_guides.php
<?php
include '../includes/config.php';
header('Content-Type: application/json');
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 0;
$query = $limit ? "SELECT guide_id, name, position, skill, language, image FROM guides WHERE is_deleted = 0 ORDER BY guide_id DESC LIMIT $limit" : "SELECT guide_id, name, position, skill, language, image FROM guides WHERE is_deleted = 0 ORDER BY guide_id DESC";
$result = mysqli_query($conn, $query) or die(json_encode(['error' => mysqli_error($conn)]));
$guides = mysqli_fetch_all($result, MYSQLI_ASSOC);
echo json_encode($guides);
?>