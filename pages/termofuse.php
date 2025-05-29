<?php
include "../includes/config.php";
include "../includes/header.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Use - Travel Cambodia</title>
    <link rel="stylesheet" href="../assets/css/style1.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        h1,
        h2,
        h3 {
            font-family: 'Montserrat', sans-serif;
        }

        .terms-container {
            padding: 40px 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .terms-container h1 {
            font-size: 2.5rem;
            color: #007bff;
            margin-bottom: 20px;
            text-align: center;
        }

        .terms-container h2 {
            font-size: 1.75rem;
            color: #333;
            margin-top: 40px;
            margin-bottom: 15px;
        }

        .terms-container p,
        .terms-container li {
            font-size: 1rem;
            color: #666;
            line-height: 1.8;
        }

        .terms-container a {
            color: #007bff;
            text-decoration: none;
        }

        .terms-container a:hover {
            text-decoration: underline;
            color: #0056b3;
        }

        /* Table of Contents */
        #toc {
            position: sticky;
            top: 20px;
        }

        .nav-link.active {
            color: #007bff;
            font-weight: bold;
        }

        /* Back Button */
        .back-btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin: 20px 0;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .terms-container {
            animation: fadeIn 0.5s ease;
        }

        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .terms-container {
                padding: 20px;
            }

            .terms-container h1 {
                font-size: 2rem;
            }

            .terms-container h2 {
                font-size: 1.5rem;
            }

            .terms-container p,
            .terms-container li {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <!-- Table of Contents -->
            <div class="col-md-3">
                <button class="btn btn-secondary d-md-none mb-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#toc">
                    Table of Contents
                </button>
                <div id="toc" class="collapse d-md-block sticky-top">
                    <h5>Table of Contents</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="#section1">1. Acceptance of Terms</a></li>
                        <li class="nav-item"><a class="nav-link" href="#section2">2. Use of Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="#section3">3. Booking and Payment</a></li>
                        <li class="nav-item"><a class="nav-link" href="#section4">4. Cancellations and Refunds</a></li>
                        <li class="nav-item"><a class="nav-link" href="#section5">5. User Conduct</a></li>
                        <li class="nav-item"><a class="nav-link" href="#section6">6. Intellectual Property</a></li>
                        <li class="nav-item"><a class="nav-link" href="#section7">7. Limitation of Liability</a></li>
                        <li class="nav-item"><a class="nav-link" href="#section8">8. Third-Party Links</a></li>
                        <li class="nav-item"><a class="nav-link" href="#section9">9. Changes to Terms</a></li>
                        <li class="nav-item"><a class="nav-link" href="#section10">10. User Accounts</a></li>
                        <li class="nav-item"><a class="nav-link" href="#section11">11. Dispute Resolution</a></li>
                        <li class="nav-item"><a class="nav-link" href="#section12">12. Governing Law</a></li>
                        <li class="nav-item"><a class="nav-link" href="#section13">13. Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <!-- Terms Content -->
            <div class="col-md-9 terms-container" data-bs-spy="scroll" data-bs-target="#toc" data-bs-offset="0"
                tabindex="0">
                <h1>Terms of Use</h1>
                <p><strong>Last Updated:</strong> May 21, 2025</p>
                <p>Welcome to Travel Cambodia ("we," "us," or "our"). By accessing or using our website and services,
                    you agree to be bound by these Terms of Use ("Terms"). If you do not agree with these Terms, please
                    do not use our services.</p>

                <h2 id="section1">1. Acceptance of Terms</h2>
                <p>By accessing our website, booking a tour, or using any of our services, you acknowledge that you have
                    read, understood, and agree to be bound by these Terms, as well as our <a
                        href="privacy_policy.php">Privacy Policy</a>. These Terms apply to all users, including
                    visitors, registered users, and customers.</p>

                <h2 id="section2">2. Use of Services</h2>
                <p>You agree to use our services only for lawful purposes and in accordance with these Terms. You are
                    responsible for ensuring that all information you provide during booking or registration is accurate
                    and up-to-date.</p>
                <ul>
                    <li>You must be at least 18 years old to book a tour or use our services.</li>
                    <li>You may not use our services to engage in any illegal or unauthorized activities.</li>
                    <li>You agree not to misuse our website, including attempting to access unauthorized areas or
                        interfere with its functionality.</li>
                </ul>

                <h2 id="section3">3. Booking and Payment</h2>
                <p>When you book a tour with us, you agree to the following:</p>
                <ul>
                    <li>All bookings are subject to availability and confirmation.</li>
                    <li>Prices are as listed at the time of booking and may change without notice.</li>
                    <li>Payments must be made in full at the time of booking unless otherwise stated.</li>
                    <li>Cancellations or modifications are subject to our cancellation policy (see Section 4).</li>
                </ul>

                <h2 id="section4">4. Cancellations and Refunds</h2>
                <p>We offer flexible cancellations as outlined below:</p>
                <ul>
                    <li>You may cancel or modify your booking up to 24 hours before the tour departure for a full
                        refund.</li>
                    <li>Cancellations within 24 hours of departure are non-refundable.</li>
                    <li>No-shows or failure to meet the tour departure requirements will not be refunded.</li>
                    <li>We reserve the right to cancel or modify tours due to unforeseen circumstances (e.g., weather,
                        safety concerns). In such cases, you will be offered an alternative or a full refund.</li>
                </ul>

                <h2 id="section5">5. User Conduct</h2>
                <p>You agree to:</p>
                <ul>
                    <li>Follow all instructions provided by our tour guides and staff during tours.</li>
                    <li>Respect local customs, laws, and regulations at tour destinations.</li>
                    <li>Not engage in behavior that is disruptive, offensive, or harmful to others.</li>
                </ul>

                <h2 id="section6">6. Intellectual Property</h2>
                <p>All content on our website, including text, images, logos, and designs, is owned by Travel Cambodia
                    or our licensors and is protected by copyright and trademark laws. You may not reproduce,
                    distribute, or use our content without prior written permission.</p>

                <h2 id="section7">7. Limitation of Liability</h2>
                <p>We strive to provide safe and enjoyable tours, but we are not liable for:</p>
                <ul>
                    <li>Personal injury, loss, or damage during tours, except where caused by our negligence.</li>
                    <li>Delays, cancellations, or changes due to factors beyond our control (e.g., natural disasters,
                        political unrest).</li>
                    <li>Third-party services or activities not directly provided by us.</li>
                </ul>
                <p>Our total liability to you for any claim arising from your use of our services is limited to the
                    amount you paid for the tour.</p>

                <h2 id="section8">8. Third-Party Links</h2>
                <p>Our website may contain links to third-party websites (e.g., Google Maps). We are not responsible for
                    the content, accuracy, or practices of these websites. Accessing third-party links is at your own
                    risk.</p>

                <h2 id="section9">9. Changes to Terms</h2>
                <p>We may update these Terms from time to time. The updated Terms will be posted on this page with a
                    revised "Last Updated" date. Your continued use of our services after changes constitutes acceptance
                    of the updated Terms.</p>

                <h2 id="section10">10. User Accounts</h2>
                <p>To access certain features of our services, you may be required to create an account. You agree to
                    provide accurate and complete information when creating your account and to keep your login
                    credentials confidential. You are responsible for all activities that occur under your account.</p>
                <ul>
                    <li>You must notify us immediately of any unauthorized use of your account.</li>
                    <li>We reserve the right to suspend or terminate your account if you violate these Terms.</li>
                </ul>

                <h2 id="section11">11. Dispute Resolution</h2>
                <p>Any disputes arising out of or relating to these Terms or our services shall be resolved through
                    binding arbitration in accordance with the rules of [Arbitration Association]. The arbitration shall
                    be conducted in [City, State], and judgment on the arbitration award may be entered into any court
                    having jurisdiction thereof.</p>

                <h2 id="section12">12. Governing Law</h2>
                <p>These Terms shall be governed by and construed in accordance with the laws of [State/Country],
                    without regard to its conflict of law provisions.</p>

                <h2 id="section13">13. Contact Us</h2>
                <p>If you have any questions about these Terms, please contact us:</p>
                <ul>
                    <li><strong>Email:</strong> <a href="mailto:support@tours.com">ounthany@gmail.com</a></li>
                    <li><strong>Phone:</strong> <a href="tel:+1234567890">015 769 953</a></li>
                    <li><strong>Telegram:</strong> <a href="https://t.me/thany_oun" target="_blank">OUN THANY</a></li>
                </ul>

                <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </div>

    <?php include "./user_footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>