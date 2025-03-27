<?php
include "../includes/config.php";
include "../includes/header.php";

// Fetch all tours with their destinations
$tours = $conn->query("SELECT tours.*, destinations.name AS destination, destinations.location FROM tours 
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
    <style>
        body{
            height: 150vh;
        }
           .card {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .card-body {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .card-text.description {
    overflow: hidden;
    max-height: 60px; /* Limit height */
    position: relative;
    display: -webkit-box;
    -webkit-line-clamp: 3; /* Limit to 3 lines */
    -webkit-box-orient: vertical;
    text-overflow: ellipsis;
}
    .see-more {
        color: blue;
        cursor: pointer;
        display: block;
        margin-top: 5px;
    }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Available Tours</h2>
    
    <div class="row">
    <?php while ($tour = $tours->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="../uploads/<?= $tour["image"] ?>" class="card-img-top" alt="Tour Image">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($tour["title"]) ?></h5>
                    <p class="card-text"><strong>Destination:</strong> <?= htmlspecialchars($tour["destination"]) ?></p>
                    
                    <?php
                    $coords = explode(",", $tour["location"]);
                    if (count($coords) == 2) {
                        $lat = trim($coords[0]);
                        $lng = trim($coords[1]);
                        $mapLink = "https://www.google.com/maps?q={$lat},{$lng}";
                    } else {
                        $mapLink = "javascript:void(0);";
                    }
                    ?>
                    <p class="card-text"><strong>Location:</strong> <a href="<?= $mapLink ?>" target="_blank">View on Map</a></p>

                    <p class="card-text description" data-fulltext="<?= htmlspecialchars($tour['description']) ?>" 
                        data-shorttext="<?= substr(htmlspecialchars($tour['description']), 0, 20) ?>">
                            <?= substr(htmlspecialchars($tour['description']), 0, 20) . '...' ?>
                    </p>

                    <span class="see-more">See More</span>

                    <p class="card-text"><strong>Duration:</strong> <?= htmlspecialchars($tour["duration"]) ?></p>
                    <p class="card-text"><strong>Price:</strong> $<?= number_format($tour["price"], 2) ?></p>
                    <a href="tour_details.php?id=<?= $tour["tour_id"] ?>" class="btn btn-primary">Booking Now</a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.see-more').forEach(function(button) {
    button.addEventListener('click', function() {
        let description = this.previousElementSibling;
        if (this.textContent === "See More") {
            description.textContent = description.dataset.fulltext;
            this.textContent = "See Less";
        } else {
            description.textContent = description.dataset.shorttext + "...";
            this.textContent = "See More";
        }
    });
});

</script>
</body>
</html>
<?php include "../admin/footer.php";
?>
