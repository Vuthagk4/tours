<?php
include '../includes/config.php';

// Set a flag to indicate this is the user profile page
define('IS_PROFILE_PAGE', true);

include '../includes/header.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$errors = [];
$success = [];

// Set time zone for +07 (as per your provided date/time)
date_default_timezone_set('Asia/Bangkok');
$currentHour = (int) date('H');
$greeting = $currentHour >= 5 && $currentHour < 12 ? "Good Morning" :
    ($currentHour >= 12 && $currentHour < 17 ? "Good Afternoon" :
        ($currentHour >= 17 && $currentHour < 22 ? "Good Evening" : "Good Night"));

// Fetch user data
$stmt = mysqli_prepare($conn, "SELECT name, email, role, profile_image FROM users WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result) ?: ['name' => 'Unknown', 'email' => 'N/A', 'role' => 'N/A', 'profile_image' => null];
mysqli_stmt_close($stmt);

// Determine profile image
$profileImage = $user['profile_image'] && file_exists('../Uploads/users/' . $user['profile_image'])
    ? '../Uploads/users/' . htmlspecialchars($user['profile_image'])
    : '../assets/images/default_profile.jpg';

// Calculate profile completion percentage
$profileCompletion = 20; // Start with a base percentage
if (!empty($user['name']))
    $profileCompletion += 20;
if (!empty($user['email']))
    $profileCompletion += 20;
if (!empty($user['profile_image']))
    $profileCompletion += 20;
if (!empty($user['role']))
    $profileCompletion += 20;

// Handle avatar upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['avatar_file'])) {
    $uploadDir = '../Uploads/users/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB

    if ($_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
        $fileType = $_FILES['avatar_file']['type'];
        $fileSize = $_FILES['avatar_file']['size'];

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
        } elseif ($fileSize > $maxFileSize) {
            $errors[] = "File size exceeds 5MB limit.";
        } else {
            $fileName = $userId . '_' . time() . '_' . basename($_FILES['avatar_file']['name']);
            $destination = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['avatar_file']['tmp_name'], $destination)) {
                $stmt = mysqli_prepare($conn, "UPDATE users SET profile_image = ? WHERE user_id = ?");
                mysqli_stmt_bind_param($stmt, "si", $fileName, $userId);
                if (mysqli_stmt_execute($stmt)) {
                    $success[] = "Avatar updated successfully!";
                    $user['profile_image'] = $fileName;
                    $profileImage = '../Uploads/users/' . htmlspecialchars($fileName);
                    $profileCompletion = min(100, $profileCompletion + 20); // Update completion
                } else {
                    $errors[] = "Error updating avatar: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            } else {
                $errors[] = "Error uploading file.";
            }
        }
    } else {
        $errors[] = "No file uploaded or upload error.";
    }
}

// Handle password update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($currentPassword)) {
        $errors[] = "Current password is required!";
    }
    if (empty($newPassword)) {
        $errors[] = "New password is required!";
    } elseif (strlen($newPassword) < 8) {
        $errors[] = "New password must be at least 8 characters!";
    }
    if ($newPassword !== $confirmPassword) {
        $errors[] = "New password and confirmation do not match!";
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($row && password_verify($currentPassword, $row['password'])) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE user_id = ?");
            mysqli_stmt_bind_param($stmt, "si", $hashedPassword, $userId);
            if (mysqli_stmt_execute($stmt)) {
                $success[] = "Password updated successfully!";
            } else {
                $errors[] = "Error updating password: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $errors[] = "Current password is incorrect!";
        }
    }
}



mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Tour & Travel Management</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Freehand&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
            list-style-type: none;
        }

        .profile-section {
            padding: 60px 0;
            min-height: calc(100vh - 200px);
        }

        .container {
            max-width: 1200px;
        }

        .greeting-card {
            background: linear-gradient(135deg, #49B11E 0%, #3a8f16 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .greeting-card h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3);
        }

        .greeting-card p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }

        .profile-card:hover {
            transform: translateY(-5px);
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #49B11E;
            transition: transform 0.3s ease;
        }

        .profile-avatar:hover {
            transform: scale(1.05);
        }

        .profile-info h3 {
            color: #223140;
            font-weight: 600;
            margin-top: 20px;
        }

        .profile-info p {
            margin: 5px 0;
            color: #555;
            font-size: 1rem;
        }

        .progress-bar {
            background-color: #49B11E;
        }

        .avatar-form,
        .password-form,
        .preferences-form,
        .booking-history,
        .recent-activity,
        .quick-links {
            margin-top: 40px;
        }

        .avatar-form h4,
        .password-form h4,
        .preferences-form h4,
        .booking-history h4,
        .recent-activity h4,
        .quick-links h4 {
            color: #223140;
            font-weight: 600;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }

        .avatar-form h4::after,
        .password-form h4::after,
        .preferences-form h4::after,
        .booking-history h4::after,
        .recent-activity h4::after,
        .quick-links h4::after {
            content: '';
            width: 50%;
            height: 3px;
            background: #ff6f61;
            position: absolute;
            bottom: -5px;
            left: 0;
            border-radius: 2px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #49B11E;
            box-shadow: 0 0 8px rgba(73, 177, 30, 0.3);
        }

        .btn-custom {
            background: #49B11E;
            border: none;
            padding: 10px 20px;
            color: white;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-custom:hover {
            background: #3a8f16;
            transform: translateY(-2px);
        }

        .alert-dismissible {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #e6f4e1;
            color: #49B11E;
            border: none;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #dc3545;
            border: none;
        }

        /* Booking History Styles */
        .booking-card {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .booking-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .booking-card p {
            margin: 5px 0;
            color: #555;
        }

        .status-pending {
            color: #ff6f61;
            font-weight: 500;
        }

        .status-confirmed {
            color: #49B11E;
            font-weight: 500;
        }

        .status-cancelled {
            color: #dc3545;
            font-weight: 500;
        }

        .btn-details {
            color: #49B11E;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .btn-details:hover {
            color: #3a8f16;
            text-decoration: underline;
        }

        /* Preferences Form */
        .preferences-form .form-select {
            border-radius: 8px;
        }

        /* Recent Activity */
        .activity-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .activity-item i {
            font-size: 1.5rem;
            color: #ff6f61;
            margin-right: 15px;
        }

        .activity-item p {
            margin: 0;
            color: #555;
        }

        /* Quick Links */
        .quick-links .btn-link {
            display: block;
            background: #f1f1f1;
            color: #223140;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
            margin-bottom: 10px;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .quick-links .btn-link:hover {
            background: #49B11E;
            color: white;
            transform: translateY(-2px);
            text-decoration: none;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .greeting-card {
                padding: 20px;
            }

            .greeting-card h1 {
                font-size: 2rem;
            }

            .greeting-card p {
                font-size: 1rem;
            }

            .profile-card {
                margin: 0 15px;
                padding: 20px;
            }

            .profile-avatar {
                width: 120px;
                height: 120px;
            }
        }

        @media (max-width: 576px) {
            .greeting-card h1 {
                font-size: 1.5rem;
            }

            .greeting-card p {
                font-size: 0.9rem;
            }

            .profile-avatar {
                width: 100px;
                height: 100px;
            }

            .profile-info h3 {
                font-size: 1.5rem;
            }

            .booking-card p {
                font-size: 0.9rem;
            }

            .quick-links .btn-link {
                font-size: 0.9rem;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <!-- Profile Section -->
    <section class="profile-section">
        <div class="container">
            <!-- Greeting Card -->
            <div class="greeting-card">
                <h1><?php echo $greeting . ", " . htmlspecialchars($user['name']) . "!"; ?></h1>
                <p>Welcome back to your travel dashboard.</p>
            </div>

            <div class="row">
                <!-- Main Profile Card -->
                <div class="col-lg-8">
                    <div class="card profile-card">
                        <div class="card-body text-center">
                            <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Avatar"
                                class="profile-avatar">
                            <div class="profile-info">
                                <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                                <p><i class="fas fa-envelope me-2"></i> <strong>Email:</strong>
                                    <?php echo htmlspecialchars($user['email']); ?></p>
                                <p><i class="fas fa-user-tag me-2"></i> <strong>Role:</strong>
                                    <?php echo htmlspecialchars($user['role']); ?></p>
                                <div class="progress mt-3" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar"
                                        style="width: <?php echo $profileCompletion; ?>%;"
                                        aria-valuenow="<?php echo $profileCompletion; ?>" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Profile <?php echo $profileCompletion; ?>% Complete</small>
                            </div>

                            <!-- Avatar Form -->
                            <div class="avatar-form">
                                <h4>Update Avatar</h4>
                                <?php if (!empty($errors)): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?php foreach ($errors as $error): ?>
                                            <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
                                        <?php endforeach; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($success)): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <?php foreach ($success as $msg): ?>
                                            <p class="mb-0"><?php echo htmlspecialchars($msg); ?></p>
                                        <?php endforeach; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                                <form action="user_profile.php" method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <input type="file" name="avatar_file" class="form-control"
                                            accept="image/jpeg,image/png,image/gif" required>
                                    </div>
                                    <button type="submit" class="btn btn-custom">Upload Avatar</button>
                                </form>
                            </div>

                            <!-- Password Form -->
                            <div class="password-form">
                                <h4>Update Password</h4>
                                <form action="user_profile.php" method="POST">
                                    <div class="mb-3">
                                        <input type="password" name="current_password" class="form-control"
                                            placeholder="Current Password" required>
                                    </div>
                                    <div class="mb-3">
                                        <input type="password" name="new_password" class="form-control"
                                            placeholder="New Password" required>
                                    </div>
                                    <div class="mb-3">
                                        <input type="password" name="confirm_password" class="form-control"
                                            placeholder="Confirm New Password" required>
                                    </div>
                                    <button type="submit" name="update_password" class="btn btn-custom">Update
                                        Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Recent Activity -->
                    <div class="card profile-card">
                        <div class="card-body">
                            <div class="recent-activity">
                                <h4>Recent Activity</h4>
                                <div class="activity-item">
                                    <i class="fas fa-history"></i>
                                    <p><strong>:</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="card profile-card">
                        <div class="card-body">
                            <div class="quick-links">
                                <h4>Quick Links</h4>
                                <a href="bookings.php" class="btn-link"><i class="fas fa-ticket-alt me-2"></i> My
                                    Bookings</a>
                                <a href="packages.php" class="btn-link"><i class="fas fa-suitcase me-2"></i> Explore
                                    Tours</a>
                                <a href="contact.php" class="btn-link"><i class="fas fa-envelope me-2"></i> Contact
                                    Support</a>
                                <a href="logout.php" class="btn-link"><i class="fas fa-sign-out-alt me-2"></i>
                                    Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
</body>

</html>