<?php
include '../includes/config.php';
include '../includes/admin_header.php';

// Handle payment status update
if (isset($_POST['action']) && isset($_POST['payment_id'])) {
    $action = $_POST['action'];
    $payment_id = $_POST['payment_id'];

    $status = match ($action) {
        'verify' => 'Verified',
        'reject' => 'Rejected',
        default => 'Pending',
    };

    $stmt = $conn->prepare("UPDATE payments SET payment_status = ? WHERE payment_id = ?");
    $stmt->bind_param('si', $status, $payment_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch monthly report data (May 2025)
$monthlyQuery = "
    SELECT 
        COUNT(*) as total_payments,
        SUM(amount) as total_amount,
        SUM(CASE WHEN payment_status = 'Verified' THEN 1 ELSE 0 END) as verified_count,
        SUM(CASE WHEN payment_status = 'Pending' THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN payment_status = 'Rejected' THEN 1 ELSE 0 END) as rejected_count
    FROM payments
    WHERE YEAR(payment_date) = 2025 AND MONTH(payment_date) = 5
";
$monthlyResult = $conn->query($monthlyQuery);
$monthlyData = $monthlyResult->fetch_assoc();
$totalPayments = $monthlyData['total_payments'] ?? 0;
$totalAmount = $monthlyData['total_amount'] ?? 0;
$verifiedCount = $monthlyData['verified_count'] ?? 0;
$pendingCount = $monthlyData['pending_count'] ?? 0;
$rejectedCount = $monthlyData['rejected_count'] ?? 0;

// Fetch yearly report data (2025)
$yearlyQuery = "
    SELECT 
        DATE_FORMAT(payment_date, '%b') as month,
        COUNT(*) as payment_count,
        SUM(amount) as total_amount
    FROM payments
    WHERE YEAR(payment_date) = 2025
    GROUP BY MONTH(payment_date)
    ORDER BY MONTH(payment_date)
";
$yearlyResult = $conn->query($yearlyQuery);
$yearlyLabels = [];
$yearlyPaymentCounts = [];
$yearlyAmounts = [];
while ($row = $yearlyResult->fetch_assoc()) {
    $yearlyLabels[] = $row['month'];
    $yearlyPaymentCounts[] = $row['payment_count'];
    $yearlyAmounts[] = $row['total_amount'];
}
$yearlyLabelsJson = json_encode($yearlyLabels);
$yearlyPaymentCountsJson = json_encode($yearlyPaymentCounts);
$yearlyAmountsJson = json_encode($yearlyAmounts);

// Fetch payment records
$query = "
    SELECT 
        p.payment_id,
        p.booking_id,
        u.name AS user_name,
        t.title AS tour_title,
        p.amount,
        p.qr_code_image,
        p.payment_status,
        p.payment_date
    FROM payments p
    JOIN users u ON p.user_id = u.user_id
    JOIN tours t ON p.tour_id = t.tour_id
    ORDER BY p.payment_date DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Payment</title>
    <link rel="stylesheet" href="../assets/css/style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js for yearly report -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-content {
            margin-left: 250px;
            padding: 20px;
            background: #f8f9fa;
            transition: margin-left 0.3s ease;
            min-height: calc(100vh - 110px);
        }

        .dashboard-content.collapsed {
            margin-left: 60px;
        }

        .main-content {
            margin-left: 250px;
            padding: 10px;
            height: 60px;
            position: sticky;
            top: 0;
            width: calc(100% - 250px);
            z-index: 999;
            background: #f8f9fa;
            color: #333;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }

        .main-content.collapsed {
            margin-left: 60px;
            width: calc(100% - 60px);
        }

        .sidebar-toggle {
            width: 40px;
            height: 40px;
            color: #333;
            font-size: 1.2rem;
            padding: 8px;
            cursor: pointer;
            border-radius: 50%;
            transition: background 0.2s ease, transform 0.2s ease;
            margin-left: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-toggle:hover {
            background: #e9ecef;
            transform: scale(1.1);
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

        .stats-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .table-responsive {
            margin-left: 260px;
            width: calc(100% - 260px);
        }

        .table-responsive.collapsed {
            margin-left: 60px;
            width: calc(100% - 60px);
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
        }
    </style>
</head>

<body>

    <!-- Dashboard Content -->
    <div class="dashboard-content" id="dashboard-content">
        <h2 class="mb-4" style="color: #333;">Payment Management</h2>
        <p style="color: #6c757d;">Reports and Payment Details</p>

        <!-- Monthly Report -->
        <h3>Monthly Report (May 2025)</h3>
        <div class="stats-row">
            <div class="dashboard-card">
                <i class="fa fa-credit-card"></i>
                <h3>Total Payments</h3>
                <p><?php echo number_format($totalPayments); ?></p>
            </div>
            <div class="dashboard-card" style="background: #e9ecef;">
                <i class="fa fa-dollar-sign"></i>
                <h3>Total Amount</h3>
                <p>$<?php echo number_format($totalAmount, 2); ?></p>
            </div>
            <div class="dashboard-card" style="background: #e9ecef;">
                <i class="fa fa-check-circle"></i>
                <h3>Verified</h3>
                <p><?php echo number_format($verifiedCount); ?></p>
            </div>
            <div class="dashboard-card" style="background: #e9ecef;">
                <i class="fa fa-hourglass-half"></i>
                <h3>Pending</h3>
                <p><?php echo number_format($pendingCount); ?></p>
            </div>
            <div class="dashboard-card" style="background: #e9ecef;">
                <i class="fa fa-times-circle"></i>
                <h3>Rejected</h3>
                <p><?php echo number_format($rejectedCount); ?></p>
            </div>
        </div>

        <!-- Yearly Report -->
        <div class="chart-container">
            <h3>Yearly Report (2025)</h3>
            <canvas id="yearlyChart"></canvas>
        </div>

        <!-- Payment Table -->
        <div class="table-responsive" id="table-responsive">
            <div class="row">
                <div class="col">
                    <h5>All Payments</h5>
                </div>
            </div>
            <table class="table table-bordered table-sm mt-3">
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Booking ID</th>
                        <th>User</th>
                        <th>Tour</th>
                        <th>Amount</th>
                        <th>QR Code</th>
                        <th>Status</th>
                        <th>Payment Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['payment_id'] ?></td>
                            <td><?= $row['booking_id'] ?></td>
                            <td><?= htmlspecialchars($row['user_name']) ?></td>
                            <td><?= htmlspecialchars($row['tour_title']) ?></td>
                            <td>$<?= number_format($row['amount'], 2) ?></td>
                            <td>
                                <?php if (isset($row['qr_code_image']) && !empty($row['qr_code_image'])): ?>
                                    <img src="../Uploads/qr_codes/<?= htmlspecialchars($row['qr_code_image']) ?>" alt="QR Code"
                                        style="max-width: 100px;">
                                <?php else: ?>
                                    <span>No QR Code</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?=
                                    $row['payment_status'] === 'Pending' ? 'warning' :
                                    ($row['payment_status'] === 'Verified' ? 'success' : 'danger') ?>">
                                    <?= $row['payment_status'] ?>
                                </span>
                            </td>
                            <td><?= $row['payment_date'] ?></td>
                            <td>
                                <?php if ($row['payment_status'] === 'Pending'): ?>
                                    <form action="" method="POST" style="display:inline-block;">
                                        <input type="hidden" name="payment_id" value="<?= $row['payment_id'] ?>">
                                        <button type="submit" name="action" value="verify"
                                            class="btn btn-success btn-sm">Verify</button>
                                        <button type="submit" name="action" value="reject"
                                            class="btn btn-danger btn-sm">Reject</button>
                                    </form>
                                <?php else: ?>
                                    <span class="badge bg-<?=
                                        $row['payment_status'] === 'Verified' ? 'success' : 'danger' ?>">
                                        <?= $row['payment_status'] ?>
                                    </span>
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

        // Chart.js for yearly report
        const ctx = document.getElementById('yearlyChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo $yearlyLabelsJson; ?>,
                datasets: [
                    {
                        label: 'Payment Count',
                        data: <?php echo $yearlyPaymentCountsJson; ?>,
                        borderColor: '#007bff',
                        fill: true,
                        backgroundColor: 'rgba(0, 123, 255, 0.2)',
                        yAxisID: 'y1'
                    },
                    {
                        label: 'Total Amount ($)',
                        data: <?php echo $yearlyAmountsJson; ?>,
                        borderColor: '#28a745',
                        fill: true,
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        yAxisID: 'y2'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y1: {
                        type: 'linear',
                        position: 'left',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Payment Count'
                        }
                    },
                    y2: {
                        type: 'linear',
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Amount ($)'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>