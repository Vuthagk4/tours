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
                            WHERE (t.title LIKE ? OR d.name LIKE ?) AND t.isDeleted = 0");
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
    body {
      background: #f8f9fa;
      font-family: 'Work Sans', sans-serif;
      min-height: 100vh;
      margin: 0;
      padding-bottom: 60px;
      position: relative;
    }

    /* Tour Card Styling */
    .tour-card {
      display: grid;
      grid-template-columns: 370px 1fr 200px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      margin: 20px 0;
      max-width: 900px;
      transition: transform 0.3s ease;
    }

    .tour-card:hover {
      transform: translateY(-5px);
    }

    .tour-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .tour-image img:hover {
      transform: scale(1.05);
    }

    .tour-info {
      padding: 20px;
      display: flex;
      flex-direction: column;
    }

    .tour-info .title {
      font-size: 1.25rem;
      font-weight: 600;
      color: #007bff;
      margin: 0 0 8px;
    }

    .tour-info .meta {
      font-size: 0.9rem;
      color: #666;
      margin: 4px 0;
    }

    .tour-info .description {
      font-size: 0.95rem;
      color: #333;
      margin: 12px 0;
      line-height: 1.4;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .tour-info .see-more {
      font-size: 0.9rem;
      color: #007bff;
      cursor: pointer;
      margin-top: auto;
    }

    .tour-info .see-more:hover {
      text-decoration: underline;
    }

    /* Booking Section Styling */
    .tour-book {
      padding: 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      border-left: 1px solid #e0e0e0;
    }

    .tour-book .price-label {
      font-size: 0.8rem;
      color: #555;
      margin: 0;
    }

    .tour-book .price-amount {
      font-size: 1.2rem;
      color: #28a745;
      font-weight: bold;
      margin: 4px 0 12px;
    }

    .tour-book label {
      font-size: 0.9rem;
      font-weight: 500;
      color: #333;
      margin-bottom: 6px;
    }

    .tour-book input {
      width: 100%;
      padding: 8px 12px;
      border: 1px solid #ced4da;
      border-radius: 5px;
      font-size: 0.9rem;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .tour-book input:focus {
      border-color: #007bff;
      box-shadow: 0 0 6px rgba(0, 123, 255, 0.2);
      outline: none;
    }

    .tour-book .travel_date {
      padding-right: 40px;
      background: #fff url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%23666" viewBox="0 0 16 16"><path d="M11 6V4h-1v2H6V4H5v2H4v7h8V6h-1zM3 3h10v1H3V3zm0 0V2h1v1h8V2h1v1h1v11H2V3h1z"/></svg>') no-repeat 95% center;
      background-size: 18px;
      cursor: pointer;
    }

    .tour-book .travel_date::-webkit-calendar-picker-indicator {
      opacity: 0;
      width: 40px;
      height: 100%;
      position: absolute;
      right: 0;
      cursor: pointer;
    }

    .btn-book {
      padding: 10px;
      background: #007bff;
      color: white;
      border-radius: 5px;
      text-align: center;
      text-decoration: none;
      transition: background 0.3s ease;
    }

    .btn-book:hover {
      background: #0056b3;
    }

    /* Filter and Search Styling */
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

    /* Highlights and Contact Styling */
    .highlights-column {
      position: fixed;
      top: 14rem;
      right: 1rem;
      max-width: 300px;
      display: flex;
      flex-direction: column;
      gap: 2rem;
    }

    .highlight-card {
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      padding: 15px;
      display: flex;
      align-items: center;
      transition: transform 0.3s ease;
    }

    .highlight-card:hover {
      transform: translateY(-3px);
    }

    .highlight-card i {
      font-size: 1.5rem;
      color: #007bff;
      margin-right: 10px;
    }

    .emergency_contact {
      width: 40px;
      height: 40px;
      background-color: #007bff;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .emergency_contact:hover {
      transform: scale(1.1);
    }

    .emergency_contact i {
      color: white;
      font-size: 18px;
    }

    .contact-modal {
      position: fixed;
      top: 14rem;
      right: -300px;
      width: 250px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
      padding: 15px;
      z-index: 1001;
      transition: right 0.3s ease;
    }

    .contact-modal.show {
      right: 3rem;
    }

    /* Modal Styling */
    #imageModal .modal-dialog {
      max-width: 600px;
    }

    #imageModal .carousel-inner img {
      border-radius: 8px;
      object-fit: cover;
    }

    /* Animations */
    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    .tour-card {
      animation: fadeIn 0.5s ease;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .tour-card {
        grid-template-columns: 1fr;
      }

      .tour-image img {
        height: 200px;
      }

      .tour-book {
        border-left: none;
        border-top: 1px solid #e0e0e0;
      }

      .highlights-column {
        position: static;
        flex-direction: row;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 20px;
      }

      .contact-modal {
        top: auto;
        bottom: 60px;
        right: 0;
        width: 100%;
        border-radius: 8px 8px 0 0;
      }

      .contact-modal.show {
        right: 0;
      }
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
        while ($tour = $tours->fetch_assoc()):
          // Simulate user booking count (replace with real data from a bookings table if available)
          $userBookingCount = rand(0, 5); // Placeholder: Random number between 0 and 5
          $yellowStars = min(floor($userBookingCount / 2), 5); // One yellow star per 2 bookings, max 5 stars
          ?>
          <div class="col-12 mb-4" data-original-index="<?= $index ?>">
            <div class="tour-card">
              <div class="tour-image">
                <img src="../Uploads/<?= htmlspecialchars($tour['image'] ?? 'placeholder.jpg') ?>"
                  alt="<?= htmlspecialchars($tour['title']) ?>" data-bs-toggle="modal" data-bs-target="#imageModal"
                  data-tour-id="<?= $tour['tour_id'] ?>">
              </div>
              <div class="tour-info">
                <h5 class="title"><?= htmlspecialchars($tour['title']) ?>
                  <?php for ($i = 0; $i < 5; $i++): ?>
                    <i class="fas fa-star" style="color: <?= $i < $yellowStars ? '#ffc107' : '#ccc' ?>;"></i>
                  <?php endfor; ?>
                </h5>
                <p class="tour-location"><?= htmlspecialchars($tour['destination']) ?> -
                  <?php
                  $coords = explode(",", $tour['location']);
                  $mapLink = (count($coords) == 2)
                    ? "https://www.google.com/maps?q=" . trim($coords[0]) . "," . trim($coords[1])
                    : "javascript:void(0);";
                  ?>
                  <a href="<?= $mapLink ?>" target="_blank">Show on map</a> <?= rand(0, 2) ?>.<?= rand(0, 9) ?>km from
                  centre
                </p>

                <?php
                $fullDesc = htmlspecialchars($tour['description']);
                $shortDesc = substr($fullDesc, 0, 100);
                ?>
                <div class="description" aria-expanded="false" data-fulltext="<?= $fullDesc ?>"
                  data-shorttext="<?= $shortDesc ?>">
                  <p><?= $shortDesc ?>…</p>
                  <a href="javascript:void(0)" class="see-more" role="button" aria-label="Toggle description">Show
                    More</a>
                </div>

              </div>
              <div class="tour-book">
                <?php
                $defaultDuration = (int) $tour['duration'];
                $defaultPrice = (float) $tour['price'];
                ?>
                <p class="price-label">Price from</p>
                <p class="price-amount">$<?= number_format($defaultPrice, 2) ?></p>
                <label>Duration (days): <input type="number" class="duration-input" min="1"
                    value="<?= $defaultDuration ?>" data-default-price="<?= $defaultPrice ?>"></label>
                <label>People: <input type="number" class="people-input" min="1" value="1"></label>
                <label>Travel Date: <input type="date" name="travel_date" class="travel_date" required></label>
                <p class="total-price">Total: $<span
                    class="dynamic-price"><?= number_format($defaultPrice * $defaultDuration, 2) ?></span></p>
                <a href="tour_details.php?id=<?= $tour['tour_id'] ?>" onclick="return customizeBooking(this)"
                  class="btn-book">Book Now</a>
              </div>
            </div>
          </div>
          <?php $index++; endwhile; ?>
      </div>
    </div>
  </div>
  <div class="highlights-column">
    <h5 class="mb-3">Why Book With Us</h5>
    <div class="highlight-card">
      <i class="fas fa-users"></i>
      <div class="content">
        <h6>Expert Guides</h6>
        <p>Our tours are led by knowledgeable local guides.</p>
      </div>
    </div>
    <div class="highlight-card">
      <i class="fas fa-undo-alt"></i>
      <div class="content">
        <h6>Flexible Cancellations</h6>
        <p>Cancel or modify up to 24 hours before departure.</p>
      </div>
    </div>
    <div class="highlight-card">
      <i class="fas fa-tag"></i>
      <div class="content">
        <h6>Best Price Guarantee</h6>
        <p>Competitive prices with no hidden fees.</p>
      </div>
    </div>
    <div class="mt-3">
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
  </div>
  <div class="contact-modal" id="contactModal">
    <span class="close-btn" onclick="toggleContactModal()"><i class="fa-solid fa-xmark"></i></span>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // Description Toggle
      document.querySelectorAll(".see-more").forEach(btn => {
        btn.addEventListener("click", () => {
          const desc = btn.parentElement;
          const fullText = desc.dataset.fulltext;
          const shortText = desc.dataset.shorttext;
          const isExpanded = desc.getAttribute("aria-expanded") === "true";
          if (!isExpanded) {
            desc.querySelector("p").textContent = fullText;
            btn.textContent = "Show Less";
            desc.setAttribute("aria-expanded", "true");
          } else {
            desc.querySelector("p").textContent = shortText + "…";
            btn.textContent = "Show More";
            desc.setAttribute("aria-expanded", "false");
          }
        });
      });

      // Dynamic Price Update with Debounce
      function debounce(func, wait) {
        let timeout;
        return function (...args) {
          clearTimeout(timeout);
          timeout = setTimeout(() => func.apply(this, args), wait);
        };
      }

      document.querySelectorAll(".tour-card").forEach(card => {
        const durationInput = card.querySelector(".duration-input");
        const peopleInput = card.querySelector(".people-input");
        const priceSpan = card.querySelector(".dynamic-price");

        function updatePrice() {
          const defaultPrice = parseFloat(durationInput.dataset.defaultPrice) || 0;
          const days = parseInt(durationInput.value) || 1;
          const people = parseInt(peopleInput.value) || 1;
          priceSpan.textContent = (defaultPrice * days * people).toFixed(2);
        }

        const debouncedUpdatePrice = debounce(updatePrice, 300);
        durationInput.addEventListener("input", debouncedUpdatePrice);
        peopleInput.addEventListener("input", debouncedUpdatePrice);
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
            div.innerHTML = `<img src="../Uploads/${image.image_path}" class="d-block w-100" alt="Tour Image">`;
            carousel.appendChild(div);
          });
        });
      });

      // Customize Booking
      window.customizeBooking = function (link) {
        const card = link.closest(".tour-card");
        const duration = card.querySelector(".duration-input").value;
        const people = card.querySelector(".people-input").value;
        const travelDate = card.querySelector(".travel_date").value;
        if (!travelDate) {
          alert("Please select a travel date.");
          return false;
        }
        const url = new URL(link.href);
        url.searchParams.set("duration", duration);
        url.searchParams.set("people", people);
        url.searchParams.set("travel_date", travelDate);
        window.location.href = url.toString();
        return false;
      };

      // Sort Cards
      window.sortCards = function () {
        const container = document.getElementById("tour-cards");
        const cards = Array.from(container.querySelectorAll(".col-12"));
        const sortOption = document.getElementById("sortSelect").value;
        container.classList.add("loading");
        setTimeout(() => {
          if (sortOption === "popular") {
            cards.sort((a, b) => {
              const aBooked = parseInt(a.querySelector(".booked-count")?.textContent) || 0;
              const bBooked = parseInt(b.querySelector(".booked-count")?.textContent) || 0;
              return bBooked - aBooked;
            });
          } else {
            cards.sort((a, b) => {
              const aIndex = parseInt(a.dataset.originalIndex) || 0;
              const bIndex = parseInt(b.dataset.originalIndex) || 0;
              return aIndex - bIndex;
            });
          }
          container.innerHTML = "";
          cards.forEach(card => container.appendChild(card));
          container.classList.remove("loading");
          const url = new URL(window.location);
          url.searchParams.set("sort", sortOption);
          window.history.pushState({}, "", url);
        }, 300);
      };

      // Contact Modal
      window.toggleContactModal = function () {
        const modal = document.getElementById("contactModal");
        modal.classList.toggle("show");
      };

      document.addEventListener("click", function (event) {
        const modal = document.getElementById("contactModal");
        if (!modal.contains(event.target) && !event.target.closest(".emergency_contact")) {
          modal.classList.remove("show");
        }
      });
    });
  </script>
</body>

</html>