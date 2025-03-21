<?php
include '../includes/admin_header.php';
include '../includes/config.php'; // Database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];

    // Upload Image
    $target_dir = "../assets/uploads/";
    $target_file = $target_dir . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

    // Use Prepared Statement for Security
    $stmt = $conn->prepare("INSERT INTO destinations (name, location, description, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $location, $description, $target_file);

    if ($stmt->execute()) {
        echo "<script>alert('Destination added successfully!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<style>
/* Form Styling */
.form {
    width: 1000px;  /* Wider Form */
    height: 500px; /* Fixed Height */
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

/* Inputs & Textarea */
input[type="text"], textarea, input[type="file"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
}

input:focus, textarea:focus {
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

/* Submit Button */
.btn {
    width: 100%;
    padding: 12px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    margin-top: 10px;
}

.btn:hover {
    background: #0056b3;
}

</style>

<form action="add_destination.php" class="form" method="POST" enctype="multipart/form-data">
    <h3>Add Destination</h3>

    <div class="form-group">
        <label for="name">Destination Name:</label>
        <input type="text" name="name" id="name" required>
    </div>

    <div class="form-group">
        <label for="location">Location:</label>
        <input type="text" id="location" name="location" required readonly>
    </div>

    <div class="full-width">
        <div id="map"></div>
    </div>

    <div class="form-group">
        <label for="description">Description:</label>
        <textarea name="description" id="description"></textarea>
    </div>

    <div class="form-group">
        <label for="image">Upload Image:</label>
        <input type="file" name="image" id="image" accept="image/*">
        <input type="submit" class="btn" value="Add Destination">

    </div>
</form>

<?php
include '../admin/footer.php';
?>

<!-- Load Leaflet.js (Free & No API Key Required) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Load Leaflet.js (Free & No API Key Required) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Load Leaflet Search Plugin (For Search Box) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
    let map;
    let marker;

    function initMap() {
        let defaultLocation = [11.5564, 104.9282]; // Default to Phnom Penh

        // Initialize the map
        map = L.map('map').setView(defaultLocation, 13);

        // Set OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
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
            defaultMarkGeocode: false
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

