<?php
include '../includes/config.php';
include '../includes/admin_header.php';

// Handle the form submission (status update)
if (isset($_POST['action']) && isset($_POST['booking_id'])) {
    $action = $_POST['action'];
    $booking_id = (int) $_POST['booking_id'];

    if ($action === 'confirm') {
        $status = 'Confirmed';
    } elseif ($action === 'reject') {
        $status = 'Rejected';
    } else {
        $status = 'Pending'; // Default case
    }

    // Update the booking status in the database
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
    $stmt->bind_param('si', $status, $booking_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to avoid form resubmission
    ("Location: manage_bookings.php");
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
        b.qr_code_image,
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
    .dashboard-content {
        margin-left: 90px;
        padding: 10px;
        background: #f8f9fa;
        transition: margin-left 0.3s ease;
    }

    .dashboard-content.collapsed {
        margin-left: 60px;
    }

    .table-responsive {
        margin-left: 260px;
        width: calc(100% - 260px);
    }

    .table-responsive.collapsed {
        margin-left: 60px;
        width: calc(100% - 60px);
    }

    .sidebar-toggle {
        color: #333;
        font-size: 1.5rem;
        padding: 10px;
        cursor: pointer;
        border-radius: 50%;
        transition: background 0.2s ease;
        margin-left: 10px;
    }

    .sidebar-toggle:hover {
        background: #e9ecef;
    }

    .dashboard-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        text-align: center;
        margin-bottom: 20px;
        transition: transform 0.2s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
    }

    .dashboard-card h3 {
        margin: 0;
        font-size: 1rem;
        color: #6c757d;
        font-weight: 500;
    }

    .dashboard-card p {
        font-size: 1.5rem;
        font-weight: 600;
        color: #007bff;
        margin: 10px 0 0;
    }

    .dashboard-card i {
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: #007bff;
    }

    .table th,
    .table td {
        padding: 8px 10px;
        font-size: 0.85rem;
        text-align: center;
    }

    .table-sm th,
    .table-sm td {
        padding: 6px 8px;
    }

    button {
        font-size: 0.85rem;
    }

    @media screen and (max-width: 768px) {
        .dashboard-content {
            margin-left: 60px;
        }

        .table-responsive {
            margin-left: 60px;
            width: calc(100% - 60px);
        }

        .main-content {
            margin-left: 60px;
            width: calc(100% - 60px);
        }

        .stats-row {
            flex-direction: column;
        }

        .dashboard-card {
            width: 100%;
        }

        .sidebar-toggle {
            width: 36px;
            height: 36px;
            font-size: 1rem;
            padding: 6px;
        }
</style>

<body>
    <div class="dashboard-content" id="dashboard-content">

        <div class="table-responsive" id="table-responsive"">
            <div class=" row">
            <div class="col">
                <h5>Booking Management</h5>
            </div>
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
                    <th>QR Code</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
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
                            <?php if ($row['qr_code_image'] && file_exists("../Uploads/qr_codes/{$row['qr_code_image']}")): ?>
                                <img src="../Uploads/qr_codes/<?= $row['qr_code_image'] ?>" alt="QR Code"
                                    style="max-width: 100px;">
                            <?php else: ?>
                                <span>No QR Code</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $status = strtolower(trim($row['status']));
                            if ($status === 'pending'): ?>
                                <form action="manage_bookings.php" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                                    <button type="submit" name="action" value="confirm"
                                        class="btn btn-success btn-sm">Confirm</button>
                                    <button type="submit" name="action" value="reject"
                                        class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            <?php elseif ($status === 'confirmed'): ?>
                                <span class="badge bg-success">Confirmed</span>
                            <?php elseif ($status === 'rejected'): ?>
                                <span class="badge bg-danger">Rejected</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    </div>
    <script>
        const dashboardContent = document.getElementById('dashboard-content');
        const tableResponsive = document.getElementById('table-responsive');

        toggleButton.addEventListener('click', () => {
            requestAnimationFrame(() => {

                dashboardContent.classList.toggle('collapsed');
                tableResponsive.classList.toggle('collapsed');
            });
        });
    </script>

</body>

</html>

<?php
$result->free();
$conn->close();
?>