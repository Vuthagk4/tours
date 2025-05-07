<?php
include '../includes/config.php';
include '../includes/header.php';

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Execute query to fetch destinations
$sql = "SELECT * FROM destinations WHERE isDelete = 0";
$result = $conn->query($sql);

// Check if query executed successfully
if ($result === false) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    .card-row {
  display: flex;
  gap: 20px;
  overflow-x: auto;
  padding: 20px;
}

.card {
  flex: 0 0 250px;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  background-color: #fff;
  transition: transform 0.3s;
}

.card:hover {
  transform: translateY(-5px);
}

.card img {
  width: 100%;
  height: 170px;
  object-fit: cover;
}

.card-content {
  padding: 15px;
}

.card-content h3 {
  margin: 0 0 5px;
  font-size: 18px;
}

.card-content p {
  margin: 0 0 10px;
  color: #555;
}

.card-content strong {
  color: #000;
}

</style>
<body>
<div class="container w-75">
<div class="row mt-5 ">
<div class="card-row">
  <?php while ($row = $result->fetch_assoc()): ?>
    <?php
      $location = htmlspecialchars($row['location']);
      $mapLink = "https://www.google.com/maps?q=" . urlencode($location);
      $image = !empty($row['image']) ? htmlspecialchars($row['image']) : 'default.jpg';
    ?>
    <div class="card">
      <img src="../uploads/<?php echo $image; ?>">
      <div class="card-content">
        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
        <p><?php echo htmlspecialchars($row['category'] ?? 'No Category'); ?></p>
        <a href="<?php echo $mapLink; ?>" target="_blank">View Map</a>
      </div>
    </div>
  <?php endwhile; ?>
</div>
</div>
</div>



<?php $conn->close(); ?>

</body>
</html>
<?php include "./user_footer.php"; ?>
