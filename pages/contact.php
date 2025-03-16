<?php
include '../includes/header.php'; // Include Header
include '../includes/config.php'; // Include Database Config

// Initialize variables
$name = $email = $message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data to prevent SQL injection
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['textarea']);

    // Insert message into the database
    $sql = "INSERT INTO contact_messages (name, email, message) VALUES ('$name', '$email', '$message')";

    if ($conn->query($sql) === TRUE) {
        // Show success message (via JavaScript Alert)
        echo "<script>alert('Your message has been sent successfully!');</script>";
    } else {
        // Show error message (via JavaScript Alert)
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }

    // Close connection
    $conn->close();
}
?>

<!-- Start contact form -->
<section id="contact" class="contact">
    <h3>Contact Us</h3>
    <div class="contact-info">
        <p>
            If you have any questions or would like to book a trip, please <br> fill
            out the form below or contact us using the information provided.
        </p>
        <ul>
            <li>
                <i class="fas fa-map-marker-alt"></i>123 Main St, Anytown USA
            </li>
            <li>
                <i class="fas fa-envelope"></i><a href="mailto:info@travelcompany.com">info@travelcompany.com</a>
            </li>
            <li>
                <i class="fas fa-phone-alt"></i><a href="tel:555-123-4567">555-123-4567</a>
            </li>
        </ul>
    </div>
    <form action="#" method="POST" class="form">
        <div class="form-group">
            <input type="text" name="name" id="name" placeholder="Enter Your Name" required />
        </div>
        <div class="form-group">
            <input type="email" name="email" id="email" placeholder="Enter Your Email" required />
        </div>
        <div class="form-group">
            <textarea name="textarea" id="textarea" cols="30" rows="10" placeholder="Message" required></textarea>
        </div>
        <button type="submit">Send Message</button>
    </form>
</section>
