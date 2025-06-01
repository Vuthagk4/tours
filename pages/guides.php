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
    <title>Our Guides</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }

        .guides-section {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .guides-section h2 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        .guides-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .guide-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
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

        @media (max-width: 768px) {
            .guides-grid {
                grid-template-columns: 1fr;
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
                    <div class="guide-card">
                        <h3><?php echo htmlspecialchars($guide['name']); ?></h3>
                        <p><strong>Position:</strong> <?php echo htmlspecialchars($guide['position']); ?></p>
                        <p><strong>Skills:</strong> <?php echo htmlspecialchars($guide['skill'] ?? 'N/A'); ?></p>
                        <p class="languages"><strong>Languages:</strong> <?php echo htmlspecialchars($guide['language']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No guides available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php include "./user_footer.php"; ?>
</body>

</html>