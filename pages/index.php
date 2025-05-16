<?php
include "../includes/config.php";
include "../includes/header.php";

// Check if a search term is provided via GET
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$sortOption = isset($_GET['sort']) ? htmlspecialchars($_GET['sort']) : 'default';

// Fetch tours with primary image
if (!empty($searchTerm)) {
    $searchTerm = '%' . $searchTerm . '%';
    $stmt = $conn->prepare("SELECT t.*, d.name AS destination, d.location 
                            FROM tours t 
                            JOIN destinations d ON t.destination_id = d.destination_id 
                            WHERE t.title LIKE ? OR d.name LIKE ? AND t.isDeleted = 0");
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $tours = $stmt->get_result();
} else {
    $tours = $conn->query("SELECT t.*, d.name AS destination, d.location 
                           FROM tours t 
                           JOIN destinations d ON t.destination_id = d.destination_id 
                           WHERE t.isDeleted = 0");
}

// Fetch all images for each tour (primary + additional)
$tour_images = [];
$result = $conn->query("SELECT tour_id, image AS primary_image FROM tours WHERE isDeleted = 0");
while ($row = $result->fetch_assoc()) {
    $tour_images[$row['tour_id']] = [['image_path' => $row['primary_image'], 'description' => 'Primary Image']];
}
$result = $conn->query("SELECT tour_id, image_path, description FROM tour_images");
while ($row = $result->fetch_assoc()) {
    $tour_images[$row['tour_id']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Tours - Discover Your Next Adventure</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        /* Hero Banner (Replaces Header Section) */
        .hero-section {
            position: relative;
            height: 600px;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .hero-section h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
        }

        .hero-section p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        /* Search Bar */
        .search-bar {
            background: white;
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            padding: 20px;
            max-width: 900px;
            margin: -60px auto 0;
            position: relative;
            z-index: 1;
        }

        .search-bar .nav-tabs {
            border-bottom: none;
            justify-content: center;
            margin-bottom: 20px;
        }

        .search-bar .nav-link {
            color: #555;
            font-weight: 600;
            border: none;
            padding: 10px 25px;
            transition: all 0.3s ease;
        }

        .search-bar .nav-link.active {
            background: #ff6f61;
            color: white;
            border-radius: 20px;
        }

        .search-bar .form-control {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .search-bar .form-control:focus {
            border-color: #ff6f61;
            box-shadow: 0 0 8px rgba(255, 111, 97, 0.3);
        }

        .search-bar .btn-search {
            background: #ff6f61;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .search-bar .btn-search:hover {
            background: #e65b50;
        }

        /* Promo Section */
        .promo-section {
            max-width: 1200px;
            margin: 50px auto;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .promo-card {
            background: linear-gradient(135deg, #ff6f61 0%, #ff9a8b 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            flex: 1;
            min-width: 300px;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .promo-card:hover {
            transform: translateY(-10px);
        }

        .promo-card h3 {
            font-size: 1.6rem;
            margin-bottom: 10px;
        }

        .promo-card p {
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .promo-card .btn-claim {
            background: white;
            color: #ff6f61;
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .promo-card .btn-claim:hover {
            background: #f0f0f0;
        }

        .promo-card .badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #ff4d4d;
            color: white;
            font-size: 0.9rem;
            padding: 5px 12px;
            border-radius: 15px;
        }

        /* Tour Section */
        .tour-section {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .tour-section h2 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 30px;
            position: relative;
            text-align: center;
        }

        .tour-section h2::after {
            content: '';
            width: 60px;
            height: 4px;
            background: #ff6f61;
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .tour-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        .tour-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .tour-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .tour-image {
            position: relative;
        }

        .tour-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .tour-image .discount-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ff4d4d;
            color: white;
            font-size: 0.9rem;
            padding: 5px 12px;
            border-radius: 15px;
        }

        .tour-info {
            padding: 20px;
        }

        .tour-info h5 {
            font-size: 1.3rem;
            color: #ff6f61;
            margin-bottom: 10px;
        }

        .tour-info .rating {
            color: #ffcc00;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .tour-info .reviews {
            font-size: 0.9rem;
            color: #666;
            margin-left: 5px;
        }

        .tour-info p {
            font-size: 0.95rem;
            color: #555;
            margin-bottom: 15px;
        }

        .tour-book {
            padding: 0 20px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .tour-book .price-info {
            text-align: left;
        }

        .tour-book .original-price {
            font-size: 0.9rem;
            color: #999;
            text-decoration: line-through;
        }

        .tour-book .price {
            font-size: 1.3rem;
            font-weight: 600;
            color: #ff6f61;
        }

        .tour-book .btn-book {
            background: #ff6f61;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .tour-book .btn-book:hover {
            background: #e65b50;
        }

        /* Modal Image Size */
        #imageModal {
            z-index: 9999;
        }

        #imageModal .modal-dialog {
            max-width: 600px;
            max-height: 60vh;
        }

        #imageModal .modal-body {
            height: 100%;
            width: 100%;
        }

        #imageModal .carousel-inner img {
            max-width: 100%;
            max-height: 100%;
            width: 100%;
            height: 100%;
            object-fit: cover;
            margin: auto;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-section {
                height: 400px;
            }

            .hero-section h1 {
                font-size: 2rem;
            }

            .search-bar {
                margin: -30px 15px 0;
                padding: 15px;
            }

            .search-bar .tab-content {
                flex-direction: column;
            }

            .promo-section {
                flex-direction: column;
                margin: 30px 15px;
            }

            .tour-cards {
                grid-template-columns: 1fr;
            }

            .tour-book {
                flex-direction: column;
                gap: 10px;
                align-items: stretch;
            }

            .tour-book .btn-book {
                text-align: center;
            }
        }

        /* Testimonials Section */
        .testimonials-section {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .testimonials-section h2 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        .testimonials-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .testimonial-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
            text-align: center;
        }

        .testimonial-card p {
            font-size: 1rem;
            color: #555;
            margin-bottom: 15px;
        }

        .testimonial-card .customer-name {
            font-weight: 600;
            color: #333;
        }

        /* Featured Destinations Section */
        .featured-destinations {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .featured-destinations h2 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        .destinations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .destination-card {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .destination-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .destination-card h3 {
            position: absolute;
            bottom: 10px;
            left: 10px;
            color: white;
            font-size: 1.2rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .destination-card p {
            position: absolute;
            bottom: 30px;
            left: 10px;
            color: white;
            font-size: 0.9rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        /* Why Choose Us Section */
        .why-choose-us {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .why-choose-us h2 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        .reasons {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        .reason {
            text-align: center;
            width: 250px;
            margin: 10px;
        }

        .reason i {
            font-size: 2.5rem;
            color: #ff6f61;
            margin-bottom: 15px;
        }

        .reason h3 {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 10px;
        }

        .reason p {
            font-size: 0.9rem;
            color: #555;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-section {
                height: 400px;
            }

            .hero-section h1 {
                font-size: 2rem;
            }

            .search-bar {
                margin: -30px 15px 0;
                padding: 15px;
            }

            .search-bar .tab-content {
                flex-direction: column;
            }

            .promo-section {
                flex-direction: column;
                margin: 30px 15px;
            }

            .tour-cards {
                grid-template-columns: 1fr;
            }

            .tour-book {
                flex-direction: column;
                gap: 10px;
                align-items: stretch;
            }

            .tour-book .btn-book {
                text-align: center;
            }

            .testimonials-container {
                flex-direction: column;
                align-items: center;
            }

            .testimonial-card {
                width: 90%;
            }

            .destinations-grid {
                grid-template-columns: 1fr;
            }

            .reasons {
                flex-direction: column;
                align-items: center;
            }

            .reason {
                width: 90%;
            }
        }
    </style>
</head>

<body>
    <!-- Hero Section -->
    <div class="hero-section">
        <div>
            <h1>Discover Your Next Adventure</h1>
            <p>Explore the world with our curated tours and exclusive offers.</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="search-bar">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tours-tab" data-bs-toggle="tab" data-bs-target="#tours"
                    type="button" role="tab" aria-controls="tours" aria-selected="true">
                    <i class="fas fa-map-marker-alt me-2"></i> Tours
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hotels-tab" data-bs-toggle="tab" data-bs-target="#hotels" type="button"
                    role="tab" aria-controls="hotels" aria-selected="false">
                    <i class="fas fa-hotel me-2"></i> Hotels & Homes
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="flights-tab" data-bs-toggle="tab" data-bs-target="#flights" type="button"
                    role="tab" aria-controls="flights" aria-selected="false">
                    <i class="fas fa-plane me-2"></i> Flights
                </button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="tours" role="tabpanel" aria-labelledby="tours-tab">
                <form method="get" action="" class="d-flex align-items-center gap-3">
                    <div class="flex-grow-1">
                        <input type="text" name="search" class="form-control"
                            placeholder="Destination, city, or tour name"
                            value="<?php echo htmlspecialchars($searchTerm); ?>">
                    </div>
                    <div>
                        <input type="date" class="form-control" name="checkin"
                            value="<?php echo date('Y-m-d', strtotime('today')); ?>">
                    </div>
                    <div>
                        <input type="date" class="form-control" name="checkout"
                            value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                    </div>
                    <div>
                        <select class="form-control" name="guests">
                            <option>1 guest</option>
                            <option>2 guests</option>
                            <option>3 guests</option>
                            <option>4+ guests</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-search"><i class="fas fa-search me-2"></i> Search</button>
                </form>
            </div>
            <div class="tab-pane fade" id="hotels" role="tabpanel" aria-labelledby="hotels-tab">
                <!-- Add hotel search form here if needed -->
            </div>
            <div class="tab-pane fade" id="flights" role="tabpanel" aria-labelledby="flights-tab">
                <!-- Add flight search form here if needed -->
            </div>
        </div>
    </div>

    <!-- Promo Section -->
    <div class="promo-section">
        <div class="promo-card">
            <span class="badge">Welcome Pack</span>
            <h3>Save Big on Your First Tour</h3>
            <p>Get 5% OFF your first booking with us!</p>
            <button class="btn-claim">Claim Now</button>
        </div>
        <div class="promo-card">
            <span class="badge">Limited Offer</span>
            <h3>Exclusive Group Discounts</h3>
            <p>Book for 4+ guests and save more!</p>
            <button class="btn-claim">Claim Now</button>
        </div>
    </div>

    <!-- Tour Section -->
    <div class="tour-section">
        <h2>Explore Our Top Tours</h2>
        <div class="tour-cards">
            <?php
            $index = 0;
            while ($tour = $tours->fetch_assoc()): ?>
                <div class="tour-card">
                    <div class="tour-image">
                        <img src="../Uploads/<?= htmlspecialchars($tour['image'] ?? 'placeholder.jpg') ?>"
                            alt="<?= htmlspecialchars($tour['title']) ?>" data-bs-toggle="modal"
                            data-bs-target="#imageModal" data-tour-id="<?= $tour['tour_id'] ?>">
                        <span class="discount-badge">10% OFF</span>
                    </div>
                    <div class="tour-info">
                        <h5><?= htmlspecialchars($tour['title']) ?></h5>
                        <div class="rating">
                            <?php
                            $rating = 4.5; // Example rating, replace with dynamic logic if available
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= floor($rating)) {
                                    echo '<i class="fas fa-star"></i>';
                                } elseif ($i - 0.5 <= $rating) {
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                            <span class="reviews">
                                <?php
                                $tour_id = $tour['tour_id'];
                                $stmt = $conn->prepare("SELECT SUM(people) AS total_people FROM bookings WHERE tour_id = ?");
                                $stmt->bind_param("i", $tour_id);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $totalPeople = $result->fetch_assoc()['total_people'] ?? 0;
                                $stmt->close();
                                echo $totalPeople . ' bookings';
                                ?>
                            </span>
                        </div>
                        <p><strong>Destination:</strong> <?= htmlspecialchars($tour['destination']) ?></p>
                    </div>
                    <div class="tour-book">
                        <div class="price-info">
                            <span class="original-price">$<?= number_format((float) $tour['price'], 2) ?></span>
                            <div class="price">$<?= number_format((float) $tour['price'] * 0.9, 2) ?></div>
                        </div>
                        <a href="booking.php?id=<?= $tour['tour_id'] ?>" class="btn-book">Book Now</a>
                    </div>
                </div>
                <?php $index++; endwhile; ?>
        </div>
    </div>
    <!-- Testimonials Section -->
    <div class="testimonials-section">
        <h2>What Our Customers Say</h2>
        <div class="testimonials-container">
            <div class="testimonial-card">
                <p>"Amazing experience! The tour guide was knowledgeable and friendly. We loved the sunset cruise in
                    Santorini!"</p>
                <span class="customer-name">John Doe</span>
            </div>
            <div class="testimonial-card">
                <p>"Best vacation ever! The itinerary was well-planned, and the accommodations were top-notch. Highly
                    recommend the Bali retreat!"</p>
                <span class="customer-name">Jane Smith</span>
            </div>
        </div>
    </div>

    <!-- Featured Destinations Section -->
    <div class="featured-destinations">
        <h2>Popular Destinations</h2>
        <div class="destinations-grid">
            <div class="destination-card">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4b/La_Tour_Eiffel_vue_de_la_Tour_Saint-Jacques%2C_Paris_ao%C3%BBt_2014_%282%29.jpg/960px-La_Tour_Eiffel_vue_de_la_Tour_Saint-Jacques%2C_Paris_ao%C3%BBt_2014_%282%29.jpg"
                    alt="Paris">
                <h3>Paris, France</h3>
                <p>Experience the city of love and lights.</p>
            </div>
            <div class="destination-card">
                <img src="https://media.cntravellerme.com/photos/64e73087238bdd124237b565/16:9/w_6016,h_3384,c_limit/GettyImages-1145042281.jpeg" alt="Bali">
                <h3>Bali, Indonesia</h3>
                <p>Relax on beautiful beaches and explore temples.</p>
            </div>
        </div>
    </div>

    <!-- Why Choose Us Section -->
    <div class="why-choose-us">
        <h2>Why Choose Us</h2>
        <div class="reasons">
            <div class="reason">
                <i class="fas fa-user-tie"></i>
                <h3>Expert Guides</h3>
                <p>Our guides are knowledgeable and passionate about travel.</p>
            </div>
            <div class="reason">
                <i class="fas fa-tags"></i>
                <h3>Best Price Guarantee</h3>
                <p>We offer competitive prices with no hidden fees.</p>
            </div>
            <div class="reason">
                <i class="fas fa-headset"></i>
                <h3>24/7 Support</h3>
                <p>Our support team is available around the clock to assist you.</p>
            </div>
        </div>
    </div>

    <!-- Image Slideshow Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Tour Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="imageCarousel" class="carousel slide">
                        <div class="carousel-inner" id="carouselImages"></div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Image Modal
            document.querySelectorAll(".tour-image img").forEach(img => {
                img.addEventListener("click", () => {
                    const tour_id = img.dataset.tourId;
                    const images = <?php echo json_encode($tour_images); ?>[tour_id] || [];
                    const carousel = document.getElementById("carouselImages");
                    carousel.innerHTML = '';
                    images.forEach((image, index) => {
                        const div = document.createElement("div");
                        div.className = `carousel-item ${index === 0 ? 'active' : ''}`;
                        div.innerHTML = `
                            <img src="../Uploads/${image.image_path}" class="d-block w-100" 
                                 alt="Tour Image" 
                                 data-bs-toggle="popover" 
                                 data-bs-trigger="hover" 
                                 data-bs-content="${image.description || 'No description'}">
                        `;
                        carousel.appendChild(div);
                    });
                    // Initialize popovers
                    const popoverImages = carousel.querySelectorAll('img');
                    popoverImages.forEach(img => {
                        new bootstrap.Popover(img);
                    });
                });
            });
        });
    </script>
    <?php include "./user_footer.php"; ?>
</body>

</html>