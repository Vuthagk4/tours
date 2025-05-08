<?php
session_start();
include "../includes/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT b.*, t.title, d.name AS destination, t.image
    FROM bookings b
    JOIN tours t ON b.tour_id = t.tour_id
    JOIN destinations d ON t.destination_id = d.destination_id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Booking History</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 30px;
            background-color: #f9f9f9;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.2em;
            color: #2c3e50;
        }

        .booking-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            padding: 20px;
            transition: box-shadow 0.3s ease;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .booking-card:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        }

        .booking-card img {
            width: 180px;
            height: 120px;
            border-radius: 8px;
            object-fit: cover;
        }

        .booking-info {
            flex: 1;
        }

        .booking-info h3 {
            margin-top: 0;
            font-size: 1.5em;
            color: #007bff;
            margin-bottom: 8px;
        }

        .booking-info p {
            margin: 6px 0;
            font-size: 1em;
            color: #555;
        }

        .booking-info .price {
            font-weight: bold;
            color: #e74c3c;
        }

        .btn {
            padding: 10px 18px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            border-radius: 6px;
            margin-top: 12px;
            transition: background-color 0.3s ease;
            display: inline-block;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .btn-confirm {
            background-color: #2ecc71;
        }

        .btn-confirm:hover {
            background-color: #27ae60;
        }

        .btn-cancel {
            background-color: #e74c3c;
        }

        .btn-cancel:hover {
            background-color: #c0392b;
        }

        .no-bookings {
            text-align: center;
            font-size: 1.2em;
            color: #777;
        }

        .card-footer {
            text-align: center;
            margin-top: 15px;
        }

        @media (max-width: 768px) {
            .booking-card {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .booking-card img {
                width: 100%;
                height: auto;
            }

            .booking-info {
                width: 100%;
            }
        }

        /* Style for buttons */
        .booking-actions {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .booking-actions .btn {
            display: inline-block;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .booking-actions .btn-confirm {
            background-color: #28a745;
            color: #fff;
            border: none;
        }

        .booking-actions .btn-confirm:hover {
            background-color: #218838;
        }

        .booking-actions .btn-cancel {
            background-color: #dc3545;
            color: #fff;
            border: none;
        }

        .booking-actions .btn-cancel:hover {
            background-color: #c82333;
        }

        /* Media Query for Small Screens */
        @media (max-width: 768px) {
            .booking-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .booking-actions .btn {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <h2>My Booking History</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($booking = $result->fetch_assoc()): ?>
            <div class="booking-card">
                <img src="../Uploads/<?= htmlspecialchars($booking['image']) ?>"
                    alt="<?= htmlspecialchars($booking['title']) ?>">
                <div class="booking-info">
                    <h3><?= htmlspecialchars($booking['title']) ?></h3>
                    <p><strong>Destination:</strong> <?= htmlspecialchars($booking['destination']) ?></p>
                    <p><strong>Travel Date:</strong> <?= htmlspecialchars($booking['travel_date']) ?></p>
                    <p><strong>People:</strong> <?= (int) $booking['people'] ?></p>
                    <p><strong class="price">Total Price:</strong> $<?= number_format($booking['price'], 2) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($booking['status']) ?></p>

                    <div class="booking-actions">
                        <?php if ($booking['status'] === 'pending'): ?>
                            <?php if ($booking['status'] === 'pending'): ?>
                                <!-- Confirm Payment -->
                                <form action="scan_payment.php" method="GET">
                                    <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                    <button type="submit" class="btn btn-confirm">Confirm Payment</button>
                                </form>
                            <?php endif; ?>


                            <!-- Cancel -->
                            <form action="update_booking.php" method="POST" style="display: flex;">
                                <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                <input type="hidden" name="action" value="cancel">
                                <button type="submit" class="btn btn-cancel"
                                    onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <br>
                    <a href="booking_detail.php?id=<?= $booking['booking_id'] ?>" class="btn">View Detail</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="no-bookings">You have no bookings yet.</p>
    <?php endif; ?>

    <script>
        document.querySelectorAll('.booking-actions form').forEach(function (form) {
            form.addEventListener('submit', function () {
                form.querySelector('button').disabled = true;
                form.querySelector('button').textContent = 'Processing...';
            });
        });
    </script>
</body>

</html>