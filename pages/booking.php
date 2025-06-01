<?php
include "../includes/config.php";
include "../includes/header.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if tour_id is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$tour_id = (int) $_GET['id'];

// Fetch tour details
$stmt = $conn->prepare("SELECT t.*, d.name AS destination 
                        FROM tours t 
                        JOIN destinations d ON t.destination_id = d.destination_id 
                        WHERE t.tour_id = ? AND t.isDeleted = 0");
$stmt->bind_param("i", $tour_id);
$stmt->execute();
$tour = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$tour) {
    header("Location: index.php");
    exit();
}

// Fetch available guides
$guides = $conn->query("SELECT guide_id, name, language FROM guides WHERE is_deleted = 0");
$guide_options = [];
while ($guide = $guides->fetch_assoc()) {
    $guide_options[] = $guide;
}

// Prefill form
$checkin = $_GET['travel_date'] ?? date('Y-m-d');
$default_duration = is_numeric($tour['duration']) ? (int) $tour['duration'] : 1; // Handle varchar duration
$checkout = date('Y-m-d', strtotime("$checkin + $default_duration days"));
$guests = max(1, (int) ($_GET['people'] ?? 1)); // Ensure at least 1 guest
$selected_guide = $_GET['guide_id'] ?? ''; // Prefill guide if provided

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $guests = (int) $_POST['guests'];
    $special_request = htmlspecialchars(trim($_POST['special_request'] ?? ''));
    $guide_id = (int) ($_POST['guide_id'] ?? 0);

    // Validate inputs
    if ($guests < 1) {
        $error_message = "Number of guests must be at least 1.";
    } elseif ($guide_id === 0) {
        $error_message = "Please select a guide.";
    } else {
        $checkin_date = new DateTime($checkin);
        $checkout_date = new DateTime($checkout);
        if ($checkout_date <= $checkin_date) {
            $error_message = "Return date must be after travel date.";
        } else {
            $duration = (int) $checkin_date->diff($checkout_date)->days;
            // Calculate discounted price including duration (10% off, multiplied by guests and days)
            $discounted_price = (float) $tour['price'] * 0.9 * $guests * $duration;

            // Insert into bookings
            $stmt = $conn->prepare("INSERT INTO bookings (user_id, tour_id, travel_date, duration, people, price, special_request, status, payment_status, guide_id) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', ?)");
            $stmt->bind_param("iisiddsi", $user_id, $tour_id, $checkin, $duration, $guests, $discounted_price, $special_request, $guide_id);
            if ($stmt->execute()) {
                $booking_id = $conn->insert_id;

                // Insert into payments
                $stmt_payment = $conn->prepare("INSERT INTO payments (booking_id, user_id, tour_id, amount, payment_status) 
                                               VALUES (?, ?, ?, ?, ?)");
                $payment_status = 'Pending';
                $stmt_payment->bind_param("iiids", $booking_id, $user_id, $tour_id, $discounted_price, $payment_status);
                if ($stmt_payment->execute()) {
                    $success_message = "Booking successful! We'll contact you soon.";
                } else {
                    $error_message = "Payment recording error: " . $conn->error;
                }
                $stmt_payment->close();
            } else {
                $error_message = "Booking error: " . $conn->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Tour - <?= htmlspecialchars($tour['title']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fff5f7;
            margin: 0;
            padding: 0;
        }

        .booking-section {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .tour-details {
            flex: 1;
            min-width: 300px;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .tour-details .tour-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .tour-details .tour-info {
            padding: 20px;
            text-align: center;
        }

        .tour-details h3 {
            font-size: 1.5rem;
            color: #007bff;
            margin-bottom: 10px;
        }

        .tour-details .rating {
            color: #ffcc00;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .tour-details .reviews {
            font-size: 0.9rem;
            color: #666;
            margin-left: 5px;
        }

        .tour-details p {
            font-size: 1rem;
            color: #555;
            margin-bottom: 15px;
        }

        .tour-details .price-info {
            margin-bottom: 20px;
        }

        .tour-details .original-price {
            font-size: 1rem;
            color: #999;
            text-decoration: line-through;
            margin-right: 5px;
        }

        .tour-details .price {
            font-size: 1.3rem;
            font-weight: 600;
            color: #007bff;
        }

        .booking-form {
            flex: 2;
            min-width: 300px;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .booking-form h2 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .booking-form .form-group {
            margin-bottom: 20px;
        }

        .booking-form label {
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }

        .booking-form .form-control {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .booking-form .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
        }

        .booking-form .btn-book {
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            width: 100%;
            transition: background 0.3s ease;
        }

        .booking-form .btn-book:hover {
            background: #0056b3;
        }

        .booking-form .alert {
            margin-bottom: 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .booking-section {
                flex-direction: column;
                margin: 20px 15px;
            }

            .tour-details,
            .booking-form {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="booking-section">
        <div class="tour-details">
            <div class="tour-image">
                <img src="../Uploads/<?= htmlspecialchars($tour['image'] ?? 'placeholder.jpg') ?>"
                    alt="<?= htmlspecialchars($tour['title']) ?>">
            </div>
            <div class="tour-info">
                <h3><?= htmlspecialchars($tour['title']) ?></h3>
                <div class="rating">
                    <?php
                    $rating = 4.5; // Replace with dynamic logic if available
                    for ($i = 1; $i <= 5; $i++) {
                        echo $i <= floor($rating) ? '<i class="fas fa-star"></i>' : ($i - 0.5 <= $rating ? '<i class="fas fa-star-half-alt"></i>' : '<i class="far fa-star"></i>');
                    }
                    ?>
                    <span class="reviews">
                        <?php
                        $stmt = $conn->prepare("SELECT SUM(people) AS total_people FROM bookings WHERE tour_id = ?");
                        $stmt->bind_param("i", $tour_id);
                        $stmt->execute();
                        $totalPeople = $stmt->get_result()->fetch_assoc()['total_people'] ?? 0;
                        $stmt->close();
                        echo $totalPeople . ' bookings';
                        ?>
                    </span>
                </div>
                <p><strong>Destination:</strong> <?= htmlspecialchars($tour['destination']) ?></p>
                <div class="price-info">
                    <span class="original-price">$<?= number_format((float) $tour['price'], 2) ?></span>
                    <span class="price">From US$<?= number_format((float) $tour['price'] * 0.9, 2) ?></span>
                </div>
            </div>
        </div>

        <div class="booking-form">
            <h2>Book Your Tour</h2>
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?= $success_message ?></div>
            <?php elseif (isset($error_message)): ?>
                <div class="alert alert-danger"><?= $error_message ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <div class="form-group">
                    <label for="checkin">Travel Date</label>
                    <input type="date" class="form-control" id="checkin" name="checkin" value="<?= $checkin ?>"
                        required>
                </div>
                <div class="form-group">
                    <label for="checkout">Return Date</label>
                    <input type="date" class="form-control" id="checkout" name="checkout" value="<?= $checkout ?>"
                        required>
                </div>
                <div class="form-group">
                    <label for="guests">Number of Guests</label>
                    <select class="form-control" id="guests" name="guests" required>
                        <option value="1" <?= $guests == 1 ? 'selected' : '' ?>>1</option>
                        <option value="2" <?= $guests == 2 ? 'selected' : '' ?>>2</option>
                        <option value="3" <?= $guests == 3 ? 'selected' : '' ?>>3</option>
                        <option value="4" <?= $guests >= 4 ? 'selected' : '' ?>>4+</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="guide_id">Select Guide</label>
                    <select class="form-control" id="guide_id" name="guide_id" required>
                        <option value="" disabled <?= empty($selected_guide) ? 'selected' : '' ?>>Choose a guide</option>
                        <?php foreach ($guide_options as $guide): ?>
                            <option value="<?= $guide['guide_id'] ?>" <?= $selected_guide == $guide['guide_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($guide['name']) ?> (Speaks:
                                <?= htmlspecialchars($guide['language']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="special_request">Special Requests (Optional)</label>
                    <textarea class="form-control" id="special_request" name="special_request" rows="3"
                        placeholder="E.g., dietary needs, accessibility"><?= htmlspecialchars($special_request ?? '') ?></textarea>
                </div>
                <p id="price-display"></p>
                <button type="submit" class="btn-book">Confirm Booking</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const checkin = document.getElementById("checkin");
            const checkout = document.getElementById("checkout");
            const guests = document.getElementById("guests");
            const priceDisplay = document.getElementById("price-display");
            const tourPrice = <?= json_encode((float) $tour['price']) ?>;

            function updatePrice() {
                const start = new Date(checkin.value);
                const end = new Date(checkout.value);
                const days = Math.max(1, (end - start) / (1000 * 60 * 60 * 24));
                const guestCount = parseInt(guests.value) || 1;
                const price = tourPrice * 0.9 * guestCount * days;
                priceDisplay.textContent = days >= 1 ? `Total: $${price.toFixed(2)} for ${days} day${days > 1 ? 's' : ''}` : "Please select valid dates";
            }

            checkin.addEventListener("change", updatePrice);
            checkout.addEventListener("change", updatePrice);
            guests.addEventListener("change", updatePrice);
            updatePrice();
        });
    </script>
    <?php include "./user_footer.php"; ?>
</body>

</html>