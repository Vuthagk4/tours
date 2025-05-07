<?php
include "../includes/config.php";

if (isset($_GET['tour_id'])) {
    $tour_id = intval($_GET['tour_id']);
    $stmt = $conn->prepare("SELECT image_id, image_path, description FROM tour_images WHERE tour_id = ?");
    $stmt->bind_param("i", $tour_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
    echo json_encode($images);
}
?>