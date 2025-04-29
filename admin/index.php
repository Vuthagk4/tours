<?php
include '../includes/config.php';
include '../includes/admin_header.php';

// Fetch all bookings with user and tour details
$query = "SELECT b.booking_id, u.name AS user_name, t.title AS tour_title, b.travel_date, b.status, 
                 p.status AS payment_status 
          FROM bookings b
          JOIN users u ON b.user_id = u.user_id
          JOIN tours t ON b.tour_id = t.tour_id
          LEFT JOIN payments p ON b.booking_id = p.booking_id
          ORDER BY b.booking_date DESC";

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
<body>

<div class="container mt-5 w-75" style="position:absolute; right:0px">
    <h2 class="text-center mb-4">Booking Dashboard</h2>

    <!-- Search and Filter -->
    <div class="d-flex justify-content-between mb-3">
        <input type="text" id="search" class="form-control w-50" placeholder="Search bookings...">
        <select id="filterStatus" class="form-select w-25">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>

    <!-- Booking Table -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>User</th>
                <th>Tour</th>
                <th>Travel Date</th>
                <th>Status</th>
                <th>Payment</th>
            </tr>
        </thead>
        <tbody id="bookingTable">
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['booking_id']; ?></td>
                    <td><?= htmlspecialchars($row['user_name']); ?></td>
                    <td><?= htmlspecialchars($row['tour_title']); ?></td>
                    <td><?= htmlspecialchars($row['travel_date']); ?></td>
                    <td class="status"><?= ucfirst($row['status']); ?></td>
                    <td class="payment"><?= ucfirst($row['payment_status'] ?? 'Pending'); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- JavaScript for Search and Filter -->
<script>
    document.getElementById("search").addEventListener("input", function () {
        let searchValue = this.value.toLowerCase();
        let rows = document.querySelectorAll("#bookingTable tr");
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(searchValue) ? "" : "none";
        });
    });

    document.getElementById("filterStatus").addEventListener("change", function () {
        let filterValue = this.value.toLowerCase();
        let rows = document.querySelectorAll("#bookingTable tr");
        rows.forEach(row => {
            let status = row.querySelector(".status").textContent.toLowerCase();
            row.style.display = filterValue === "" || status.includes(filterValue) ? "" : "none";
        });
    });
</script>

</body>
</html>
<?php
include '../admin/footer.php';
?>