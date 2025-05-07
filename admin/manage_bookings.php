<?php
include '../includes/config.php';
include '../includes/admin_header.php';

// Handle the form submission (status update)
if (isset($_POST['action']) && isset($_POST['booking_id'])) {
    $action = $_POST['action'];
    $booking_id = $_POST['booking_id'];

    if ($action === 'confirm') {
        $status = 'Confirmed';
    } elseif ($action === 'reject') {
        $status = 'Rejected';
    } else {
        $status = 'Pending';  // Default case (shouldn't normally happen)
    }

    // Update the booking status in the database
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
    $stmt->bind_param('si', $status, $booking_id); 
    $stmt->execute();
    $stmt->close();
}

// Fetch all bookings with user and tour details
$query = "
    SELECT 
        b.booking_id,
        u.name AS user_name,
        t.title AS tour_title,
        b.people,
        b.duration,
        b.price,
        b.booking_date,
        b.travel_date,
        b.status
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN tours t ON b.tour_id = t.tour_id
    ORDER BY b.booking_id DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<style>
    .table-responsive {
        max-height: 500px;
    }

    .table th, .table td {
        padding: 8px 10px;
        font-size: 0.85rem;
    }

    .table-sm th, .table-sm td {
        padding: 6px 8px;
    }

    .table th, .table td {
        text-align: center;
    }

    button {
        font-size: 0.85rem;
    }
</style>
<body>

<div style="position:absolute; right:2rem;padding-top:1rem;" class="w-75 table-responsive mt-3">
    <div class="row">
        <div class="col"><h5>Booking Management/</h5></div>
    </div>
    <table class="table table-bordered table-sm mt-3">
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>User</th>
                <th>Tour</th>
                <th>People</th>
                <th>Duration</th>
                <th>Price</th>
                <th>Booking Date</th>
                <th>Travel Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['booking_id'] ?></td>
                    <td><?= htmlspecialchars($row['user_name']) ?></td>
                    <td><?= htmlspecialchars($row['tour_title']) ?></td>
                    <td><?= $row['people'] ?></td>
                    <td><?= $row['duration'] ?> days</td>
                    <td>$<?= number_format($row['price'], 2) ?></td>
                    <td><?= $row['booking_date'] ?></td>
                    <td><?= $row['travel_date'] ?></td>
                    <td>
                        <span class="badge bg-<?= 
                            $row['status'] === 'Pending' ? 'warning' : 
                            ($row['status'] === 'Confirmed' ? 'success' : 'danger') ?>">
                            <?= $row['status'] ?>
                        </span>
                    </td>
                    <td>
                    <?php 
                        $status = strtolower(trim($row['status']));
                        if ($status == 'pending'): ?>
                        <form action="update_status.php" method="POST" style="display:inline-block;">
                            <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                            <button type="submit" name="action" value="confirm" class="btn btn-success btn-sm">Confirm</button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                        </form>
                    <?php elseif ($status == 'confirmed'): ?>
                        <span class="badge bg-success">Confirmed</span>
                    <?php elseif ($status == 'rejected'): ?>
                        <span class="badge bg-danger">Rejected</span>
                    <?php endif; ?>
                </td>

                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
include '../admin/footer.php';
?>
