<?php
include "../includes/config.php";
include "../includes/header.php";

// Fetch all tours with their destinations
$tours = $conn->query("SELECT tours.*, destinations.name AS destination, destinations.location FROM tours 
                        JOIN destinations ON tours.destination_id = destinations.destination_id");



// Check if a search term is provided via GET
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

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
    <style>
            body{
            height: 150vh;
            }
            .tour-card {
                    display: flex;
                    background: #f7f9fc;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                    margin: 20px auto;
                    max-width: 900px;
            }

            /* container grid: fixed 3 cols, locked to image height */
            .tour-card {
            display: grid;
            grid-template-columns: 370px 1fr 200px;
            background: #f7f9fc;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            align-items: stretch;      /* make all three cols same height */
            min-height: 200px;         /* tie total card height to image height */
            }

            /* 1) Image */
            .tour-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            }
      

            /* 2) Info */
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
            /* truncate to 3 lines */
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
            margin-top: auto;  /* push “Show more” to bottom of info section */
            }

            /* 3) Booking */
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
    <!-- Add Sorting Dropdown -->
     <div class="d-flex">
     Order By Popular :
    <select id="sortSelect" onchange="sortCards()" class="form-select mb-4" style="max-width:200px;">
      <option value="default">Default</option>
      <option value="popular">Filter by Popular</option>
    </select>
     </div>

    <form method="get" action="" class="mb-4">
        <div class="input-group">
            <input style="max-width:300px;" type="text" name="search" class="form-control" placeholder="Search by title or destination" 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
        <?php if (!empty($searchTerm)): ?>
            <a href="?" class="btn btn-link mt-2">Clear Search</a>
        <?php endif; ?>
    </form>


    <div class="row">
    <?php
    $index = 0; // Initialize index to track original order
    while ($tour = $tours->fetch_assoc()): ?>
    <!-- Add data-original-index to track original order -->
    <div class="col-12 mb-4" data-original-index="<?= $index ?>">
      <div class="tour-card">
        <!-- 1) IMAGE COLUMN -->
        <div class="tour-image">
          <img src="../uploads/<?= htmlspecialchars($tour["image"]) ?>"
               alt="<?= htmlspecialchars($tour["title"]) ?>">
        </div>

        <!-- 2) INFO COLUMN -->
        <div class="tour-info" style="position: relative;">
          <h5 class="title"><?= htmlspecialchars($tour["title"]) ?></h5>
          <p class="meta"><strong>Destination:</strong> <?= htmlspecialchars(
              $tour["destination"]
          ) ?></p>

          <!-- Location Map Link (unchanged) -->
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
          <p class="meta">
            <strong>Location:</strong>
            <a href="<?= $mapLink ?>" target="_blank">View on Map</a>
          </p>

          <!-- Description (unchanged) -->
          <?php
          $fullDesc = htmlspecialchars($tour["description"]);
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
            $tour_id = $tour["tour_id"];
            $stmt = $conn->prepare(
                "SELECT SUM(people) AS total_people FROM bookings WHERE tour_id = ?"
            );
            $stmt->bind_param("i", $tour_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $totalPeople = 0;
            if ($row = $result->fetch_assoc()) {
                $totalPeople = intval($row["total_people"]);
            }
            $stmt->close();
            $starColor = $totalPeople >= 100 ? "gold" : "black";
            ?>
            <!-- Wrap the number in a span with class booked-count -->
          <?php
                    $stars = min(floor($totalPeople / 100), 5);
            ?>
            <span class="booked-count"><?= $totalPeople ?></span>
            <?php for ($i = 0; $i < $stars; $i++): ?>
                <i style="color: <?= $starColor ?>;" class="fa-solid fa-star"></i>
            <?php endfor; ?>
            people booked
          </div>
        </div>

        <!-- 3) BOOKING COLUMN (unchanged) -->
        <div class="tour-book">
          <?php
          $defaultDuration = (int) $tour["duration"];
          $defaultPrice = (float) $tour["price"];
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
          <label for="calendar">Date:</label> 
          <input type="text" id="calendar" name="booking_date" class="booking_date" placeholder="yyyy-mm-dd">
          <p class="total-price">
            Total: $<span class="dynamic-price">
              <?= number_format($defaultPrice * $defaultDuration, 2) ?>
            </span>
          </p>
          <a href="tour_details.php?id=<?= $tour["tour_id"] ?>"
             onclick="return customizeBooking(this)"
             class="btn btn-primary btn-book">
            Book Now
          </a>
        </div>
      </div>
    </div>
    <?php $index++;endwhile; // Increment index for the next tour card
    ?>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Start Of Des dynamic -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".see-more").forEach(btn => {
    btn.addEventListener("click", () => {
      const desc     = btn.previousElementSibling;
      const fullText = desc.dataset.fulltext;
      const shortText= desc.dataset.shorttext;

      if (desc.textContent.trim().endsWith("…")) {
        desc.textContent = fullText;
        btn.textContent  = "Show Less";
      } else {
        desc.textContent = shortText + "…";
        btn.textContent  = "Show More";
      }
    });
  });
});
</script>

<!-- End Of Des dynamic -->

<!-- Dynamic Price -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".tour-card").forEach(card => {
    const durationInput = card.querySelector(".duration-input");
    const peopleInput   = card.querySelector(".people-input");
    const priceSpan     = card.querySelector(".dynamic-price");

    function updatePrice() {
      const defaultPrice = parseFloat(durationInput.dataset.defaultPrice) || 0;
      const days         = parseInt(durationInput.value, 10) || 1;
      const people       = parseInt(peopleInput.value,   10) || 1;
      priceSpan.textContent = (defaultPrice * days * people).toFixed(2);
    }

    // initialize
    updatePrice();

    durationInput.addEventListener("input", updatePrice);
    peopleInput.addEventListener("input", updatePrice);
  });
});

// Fixing customizeBooking
function customizeBooking(link) {
  const card = link.closest(".tour-card");
  const durationInput = card.querySelector(".duration-input");
  const peopleInput = card.querySelector(".people-input");
  const dateInput = card.querySelector(".booking_date");

  const duration = parseInt(durationInput.value) || 1;
  const people = parseInt(peopleInput.value) || 1;
  const bookingDate = dateInput.value || '';

  if (bookingDate.trim() === '') {
    alert('Please select a booking date.');
    return false; // prevent navigation
  }

  const url = new URL(link.href);
  url.searchParams.set('duration', duration);
  url.searchParams.set('people', people);
  url.searchParams.set('booking_date', bookingDate);

  window.location.href = url.toString();
  return false; // prevent default <a> behavior
}
</script>

<script>
document.getElementById("calendar").addEventListener("change", function() {
  let inputDate = this.value.trim();
  
  let formattedDate = "";

  if (/^\d{2}-\d{2}-\d{4}$/.test(inputDate)) {
    // d-m-y
    let [day, month, year] = inputDate.split("-");
    formattedDate = `${year}-${month}-${day}`; // to standard yyyy-mm-dd
  } else if (/^\d{4}-\d{2}-\d{2}$/.test(inputDate)) {
    // y-m-d
    let [year, month, day] = inputDate.split("-");
    formattedDate = `${year}-${month}-${day}`;
  } else {
    alert("Please enter date in dd-mm-yyyy or yyyy-mm-dd format!");
    this.value = "";
    return;
  }

  console.log(formattedDate);
});

</script>


<!-- End Of Dynamic Price -->
<script>
function sortCards() {
  const container = document.querySelector(".row"); // The div that holds all tour cards
  const cards = Array.from(container.querySelectorAll(".col-12")); // All tour cards
  const sortOption = document.getElementById("sortSelect").value;

  if (sortOption === "popular") {
    // Sort by popularity (highest number of people booked first)
    cards.sort((a, b) => {
      const aBooked = parseInt(a.querySelector(".booked-count").textContent) || 0;
      const bBooked = parseInt(b.querySelector(".booked-count").textContent) || 0;
      return bBooked - aBooked; // Descending order
    });
  } else if (sortOption === "default") {
    // Sort by original order
    cards.sort((a, b) => {
      const aIndex = parseInt(a.dataset.originalIndex);
      const bIndex = parseInt(b.dataset.originalIndex);
      return aIndex - bIndex; // Ascending order
    });
  }

  // Re-append cards in the sorted order
  container.innerHTML = "";
  cards.forEach(card => container.appendChild(card));
}
</script>
</body>
</html>
<?php include "../admin/footer.php";
?>
