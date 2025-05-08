<?php
// session_name('UserSession')
session_start(); 
include 'config.php';
$cartCount = 0;
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM bookings WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $cartCount = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour & Travel Management</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Freehand&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        /* Top Bar */
        .top-bar {
            background: #223140;
            color: white;
            padding: 10px 0;
            font-size: 0.9rem;
            font-family: 'Work Sans', sans-serif;
        }

        .top-bar a {
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .top-bar a:hover {
            color: #49B11E;
        }

        .top-bar .home-link {
            display: flex;
            align-items: center;
        }

        .top-bar .home-link i {
            font-size: 1.2rem;
            margin-right: 8px;
        }

        .top-bar .auth-links {
            display: flex !important;
            align-items: center;
            gap: 10px;
            opacity: 1 !important;
            visibility: visible !important;
            /* Temporary border for debugging visibility */
            
        }

        .top-bar .auth-links a {
            font-weight: 500;
        }

        .top-bar .auth-links span {
            color: #ccc;
        }

        /* Logo Section */
        .logo-section {
            background: white;
            padding: 20px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-family: 'Work Sans', sans-serif;
        }

        .logo-section h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        .logo-section .tour {
            color: #49B11E;
        }

        .logo-section .and {
            color: #ff7f50;
        }

        .logo-section .management {
            color: #223140;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info .btn-logout {
            background:red;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 0.9rem;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .user-info .btn-logout:hover {
            background: #3a8f16;
        }

        .user-info .btn-login {
            color: #49B11E;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .user-info .btn-login:hover {
            color: #3a8f16;
            text-decoration: underline;
        }

        /* Navigation Menu */
        .navbar-custom {
            background: #223140;
            font-family: 'Work Sans', sans-serif;
        }

        .navbar-custom .navbar-nav .nav-link {
            color: white;
            font-size: 1.1rem;
            padding: 10px 15px;
            transition: color 0.3s ease, background 0.3s ease;
        }

        .navbar-custom .navbar-nav .nav-link:hover {
            color: #49B11E;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        .navbar-custom .navbar-toggler {
            border-color: white;
        }

        .navbar-custom .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .top-bar .auth-links {
                flex-direction: column;
                align-items: flex-end;
                gap: 5px;
            }

            .logo-section h1 {
                font-size: 1.5rem;
            }

            .user-info {
                flex-direction: column;
                align-items: flex-end;
                gap: 10px;
            }
        }

        @media (max-width: 576px) {
            .top-bar .container {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            .top-bar .home-link {
                margin-bottom: 10px;
            }

            .top-bar .auth-links {
                flex-direction: row;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="home-link">
                <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
            </div>
            <div class="auth-links">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="register.php">Register</a>
                    <span>|</span>
                    <a href="login.php">Login</a>
                <?php else: ?>
                    <span class="login_as">Logged in as <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Logo Section -->
    <div class="logo-section">
        <div class="container d-flex justify-content-between align-items-center">
            <h1>
                <span class="tour">Tour</span><span class="and">&</span><span class="management">Travel Management</span>
            </h1>
            <div class="user-info">
                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['role'])): ?>
                    <span class="fw-medium"><?php echo htmlspecialchars($_SESSION['name']) . " (" . htmlspecialchars($_SESSION['role']) . ")"; ?></span>
                    <form action="../pages/logout.php" method="POST" style="display:inline;">
                        <button type="submit" class="btn btn-logout">Logout <i class="fa-solid fa-right-from-bracket"></i></button>
                    </form>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="navbar navbar-expand-lg navbar-custom" style="position:sticky;top:0px;z-index:9999;">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="packages.php">Tour Package</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Booking</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Term of Use</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Enquiry</a></li>
                    <li class="nav-item position-relative">
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart"></i> Buy
                            <span id="cart-count" class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill"><?php echo $cartCount; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
<script>
let cart = JSON.parse(localStorage.getItem(userCartKey)) || [];
cart.push(booking);
localStorage.setItem(userCartKey, JSON.stringify(cart));
updateCartCount();
function updateCartCount() {
    let cart = JSON.parse(localStorage.getItem(userCartKey)) || [];
    document.getElementById('cart-count').textContent = cart.length;
}
    </script>

</script>
</html>