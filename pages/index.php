
<?php
include "../includes/config.php";
include "../includes/header.php";

// Check if a search term is provided via GET
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$sortOption = isset($_GET['sort']) ? htmlspecialchars($_GET['sort']) : 'default';

// Fetch tours with primary image
if (!empty($searchTerm)) {
    $searchTerm = '%' . $searchTerm . '%';
    $stmt = $conn->prepare("SELECT t.*, d.name AS destination, d.location 
                            FROM tours t 
                            JOIN destinations d ON t.destination_id = d.destination_id 
                            WHERE t.title LIKE ? OR d.name LIKE ? AND t.isDeleted = 0");
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $tours = $stmt->get_result();
} else {
    $tours = $conn->query("SELECT t.*, d.name AS destination, d.location 
                           FROM tours t 
                           JOIN destinations d ON t.destination_id = d.destination_id 
                           WHERE t.isDeleted = 0");
}

// Fetch all images for each tour (primary + additional)
$tour_images = [];
$result = $conn->query("SELECT tour_id, image AS primary_image FROM tours WHERE isDeleted = 0");
while ($row = $result->fetch_assoc()) {
    $tour_images[$row['tour_id']] = [['image_path' => $row['primary_image'], 'description' => 'Primary Image']];
}
$result = $conn->query("SELECT tour_id, image_path, description FROM tour_images");
while ($row = $result->fetch_assoc()) {
    $tour_images[$row['tour_id']][] = $row;
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
            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
            }

            .container {
                flex: 1 0 auto; /* Grow to fill available space, pushing footer down */
            }
        body { 
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ensure the body takes up at least the viewport height */
            margin: 0; 
            padding-bottom: 60px; /* Space for the footer */
            min-height: 100vh;
            position: relative;
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
            margin: 20px 0;
            max-width: 900px;
        }
        .tour-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            cursor: pointer;
        }
        .tour-info { padding: 20px; display: flex; flex-direction: column; }
        .tour-info .title { margin: 0 0 8px; color: #0071c2; }
        .tour-info .meta { margin: 4px 0; font-size: 14px; color: #555; }
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
        .tour-book .price-label { font-size: 12px; color: #555; margin: 0; }
        .tour-book .price-amount { font-size: 20px; color: green; font-weight: bold; margin: 4px 0 12px; }
        .tour-book label { font-size: 14px; color: #333; display: block; margin-bottom: 8px; }
        .tour-book input {
            width: 100%;
            margin-top: 4px;
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .tour-book .total-price { font-size: 14px; font-weight: bold; margin: 12px 0; }
        .btn-book {
            display: block;
            text-align: center;
            padding: 10px 0;
            background: #0071c2;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
        }
        .btn-book:hover { background: #005999; }
        .description { color: #555; }
        .see-more { color: blue; cursor: pointer; display: block; margin-top: 5px; }
        .see-more:hover { text-decoration: underline; }
        .filter-search-container { padding: 15px 0; margin-bottom: 20px; }
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
        .filter-search-container .btn-primary:hover { background-color: #0056b3; }
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
        .tour-container { display: flex; justify-content: flex-start; gap: 20px; padding: 0 15px; }
        .tour-cards { flex: 3; }
        .highlights-column { flex: 1; max-width: 300px; padding: 15px; }
        .highlight-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .highlight-card i { font-size: 24px; color: #0071c2; margin-right: 10px; }
        .highlight-card .content { flex: 1; }
        .highlight-card h6 { font-size: 14px; color: #333; margin: 0 0 5px; }
        .highlight-card p { font-size: 12px; color: #666; margin: 0; }
        .highlights-column {
            position: fixed;
            top: 14rem;
            right: 0.2rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
            z-index: 1000;
        }
        .highlights-column > a { text-decoration: none; }
        .emergency_contact {
            width: 40px;
            height: 40px;
            background-color: dodgerblue;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        .emergency_contact:hover { transform: scale(1.1); }
        .emergency_contact i { color: white; font-size: 18px; }
        .contact-modal {
            position: fixed;
            top: 14rem;
            right: -300px;
            width: 250px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            padding: 15px;
            z-index: 1001;
            transition: right 0.3s ease;
        }
        .contact-modal.show { right: 3rem; }
        .contact-modal .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 18px;
            color: #666;
        }
        .contact-modal h6 { font-size: 16px; margin-bottom: 15px; color: #333; }
        .contact-option { display: flex; align-items: center; padding: 10px; margin-bottom: 10px; border-radius: 5px; transition: background 0.3s ease; }
        .contact-option:hover { background: #f0f0f0; }
        .contact-option i { margin-right: 10px; font-size: 18px; color: dodgerblue; }
        .contact-option a { text-decoration: none; color: #333; font-size: 14px; }
        /* Modal Image Size */
                    #imageModal {
                z-index: 99999999999999999999999999999; /* Extremely high z-index to ensure modal is on top */
            }
            #imageModal .modal-dialog {
                max-width: 600px; /* Reduced modal width */
                max-height: 60vh; /* Reduced modal height */
            }
            #imageModal .modal-body {
                height: 100%;
                width: 100%;
            }
            #imageModal .carousel-inner {
                position: relative;
                height: 100%;
            }
            #imageModal .carousel-inner img {
                max-width: 100%;
                max-height: 100%;
                width: 100%;
                height: 100%;
                object-fit: cover; /* Ensures image covers the full area, cropping if necessary */
                margin: auto;
            }
                    /* Sticky Footer */
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #f8f9fa; /* Adjust based on your footer style */
            z-index: 1000; /* Ensure it stays above other content */
            padding: 10px 0;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
        }
        @media (max-width: 768px) {
            .highlights-column { position: static; flex-direction: row; justify-content: center; gap: 1rem; margin-bottom: 20px; }
            .emergency_contact { width: 35px; height: 35px; }
            .emergency_contact i { font-size: 16px; }
            .contact-modal { top: auto; bottom: 60px; right: 0; width: 100%; border-radius: 8px 8px 0 0; } /* Adjusted for footer height */
            .contact-modal.show { right: 0; }
            .tour-container { flex-direction: column; }
            .tour-cards { order: 2; }
            .highlights-column { order: 1; max-width: 100%; }
            .tour-card { grid-template-columns: 1fr; max-width: 100%; }
            .tour-image img { height: 200px; }
            .tour-book { border-left: none; border-top: 1px solid #e0e0e0; }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Available Tours</h2>
    <div class="filter-search-container">
        <div class="row align-items-center gx-3">
            <div class="col-sm-4 d-flex align-items-center">
                <label for="sortSelect" class="me-2 fw-medium">Sort By:</label>
                <select id="sortSelect" name="sort" onchange="sortCards()" class="form-select" style="max-width: 200px;">
                    <option value="default" <?php echo $sortOption === 'default' ? 'selected' : ''; ?>>Default</option>
                    <option value="popular" <?php echo $sortOption === 'popular' ? 'selected' : ''; ?>>Popular</option>
                </select>
            </div>
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
    <div class="tour-container">
        <div class="tour-cards" id="tour-cards">
            <?php
            $index = 0;
            while ($tour = $tours->fetch_assoc()): ?>
                <div class="col-12 mb-4" data-original-index="<?= $index ?>">
                    <div class="tour-card">
                        <div class="tour-image">
                            <img src="../Uploads/<?= htmlspecialchars($tour['image'] ?? 'placeholder.jpg') ?>" 
                                 alt="<?= htmlspecialchars($tour['title']) ?>" 
                                 data-bs-toggle="modal" 
                                 data-bs-target="#imageModal"
                                 data-tour-id="<?= $tour['tour_id'] ?>">
                        </div>
                        <div class="tour-info" style="position: relative;">
                            <h5 class="title"><?= htmlspecialchars($tour['title']) ?></h5>
                            <p class="meta"><strong>Destination:</strong> <?= htmlspecialchars($tour['destination']) ?></p>
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
                            <p><strong class="meta">Type: <span><?= htmlspecialchars($tour['type'] ?? '--') ?></span></strong></p>
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
        <div class="highlights-column" style="position:fixed; top: 14rem; right: 8rem;">
            <h5 class="mb-3">Why Book With Us</h5>
            <div class="highlight-card">
                <i class="fas fa-users"></i>
                <div class="content">
                    <h6>Expert Guides</h6>
                    <p>Our tours are led by knowledgeable local guides who ensure an enriching experience.</p>
                </div>
            </div>
            <div class="highlight-card">
                <i class="fas fa-undo-alt"></i>
                <div class="content">
                    <h6>Flexible Cancellations</h6>
                    <p>Change of plans? Cancel or modify your booking with ease up to 24 hours before departure.</p>
                </div>
            </div>
            <div class="highlight-card">
                <i class="fas fa-tag"></i>
                <div class="content">
                    <h6>Best Price Guarantee</h6>
                    <p>We offer competitive prices with no hidden fees, ensuring you get the best deal.</p>
                </div>
            </div>
        </div>
        <div class="highlights-column">
            <a href="javascript:void(0)" class="emergency_contact" onclick="toggleContactModal()">
                <i class="fa-solid fa-phone"></i>
            </a>
            <a href="javascript:void(0)" class="emergency_contact" onclick="toggleContactModal()">
                <i class="fa-brands fa-telegram"></i>
            </a>
            <a href="javascript:void(0)" class="emergency_contact" onclick="toggleContactModal()">
                <i class="fa-solid fa-envelope"></i>
            </a>
        </div>
        <div class="contact-modal" id="contactModal">
            <span class="close-btn" onclick="toggleContactModal()"> <i class="fa-solid fa-xmark"></i> </span>
            <h6>Contact Us</h6>
            <div class="contact-option">
                <i class="fa-solid fa-phone"></i>
                <a href="tel:+1234567890">015 769 953</a>
            </div>
            <div class="contact-option">
                <i class="fa-brands fa-telegram"></i>
                <a href="https://t.me/thany_oun" target="_blank">OUN THANY</a>
            </div>
            <div class="contact-option">
                <i class="fa-solid fa-envelope"></i>
                <a href="mailto:support@tours.com">ounthany@gmail.com</a>
            </div>
        </div>
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
    // Image Modal
    document.querySelectorAll(".tour-image img").forEach(img => {
        img.addEventListener("click", () => {
            const tour_id = img.dataset.tourId;
            const images = <?php echo json_encode($tour_images); ?>[tour_id] || [];
            const carousel = document.getElementById("carouselImages");
            carousel.innerHTML = '';
            images.forEach((image, index) => {
                const div = document.createElement("div");
                div.className = `carousel-item ${index === 0 ? 'active' : ''}`;
                div.innerHTML = `
                    <img src="../Uploads/${image.image_path}" class="d-block w-100" 
                         alt="Tour Image" 
                         data-bs-toggle="popover" 
                         data-bs-trigger="hover" 
                         data-bs-content="${image.description || 'No description'}">
                `;
                carousel.appendChild(div);
            });
            // Initialize popovers
            const popoverImages = carousel.querySelectorAll('img');
            popoverImages.forEach(img => {
                new bootstrap.Popover(img);
            });
        });
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
            let [day, month, year] = inputDate.split("-");
            formattedDate = `${year}-${month}-${day}`;
        } else if (/^\d{4}-\d{2}-\d{2}$/.test(inputDate)) {
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
                return bBooked - aBooked;
            });
        } else if (sortOption === 'default') {
            cards.sort((a, b) => {
                const aIndex = parseInt(a.dataset.originalIndex) || 0;
                const bIndex = parseInt(b.dataset.originalIndex) || 0;
                return aIndex - bIndex;
            });
        }
        container.innerHTML = '';
        cards.forEach(card => container.appendChild(card));
        const url = new URL(window.location);
        url.searchParams.set('sort', sortOption);
        window.history.pushState({}, '', url);
    } catch (error) {
        console.error('Error sorting cards:', error);
    }
}
function toggleContactModal() {
    const modal = document.getElementById('contactModal');
    modal.classList.toggle('show');
}
</script>
<!-- Image Slideshow Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Tour Images</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="imageCarousel" class="carousel slide">
                    <div class="carousel-inner" id="carouselImages"></div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "./user_footer.php"; ?>
</body>
</html>