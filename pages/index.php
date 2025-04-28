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
    <div class="row">
    <?php while ($tour = $tours->fetch_assoc()): ?>
    <div class="col-12 mb-4">
      <div class="tour-card">
        <!-- 1) IMAGE COLUMN -->
        <div class="tour-image">
          <img src="../uploads/<?= htmlspecialchars($tour['image']) ?>"
               alt="<?= htmlspecialchars($tour['title']) ?>">
        </div>

        <!-- 2) INFO COLUMN -->
        <div class="tour-info" style="position: relative;">
          <h5 class="title"><?= htmlspecialchars($tour['title']) ?></h5>
          <p class="meta"><strong>Destination:</strong> <?= htmlspecialchars($tour['destination']) ?></p>

          <!-- Location Map Link -->
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

          <!-- Description -->
          <?php
            $fullDesc  = htmlspecialchars($tour['description']);
            $shortDesc = substr($fullDesc, 0, 100);
          ?>
          <p class="description"
             data-fulltext="<?= $fullDesc ?>"
             data-shorttext="<?= $shortDesc ?>">
            <?= $shortDesc ?>…
          </p>
          <a href="javascript:void(0)" class="see-more">Show More</a>

          <!-- Total People Booked -->
          <div style="position: absolute; right:10px; top:0;">
            <?php
            $tour_id = $tour["tour_id"];
            $stmt = $conn->prepare("SELECT SUM(people) AS total_people FROM bookings WHERE tour_id = ?");
            $stmt->bind_param("i", $tour_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                echo intval($row['total_people']);
            } else {
                echo "0";
            }
            $totalPeople = $row['total_people'] ?? 0; 
            $starColor = ($totalPeople >= 100) ? 'gold' : 'black';
            $stmt->close();
            ?>
            <i style="color: <?php echo $starColor; ?>;" class="fa-solid fa-star"></i>

            people booked
          </div>
        </div>

        <!-- 3) BOOKING COLUMN -->
        <div class="tour-book">
          <?php
            $defaultDuration = (int)$tour["duration"];
            $defaultPrice    = (float)$tour["price"];
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
          <!-- calendar booking -->

          <label for="calendar">Date:</label> 
          <input type="text" id="calendar" name="booking_date" class="booking_date" placeholder="yyyy-mm-dd">


            <!--  -->
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
<?php endwhile; ?>
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

  console.log(formattedDate); // Now always in yyyy-mm-dd
});

</script>


<!-- End Of Dynamic Price -->

</body>
</html>
<?php include "../admin/footer.php";
?>
