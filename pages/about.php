<?php

include '../includes/header.php'; // Include Header
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Page</title>
  <!-- link styles css -->
  <link rel="stylesheet" href="../assets/css/about.css">
  <!-- link google font awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <!-- link google font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Freehand&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">
  <!-- link icon browser -->
  <link rel="shortcut icon" href="../images/R.png" type="image/x-icon">
</head>

<body>
  <div class="watcher-scroll"></div>
  <!-- section -> start class hero -->
  <section class="hero">
    <div class="hero-image">
      <img src="../images/hero-about.jpeg" alt="about_img" />
    </div>
    <div class="hero-content">
      <h1>Welcome to Pag About</h1>
      <p>Please drag down To Views Pag About</p>
      <!-- <a href="#" class="hero-button">Book Now</a> -->
    </div>
  </section>
  <!-- section -> end class hero -->
  <!-- start sections about us  -->
  <div class="about-use">
    <div class="sub-about-use">
      <div class="sub-about-use-img">
        <img src="../images/hero-about1.jpg" alt="">
      </div>
      <div class="sub-about-use-txt">
        <div class="subss-txt">
          <h2>About Use</h2>
          <h2>World Best Travle Agency</h2>
          <p>
          Discover World Best Travel Agency! With 10+ years of expertise, we craft personalized adventures to 200+ destinations, offering seamless bookings, 30% discounts, and unforgettable experiences. Travel your way!
          </p>
        </div>
      </div>
    </div>
    <button><a href="">Explore More</a></button>
  </div>
  <!-- end sections about us  -->
  <!-- start class stat  -->
  <section class="stats-section">
    <div class="overlay">
        <h2><span class="highlight">Statistics</span></h2>
        <h1>We have over 10 years Experience</h1>
        <div class="stats-container">
            <div class="stat-box">
                <div class="icon"><i class="fas fa-route"></i></div>
                <h3 data-target="200">0</h3>
                <p>Total Destinations</p>
            </div>
            <div class="stat-box">
                <div class="icon"><i class="fas fa-smile"></i></div>
                <h3 data-target="100">0</h3>
                <p>Happy People</p>
            </div>
            <div class="stat-box">
                <div class="icon"><i class="fas fa-medal"></i></div>
                <h3 data-target="30">0</h3>
                <p>Awards Won</p>
            </div>
            <div class="stat-box">
                <div class="icon"><i class="fas fa-umbrella-beach"></i></div>
                <h3 data-target="130">0</h3>
                <p>Stunning Places</p>
            </div>
        </div>
    </div>
</section>
  <!-- end class stat  -->
  <!-- start class about -->
  <h2 class="hight">About Team Members</h2>
  <p id="p">
    Our company is dedicated to providing the best travel experiences to <br>
    our customers. We specialize in creating custom itineraries that cater
    to each individual's interests and preferences.
  </p>
  <section id="about" class="about">
<!-- Data map from JS -->
  </section>
  <!-- end class about -->
   
  <!-- start class feature -->
  <section class="feature">
    <div class="feature-img">
      <img src="../images/feature.jpg" alt="">
    </div>
    <div class="feature-txt">
      <div class="sub-feature-txt">
        <h2>Our Feature</h2>
        <h1>Why Choose Tours!</h1>
        <p>Discover unforgettable journeys designed with care, comfort, <br> and local insight. Our tours are built on
          trust, experience, <br> and a deep love for exploration.</p>
      </div>
      <div class="feature-box">
        <div class="icon"><i class="fas fa-certificate"></i></div>
        <div class="text">
          <h3>Professional and Certified</h3>
          <p>Excepteur sint occaecat cupidatat non proident, sunt in culpa <br> qui officia deserunt mollit.</p>
        </div>
      </div>

      <div class="feature-box">
        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
        <div class="text">
          <h3>Get Instant Tour Bookings</h3>
          <p>Book your dream tour in seconds with our seamless and secure online <br> booking system — no waiting, no
            hassle!</p>
        </div>
      </div>
    </div>
    </div>
  </section>
  <!-- end class feature -->



</body>
<!--link js -->
<script src="../assets/js/about.js"></script>

</html>
<?php
include "../admin/footer.php";
?>