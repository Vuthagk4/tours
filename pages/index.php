<?php
include "../includes/config.php";

// Check if a search term or destination is provided via GET
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$sortOption = isset($_GET['sort']) ? htmlspecialchars($_GET['sort']) : 'default';
$selectedDestination = isset($_GET['destination']) ? htmlspecialchars($_GET['destination']) : '';

// Fetch tours based on search term or selected destination
if (!empty($selectedDestination)) {
    $searchDest = '%' . $selectedDestination . '%';
    $stmt = $conn->prepare("SELECT t.*, d.name AS destination, d.location 
                            FROM tours t 
                            JOIN destinations d ON t.destination_id = d.destination_id 
                            WHERE d.name LIKE ? AND t.isDeleted = 0");
    $stmt->bind_param("s", $searchDest);
    $stmt->execute();
    $tours = $stmt->get_result();
} elseif (!empty($searchTerm)) {
    $searchTerm = '%' . $searchTerm . '%';
    $stmt = $conn->prepare("SELECT t.*, d.name AS destination, d.location 
                            FROM tours t 
                            JOIN destinations d ON t.destination_id = d.destination_id 
                            WHERE (t.title LIKE ? OR d.name LIKE ?) AND t.isDeleted = 0");
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
include "../includes/header.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Tours - Discover Your Next Adventure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64+BUTbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
            list-style-type: none;
        }

        #heroCarousel {
            position: relative;
            height: 600px;
            overflow: hidden;
        }

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
            margin-top: 18px;
            
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









        .tour-section {
            max-width: 95%;
            margin: 40px auto;
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
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
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

        .trending-destinations {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .trending-destinations h2 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 10px;
            text-align: left;
        }

        .trending-destinations p {
            font-size: 1rem;
            color: #666;
            margin-bottom: 30px;
            text-align: left;
        }

        .trending-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .trending-card {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .trending-card:hover {
            transform: translateY(-5px);
        }

        .trending-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .trending-card .flag {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 30px;
            height: 20px;
        }

        .trending-card h3 {
            position: absolute;
            top: 10px;
            left: 50px;
            color: white;
            font-size: 1.4rem;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .trip-planner {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .trip-planner h2 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 10px;
            text-align: left;
        }

        .trip-planner p {
            font-size: 1rem;
            color: #666;
            margin-bottom: 20px;
            text-align: left;
        }

        .trip-planner .nav-tabs {
            border-bottom: none;
            margin-bottom: 20px;
        }

        .trip-planner .nav-link {
            color: #555;
            font-weight: 500;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 8px 20px;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .trip-planner .nav-link.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .trip-planner-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .planner-card {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .planner-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .planner-card p {
            position: absolute;
            bottom: 10px;
            left: 10px;
            color: white;
            font-size: 1rem;
            font-weight: 500;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .planner-card .distance {
            position: absolute;
            bottom: 30px;
            left: 10px;
            color: white;
            font-size: 0.9rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        @media (max-width: 768px) {
            .trending-grid {
                grid-template-columns: 1fr;
            }

            .trip-planner-grid {
                grid-template-columns: 1fr;
            }
        }

        .header {
            display: flex;
            justify-content: space-around;
            background-color: #f1f1f1;
            padding: 20px;
        }

        .header-panel {
            text-align: center;
            width: 30%;
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
        }

        .header-panel i {
            font-size: 24px;
            margin-bottom: 10px;
            color: #007bff;
        }

        .header-panel p {
            margin: 5px 0;
            font-size: 16px;
            color: #000;
        }

        .header-panel small {
            font-size: 12px;
            color: #666;
        }

        .banner {
            position: relative;
            background-color: #007bff;
            height: 400px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .banner::before {
            content: '';
            position: absolute;
            bottom: -50%;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #ffc107;
            border-radius: 50%;
            z-index: 0;
        }

        .banner-content {
            z-index: 1;
            text-align: center;
            color: white;
        }

        .banner-content h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .banner-content p {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .search-box {
            background-color: white;
            border-radius: 25px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            width: 300px;
            margin: 0 auto;
        }

        .search-box input {
            border: none;
            outline: none;
            margin-left: 10px;
            font-size: 16px;
            width: 100%;
        }

        .illustration {
            position: absolute;
            bottom: 0;
            right: 10%;
            width: 200px;
            height: 200px;
            background: url('https://via.placeholder.com/200x200?text=Cambodia+Temple') no-repeat center;
            background-size: contain;
            z-index: 1;
        }

        .promo {
            padding: 20px;
            text-align: center;
        }

        .promo h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #000;
        }

        .promo p {
            font-size: 16px;
            margin-bottom: 20px;
            color: #333;
        }

        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .destinations {
            background-color: #f8f9fa;
            padding: 40px 0;
        }

        .destinations h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            position: relative;
        }

        .destinations h2::after {
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

        .destination-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .destination-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .destination-image {
            position: relative;
            overflow: hidden;
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            width: 100%;
            transition: transform 0.3s ease;
        }

        .destination-card:hover .card-img-top {
            transform: scale(1.05);
        }

        .card-body {
            text-align: center;
            flex-direction: column;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
            margin: 0;
            word-wrap: break-word;
            overflow-wrap: anywhere;
            line-height: 1.3;
        }

        .card-text {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.5;
            margin: 0;
            word-wrap: break-word;
            overflow-wrap: anywhere;
            max-height: none;
        }

        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

.btn-primarys {
    margin: auto;
}
        .btn-primary {
            background-color: #ff6f61;
            border-color: #ff6f61;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 8px 16px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #e65b50;
            border-color: #e65b50;
            transform: translateY(-2px);
        }











        @media (max-width: 576px) {
            .destinations h2 {
                font-size: 1.5rem;
            }

            .card-img-top {
                height: 180px;
            }

            .card-title {
                font-size: 1.1rem;
            }

            .card-text {
                font-size: 0.85rem;
            }

            .btn-primary {
                font-size: 0.85rem;
                padding: 6px 12px;
            }
        }

        .tours {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .tours h2 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #000;
        }

        .tour-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .tour-item {
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
        }

        .tour-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
        }

        .tour-item h3 {
            margin: 10px 0;
            color: #000;
        }

        .tour-item p {
            margin: 5px 0;
            color: #333;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: center;
            }

            .header-panel {
                width: 80%;
                margin-bottom: 20px;
            }

            .banner {
                height: 300px;
            }

            .banner-content h1 {
                font-size: 24px;
            }

            .banner-content p {
                font-size: 14px;
            }

            .search-box {
                width: 80%;
            }

            .illustration {
                width: 100px;
                height: 100px;
            }

            .destination-list,
            .tour-list {
                grid-template-columns: 1fr;
            }
        }






        .destinations {
            max-width: 95%;
        }











/* start section customers say */
    h2 {
      font-size: 2rem;
      color: #333;
      /* margin-bottom: 30px; */
      position: relative;
      text-align: center;
    }

    h2::after {
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

    .feedback-section {
      width: 100%;
      margin-top: 30px;
      background-color: #eeee;
    }

    .feedback-section .sub-feedback-section {
      width: 80%;
      margin: auto;
    }

    .sub-feedback-section .sub-feedback-content-header {
      display: flex;
      gap: 5%;
    }

    .feedback-section .sub-feedback-section .sub-feedback-content-header .sub-feedback-content-header-left {
      width: 45%;
      margin-top: 60px;
    }

    /* .feedback-section .sub-feedback-section .sub-feedback-content-header .sub-feedback-content-header-left h1 {
    font-family: "Playwrite AU SA", cursive;
} */
    .feedback-section .sub-feedback-section .sub-feedback-content-header .sub-feedback-content-header-right {
      width: 50%;
      margin-top: 60px;
    }

    .feedback-section .sub-feedback-section .sub-feedback-content-header .sub-feedback-content-header-right p {
      font-size: 1.1rem;
    }

    .feedback-section .sub-feedback-content-buttom .sub-feedback-content-buttom-grid {
      display: flex;
      margin-top: 50px;
      gap: 2%;

    }

    .feedback-section .sub-feedback-content-buttom .sub-feedback-content-buttom-grid .sub-feedback-content-buttom-grid1 {
      width: 32%;
      background-color: #ffff;
      border-radius: 20px;
      margin-bottom: 50px;
    }

    .feedback-section .sub-feedback-content-buttom .sub-feedback-content-buttom-grid .sub-feedback-content-buttom-grid1 .sub-feedback-contents {
      padding: 30px 40px;
      text-align: center;
      border-radius: 20px;
      /* padding: 20px;
    margin: 10px; */
      /* flex: 1 1 30%; */
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      /*Note about shadow*/
      height: 520px;
    }

    .feedback-section .sub-feedback-content-buttom .sub-feedback-content-buttom-grid .sub-feedback-content-buttom-grid1 .sub-feedback-contents .images {
      border-radius: 50%;
      width: 100px;
      height: 100px;
      object-fit: cover;
    }

    .feedback-section .sub-feedback-content-buttom .sub-feedback-content-buttom-grid .sub-feedback-content-buttom-grid1 .sub-feedback-contents h1 {
      font-size: 1.8rem;
      margin-top: 10px;
    }

    .feedback-section .sub-feedback-content-buttom .sub-feedback-content-buttom-grid .sub-feedback-content-buttom-grid1 .sub-feedback-contents p {
      font-size: 1.1rem;
      color: #777;
      /* margin-top: 20px; */
      line-height: 1.5;
    }

    .feedback-section .sub-feedback-content-buttom .sub-feedback-content-buttom-grid .sub-feedback-content-buttom-grid1 .sub-feedback-contents .socail {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-top: 20px;
      font-size: 1.5rem;
    }

    .feedback-section .sub-feedback-content-buttom .sub-feedback-content-buttom-grid .sub-feedback-content-buttom-grid1 .sub-feedback-contents .socail .sub-social {
      background-color: #ff6f61;
      color: white;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      text-align: center;
      justify-content: center;
      border-radius: 10px;
    }

    .feedback-section .sub-feedback-content-buttom .sub-feedback-content-buttom-grid .sub-feedback-content-buttom-grid1 .sub-feedback-contents .socail .sub-social:hover:nth-child(1) {
      background-color: #3b5998;
      transition: all 0.1s ease;
    }

    .feedback-section .sub-feedback-content-buttom .sub-feedback-content-buttom-grid .sub-feedback-content-buttom-grid1 .sub-feedback-contents .socail .sub-social:hover:nth-child(2) {
      background-color: #ed0e37;
      transition: all 0.1s ease;
    }

    .feedback-section .sub-feedback-content-buttom .sub-feedback-content-buttom-grid .sub-feedback-content-buttom-grid1 .sub-feedback-contents .socail .sub-social:hover:nth-child(3) {
      background-color: #ff3b30;
      transition: all 0.1s ease;
    }

    /* end section customers say */





    /* start section why choose us */
    .adventure-section {
      width: 100%;
      margin: auto;
    }

    .adventure-section .sub-adventure-section {
      width: 80%;
      margin: auto;
      padding: 40px 0;
    }

    .adventure-header {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 60px;
    }

    .header-left {
      flex: 1;
      min-width: 300px;
    }

    .header-left h1 {
      font-size: 36px;
      font-weight: bold;
      color: #222;
    }

    .dot {
      color: red;
    }

    .subtitle {
      font-style: italic;
      color: #777;
      margin-top: 10px;
    }

    .header-right {
      flex: 1;
      min-width: 300px;
      font-size: 16px;
      line-height: 1.6;
      color: #555;
    }

    .header-right p {
      font-size: 1.1rem;
    }

    .features {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 30px;
    }

    .feature-item {
      flex: 1;
      min-width: 360px;
      text-align: center;
    }

    .feature-item img {
      width: 80px;
      height: 80px;
      color: #ff6f61;
      padding: 15px;
    }

    .feature-item h3 {
      font-size: 20px;
      font-weight: bold;
      margin-bottom: 15px;
    }

    .feature-item p {
      font-size: 1.1rem;
      color: #666;
      line-height: 1.5;
    }

    /* end section choose us */








    </style>
</head>

<body>
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"
                aria-label="Slide 1" aria-current="true"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active"
                style="background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80'); background-size: cover; background-position: center; height: 600px;">
                <div class=" carousel-caption d-flex flex-column align-items-center justify-content-center h-100">
                    <h1 class="display-4 fw-bold" style="text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);">Discover Your
                        Next Adventure</h1>
                    <p class="lead" style="text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);">Explore the world with our
                        curated tours and exclusive offers.</p>
                </div>
            </div>
            <div class="carousel-item"
                style="background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1472214103451-9374bd1c798e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80'); background-size: cover; background-position: center; height: 600px;">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center h-100">
                    <h1 class="display-4 fw-bold" style="text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);">Unforgettable
                        Journeys</h1>
                    <p class="lead" style="text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);">Experience the best
                        destinations with us.</p>
                </div>
            </div>
            <div class="carousel-item"
                style="background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1501785888041-af3ef285b470?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80'); background-size: cover; background-position: center; height: 600px;">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center h-100">
                    <h1 class="display-4 fw-bold" style="text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);">Travel with Ease
                    </h1>
                    <p class="lead" style="text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);">Book your dream vacation today!
                    </p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
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









    <div class="tour-section" id="tours">
        <h2><?php echo !empty($selectedDestination) ? "Tours in " . htmlspecialchars($selectedDestination) : "Explore Our Top Tours"; ?>
        </h2>
        <div class="tour-cards">
            <?php
            $index = 0;
            $limit = 6;
            $showAll = isset($_GET['show_all']) && $_GET['show_all'] === 'true';
            if ($tours->num_rows > 0) {
                $allTours = [];
                while ($row = $tours->fetch_assoc()) {
                    $allTours[] = $row;
                }
                foreach ($allTours as $tour) {
                    if (!$showAll && $index >= $limit) {
                        break;
                    }
                    ?>
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
                                $rating = 4.5;
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
                    <?php
                    $index++;
                }
                if (count($allTours) > $limit && !$showAll) {
                    $seeAllLink = '?show_all=true#tours';
                    if (!empty($selectedDestination)) {
                        $seeAllLink .= '&destination=' . urlencode($selectedDestination);
                    }
                    if (!empty($searchTerm)) {
                        $seeAllLink .= '&search=' . urlencode($searchTerm);
                    }
                    echo '<div style="margin-top: 20px;">';
                    echo '<a href="' . $seeAllLink . '" class="btn btn-primary center">See All (' . count($allTours) . ' Tours)</a>';
                    echo '</div>';
                }
            } else {
                echo "<p>No tours available for this " . (!empty($selectedDestination) ? "destination" : "search") . ".</p>";
            }
            ?>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if (window.location.hash === '#tours') {
                const tourSection = document.getElementById('tours');
                if (tourSection) {
                    tourSection.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    </script>











    <div class="trending-destinations">
        <h2>Trending Destinations</h2>
        <p>Most popular choices for travellers from Cambodia</p>
        <div class="trending-grid">
            <a href="?destination=Phnom Penh" class="trending-card">
                <img src="https://images.unsplash.com/photo-1542051841857-5f90071e7989?q=80&w=1000&auto=format&fit=crop"
                    alt="Phnom Penh">
                <img src="https://flagcdn.com/16x12/kh.png" class="flag" alt="Cambodia Flag">
                <h3>PHNOM PENH</h3>
            </a>
            <a href="?destination=Siem Reap" class="trending-card">
                <img src="https://i.pinimg.com/736x/81/3f/d1/813fd158cca3d458369af3b6337a6ca5.jpg" alt="Siem Reap">
                <img src="https://flagcdn.com/16x12/kh.png" class="flag" alt="Cambodia Flag">
                <h3>SIEM REAP</h3>
            </a>
            <a href="?destination=Bangkok" class="trending-card">
                <img src="https://i.pinimg.com/736x/2a/0f/c5/2a0fc56c63ed836b7a4e2151179c2edf.jpg" alt="Bangkok">
                <img src="https://flagcdn.com/16x12/th.png" class="flag" alt="Thailand Flag">
                <h3>BANGKOK</h3>
            </a>
            <a href="?destination=Kompot" class="trending-card">
                <img src="https://images.unsplash.com/photo-1562790351-d273a961e0e9?q=80&w=1000&auto=format&fit=crop"
                    alt="Kampot">
                <h3>KAMPOT</h3>
            </a>
            <a href="?destination=Sihanoukville" class="trending-card">
                <img src="https://i.pinimg.com/736x/2d/65/b1/2d65b17408e7afcd69e88dfb62b5dfb6.jpg" alt="Sihanoukville">
                <h3>SIHANOUKVILLE</h3>
            </a>
            <a href="?destination=Kep" class="trending-card">
                <img src="https://i.pinimg.com/736x/cd/b3/67/cdb367bb6960d3155aa557b71dabf09d.jpg" alt="Kep">
                <h3>KEP</h3>
            </a>
        </div>
    </div>
    <div class="trip-planner">
        <h2>Quick and Easy Trip Planner</h2>
        <p>Pick a vibe and explore the top destinations in Cambodia</p>
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#city" type="button" role="tab">
                    <i class="fas fa-city me-2"></i> City
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#beach" type="button" role="tab">
                    <i class="fas fa-umbrella-beach me-2"></i> Beach
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#relax" type="button" role="tab">
                    <i class="fas fa-spa me-2"></i> Relax
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#romance" type="button" role="tab">
                    <i class="fas fa-heart me-2"></i> Romance
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#food" type="button" role="tab">
                    <i class="fas fa-utensils me-2"></i> Food
                </button>
            </li>
        </ul>
        <div class="tab-content">
            <?php
            $types = ['City', 'Beach', 'Relaxation', 'Romance', 'Food'];
            foreach ($types as $type) {
                $stmt = $conn->prepare("SELECT t.*, d.name AS destination, d.location 
                                   FROM tours t 
                                   JOIN destinations d ON t.destination_id = d.destination_id 
                                   WHERE t.type = ? AND t.isDeleted = 0");
                $stmt->bind_param("s", $type);
                $stmt->execute();
                $toursByType = $stmt->get_result();
                ?>
                <div class="tab-pane fade <?= $type == 'Beach' ? 'show active' : '' ?>" id="<?= strtolower($type) ?>"
                    role="tabpanel">
                    <div class="trip-planner-grid">
                        <?php if ($toursByType->num_rows > 0): ?>
                            <?php while ($tour = $toursByType->fetch_assoc()): ?>
                                <div class="planner-card">
                                    <img src="../Uploads/<?= htmlspecialchars($tour['image'] ?? 'placeholder.jpg') ?>"
                                        alt="<?= htmlspecialchars($tour['title']) ?>">
                                    <p class="distance"><?= rand(1, 300) ?> km away</p>
                                    <p><?= htmlspecialchars($tour['destination']) ?></p>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No tours available for this category.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
                $stmt->close();
            }
            ?>
        </div>
    </div>



    <div class="header">
        <div class="header-panel">
            <i class="fas fa-check-circle"></i>
            <p>Book now, pay later</p>
            <small>FREE cancellation on most tours</small>
        </div>
        <div class="header-panel">
            <i class="fas fa-globe"></i>
            <p>Explore Cambodia</p>
            <small>Tours, beaches, temples, and more...</small>
        </div>
        <div class="header-panel">
            <i class="fas fa-headset"></i>
            <p>24/7 Customer Support</p>
            <small>We’re here to assist you</small>
        </div>
    </div>






    <div class="banner">
        <div class="banner-content">
            <h1>Discover Cambodia’s Wonders</h1>
            <p>Book your next adventure today</p>
            <div class="search-box">
                <form action="search.php" method="GET">
                    <i class="fas fa-search"></i>
                    <input type="text" name="query" placeholder="Search tours or destinations">
                    <button type="submit" class="btn">Search</button>
                </form>
            </div>
        </div>
        <div class="illustration"></div>
    </div>
    <div class="promo">
        <h2>Travel Cambodia, Save More</h2>
        <p>Sign in to unlock exclusive discounts on tours and destinations.</p>
    </div>







    <div class="destinations container mt-5">
        <h2 class="text-center mb-4">Popular Destinations in Cambodia</h2>
        <div class="row g-4">
            <?php
            $sql = "SELECT destination_id, name, description, image 
                FROM destinations 
                WHERE isDelete = 0 
                LIMIT 6";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col-sm-6 col-md-4 mb-4">';
                    echo '<div class="card destination-card">';
                    echo '<div class="destination-image">';
                    echo '<img src="' . htmlspecialchars($row["image"]) . '" class="card-img-top" alt="' . htmlspecialchars($row["name"]) . '" loading="lazy">';
                    echo '</div>';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($row["name"]) . '</h5>';
                    echo '<p class="card-text">' . htmlspecialchars(substr($row["description"], 0, 150)) . (strlen($row["description"]) > 150 ? '...' : '') . '</p>';
                    echo '<div class="button-container">';
                    echo '<a href="destination.php?id=' . $row["destination_id"] . '" class="btn btn-primary stretched-link">Explore Tours</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p class="text-center col-12">No destinations available at the moment.</p>';
            }
            ?>
        </div>
    </div>





    
    <div class="tours">
        <h2>Featured Tours</h2>
        <div class="tour-list">
            <?php
            $sql = "SELECT t.tour_id, t.title, t.description, t.price, t.image, d.name as destination 
                    FROM tours t 
                    JOIN destinations d ON t.destination_id = d.destination_id 
                    WHERE t.isDeleted = 0 
                    LIMIT 3";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="tour-item">';
                    echo '<img src="' . htmlspecialchars($row["image"]) . '" alt="' . htmlspecialchars($row["title"]) . '">';
                    echo '<h3>' . htmlspecialchars($row["title"]) . '</h3>';
                    echo '<p>' . htmlspecialchars(substr($row["description"], 0, 100)) . '...</p>';
                    echo '<p>Destination: ' . htmlspecialchars($row["destination"]) . '</p>';
                    echo '<p>Price: $' . number_format($row["price"], 2) . '</p>';
                    echo '<a href="tour.php?id=' . $row["tour_id"] . '">Book Now</a>';
                    echo '</div>';
                }
            } else {
                echo '<p>No tours available at the moment.</p>';
            }
            ?>
        </div>
    </div>






    <!-- <div class="testimonials-section">
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
    </div> -->








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
                <img src="https://media.cntravellerme.com/photos/64e73087238bdd124237b565/16:9/w_6016,h_3384,c_limit/GettyImages-1145042281.jpeg"
                    alt="Bali">
                <h3>Bali, Indonesia</h3>
                <p>Relax on beautiful beaches and explore temples.</p>
            </div>
        </div>
    </div>









  <!-- section customer say  -->
  <h2 style="margin-top: 6rem;">What Our Customers Say</h2>
  <section class="feedback-section">
    <div class="sub-feedback-section">
      <div class="sub-feedback-content-header">
        <div class="sub-feedback-content-header-left">
          <h1>Impressions About <br> Our Trips.</h1>
          <p>- what client say</p>
        </div>
        <div class="sub-feedback-content-header-right">
          <p>We do our best to provide you with maximum comfort. Each client is dear to us, and we try to organize
            each trip so that it is as interesting, bright, and rich as possible. We give the possibility to see the
            wonders of the Earth just the way you've seen them on Instagram. Check out our reviews! People who have
            already traveled with us will tell about us!</p>
        </div>
      </div>
      <div class="sub-feedback-content-buttom">
        <div class="sub-feedback-content-buttom-grid">
          <div class="sub-feedback-content-buttom-grid1">
            <div class="sub-feedback-contents">
              <img src="../uploads/say1.jpg" alt="" class="images">
              <h1>Dave Peterson</h1>
              <p>October 28,2018</p>
              <p>“I love to travel to beautiful places around the world. I've gone on tours with this company three
                times already! Everything was amazing! During the entire trip, I have never had any problems, and
                their photographers are really professionals.”</p>
              <div class="socail">
                <div class="sub-social"><i class="fa-brands fa-facebook"></i></div>
                <div class="sub-social"><i class="fa-brands fa-square-instagram"></i></div>
                <div class="sub-social"><i class="fa-brands fa-youtube"></i></div>
              </div>
            </div>
          </div>
          <div class="sub-feedback-content-buttom-grid1">
            <div class="sub-feedback-contents">
              <img src="../uploads/say2.jpg" alt="" class="images">
              <h1>Amanda Olson</h1>
              <p>December 20,2020</p>
              <p>“Let's be honest. When traveling, we always try to take beautiful photos for our Instagram and it
                doesn't always work out well. But with this agency, you don't have to think about it! You just enjoy
                the trip and pose, and then get your stunning photos!”</p>
              <div class="socail">
                <div class="sub-social"><i class="fa-brands fa-facebook"></i></div>
                <div class="sub-social"><i class="fa-brands fa-square-instagram"></i></div>
                <div class="sub-social"><i class="fa-brands fa-youtube"></i></div>
              </div>
            </div>
          </div>
          <div class="sub-feedback-content-buttom-grid1">
            <div class="sub-feedback-contents">
              <img src="../uploads/say3.jpg" alt="" class="images">
              <h1>Erica Jameson</h1>
              <p>May 08, 2020</p>
              <p>“My Instagram is everything for me! I love to post vivid photos and delight my followers! With this
                company, it has become much easier. These guys organize great tours to really cool places that look
                stunning in real life, too, not just on Instagram.”</p>
              <div class="socail">
                <div class="sub-social"><i class="fa-brands fa-facebook"></i></div>
                <div class="sub-social"><i class="fa-brands fa-square-instagram"></i></div>
                <div class="sub-social"><i class="fa-brands fa-youtube"></i></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end section customer say -->




<!-- section why choose us -->
  <h2 style="margin-top: 6rem;">Why Choose Use</h2>
  <section class="adventure-section">
    <div class="sub-adventure-section">
      <div class="adventure-header">
        <div class="header-left">
          <h1>Adventures You'll <br> Definitely Love<span class="dot">.</span></h1>
          <p class="subtitle">- why we are so popular</p>
        </div>
        <div class="header-right">
          <p>Travel is our life and our passion. We do what we love - we give pleasant emotions to our clients. Our
            experts know exactly how to organize a trip so that it will bring maximum pleasure. Want to see those
            places you've seen on Instagram with your own eyes and get more followers? Travel with us! We promise you
            won't be disappointed.</p>
        </div>
      </div>

      <div class="features">
        <div class="feature-item">
          <div class="reason">
            <img src="../uploads/icons8-travel-diary-50 (1).png" alt="">
            <h3>The most beautiful places</h3>
            <p>You will visit the most popular places on our planet. Enjoy stunning landscapes, touch the history and
              make brilliant photos!</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="reason">
            <img src="../uploads/icons8-professional-photographer-50 (1).png" alt="">
            <h3>Professional photographers</h3>
            <p>During the trip, you will be accompanied by our professional photographers who will take the best photos
              of you.</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="reason">
            <img src="../uploads/icons8-trophy-64 (1).png" alt="">
            <h3>Full support</h3>
            <p>By ordering a tour in our company, you can choose the best outfits for photos because nothing else is
              required from you! We will fully organize your trip.</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="reason">
            <i class="fas fa-user-tie"></i>
            <h3>Expert Guides</h3>
            <p>Our guides are knowledgeable and passionate about travel.</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="reason">
            <i class="fas fa-tags"></i>
            <h3>Best Price Guarantee</h3>
            <p>We offer competitive prices with no hidden fees.</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="reason">
            <i class="fas fa-headset"></i>
            <h3>24/7 Support</h3>
            <p>Our support team is available around the clock to assist you.</p>
          </div>
        </div>
            <div style="text-align: center;" class="btn-primarys">
            <a href="guides.php" class="btn btn-primary">Meet Our Guides</a>
        </div>
        </section> 
        <!-- Why Choose Us Section -->
    






















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
<!-- end class why choose us -->














    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
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