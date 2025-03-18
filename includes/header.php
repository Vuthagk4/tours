<?php
session_start(); // Start session to check login status
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- link styles css -->
    <link rel="stylesheet" href="../assets/css/style1.css" />
    <!-- link google font awesome -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
     <!-- link google font -->
     <link rel="preconnect" href="https://fonts.googleapis.com">
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
     <link href="https://fonts.googleapis.com/css2?family=Freehand&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
<div class="watcher-scroll"></div>
    <!-- start class bar bar header -->
    <div class="bar-header">
        <div class="bar-head">
            <div class="right-bottom">
                <a href=""><i class="fa-solid fa-house"></i></a>
                <span><a href="#" style="text-decoration: none;"></a></span>
            </div>
                <div class="left-bottom">
                <!-- <p> Number:123-456789</p> -->
                <a href="register.php"><li>Reigister</li></a>
                <p>/</p>
                <a href="login.php"><li>Login</li></a>
                </div>
        </div>
    </div>
    <!-- end  class bar bar header -->
    <!-- start class nav-bar -->
    <div class="bar" style="display: flex;justify-content:space-between;align-items:center;font-size:xx-large;">
        <ul>
            <li>
                <h1><span style="color: #49B11E;">Tour</span><span style="color: #ff7f50;">& </span>Travel Management</h1>
            </li>
        </ul>
        <ul>
            <li>
                
                <?php 
                        if (isset($_SESSION['admin_name'])) {
                            echo "<span>Admin/" . htmlspecialchars($_SESSION['admin_name']) . "</span>";
                            echo '
                            <form action="logout.php" method="POST" style="display:inline;">
                                <button type="submit" style="padding:7px; background-color:red;color:white;border:none;" >Logout</button>
                            </form>';
                        } else {
                            echo '<a href="login.php">Login</a>';
                        }

                ?>
            </li>
        </ul>
    </div>
<!-- end  class nav-bar -->
<!-- start class Menu -->
 <div class="nav-bar">
    <div class="sub-nav">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="packages.php">Tour Package</a></li>
            <li><a href="">Booking</a></li>
            <li><a href="">Term of Use</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="">Enquiry</a></li>
        </ul>
    </div>
 </div>
</body>
</html>