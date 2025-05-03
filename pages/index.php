<?php
include "../includes/config.php";
include "../includes/header.php";

// Check if a search term is provided via GET
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
// Check sort option
$sortOption = isset($_GET['sort']) ? htmlspecialchars($_GET['sort']) : 'default';

// Fetch tours based on search term or display all if no search
if (!empty($searchTerm)) {
    $searchTerm = '%' . $searchTerm . '%';
    $stmt = $conn->prepare("SELECT tours.*, destinations.name AS destination, destinations.location 
                            FROM tours 
                            JOIN destinations ON tours.destination_id = destinations.destination_id 
                            WHERE tours.title LIKE ? OR destinations.name LIKE ?");
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $tours = $stmt->get_result();
} else {
    $tours = $conn->query("SELECT tours.*, destinations.name AS destination, destinations.location 
                           FROM tours 
                           JOIN destinations ON tours.destination_id = destinations.destination_id");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Tours</title>
    <link rel="stylesheet" href="../assets/css/style1.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body {
            height: 150vh;
        }

        .tour-card {
            display: grid;
            grid-template-columns: 370px 1fr 200px;
            background: #f7f9fc;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            align-items: stretch;
            min-height: 200px;
            margin: 20px auto;
            max-width: 900px;
        }

        .tour-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .tour-info {
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .tour-info .title {
            margin: 0 0 8px;
            color: #0071c2;
        }

        .tour-info .meta {
            margin: 4px 0;
            font-size: 14px;
            color: #555;
        }

        .tour-info .description {
            margin: 12px 0;
            font-size: 14px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .tour-info .see-more {
            font-size: 14px;
            color: #0071c2;
            text-decoration: underline;
            cursor: pointer;
            margin-top: auto;
        }

        .tour-book {
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-left: 1px solid #e0e0e0;
        }

        .tour-book .price-label {
            font-size: 12px;
            color: #555;
            margin: 0;
        }

        .tour-book .price-amount {
            font-size: 20px;
            color: green;
            font-weight: bold;
            margin: 4px 0 12px;
        }

        .tour-book label {
            font-size: 14px;
            color: #333;
            display: block;
            margin-bottom: 8px;
        }

        .tour-book input {
            width: 100%;
            margin-top: 4px;
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .tour-book .total-price {
            font-size: 14px;
            font-weight: bold;
            margin: 12px 0;
        }

        .btn-book {
            display: block;
            text-align: center;
            padding: 10px 0;
            background: #0071c2;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
        }

        .btn-book:hover {
            background: #005999;
        }

        .description {
            color: #555;
        }

        .see-more {
            color: blue;
            cursor: pointer;
            display: block;
            margin-top: 5px;
        }

        .see-more:hover {
            text-decoration: underline;
        }

        .filter-search-container {
            padding: 15px 0;
            margin-bottom: 20px;
        }

        .filter-search-container .form-select,
        .filter-search-container .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .filter-search-container .form-select:focus,
        .filter-search-container .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }

        .filter-search-container .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            transition: background-color 0.3s ease;
        }

        .filter-search-container .btn-primary:hover {
            background-color: #0056b3;
        }

        .filter-search-container .btn-link {
            color: #dc3545;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .filter-search-container .btn-link:hover {
            color: #b02a37;
            text-decoration: underline;
        }

        .filter-search-container .input-group-text {
            background: #fff;
            border: 1px solid #ced4da;
            border-right: none;
            border-radius: 8px 0 0 8px;
        }

        @media (max-width: 576px) {
            .filter-search-container .col-sm-4,
            .filter-search-container .col-sm-8 {
                margin-bottom: 15px;
            }

            .filter-search-container .row {
                flex-direction: column;
                align-items: stretch;
            }

            .tour-card {
                grid-template-columns: 1fr;
                max-width: 100%;
            }

            .tour-image img {
                height: 200px;
            }

            .tour-book {
                border-left: none;
                border-top: 1px solid #e0e0e0;
            }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Available Tours</h2>
    <!-- Filter and Search UI -->
    <div class="filter-search-container">
        <div class="row align-items-center gx-3">
            <!-- Sort Dropdown -->
            <div class="col-sm-4 d-flex align-items-center">
                <label for="sortSelect" class="me-2 fw-medium">Sort By:</label>
                <select id="sortSelect" name="sort" onchange="sortCards()" class="form-select" style="max-width: 200px;">
                    <option value="default" <?php echo $sortOption === 'default' ? 'selected' : ''; ?>>Default</option>
                    <option value="popular" <?php echo $sortOption === 'popular' ? 'selected' : ''; ?>>Popular</option>
                </select>
            </div>
            <!-- Search Form -->
            <div class="col-sm-8">
                <form method="get" action="" class="d-flex align-items-center">
                    <div class="input-group" style="max-width: 350px;">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by title or destination" 
                               value="<?php echo htmlspecialchars($searchTerm); ?>">
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sortOption); ?>">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                    <?php if (!empty($searchTerm)): ?>
                        <a href="?" class="btn btn-link ms-3">Clear Search</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Tour Cards Container -->
    <div class="row" id="tour-cards">
        <?php
        $index = 0; // Initialize index to track original order
        while ($tour = $tours->fetch_assoc()): ?>
            <!-- Add data-original-index to track original order -->
            <div class="col-12 mb-4" data-original-index="<?= $index ?>">
                <div class="tour-card">
                    <!-- 1) IMAGE COLUMN -->
                    <div class="tour-image">
                        <img src="../Uploads/<?= htmlspecialchars($tour['image']) ?>" 
                             alt="<?= htmlspecialchars($tour['title']) ?>">
                    </div>

                    <!-- 2) INFO COLUMN -->
                    <div class="tour-info" style="position: relative;">
                        <h5 class="title"><?= htmlspecialchars($tour['title']) ?></h5>
                        <p class="meta"><strong>Destination:</strong> <?= htmlspecialchars($tour['destination']) ?></p>

                        <!-- Location Map Link -->
                        <?php
                        $coords = explode(",", $tour['location']);
                        $mapLink = (count($coords) == 2) 
                            ? "https://www.google.com/maps?q=" . trim($coords[0]) . "," . trim($coords[1])
                            : "javascript:void(0);";
                        ?>
                        <p class="meta">
                            <strong>Location:</strong>
                            <a href="<?= $mapLink ?>" target="_blank">View on Map</a>
                        </p>

                        <!-- Description -->
                        <?php
                        $fullDesc = htmlspecialchars($tour['description']);
                        $shortDesc = substr($fullDesc, 0, 100);
                        ?>
                        <p class="description" 
                           data-fulltext="<?= $fullDesc ?>" 
                           data-shorttext="<?= $shortDesc ?>">
                            <?= $shortDesc ?>…
                        </p>
                        <a href="javascript:void(0)" class="see-more">Show More</a>

                        <!-- Total People Booked with span for sorting -->
                        <div style="position: absolute; right:10px; top:0;">
                            <?php
                            $tour_id = $tour['tour_id'];
                            $stmt = $conn->prepare("SELECT SUM(people) AS total_people FROM bookings WHERE tour_id = ?");
                            $stmt->bind_param("i", $tour_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $totalPeople = $result->fetch_assoc()['total_people'] ?? 0;
                            $stmt->close();
                            $starColor = $totalPeople >= 100 ? "gold" : "black";
                            $stars = min(floor($totalPeople / 100), 5);
                            ?>
                            <span class="booked-count"><?= $totalPeople ?></span>
                            <?php for ($i = 0; $i < $stars; $i++): ?>
                                <i style="color: <?= $starColor ?>;" class="fa-solid fa-star"></i>
                            <?php endfor; ?>
                            people booked
                        </div>
                    </div>

                    <!-- 3) BOOKING COLUMN -->
                    <div class="tour-book">
                        <?php
                        $defaultDuration = (int) $tour['duration'];
                        $defaultPrice = (float) $tour['price'];
                        ?>
                        <p class="price-label">Price from</p>
                        <p class="price-amount">$<?= number_format($defaultPrice, 2) ?></p>
                        <label>
                            Duration:
                            <input type="number" 
                                   class="duration-input" 
                                   min="1" 
                                   value="<?= $defaultDuration ?>" 
                                   data-default-price="<?= $defaultPrice ?>">
                        </label>
                        <label>
                            People:
                            <input type="number" class="people-input" min="1" value="1">
                        </label>
                        <label for="calendar-<?= $tour['tour_id'] ?>">Travel Date:</label> 
                        <input type="text" 
                               id="calendar-<?= $tour['tour_id'] ?>" 
                               name="travel_date" 
                               class="travel_date" 
                               placeholder="yyyy-mm-dd">
                        <p class="total-price">
                            Total: $<span class="dynamic-price">
                                <?= number_format($defaultPrice * $defaultDuration, 2) ?>
                            </span>
                        </p>
                        <a href="tour_details.php?id=<?= $tour['tour_id'] ?>" 
                           onclick="return customizeBooking(this)" 
                           class="btn btn-primary btn-book">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
        <?php $index++; endwhile; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Show More/Show Less for description
    document.querySelectorAll(".see-more").forEach(btn => {
        btn.addEventListener("click", () => {
            const desc = btn.previousElementSibling;
            const fullText = desc.dataset.fulltext;
            const shortText = desc.dataset.shorttext;

            if (desc.textContent.trim().endsWith("…")) {
                desc.textContent = fullText;
                btn.textContent = "Show Less";
            } else {
                desc.textContent = shortText + "…";
                btn.textContent = "Show More";
            }
        });
    });

    // Dynamic Price Update
    document.querySelectorAll(".tour-card").forEach(card => {
        const durationInput = card.querySelector(".duration-input");
        const peopleInput = card.querySelector(".people-input");
        const priceSpan = card.querySelector(".dynamic-price");

        function updatePrice() {
            const defaultPrice = parseFloat(durationInput.dataset.defaultPrice) || 0;
            const days = parseInt(durationInput.value, 10) || 1;
            const people = parseInt(peopleInput.value, 10) || 1;
            priceSpan.textContent = (defaultPrice * days * people).toFixed(2);
        }

        updatePrice();
        durationInput.addEventListener("input", updatePrice);
        peopleInput.addEventListener("input", updatePrice);
    });
});

// Customize Booking
function customizeBooking(link) {
    const card = link.closest(".tour-card");
    const durationInput = card.querySelector(".duration-input");
    const peopleInput = card.querySelector(".people-input");
    const dateInput = card.querySelector(".travel_date");

    const duration = parseInt(durationInput.value) || 1;
    const people = parseInt(peopleInput.value) || 1;
    const travelDate = dateInput.value || '';

    if (travelDate.trim() === '') {
        alert('Please select a travel date.');
        return false;
    }

    const url = new URL(link.href);
    url.searchParams.set('duration', duration);
    url.searchParams.set('people', people);
    url.searchParams.set('travel_date', travelDate);

    window.location.href = url.toString();
    return false;
}

// Date Input Validation
document.querySelectorAll(".travel_date").forEach(input => {
    input.addEventListener("change", function() {
        let inputDate = this.value.trim();
        let formattedDate = "";

        if (/^\d{2}-\d{2}-\d{4}$/.test(inputDate)) {
            // dd-mm-yyyy
            let [day, month, year] = inputDate.split("-");
            formattedDate = `${year}-${month}-${day}`; // to yyyy-mm-dd
        } else if (/^\d{4}-\d{2}-\d{2}$/.test(inputDate)) {
            // yyyy-mm-dd
            formattedDate = inputDate;
        } else {
            alert("Please enter date in dd-mm-yyyy or yyyy-mm-dd format!");
            this.value = "";
            return;
        }

        this.value = formattedDate;
    });
});

// Sort Cards
function sortCards() {
    const container = document.getElementById('tour-cards');
    const cards = Array.from(container.querySelectorAll('.col-12'));
    const sortOption = document.getElementById('sortSelect').value;

    try {
        if (sortOption === 'popular') {
            cards.sort((a, b) => {
                const aBooked = parseInt(a.querySelector('.booked-count')?.textContent) || 0;
                const bBooked = parseInt(b.querySelector('.booked-count')?.textContent) || 0;
                return bBooked - aBooked; // Descending order
            });
        } else if (sortOption === 'default') {
            cards.sort((a, b) => {
                const aIndex = parseInt(a.dataset.originalIndex) || 0;
                const bIndex = parseInt(b.dataset.originalIndex) || 0;
                return aIndex - bIndex; // Ascending order
            });
        }

        // Re-append cards
        container.innerHTML = '';
        cards.forEach(card => container.appendChild(card));

        // Persist sort selection in URL
        const url = new URL(window.location);
        url.searchParams.set('sort', sortOption);
        window.history.pushState({}, '', url);
    } catch (error) {
        console.error('Error sorting cards:', error);
    }
}
</script>
</body>
</html>
<?php include "../admin/footer.php"; ?>