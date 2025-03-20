<?php
include '../includes/config.php';
include '../includes/header.php';

// Fetch all tours with their destinations
$tours = $conn->query("SELECT tours.*, destinations.name AS destination FROM tours 
                        JOIN destinations ON tours.destination_id = destinations.destination_id");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Tours</title>
    <link rel="stylesheet" href="../assets/css/style1.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Available Tours</h2>
    
    <div class="row">
        <?php while ($tour = $tours->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="../uploads/<?= $tour['image']; ?>" class="card-img-top" alt="Tour Image">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($tour['title']); ?></h5>
                        <p class="card-text"><strong>Destination:</strong> <?= htmlspecialchars($tour['destination']); ?></p>
                        <p class="card-text"><?= nl2br(htmlspecialchars($tour['description'])); ?></p>
                        <p class="card-text"><strong>Duration:</strong> <?= htmlspecialchars($tour['duration']); ?></p>
                        <p class="card-text"><strong>Price:</strong> $<?= number_format($tour['price'], 2); ?></p>
                        <a href="tour_details.php?id=<?= $tour['tour_id']; ?>" class="btn btn-primary">Booking Now</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// include '../admin/footer.php';
?>