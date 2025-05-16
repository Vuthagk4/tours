<?php
include '../includes/admin_header.php';
include '../includes/config.php';

// Handle destination deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("UPDATE destinations SET isDelete = 1 WHERE destination_id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<script>alert('Destination deleted successfully!'); window.location.href='add_destination.php';</script>";
    } else {
        echo "<script>alert('Error deleting destination.');</script>";
    }
    $stmt->close();
}

// Handle destination addition
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $location = htmlspecialchars(trim($_POST['location']));
    $category = htmlspecialchars(trim($_POST['category']));

    // Handle image upload
    $image = $_FILES['image'];
    $image_name = uniqid() . '_' . basename($image['name']);
    $upload_dir = '../uploads/';
    $upload_file = $upload_dir . $image_name;

    if (move_uploaded_file($image['tmp_name'], $upload_file)) {
        // Image uploaded successfully
        $stmt = $conn->prepare("INSERT INTO destinations (name, location, image, isDelete) VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("ssss", $name, $location, $image_name);

        if ($stmt->execute()) {
            echo "<script>alert('Destination added successfully!'); window.location.href='add_destination.php';</script>";
        } else {
            echo "<script>alert('Error: " . addslashes($stmt->error) . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Error uploading image.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Destination</title>
    <link rel="stylesheet" href="../assets/css/style1.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@200;300;400;500;600&display=swap"
        rel="stylesheet">
    <!-- Leaflet.js and Leaflet Control Geocoder -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
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

        .form-container {
            max-width: 900px;
            margin: 0 auto 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 25px;
            transition: transform 0.2s ease;
        }

        .form-container:hover {
            transform: translateY(-3px);
        }

        .form-header {
            font-size: 1.5rem;
            font-weight: 500;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-group {
            flex: 1;
            min-width: 300px;
        }

        .form-group label {
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 0.9rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
            outline: none;
        }

        .form-group input[type="file"] {
            padding: 5px;
        }

        .full-width {
            flex: 0 0 100%;
        }

        #map {
            height: 300px;
            width: 100%;
            border-radius: 5px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
        }

        .leaflet-control-geocoder {
            padding: 5px !important;
            border-radius: 5px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.2);
        }

        .leaflet-control-geocoder-icon {
            display: none !important;
        }

        .btn-submit {
            background: #007bff;
            color: white;
            padding: 12px;
            font-size: 1rem;
            border-radius: 5px;
            border: none;
            width: 100%;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-submit:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .table-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .table-header {
            font-size: 1.5rem;
            font-weight: 500;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background: #007bff;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .table tbody tr {
            transition: background 0.3s ease;
        }

        .table tbody tr:hover {
            background: #f1f3f5;
        }

        .table td {
            padding: 12px;
            font-size: 0.85rem;
            vertical-align: middle;
        }

        .table img {
            border-radius: 4px;
            object-fit: cover;
        }

        .btn-action {
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: 4px;
            transition: transform 0.2s ease;
        }

        .btn-action:hover {
            transform: translateY(-1px);
        }

        @media screen and (max-width: 768px) {
            .dashboard-content {
                margin-left: 60px;
            }

            .form-container,
            .table-container {
                padding: 15px;
                margin: 0 10px;
            }

            .form-group {
                min-width: 100%;
            }

            .table th,
            .table td {
                font-size: 0.8rem;
                padding: 8px;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-content" id="dashboard-content">
        <!-- Add Destination Form -->
        <div class="form-container">
            <h3 class="form-header">Add New Destination</h3>
            <form action="add_destination.php" class="form" method="POST" enctype="multipart/form-data">
                <div class="form-group full-width">
                    <label for="name">Destination Name</label>
                    <input type="text" name="name" id="name" placeholder="Enter destination..." required>
                </div>
                <div class="form-group">
                    <label for="location">Location (Latitude, Longitude)</label>
                    <input type="text" id="location" name="location" placeholder="Click on map to set location" required
                        readonly>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" name="category" id="category" placeholder="e.g., Beach, Mountain, City..."
                        required>
                </div>
                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" name="image" id="image" accept="image/*" required>
                </div>
                <div class="form-group full-width">
                    <label>Map Location</label>
                    <div id="map"></div>
                </div>
                <button type="submit" class="btn-submit">Add Destination</button>
            </form>
        </div>

        <!-- Destination Table -->
        <div class="table-container">
            <h3 class="table-header">All Destinations</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Type</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM destinations WHERE isDelete = 0 ORDER BY destination_id DESC");
                    if ($result->num_rows > 0) {
                        $i = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>$i</td>
                                <td>" . htmlspecialchars($row['name']) . "</td>
                                <td>" . htmlspecialchars($row['location']) . "</td>
                                <td><img src='../uploads/" . htmlspecialchars($row['image']) . "' width='100' height='70' alt='Destination Image'></td>
                                <td>
                                    <a href='update_destination.php?id={$row['destination_id']}' class='btn btn-warning btn-sm btn-action'>
                                        <i class='fa fa-edit me-1'></i>Update
                                    </a>
                                    <a href='?delete_id={$row['destination_id']}' class='btn btn-danger btn-sm btn-action' onclick='return confirm(\"Are you sure to delete this destination?\")'>
                                        <i class='fa fa-trash me-1'></i>Delete
                                    </a>
                                </td>
                            </tr>";
                            $i++;
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center text-muted'>No destinations found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../admin/footer.php'; ?>

    <!-- Leaflet.js Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toggleButton = document.getElementById('sidebar-toggle');
            const dashboardContent = document.getElementById('dashboard-content');

            toggleButton.addEventListener('click', () => {
                requestAnimationFrame(() => {
                    dashboardContent.classList.toggle('collapsed');
                });
            });

            let map;
            let marker;

            function initMap() {
                let defaultLocation = [11.5564, 104.9282]; // Phnom Penh, Cambodia
                map = L.map('map').setView(defaultLocation, 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                marker = L.marker(defaultLocation, { draggable: true }).addTo(map);
                document.getElementById("location").value = defaultLocation.join(", ");

                marker.on('dragend', function () {
                    let position = marker.getLatLng();
                    document.getElementById("location").value = position.lat + ", " + position.lng;
                });

                map.on('click', function (event) {
                    let position = event.latlng;
                    marker.setLatLng(position);
                    document.getElementById("location").value = position.lat + ", " + position.lng;
                });

                L.Control.geocoder({
                    placeholder: "Search location...",
                    collapsed: false,
                    defaultMarkGeocode: false,
                }).on('markgeocode', function (event) {
                    let center = event.geocode.center;
                    map.setView(center, 13);
                    marker.setLatLng(center);
                    document.getElementById("location").value = center.lat + ", " + center.lng;
                }).addTo(map);
            }

            initMap();
        });
    </script>
</body>

</html>