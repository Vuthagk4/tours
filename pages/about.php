<?php
include '../includes/header.php'; // Include Header
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Page - [Company Name]</title>
  <!-- Link styles CSS -->
  <link rel="stylesheet" href="../assets/css/about.css" />
  <!-- Link Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <!-- Link Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Freehand&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet" />
  <!-- Link browser icon -->
  <link rel="shortcut icon" href="../images/R.png" type="image/x-icon" />
</head>

<body>
  <div class="watcher-scroll"></div>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-image">
      <img src="../images/hero-about.jpeg" alt="Scenic travel destination" />
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1>Welcome to [Company Name]</h1>
      <p>Explore our story, meet our team, and discover why we're the best choice for your next adventure.</p>
      <a href="#about-us" class="hero-button">Learn More</a>
    </div>
  </section>

  <!-- About Us Section -->
  <div class="about-use" id="about-us">
    <div class="sub-about-use">
      <div class="sub-about-use-img">
        <img src="../images/hero-about1.jpg" alt="Team at work" />
      </div>
      <div class="sub-about-use-txt">
        <div class="subss-txt">
          <h2>About Us</h2>
          <h3>World's Best Travel Agency</h3>
          <p>At [Company Name], we are passionate about creating unforgettable travel experiences. Founded in [Year],
            our agency has grown from a small team of travel enthusiasts to a globally recognized brand, serving
            thousands of happy customers each year. Our mission is to make travel accessible, enjoyable, and tailored to
            your unique preferences.</p>
          <p>With over a decade of experience, we’ve curated a diverse portfolio of destinations, from the serene
            beaches of Bali to the bustling streets of Tokyo. Our team of expert travel consultants works tirelessly to
            ensure every aspect of your journey is seamless, from booking to your safe return home.</p>
          <p>We pride ourselves on our commitment to customer satisfaction, offering 24/7 support, flexible booking
            options, and exclusive discounts. Whether you're planning a solo adventure, a romantic getaway, or a family
            vacation, we’re here to make your dream trip a reality.</p>
          <p>Join us and discover why we’re the world’s best travel agency!</p>
        </div>
      </div>
    </div>
    <button><a href="contact.php">Contact Us</a></button>
  </div>

  <!-- Statistics Section -->
  <section class="stats-section">
    <div class="overlay">
      <h2><span class="highlight">Statistics</span></h2>
      <h1>We Have Over 10 Years of Experience</h1>
      <div class="stats-container">
        <div class="stat-box">
          <div class="icon"><i class="fas fa-route"></i></div>
          <h3 data-target="200">0</h3>
          <p>Total Destinations</p>
        </div>
        <div class="stat-box">
          <div class="icon"><i class="fas fa-smile"></i></div>
          <h3 data-target="10000">0</h3>
          <p>Happy Customers</p>
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

  <!-- Team Members Section -->
  <section id="about" class="about">
    <h2 class="hight">Meet Our Team</h2>
    <p id="p">Our dedicated team of travel experts is here to make your dream vacation a reality.</p>
    <div class="team-container">
      <div class="team-member">
        <img src="../images/team1.jpg" alt="John Doe" />
        <h3>John Doe</h3>
        <p>Founder & CEO</p>
        <p>With over 15 years of experience in the travel industry, John is passionate about creating unique and
          memorable travel experiences for our clients.</p>
      </div>
      <div class="team-member">
        <img src="../images/team2.jpg" alt="Jane Smith" />
        <h3>Jane Smith</h3>
        <p>Head of Operations</p>
        <p>Jane ensures that every trip runs smoothly, with a keen eye for detail and customer satisfaction.</p>
      </div>
      <div class="team-member">
        <img src="../images/teamFree for non-commercial use under the CC BY-NC-SA 4.0 license." alt="Mike Johnson" />
        <h3>Mike Johnson</h3>
        <p>Lead Travel Consultant</p>
        <p>Mike's extensive knowledge of global destinations helps him craft personalized itineraries that exceed
          expectations.</p>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="feature">
    <div class="feature-img">
      <img src="../images/feature.jpg" alt="Travel feature" />
    </div>
    <div class="feature-txt">
      <div class="sub-feature-txt">
        <h2>Our Features</h2>
        <h1>Why Choose Us!</h1>
        <p>Discover unforgettable journeys designed with care, comfort, and local insight. Our tours are built on trust,
          experience, and a deep love for exploration.</p>
      </div>
      <div class="feature-box">
        <div class="icon"><i class="fas fa-certificate"></i></div>
        <div class="text">
          <h3>Professional and Certified</h3>
          <p>Our team is certified and experienced in providing top-notch travel services.</p>
        </div>
      </div>
      <div class="feature-box">
        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
        <div class="text">
          <h3>Get Instant Tour Bookings</h3>
          <p>Book your dream tour in seconds with our seamless and secure online booking system.</p>
        </div>
      </div>
      <div class="feature-box">
        <div class="icon"><i class="fas fa-headset"></i></div>
        <div class="text">
          <h3>24/7 Customer Support</h3>
          <p>Our dedicated support team is available around the clock to assist you.</p>
        </div>
      </div>
      <div class="feature-box">
        <div class="icon"><i class="fas fa-tags"></i></div>
        <div class="text">
          <h3>Exclusive Discounts</h3>
          <p>Enjoy special discounts and offers on selected tours and packages.</p>
        </div>
      </div>
    </div>
  </section>

</body>
<!-- Link JS -->
<script src="../assets/js/about.js"></script>

</html>