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
            .tour-card {
            max-width: 300px;
            max-height: 650px;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
            background-color: #fff;
            }

            .tour-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            }

            .image-container {
            height: 200px;
            height: 400px; /* or auto if you want it to grow with content */
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
            }

            .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            }

            .card-body {
            padding: 1rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            }

            .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            }

            .card-text {
            font-size: 0.95rem;
            margin-bottom: 0.4rem;
            }

            .description {
            color: #555;
            }

            .see-more {
            color: #007bff;
            cursor: pointer;
            font-size: 0.9rem;
            margin-bottom: 0.8rem;
            }

            .see-more:hover {
            text-decoration: underline;
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
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card tour-card shadow-sm">
                <div class="image-container">
                <img src="../uploads/<?= $tour["image"] ?>" class="card-img-top" alt="Tour Image">
                </div>
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
            <!-- Dynamic Price  -->
                <?php
                $defaultDuration = (int)$tour["duration"];
                $defaultPrice = (float)$tour["price"];
                ?>

                <div class="mb-2">
                    <label class="form-label"><strong>Duration (days):</strong></label>
                    <input type="number" class="form-control duration-input" min="1"
                        value="<?= $defaultDuration ?>" data-default-price="<?= $defaultPrice ?>">
                </div>

                <div class="mb-2">
                    <label class="form-label"><strong>People:</strong></label>
                    <input type="number" class="form-control people-input" min="1" value="1">
                </div>

                <p class="card-text mb-2">
                    <strong>Total Price:</strong> 
                    $<span class="dynamic-price"><?= number_format($defaultPrice * $defaultDuration, 2) ?></span>
                </p>

                <a href="tour_details.php?id=<?= $tour["tour_id"] ?>" 
                onclick="return customizeBooking(this)" 
                class="btn btn-primary w-100">Book Now</a>
   <!-- End of Dynamic Booking -->



            </div>
            </div>
        </div>

    <?php endwhile; ?>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Start Of Des dynamic -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll(".see-more").forEach(btn => {
            btn.addEventListener("click", () => {
                const desc = btn.previousElementSibling;
                const fullText = desc.dataset.fulltext;
                const shortText = desc.dataset.shorttext;

                if (desc.textContent.trim().endsWith("...")) {
                    desc.textContent = fullText;
                    btn.textContent = "See Less";
                } else {
                    desc.textContent = shortText + "...";
                    btn.textContent = "See More";
                }
            });
        });
    });
</script>
<!-- End Of Des dynamic -->

<!-- Dynamic Price -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".card-body").forEach(card => {
        const durationInput = card.querySelector(".duration-input");
        const peopleInput = card.querySelector(".people-input");
        const priceSpan = card.querySelector(".dynamic-price");

        const updatePrice = () => {
            const defaultPrice = parseFloat(durationInput.dataset.defaultPrice);
            const duration = parseInt(durationInput.value) || 1;
            const people = parseInt(peopleInput.value) || 1;
            const total = defaultPrice * duration * people;
            priceSpan.textContent = total.toFixed(2);
        };

        durationInput.addEventListener("input", updatePrice);
        peopleInput.addEventListener("input", updatePrice);
    });
});

function customizeBooking(link) {
    const card = link.closest(".card-body");
    const duration = card.querySelector(".duration-input").value;
    const people = card.querySelector(".people-input").value;
    const defaultPrice = parseFloat(card.querySelector(".duration-input").dataset.defaultPrice);
    const totalPrice = (duration * defaultPrice * people).toFixed(2);

    const url = new URL(link.href);
    url.searchParams.set("duration", duration);
    url.searchParams.set("people", people);
    url.searchParams.set("price", totalPrice);

    window.location.href = url.toString();
    return false;
}
</script>

<!-- End Of Dynamic Price -->

</body>
</html>
<?php include "../admin/footer.php";
?>
