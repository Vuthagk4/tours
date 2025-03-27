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
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@200;300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            height: 100vh;
            background:rgb(34, 49, 64);
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
            transition: all 0.3s ease;
            z-index: 99999999999;
            box-shadow: rgba(0, 0, 0, 0.15) 0px 5px 15px 0px;
        }
         .sidebar ul {
            margin-top: 3rem;
            list-style: none;
            padding: 0 ;
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
            transition: 0.3s;
        }

        .sidebar ul li a i {
            margin-right: 10px;
        }

        .sidebar ul li a:hover {
            background: #49B11E;
        }

        /* Sidebar Toggle */
        .sidebar-toggle {
            position: absolute;
            top: 10px;
            right: -40px;
            background: #49B11E;
            color: white;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            height: 110px;
            transition: 0.3s;
            display: flex;
            position: sticky; /* Fix it on top */
            top: 0;
            width: calc(100% - 250px);
            z-index: 999;
            align-items: center;
            background: rgba(255, 255, 255, 0.8); /* Slight transparency */
            color: black;
            box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;
            backdrop-filter: blur(100px); /* Optional blur effect */
}

        .main-content > form{
            position: absolute;
            right: 10px;
            display: flex;
        }
        .admin-name{
            color: black;
            padding: 12px;
            font-size: 18px;
        }

        /* Responsive */
        @media screen and (max-width: 768px) {
            .sidebar {
                width: 60px;
            }

            .sidebar ul li a {
                justify-content: center;
            }

            .sidebar ul li a span {
                display: none;
            }

            .main-content {
                margin-left: 60px;
            }
        }
        .img{
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <!-- <h1>Tour & Travel Admin</h1> -->
     <img class="img" src="../assets/images/logo.jpg" alt="">
     <div class="hr"></div>
    <ul>
        <li><a href="index.php"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>
        <li><a href="../admin/add_destination.php"><i class="fa fa-map"></i> <span>Add Destination</span></a></li>
        <li><a href="../admin/addTour.php"><i class="fa fa-plane"></i> <span>Add Tour</span></a></li>
        <li><a href="../admin/manage_bookings.php"><i class="fa fa-book"></i> <span>View Booking</span></a></li>
        <li><a href="#"><i class="fa fa-credit-card"></i> <span>Check Payment</span></a></li>
    </ul>

  
</div>
<div class="main-content">
      <!-- User Session -->
        <?php 
    if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
        echo '
        <form action="../pages/logout.php" method="POST">
            <span class="admin-name">' . htmlspecialchars($_SESSION['name']) . ' (' . htmlspecialchars($_SESSION['role']) . ')</span><br><br>
            <button type="submit" class="btn btn-danger">Logout <i class="fa-solid fa-arrow-up-right-from-square"></i></button>
        </form>';
    } else {
        echo '<a href="login.php" style="color: white;">Login</a>';
    }

        ?>
    </div>
</div>

</body>
</html>
