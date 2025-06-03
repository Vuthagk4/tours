<?php
include "../includes/config.php"; // Assumes database connection
include "../includes/header.php";

$guides = $conn->query("SELECT * FROM guides WHERE is_deleted = 0");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Guides - [Company Name]</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
            list-style-type: none;
        }

        .guides-section {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .guides-section h2 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 40px;
            text-align: center;
            animation: fadeIn 1s ease-in;
        }

        .guides-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .guide-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            animation: fadeInUp 0.8s ease-in-out;
            animation-fill-mode: both;
        }

        .guide-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .guide-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .guide-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .guide-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        .guide-card:nth-child(5) {
            animation-delay: 0.5s;
        }

        .guide-card:nth-child(6) {
            animation-delay: 0.6s;
        }

        .guide-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .guide-card img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
            border: 3px solid #ff6f61;
        }

        .guide-card h3 {
            font-size: 1.5rem;
            color: #ff6f61;
            margin-bottom: 10px;
        }

        .guide-card p {
            font-size: 1rem;
            color: #555;
            margin-bottom: 10px;
        }

        .guide-card .languages {
            font-size: 0.9rem;
            color: #666;
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 15px;
            animation: slideIn 0.5s ease-in-out;
        }

        .modal-header {
            background: #ff6f61;
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .modal-title {
            font-size: 1.8rem;
        }

        .modal-body img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .modal-body p {
            font-size: 1.1rem;
            color: #333;
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

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .guides-grid {
                grid-template-columns: 1fr;
            }

            .guide-card img {
                width: 120px;
                height: 120px;
            }

            .modal-body img {
                width: 150px;
                height: 150px;
            }
        }
    </style>
</head>

<body>
    <div class="guides-section">
        <h2>Meet Our Expert Guides</h2>
        <div class="guides-grid">
            <?php if ($guides->num_rows > 0): ?>
                <?php while ($guide = $guides->fetch_assoc()): ?>
                    <div class="guide-card" data-bs-toggle="modal"
                        data-bs-target="#guideModal<?php echo htmlspecialchars($guide['guide_id']); ?>">
                        <img src="../<?php echo htmlspecialchars($guide['image']); ?>"
                            alt="<?php echo htmlspecialchars($guide['name']); ?>">
                        <h3><?php echo htmlspecialchars($guide['name']); ?></h3>
                        <p><strong>Position:</strong> <?php echo htmlspecialchars($guide['position']); ?></p>
                        <p><strong>Skills:</strong> <?php echo htmlspecialchars($guide['skill'] ?? 'N/A'); ?></p>
                        <p class="languages"><strong>Languages:</strong> <?php echo htmlspecialchars($guide['language']); ?></p>
                    </div>

                    <!-- Modal for Guide Details -->
                    <div class="modal fade" id="guideModal<?php echo htmlspecialchars($guide['guide_id']); ?>" tabindex="-1"
                        aria-labelledby="guideModalLabel<?php echo htmlspecialchars($guide['guide_id']); ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"
                                        id="guideModalLabel<?php echo htmlspecialchars($guide['guide_id']); ?>">
                                        <?php echo htmlspecialchars($guide['name']); ?>
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img src="../<?php echo htmlspecialchars($guide['image']); ?>"
                                        alt="<?php echo htmlspecialchars($guide['name']); ?>">
                                    <p><strong>Position:</strong> <?php echo htmlspecialchars($guide['position']); ?></p>
                                    <p><strong>Skills:</strong> <?php echo htmlspecialchars($guide['skill'] ?? 'N/A'); ?></p>
                                    <p><strong>Languages:</strong> <?php echo htmlspecialchars($guide['language']); ?></p>
                                    <p><strong>Bio:</strong>
                                        <?php echo htmlspecialchars($guide['skill'] ? "Experienced in {$guide['skill']}, {$guide['name']} is dedicated to making your tour unforgettable." : 'N/A'); ?>
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">No guides available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php include "./user_footer.php"; ?>
</body>

</html>