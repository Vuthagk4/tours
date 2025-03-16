<?php
$title = "About Us - Tour Travel";
include '../includes/header.php'; // Include Header
?>

<div class="hero">
    <h1>Discover the World with Tour Travel</h1>
    <p>Experience the best destinations with our expert-guided tours.</p>
</div>

<section class="about">
    <div class="container">
        <h2>Who We Are</h2>
        <p>
            At <b>Tour Travel</b>, we specialize in offering world-class travel experiences. Our mission is to make traveling effortless and memorable, ensuring that every trip is filled with joy and adventure.
        </p>
    </div>
</section>

<section class="mission">
    <div class="container">
        <h2>Our Mission & Vision</h2>
        <p>🌍 <b>Mission:</b> To provide unforgettable travel experiences with top-notch service.</p>
        <p>🚀 <b>Vision:</b> To be the leading travel company offering unique and authentic tours worldwide.</p>
    </div>
</section>

<section class="team">
    <div class="container">
        <h2>Meet Our Team</h2>
        <div class="team-grid">
            <?php
            $team = [
                ["John Doe", "Founder & CEO", "https://source.unsplash.com/100x100/?man"],
                ["Jane Smith", "Travel Consultant", "https://source.unsplash.com/100x100/?woman"],
                ["Michael Brown", "Tour Guide", "https://source.unsplash.com/100x100/?person"]
            ];
            foreach ($team as $member) {
                echo "
                <div class='team-member'>
                    <img src='{$member[2]}' alt='{$member[0]}'>
                    <h3>{$member[0]}</h3>
                    <p>{$member[1]}</p>
                </div>";
            }
            ?>
        </div>
    </div>
</section>

<?php include 'footer.php'; // Include Footer ?>
