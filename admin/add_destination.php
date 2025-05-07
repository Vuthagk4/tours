<?php
include '../includes/admin_header.php';
include '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $category = $_POST['category'];

    // Handle image upload
    $image = $_FILES['image'];
    $image_name = uniqid() . '_' . basename($image['name']);
    $upload_dir = '../uploads/';
    $upload_file = $upload_dir . $image_name;

    if (move_uploaded_file($image['tmp_name'], $upload_file)) {
        // Image uploaded successfully
    } else {
        echo "Error uploading image.";
        exit;
    }

    // Use Prepared Statement for Security
    $stmt = $conn->prepare("INSERT INTO destinations (name, location, type, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $location, $category, $image_name);

    if ($stmt->execute()) {
        echo "<script>alert('Destination added successfully!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<style>
body {
    font-family: 'Work Sans', sans-serif;
    margin: 0;
    padding: 0;
    position: relative;
    height: 120vh;
    overflow-y: scroll;
}
/* Form Styling */
.form {
    width: 800px;  /* Wider Form */
    height: auto; /* Adjusted Height since we removed fields */
    margin: 50px auto;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

/* Form Heading */
h3 {
    width: 100%;
    text-align: center;
    color: #333;
    margin-bottom: 15px;
}

/* Labels */
label {
    font-weight: bold;
    color: #555;
}

/* Inputs */
input[type="text"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
}

input:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

/* Flex Layout for Better Organization */
.form-group {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-width: 48%;
}

/* Full Width for Some Fields */
.full-width {
    width: 100%;
}

/* Map Styling */
#map {
    height: 300px;
    width: 100%;
    border-radius: 5px;
    margin-top: 10px;
}

/* Hide the search icon inside the Leaflet search box */
.leaflet-control-geocoder-icon {
    display: none !important;
}

/* Adjust padding to align text properly */
.leaflet-control-geocoder {
    padding: 5px !important;
}
</style>

<form style="box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;" action="add_destination.php" class="form" method="POST" enctype="multipart/form-data">
    <h3>Add Destination</h3>

    <div class="form-group w-100">
        <label for="name">Destination Name:</label>
        <input type="text" name="name" id="name" placeholder="Enter destination..." required>
    </div>

    <div class="form-group">
        <label for="location">Location:</label>
        <input type="text" id="location" name="location" placeholder="location" required>
    </div>

    <div class="form-group">
        <label for="category">Category:</label>
        <input type="text" name="category" id="category" placeholder="Enter category..." required>
    </div>

    <div class="form-group">
        <label for="image">Image:</label>
        <input type="file" name="image" id="image" accept="image/*" required>
    </div>

    <div class="full-width">
        <div id="map"></div>
    </div>

    <input type="submit" class="w-100 btn btn-primary" value="Add Destination">
</form>

<!-- Destination Table -->
<div class="container mt-4">
    <h3 class="text-center mb-3">All Destinations</h3>
    <table class="table table-bordered table-hover w-75" style="position:relative; right:1rem;">
        <thead class="table-dark">
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
                        <td>" . $i++ . "</td>
                        <td>{$row['name']}</td>
                        <td>{$row['location']}</td>
                        <td>{$row['type']}</td>
                        <td><img src='../uploads/{$row['image']}' width='100' height='70'></td>
                        <td>
                            <a href='update_destination.php?id={$row['destination_id']}' class='btn btn-sm btn-warning'>Update</a>
                            <a href='?delete_id={$row['destination_id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure to delete this destination?\")'>Delete</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No destinations found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
include '../admin/footer.php';
?>

<!-- Load Leaflet.js (Free & No API Key Required) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Load Leaflet Search Plugin (For Search Box) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
    let map;
    let marker;

    function initMap() {
        let defaultLocation = [11.5564, 104.9282];
        // Initialize the map
        map = L.map('map').setView(defaultLocation, 13);
        // Set OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        // Add a draggable marker
        marker = L.marker(defaultLocation, { draggable: true }).addTo(map);

        // Update input field when marker is dragged
        marker.on('dragend', function () {
            let position = marker.getLatLng();
            document.getElementById("location").value = position.lat + ", " + position.lng;
        });

        // Update marker position when clicking on the map
        map.on('click', function (event) {
            let position = event.latlng;
            marker.setLatLng(position);
            document.getElementById("location").value = position.lat + ", " + position.lng;
        });

        // Add Search Box (Uses OpenStreetMap's Free Geocoder)
        L.Control.geocoder({
            placeholder: "Search location...", // Custom Placeholder
            collapsed: false, // Always show the input box
            defaultMarkGeocode: false, // Prevent auto marker
        }).on('markgeocode', function (event) {
            let center = event.geocode.center;
            map.setView(center, 13);
            marker.setLatLng(center);
            document.getElementById("location").value = center.lat + ", " + center.lng;
        }).addTo(map);
    }

    // Initialize the map when the page loads
    document.addEventListener("DOMContentLoaded", initMap);
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>