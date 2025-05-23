<?php
session_start();
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- link styles css -->
    <link rel="stylesheet" href="../assets/css/style1.css" />
    <!-- link font-awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- link google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@200;300;400;500;600&display=swap"
        rel="stylesheet">
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
    <style>
        .sidebar {
            width: 250px;
            height: 100vh;
            background: rgba(0, 0, 0, 0.91);
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            padding-bottom: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
            transition: width 0.3s ease;
            z-index: 999;
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
            font-size: 18px;
            transition: padding 0.3s ease, background 0.3s ease;
        }

        .sidebar ul li a i {
            margin-right: 10px;
            transition: margin-right 0.3s ease;
        }

        .sidebar ul li a:hover {
            background: rgba(66, 70, 64, 0.5);
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
            height: 110px;
            transition: margin-left 0.3s ease, width 0.3s ease;
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            width: calc(100% - 250px);
            z-index: 999;
            background: rgba(255, 255, 255, 0.8);
            color: black;
            box-shadow: rgba(0, 0, 0, 0.16) 0px 3px 8px;
            backdrop-filter: blur(100px);
        }

        .main-content.collapsed {
            margin-left: 60px;
            width: calc(100% - 60px);
        }

        .sidebar-toggle {

            color: black;
            font-size: larger;
            padding: 15px;
            cursor: pointer;
            border-radius: 5px;
            transition: transform 0.3s ease, background 0.3s ease;
            margin-left: 10px;
        }

        .sidebar-toggle:hover {
            transform: scale(1.1);
        }

        .admin-info {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 10px;
            position: absolute;
            right: 20px;
            top: 35px;
            background: white;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            color: black;
            font-weight: 500;
        }

        .admin-info form {
            margin: 0;
        }

        .admin-info button {
            background-color: #dc3545;
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
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

        /* Responsive */
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
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Section 1: Logo and Company Name -->
        <div class="section-1">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-3 d-flex justify-content-center">
                    <img class="img" src="../assets/images/logo.jpg" alt="Khmer Tour Logo">
                </div>
                <div class="col-9 d-flex align-items-center">
                    <span class="company-name">Khmer Tour</span>
                </div>
            </div>
        </div>
        <hr>
        <!-- Section 2: Menu -->
        <ul>
            <li><a href="index.php"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="../admin/add_destination.php"><i class="fa fa-map"></i> <span>Add Destination</span></a></li>
            <li><a href="../admin/addTour.php"><i class="fa fa-plane"></i> <span>Add Tour</span></a></li>
            <li><a href="../admin/manage_bookings.php"><i class="fa fa-book"></i> <span>View Booking</span></a></li>
            <li><a href="../admin/manage_payments.php"><i class="fa fa-credit-card"></i> <span>Check Payment</span></a>
            </li>
        </ul>
    </div>

    <!-- Main Content (Header) -->
    <div class="main-content" id="main-content">
        <div class="sidebar-toggle" id="sidebar-toggle">
            <i class="fa fa-bars"></i>
        </div>
        <!-- User Session -->
        <?php
        if (isset($_SESSION['admin_id'])) {
            $adminId = $_SESSION['admin_id'];
            $query = mysqli_query($conn, "SELECT * FROM admins WHERE admin_id = $adminId");
            $admin = mysqli_fetch_assoc($query);

            if ($admin) {
                echo '
            <div class="admin-info d-flex">
                <span>' . htmlspecialchars($admin['name']) . '</span>
                <form action="admin_logout.php" method="POST">
                    <button type="submit" class="btn btn-danger">Logout <i class="fa-solid fa-right-from-bracket"></i></button>
                </form>
            </div>';
            }
        } else {
            echo '<a href="admin_login.php" style="color: black;">Login</a>';
        }
        ?>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const toggleButton = document.getElementById('sidebar-toggle');


        toggleButton.addEventListener('click', () => {
            requestAnimationFrame(() => {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('collapsed');
            });
        });
    </script>
</body>

</html>