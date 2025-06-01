<?php
include '../includes/config.php';
include '../includes/admin_header.php';


// Handle guide addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_guide'])) {
    $name = trim($_POST['add_name']);
    $position = trim($_POST['add_position']);
    $skills = trim($_POST['add_skills']) ?: null;
    $languages = trim($_POST['add_languages']);
    $image_path = null;

    if (isset($_FILES['add_guide_image']) && $_FILES['add_guide_image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../Uploads/guides/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        if (!is_writable($target_dir)) {
            $error_message = "Upload directory is not writable.";
        } else {
            $image_name = uniqid('guide_') . '_' . basename($_FILES["add_guide_image"]["name"]);
            $target_file = $target_dir . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["add_guide_image"]["tmp_name"]);
            if ($check && $_FILES["add_guide_image"]["size"] <= 5 * 1024 * 1024 && in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (move_uploaded_file($_FILES["add_guide_image"]["tmp_name"], $target_file)) {
                    $image_path = "Uploads/guides/" . $image_name;
                } else {
                    $error_message = "Failed to upload image.";
                }
            } else {
                $error_message = "Invalid image. Use JPG, JPEG, PNG, or GIF under 5MB.";
            }
        }
    }

    if (!isset($error_message) && !empty($name) && !empty($position) && !empty($languages)) {
        $stmt = $conn->prepare("INSERT INTO guides (name, position, skill, language, image, is_deleted) VALUES (?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("sssss", $name, $position, $skills, $languages, $image_path);
        if ($stmt->execute()) {
            $success_message = "Guide added successfully!";
        } else {
            $error_message = "Error adding guide: " . $conn->error;
        }
        $stmt->close();
    } elseif (!isset($error_message)) {
        $error_message = "Name, position, and languages are required.";
    }
}

// Handle guide deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_guide_id'])) {
    $guide_id = (int) $_POST['delete_guide_id'];
    $stmt_check = $conn->prepare("SELECT booking_id FROM bookings WHERE guide_id = ? AND status IN ('pending', 'confirmed')");
    $stmt_check->bind_param("i", $guide_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    $bookings = $result->fetch_all(MYSQLI_ASSOC);
    $stmt_check->close();

    if ($result->num_rows > 0) {
        $booking_ids = implode(', ', array_column($bookings, 'booking_id'));
        $error_message = "Cannot delete guide due to active bookings (IDs: $booking_ids).";
    } else {
        $stmt = $conn->prepare("UPDATE guides SET is_deleted = 1 WHERE guide_id = ?");
        $stmt->bind_param("i", $guide_id);
        if ($stmt->execute()) {
            $success_message = "Guide deleted successfully!";
        } else {
            $error_message = "Error deleting guide: " . $conn->error;
        }
        $stmt->close();
    }
}

// Handle guide edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_guide_id'])) {
    $guide_id = (int) $_POST['edit_guide_id'];
    $name = trim($_POST['edit_name']);
    $position = trim($_POST['edit_position']);
    $skills = trim($_POST['edit_skills']) ?: null;
    $languages = trim($_POST['edit_languages']);
    $image_path = isset($_POST['existing_image']) && $_POST['existing_image'] ? $_POST['existing_image'] : null;

    if (isset($_FILES['edit_guide_image']) && $_FILES['edit_guide_image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../Uploads/guides/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        if (!is_writable($target_dir)) {
            $error_message = "Upload directory is not writable.";
        } else {
            $image_name = uniqid('guide_') . '_' . basename($_FILES["edit_guide_image"]["name"]);
            $target_file = $target_dir . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["edit_guide_image"]["tmp_name"]);
            if ($check && $_FILES["edit_guide_image"]["size"] <= 5 * 1024 * 1024 && in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (move_uploaded_file($_FILES["edit_guide_image"]["tmp_name"], $target_file)) {
                    $image_path = "Uploads/guides/" . $image_name;
                } else {
                    $error_message = "Failed to upload image.";
                }
            } else {
                $error_message = "Invalid image. Use JPG, JPEG, PNG, or GIF under 5MB.";
            }
        }
    }

    if (!isset($error_message) && !empty($name) && !empty($position) && !empty($languages)) {
        $stmt = $conn->prepare("UPDATE guides SET name = ?, position = ?, skill = ?, language = ?, image = ? WHERE guide_id = ?");
        $stmt->bind_param("sssssi", $name, $position, $skills, $languages, $image_path, $guide_id);
        if ($stmt->execute()) {
            $success_message = "Guide updated successfully!";
        } else {
            $error_message = "Error updating guide: " . $conn->error;
        }
        $stmt->close();
    } elseif (!isset($error_message)) {
        $error_message = "Name, position, and languages are required.";
    }
}

// Fetch dashboard data
$totalUsersQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM users") or die("Query failed: " . mysqli_error($conn));
$totalUsers = mysqli_fetch_assoc($totalUsersQuery)['total'] ?? 0;

$adminUsersQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'admin'") or die("Query failed: " . mysqli_error($conn));
$adminUsers = mysqli_fetch_assoc($adminUsersQuery)['total'] ?? 0;

$customerUsersQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'customer'") or die("Query failed: " . mysqli_error($conn));
$customerUsers = mysqli_fetch_assoc($customerUsersQuery)['total'] ?? 0;

$totalGuidesQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM guides WHERE is_deleted = 0") or die("Query failed: " . mysqli_error($conn));
$totalGuides = mysqli_fetch_assoc($totalGuidesQuery)['total'] ?? 0;

$recentUsersQuery = mysqli_query($conn, "SELECT user_id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5") or die("Query failed: " . mysqli_error($conn));

$recentGuidesQuery = mysqli_query($conn, "SELECT guide_id, name, position, skill, language, image FROM guides WHERE is_deleted = 0 ORDER BY guide_id DESC LIMIT 5") or die("Query failed: " . mysqli_error($conn));

$chartDataQuery = mysqli_query($conn, "SELECT DATE_FORMAT(created_at, '%b %d') as month, COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY DATE_FORMAT(created_at, '%Y-%m-%d') ORDER BY created_at") or die("Query failed: " . mysqli_error($conn));
$chartLabels = [];
$chartValues = [];
while ($row = mysqli_fetch_assoc($chartDataQuery)) {
    $chartLabels[] = $row['month'];
    $chartValues[] = $row['count'];
}
$chartLabelsJson = json_encode($chartLabels);
$chartValuesJson = json_encode($chartValues);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Work Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: #f8f9fa;
        }

        .dashboard-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            min-height: calc(100vh - 60px);
        }

        .dashboard-content.collapsed {
            margin-left: 60px;
        }

        .sidebar-toggle {
            color: #333;
            font-size: 1.5rem;
            padding: 10px;
            cursor: pointer;
            border-radius: 50%;
            transition: background 0.2s ease;
            margin-left: 10px;
        }

        .sidebar-toggle:hover {
            background: #e9ecef;
        }

        .admin-info {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
            padding: 8px 15px;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            color: #333;
            font-weight: 500;
        }

        .admin-info button {
            background-color: #dc3545;
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .admin-info button:hover {
            background-color: #c82333;
        }

        .img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }

        .dashboard-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 20px;
            transition: transform 0.2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .dashboard-card h3 {
            margin: 0;
            font-size: 1rem;
            color: #6c757d;
            font-weight: 500;
        }

        .dashboard-card p {
            font-size: 1.5rem;
            font-weight: 600;
            color: #007bff;
            margin: 10px 0 0;
        }

        .dashboard-card i {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #007bff;
        }

        .stats-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .daily-sales {
            background: #007bff;
            color: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .daily-sales h3 {
            margin: 0;
            font-size: 1rem;
            color: white;
        }

        .daily-sales p {
            font-size: 1.8rem;
            font-weight: 600;
            margin: 10px 0 0;
            color: white;
        }

        .btn-custom {
            background: #007bff;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            font-size: 0.9rem;
            cursor: pointer;
            margin-left: 10px;
        }

        .btn-custom:hover {
            background: #0056b3;
        }

        .recent-users,
        .recent-guides {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 15px;
        }

        .recent-users table,
        .recent-guides table {
            width: 100%;
            border-collapse: collapse;
        }

        .recent-users th,
        .recent-users td,
        .recent-guides th,
        .recent-guides td {
            padding: 12px;
            text-align: left;
        }

        .recent-users th,
        .recent-guides th {
            background: #007bff;
            color: white;
        }

        .recent-users tr:nth-child(even),
        .recent-guides tr:nth-child(even) {
            background: #f8f9fa;
        }

        .recent-guides img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }

        .recent-guides .btn-action {
            padding: 5px 10px;
            font-size: 0.85rem;
            margin-right: 5px;
        }

        .see-more-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            margin-top: 10px;
            display: inline-block;
        }

        .see-more-btn:hover {
            background: #218838;
        }

        .guide-modal img,
        .edit-guide-modal img,
        .add-guide-modal img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        .guide-details p,
        .edit-guide-modal p,
        .add-guide-modal p {
            margin: 5px 0;
            font-size: 1rem;
        }

        .guide-details strong {
            color: #333;
        }

        .edit-guide-modal .form-group,
        .add-guide-modal .form-group {
            margin-bottom: 15px;
        }

        .edit-guide-modal label,
        .add-guide-modal label {
            font-weight: 500;
            color: #333;
        }

        .edit-guide-modal .form-control,
        .add-guide-modal .form-control {
            border-radius: 5px;
        }

        .alert {
            margin-bottom: 20px;
        }

        @media screen and (max-width: 768px) {
            .sidebar {
                width: 60px;
            }

            .sidebar .section-1 .row .col-9 {
                opacity: 0;
                display: none;
            }

            .sidebar .section-1 {
                justify-content: center;
            }

            .sidebar ul li a span {
                opacity: 0;
                display: none;
            }

            .sidebar ul li a {
                justify-content: center;
                padding: 12px 10px;
            }

            .sidebar ul li a i {
                margin-right: 0;
            }

            .main-content {
                margin-left: 60px;
                width: calc(100% - 60px);
            }

            .dashboard-content {
                margin-left: 60px;
            }

            .stats-row {
                flex-direction: column;
            }

            .dashboard-card {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-content" id="dashboard-content">
        <?php if (isset($success_message)): ?>
            <div class='alert alert-success'><?php echo htmlspecialchars($success_message); ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class='alert alert-danger'><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <h2 class="mb-4" style="color: #333;">Admin Dashboard</h2>
        <p style="color: #6c757d;">Tour & Travel Admin</p>
        <div class="stats-row">
            <div class="dashboard-card"><i class="fa fa-users"></i>
                <h3>Total Users</h3>
                <p><?php echo number_format($totalUsers); ?></p>
            </div>
            <div class="dashboard-card" style="background: #e9ecef;"><i class="fa fa-check-circle"></i>
                <h3>Admin Users</h3>
                <p><?php echo number_format($adminUsers); ?></p>
            </div>
            <div class="dashboard-card" style="background: #e9ecef;"><i class="fa fa-users"></i>
                <h3>Customer Users</h3>
                <p><?php echo number_format($customerUsers); ?></p>
            </div>
            <div class="dashboard-card" style="background: #e9ecef;"><i class="fa fa-user-tie"></i>
                <h3>Total Guides</h3>
                <p><?php echo number_format($totalGuides); ?></p>
            </div>
        </div>

        <div class="chart-container">
            <h3>User Statistics</h3>
            <canvas id="usersChart"></canvas>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="daily-sales">
                    <h3>Daily Users</h3>
                    <p><?php echo number_format($customerUsers / 30, 2); ?></p>
                    <small style="color: #dee2e6;">May 31, 2025</small>
                </div>
            </div>
            <div class="col-md-8">
                <button class="btn-custom">Manage Users</button>
                <button class="btn-custom">Add User</button>
                <button class="btn-custom"
                    onclick="new bootstrap.Modal(document.getElementById('addGuideModal')).show()">Add Guide</button>
            </div>
        </div>

        <div class="recent-users mt-4">
            <h3>Recent Users</h3>
            <table class="table table-bordered table-sm" id="usersTable">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody id="usersBody">
                    <?php foreach ($recentUsersQuery as $row): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($row['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['role']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($totalUsers > 5): ?>
                <button class="see-more-btn" onclick="toggleSeeMore('users')">See More</button>
            <?php endif; ?>
        </div>

        <div class="recent-guides mt-4">
            <h3>Recent Guides</h3>
            <table class="table table-bordered table-sm" id="guidesTable">
                <thead>
                    <tr>
                        <th>Guide ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="guidesBody">
                    <?php foreach ($recentGuidesQuery as $guide): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($guide['guide_id']); ?></td>
                            <td><img src="../<?php echo htmlspecialchars($guide['image']); ?>"
                                    alt="<?php echo htmlspecialchars($guide['name']); ?>"></td>
                            <td><?php echo htmlspecialchars($guide['name']); ?></td>
                            <td><?php echo htmlspecialchars($guide['position']); ?></td>
                            <td>
                                <button class="btn btn-primary btn-action"
                                    onclick="showGuideDetails(<?php echo $guide['guide_id']; ?>, '<?php echo addslashes(htmlspecialchars($guide['name'])); ?>', '<?php echo addslashes(htmlspecialchars($guide['position'])); ?>', '<?php echo addslashes(htmlspecialchars($guide['skill'] ?? 'N/A')); ?>', '<?php echo addslashes(htmlspecialchars($guide['language'])); ?>', '<?php echo addslashes(htmlspecialchars($guide['image'] ?? 'assets/images/placeholder.jpg')); ?>')">View</button>
                                <button class="btn btn-warning btn-action"
                                    onclick="showEditGuideModal(<?php echo $guide['guide_id']; ?>, '<?php echo addslashes(htmlspecialchars($guide['name'])); ?>', '<?php echo addslashes(htmlspecialchars($guide['position'])); ?>', '<?php echo addslashes(htmlspecialchars($guide['skill'] ?? '')); ?>', '<?php echo addslashes(htmlspecialchars($guide['language'])); ?>', '<?php echo addslashes(htmlspecialchars($guide['image'] ?? 'assets/images/placeholder.jpg')); ?>')">Edit</button>
                                <button class="btn btn-danger btn-action"
                                    onclick="showDeleteGuideModal(<?php echo $guide['guide_id']; ?>, '<?php echo addslashes(htmlspecialchars($guide['name'])); ?>')">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($totalGuides > 5): ?>
                <button class="see-more-btn" onclick="toggleSeeMore('guides')">See More</button>
            <?php endif; ?>
        </div>

        <!-- Add Guide Modal -->
        <div class="modal fade" id="addGuideModal" tabindex="-1" aria-labelledby="addGuideModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addGuideModalLabel">Add New Guide</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body add-guide-modal">
                        <form id="addGuideForm" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="add-name">Name</label>
                                <input type="text" class="form-control" id="add-name" name="add_name" required>
                            </div>
                            <div class="form-group">
                                <label for="add-position">Position</label>
                                <input type="text" class="form-control" id="add-position" name="add_position" required>
                            </div>
                            <div class="form-group">
                                <label for="add-skills">Skills (Optional)</label>
                                <textarea class="form-control" id="add-skills" name="add_skills" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="add-languages">Languages</label>
                                <input type="text" class="form-control" id="add-languages" name="add_languages"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="add-guide-image">Guide Image (Optional)</label>
                                <input type="file" class="form-control" id="add-guide-image" name="add_guide_image"
                                    accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-primary" name="add_guide">Add Guide</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guide Details Modal -->
        <div class="modal fade" id="guideModal" tabindex="-1" aria-labelledby="guideModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="guideModalLabel">Guide Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body guide-modal">
                        <img id="guide-img" src="" alt="Guide Image">
                        <div class="guide-details">
                            <p><strong>ID:</strong> <span id="guide-id"></span></p>
                            <p><strong>Name:</strong> <span id="guide-name"></span></p>
                            <p><strong>Position:</strong> <span id="guide-position"></span></p>
                            <p><strong>Skills:</strong> <span id="guide-skills"></span></p>
                            <p><strong>Languages:</strong> <span id="guide-languages"></span></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Guide Modal -->
        <div class="modal fade" id="editGuideModal" tabindex="-1" aria-labelledby="editGuideModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editGuideModalLabel">Edit Guide</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body edit-guide-modal">
                        <form id="editGuideForm" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="edit_guide_id" id="edit-guide-id">
                            <input type="hidden" name="existing_image" id="edit-existing-image">
                            <div class="form-group">
                                <label for="edit-name">Name</label>
                                <input type="text" class="form-control" id="edit-name" name="edit_name" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-position">Position</label>
                                <input type="text" class="form-control" id="edit-position" name="edit_position"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="edit-skills">Skills (Optional)</label>
                                <textarea class="form-control" id="edit-skills" name="edit_skills" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="edit-languages">Languages</label>
                                <input type="text" class="form-control" id="edit-languages" name="edit_languages"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="edit-guide-image">Guide Image (Optional)</label>
                                <input type="file" class="form-control" id="edit-guide-image" name="edit_guide_image"
                                    accept="image/*">
                                <img id="edit-guide-img" src="" alt="Guide Image" style="margin-top: 10px;">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Guide Confirmation Modal -->
        <div class="modal fade" id="deleteGuideModal" tabindex="-1" aria-labelledby="deleteGuideModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteGuideModalLabel">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete guide "<span id="delete-guide-name"></span>"?</p>
                        <form id="deleteGuideForm" method="post">
                            <input type="hidden" name="delete_guide_id" id="delete-guide-id">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" onclick="submitDeleteGuideForm()">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            (function () {
                // Sidebar toggle
                const dashboardContent = document.getElementById('dashboard-content');
                const toggleButton = document.querySelector('#sidebar-toggle');
                if (toggleButton) {
                    toggleButton.addEventListener('click', () => {
                        dashboardContent.classList.toggle('collapsed');
                    });
                }

                // Chart.js
                const ctx = document.getElementById('usersChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: <?php echo $chartLabelsJson; ?>,
                        datasets: [{ label: 'New Users', data: <?php echo $chartValuesJson; ?>, borderColor: '#007bff', fill: true, backgroundColor: 'rgba(0,123,255,0.2)' }]
                    },
                    options: { responsive: true, scales: { y: { beginAtZero: true } } }
                });

                // Toggle See More
                let usersShowingAll = false;
                let guidesShowingAll = false;
                function escapeString(str) {
                    if (str == null) return '';
                    return str.replace(/[&<>"']/g, function (m) {
                        return {
                            '&': '&amp;',
                            '<': '&lt;',
                            '>': '&gt;',
                            '"': '&quot;',
                            "'": '&apos;'
                        }[m];
                    });
                }

                window.toggleSeeMore = function (type) {
                    console.log(`Fetching ${type} data...`);
                    const tableBody = document.getElementById(type === 'users' ? 'usersBody' : 'guidesBody');
                    const button = event.target;
                    const isShowingAll = type === 'users' ? usersShowingAll : guidesShowingAll;
                    const url = isShowingAll ? `fetch_${type}.php?limit=5` : `fetch_${type}.php`;

                    fetch(url)
                        .then(response => {
                            if (!response.ok) throw new Error(`HTTP error ${response.status}`);
                            return response.json();
                        })
                        .then(data => {
                            console.log(`${type} data received:`, data);
                            tableBody.innerHTML = '';
                            if (type === 'users') {
                                data.forEach(user => {
                                    tableBody.innerHTML += `
                                        <tr>
                                            <td>#${user.user_id}</td>
                                            <td>${escapeString(user.name)}</td>
                                            <td>${escapeString(user.email)}</td>
                                            <td>${escapeString(user.role)}</td>
                                            <td>${escapeString(user.created_at)}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                data.forEach(guide => {
                                    tableBody.innerHTML += `
                                        <tr>
                                            <td>#${guide.guide_id}</td>
                                            <td><img src="../${escapeString(guide.image ?? 'assets/images/placeholder.jpg')}" alt="${escapeString(guide.name)}" style="width:40px;height:40px;border-radius:50%;"></td>
                                            <td>${escapeString(guide.name)}</td>
                                            <td>${escapeString(guide.position)}</td>
                                            <td>
                                                <button class="btn btn-primary btn-action" onclick="showGuideDetails(${guide.guide_id}, '${escapeString(guide.name)}', '${escapeString(guide.position)}', '${escapeString(guide.skill ?? 'N/A')}', '${escapeString(guide.language)}', '${escapeString(guide.image)}')">View</button>
                                                <button class="btn btn-warning btn-action" onclick="showEditGuideModal(${guide.guide_id}, '${escapeString(guide.name)}', '${escapeString(guide.position)}', '${escapeString(guide.skill ?? '')}', '${escapeString(guide.language)}', '${escapeString(guide.image)}')">Edit</button>
                                                <button class="btn btn-danger btn-action" onclick="showDeleteGuideModal(${guide.guide_id}, '${escapeString(guide.name)}')">Delete</button>
                                            </td>
                                        </tr>
                                    `;
                                });
                            }
                            button.textContent = isShowingAll ? 'See More' : 'Show Less';
                            if (type === 'users') usersShowingAll = !isShowingAll;
                            else guidesShowingAll = !isShowingAll;
                        })
                        .catch(error => {
                            console.error(`Error fetching ${type}:`, error);
                            alert(`Failed to load ${type} data. Check console for details.`);
                        });
                }

                // Guide details modal
                window.showGuideDetails = function (id, name, position, skills, languages, image) {
                    console.log('Showing guide details:', { id, name, position, skills, languages, image });
                    document.getElementById('guide-id').textContent = '#' + id;
                    document.getElementById('guide-name').textContent = name;
                    document.getElementById('guide-position').textContent = position;
                    document.getElementById('guide-skills').textContent = skills;
                    document.getElementById('guide-languages').textContent = languages;
                    document.getElementById('guide-img').src = '../' + image;
                    new bootstrap.Modal(document.getElementById('guideModal')).show();
                }

                // Edit guide modal
                window.showEditGuideModal = function (id, name, position, skills, languages, image) {
                    console.log('Opening edit modal:', { id, name, position, skills, languages, image });
                    document.getElementById('edit-guide-id').value = id;
                    document.getElementById('edit-name').value = name;
                    document.getElementById('edit-position').value = position;
                    document.getElementById('edit-skills').value = skills || '';
                    document.getElementById('edit-languages').value = languages;
                    document.getElementById('edit-existing-image').value = image;
                    document.getElementById('edit-guide-img').src = '../' + image;
                    new bootstrap.Modal(document.getElementById('editGuideModal')).show();
                }

                // Delete guide modal
                window.showDeleteGuideModal = function (id, name) {
                    console.log('Opening delete modal:', { id, name });
                    document.getElementById('delete-guide-name').textContent = name;
                    document.getElementById('delete-guide-id').value = id;
                    new bootstrap.Modal(document.getElementById('deleteGuideModal')).show();
                }

                // Submit delete guide form
                window.submitDeleteGuideForm = function () {
                    console.log('Submitting delete guide form');
                    document.getElementById('deleteGuideForm').submit();
                }
            })();
        </script>
</body>

</html>