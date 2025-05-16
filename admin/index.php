<?php
include '../includes/config.php';
include '../includes/admin_header.php';

// Fetch data for dashboard cards (using users table)
$totalUsersQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$totalUsers = mysqli_fetch_assoc($totalUsersQuery)['total'] ?? 0;

$adminUsersQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'admin'");
$adminUsers = mysqli_fetch_assoc($adminUsersQuery)['total'] ?? 0;

$customerUsersQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
$customerUsers = mysqli_fetch_assoc($customerUsersQuery)['total'] ?? 0;

// Fetch recent users
$recentUsersQuery = mysqli_query($conn, "
    SELECT user_id, name, email, role, created_at
    FROM users
    ORDER BY created_at DESC
    LIMIT 5
");
if (!$recentUsersQuery) {
    die("Query failed: " . mysqli_error($conn));
}

// Fetch data for chart (user registrations per month)
$chartDataQuery = mysqli_query($conn, "
    SELECT DATE_FORMAT(created_at, '%b %d') as month, COUNT(*) as count
    FROM users
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m-%d')
    ORDER BY created_at
");
$chartLabels = [];
$chartValues = [];
while ($row = mysqli_fetch_assoc($chartDataQuery)) {
    $chartLabels[] = $row['month'];
    $chartValues[] = $row['count'];
}
$chartLabelsJson = json_encode($chartLabels);
$chartValuesJson = json_encode($chartValues);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #1a1a1a;
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: width 0.3s ease;
            z-index: 99999999999;
            box-shadow: rgba(0, 0, 0, 0.3) 0px 19px 38px, rgba(0, 0, 0, 0.22) 0px 15px 12px;
        }

        .sidebar.collapsed {
            width: 60px;
        }

        .sidebar .section-1 {
            width: 100%;
            padding: 0 10px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .sidebar.collapsed .section-1 {
            justify-content: center;
        }

        .sidebar .section-1 .row {
            width: 100%;
            transition: opacity 0.2s ease;
        }

        .sidebar.collapsed .section-1 .row .col-9 {
            opacity: 0;
            display: none;
        }

        .sidebar .section-1 .company-name {
            color: white;
            font-weight: 500;
            opacity: 1;
            transition: opacity 0.2s ease;
            font-size: 1.5rem;
        }

        .sidebar.collapsed .section-1 .company-name {
            opacity: 0;
            display: none;
        }

        .sidebar hr {
            width: 100%;
            border-color: rgba(255, 255, 255, 0.2);
            margin: 15px 0;
            transition: width 0.3s ease;
        }

        .sidebar.collapsed hr {
            width: 100%;
        }

        .sidebar ul {
            margin-top: 1rem;
            list-style: none;
            padding: 0;
            width: 100%;
        }

        .sidebar ul li {
            width: 100%;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            font-size: 16px;
            transition: padding 0.3s ease, background 0.3s ease;
        }

        .sidebar ul li a i {
            margin-right: 10px;
            transition: margin-right 0.3s ease;
            font-size: 1.2rem;
        }

        .sidebar ul li a:hover {
            background: #343a40;
        }

        .sidebar.collapsed ul li a span {
            opacity: 0;
            display: none;
        }

        .sidebar.collapsed ul li a {
            justify-content: center;
            padding: 12px 10px;
        }

        .sidebar.collapsed ul li a i {
            margin-right: 0;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            height: 80px;
            transition: margin-left 0.3s ease, width 0.3s ease;
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            width: calc(100% - 250px);
            z-index: 999;
            background: #f8f9fa;
            color: #333;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .main-content.collapsed {
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

        .admin-info {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
            padding: 8px 15px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            color: #333;
            font-weight: 500;
        }

        .admin-info button {
            background-color: #dc3545;
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .admin-info button:hover {
            background-color: #c82333;
        }

        .img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }

        .dashboard-content {
            margin-left: 250px;
            padding: 20px;
            background: #f8f9fa;
            transition: margin-left 0.3s ease;
            min-height: calc(100vh - 80px);
        }

        .dashboard-content.collapsed {
            margin-left: 60px;
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

        .daily-sales {
            background: #007bff;
            color: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .daily-sales h3 {
            margin: 0;
            font-size: 1rem;
            color: white;
        }

        .daily-sales p {
            font-size: 1.8rem;
            font-weight: 600;
            margin: 10px 0 0;
            color: white;
        }

        .btn-custom {
            background: #007bff;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            font-size: 0.9rem;
            cursor: pointer;
            margin-left: 10px;
        }

        .btn-custom:hover {
            background: #0056b3;
        }

        .recent-users table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        .recent-users th,
        .recent-users td {
            padding: 12px;
            text-align: left;
        }

        .recent-users th {
            background: #007bff;
            color: white;
        }

        .recent-users tr:nth-child(even) {
            background: #f8f9fa;
        }

        @media screen and (max-width: 768px) {
            .sidebar {
                width: 60px;
            }

            .sidebar .section-1 .row .col-9 {
                opacity: 0;
                display: none;
            }

            .sidebar .section-1 {
                justify-content: center;
            }

            .sidebar ul li a span {
                opacity: 0;
                display: none;
            }

            .sidebar ul li a {
                justify-content: center;
                padding: 12px 10px;
            }

            .sidebar ul li a i {
                margin-right: 0;
            }

            .main-content {
                margin-left: 60px;
                width: calc(100% - 60px);
            }

            .dashboard-content {
                margin-left: 60px;
            }

            .stats-row {
                flex-direction: column;
            }

            .dashboard-card {
                width: 100%;
            }
        }
    </style>
</head>

<body>




    <!-- Dashboard Content -->
    <div class="dashboard-content" id="dashboard-content">
        <h2 class="mb-4" style="color: #333;">Dashboard</h2>
        <p style="color: #6c757d;">Free Bootstrap Admin Dashboard</p>
        <div class="stats-row">
            <div class="dashboard-card">
                <i class="fa fa-users"></i>
                <h3>Total Users</h3>
                <p><?php echo number_format($totalUsers); ?></p>
            </div>
            <div class="dashboard-card" style="background: #e9ecef;">
                <i class="fa fa-check-circle"></i>
                <h3>Admin Users</h3>
                <p><?php echo number_format($adminUsers); ?></p>
            </div>
            <div class="dashboard-card" style="background: #e9ecef;">
                <i class="fa fa-users"></i>
                <h3>Customer Users</h3>
                <p><?php echo number_format($customerUsers); ?></p>
            </div>
            <div class="dashboard-card" style="background: #e9ecef;">
                <i class="fa fa-dollar-sign"></i>
                <h3>Sales</h3>
                <p>$<?php echo number_format($customerUsers * 10, 2); ?></p> <!-- Placeholder -->
            </div>
        </div>

        <div class="chart-container">
            <h3>User Statistics</h3>
            <canvas id="usersChart"></canvas>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="daily-sales">
                    <h3>Daily Users</h3>
                    <p><?php echo number_format($customerUsers / 30, 2); ?></p> <!-- Placeholder average -->
                    <small style="color: #dee2e6;">May 16, 2025</small>
                </div>
            </div>
            <div class="col-md-8">
                <button class="btn-custom">Manage</button>
                <button class="btn-custom">Add User</button>
            </div>
        </div>

        <div class="recent-users mt-4">
            <h3>Recent Users</h3>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($recentUsersQuery)) { ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($user['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        </tr>
                    <?php } ?>
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

        // Chart.js for user registration trend
        const ctx = document.getElementById('usersChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo $chartLabelsJson; ?>,
                datasets: [{
                    label: 'New Users',
                    data: <?php echo $chartValuesJson; ?>,
                    borderColor: '#007bff',
                    fill: true,
                    backgroundColor: 'rgba(0, 123, 255, 0.2)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>