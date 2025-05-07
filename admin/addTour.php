<?php
include "../includes/config.php";
include "../includes/admin_header.php";

// Fetch all destinations for dropdown
$destinations = $conn->query("SELECT * FROM destinations");

// Handle Tour Deletion
if (isset($_GET["delete"])) {
    $tour_id = intval($_GET["delete"]);
    $stmt = $conn->prepare("DELETE FROM tours WHERE tour_id = ?");
    $stmt->bind_param("i", $tour_id);
    if ($stmt->execute()) {
        echo "<script>alert('Tour deleted successfully!'); window.location.href='addTour.php';</script>";
    }
}

// Handle Tour Insertion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_tour"])) {
    $destination_id = intval($_POST["destination_id"]);
    $title = htmlspecialchars(trim($_POST["title"]));
    $description = htmlspecialchars(trim($_POST["description"]));
    $type = htmlspecialchars(trim($_POST["type"]));
    $price = floatval($_POST["price"]);
    $duration = htmlspecialchars(trim($_POST["duration"]));
    $primary_image = null;

    // Handle primary image upload
    if (!empty($_FILES["primary_image"]["name"])) {
        $image = $_FILES["primary_image"]["name"];
        $image_tmp = $_FILES["primary_image"]["tmp_name"];
        $image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];

        if (in_array($image_ext, $allowed_extensions)) {
            $primary_image = uniqid("tour_", true) . "." . $image_ext;
            $upload_path = "../Uploads/" . $primary_image;
            move_uploaded_file($image_tmp, $upload_path);
        } else {
            echo "<script>alert('Invalid file type for primary image! Only JPG, JPEG, PNG, and GIF are allowed.');</script>";
        }
    }

    // Insert tour into database
    $stmt = $conn->prepare("INSERT INTO tours (destination_id, title, description, type, price, duration, image, isDeleted) VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("isssdss", $destination_id, $title, $description, $type, $price, $duration, $primary_image);

    if ($stmt->execute()) {
        $tour_id = $conn->insert_id;

        // Handle additional image uploads
        if (!empty($_FILES["images"]["name"][0])) {
            $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
            foreach ($_FILES["images"]["name"] as $key => $image_name) {
                if (!empty($image_name)) {
                    $image_tmp = $_FILES["images"]["tmp_name"][$key];
                    $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                    $image_description = htmlspecialchars(trim($_POST["image_descriptions"][$key]));

                    if (in_array($image_ext, $allowed_extensions)) {
                        $new_image_name = uniqid("tour_", true) . "." . $image_ext;
                        $upload_path = "../Uploads/" . $new_image_name;
                        if (move_uploaded_file($image_tmp, $upload_path)) {
                            $stmt = $conn->prepare("INSERT INTO tour_images (tour_id, image_path, description) VALUES (?, ?, ?)");
                            $stmt->bind_param("iss", $tour_id, $new_image_name, $image_description);
                            $stmt->execute();
                        }
                    } else {
                        echo "<script>alert('Invalid file type for image $image_name! Only JPG, JPEG, PNG, and GIF are allowed.');</script>";
                    }
                }
            }
        }
        echo "<script>alert('Tour added successfully!'); window.location.href='addTour.php';</script>";
    } else {
        echo "<script>alert('Error adding tour!');</script>";
    }
}

// Handle Tour Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_tour"])) {
    $tour_id = intval($_POST["tour_id"]);
    $destination_id = intval($_POST["destination_id"]);
    $title = htmlspecialchars(trim($_POST["title"]));
    $description = htmlspecialchars(trim($_POST["description"]));
    $type = htmlspecialchars(trim($_POST["type"]));
    $price = floatval($_POST["price"]);
    $duration = htmlspecialchars(trim($_POST["duration"]));

    // Handle primary image update
    $primary_image = $_POST["current_primary_image"];
    if (!empty($_FILES["primary_image"]["name"])) {
        $image = $_FILES["primary_image"]["name"];
        $image_tmp = $_FILES["primary_image"]["tmp_name"];
        $image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];

        if (in_array($image_ext, $allowed_extensions)) {
            $primary_image = uniqid("tour_", true) . "." . $image_ext;
            $upload_path = "../Uploads/" . $primary_image;
            move_uploaded_file($image_tmp, $upload_path);
            // Optionally delete old image
            $old_image = $_POST["current_primary_image"];
            if ($old_image && file_exists("../Uploads/" . $old_image)) {
                unlink("../Uploads/" . $old_image);
            }
        } else {
            echo "<script>alert('Invalid file type for primary image! Only JPG, JPEG, PNG, and GIF are allowed.');</script>";
        }
    }

    // Update tour details
    $stmt = $conn->prepare("UPDATE tours SET destination_id = ?, title = ?, description = ?, type = ?, price = ?, duration = ?, image = ? WHERE tour_id = ?");
    $stmt->bind_param("isssdssi", $destination_id, $title, $description, $type, $price, $duration, $primary_image, $tour_id);
    $stmt->execute();

    // Handle additional image uploads
    if (!empty($_FILES["images"]["name"][0])) {
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
        foreach ($_FILES["images"]["name"] as $key => $image_name) {
            if (!empty($image_name)) {
                $image_tmp = $_FILES["images"]["tmp_name"][$key];
                $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                $image_description = htmlspecialchars(trim($_POST["image_descriptions"][$key]));

                if (in_array($image_ext, $allowed_extensions)) {
                    $new_image_name = uniqid("tour_", true) . "." . $image_ext;
                    $upload_path = "../Uploads/" . $new_image_name;
                    if (move_uploaded_file($image_tmp, $upload_path)) {
                        $stmt = $conn->prepare("INSERT INTO tour_images (tour_id, image_path, description) VALUES (?, ?, ?)");
                        $stmt->bind_param("iss", $tour_id, $new_image_name, $image_description);
                        $stmt->execute();
                    }
                } else {
                    echo "<script>alert('Invalid file type for image $image_name! Only JPG, JPEG, PNG, and GIF are allowed.');</script>";
                }
            }
        }
    }

    // Handle image deletions
    if (!empty($_POST["delete_images"])) {
        $delete_ids = array_map('intval', $_POST["delete_images"]);
        $stmt = $conn->prepare("DELETE FROM tour_images WHERE image_id IN (" . implode(',', array_fill(0, count($delete_ids), '?')) . ")");
        $stmt->bind_param(str_repeat('i', count($delete_ids)), ...$delete_ids);
        $stmt->execute();
    }

    echo "<script>alert('Tour updated successfully!'); window.location.href='addTour.php';</script>";
}

// Fetch all tours with the primary image
$tours = $conn->query("SELECT t.*, d.name AS destination 
                       FROM tours t 
                       JOIN destinations d ON t.destination_id = d.destination_id 
                       WHERE t.isDeleted = 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tours</title>
    <link rel="stylesheet" href="../assets/style.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
body {
    font-family: 'Work Sans', sans-serif;
    margin: 0;
    padding: 0;
    position: relative;
}
.dashboard {
    position: relative;
    min-height: 100vh;
}
.sidebar {
    width: 250px;
    height: 100vh;
    background: rgba(0, 0, 0, 0.91);
    color: white;
    position: fixed;
    left: 0;
    top: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 20px;
    transition: width 0.3s ease;
    z-index: 99999999999;
    box-shadow: rgba(0, 0, 0, 0.3) 0px 19px 38px, rgba(0, 0, 0, 0.22) 0px 15px 12px;
}
.table {
    width: calc(100% - 250px);
    margin-left: 250px;
    padding: 20px;
    background: white;
    position: relative;
}
@media screen and (max-width: 768px) {
    .sidebar {
        width: 60px;
    }
    .table {
        width: calc(100% - 60px);
        margin-left: 60px;
    }
}
.modal-body label {
    font-weight: bold;
    color: #555;
    margin-top: 10px;
}
.modal-body .form-control {
    margin-bottom: 15px;
}
.image-upload-group {
    margin-bottom: 15px;
}
.image-upload-group .form-control {
    margin-bottom: 5px;
}
</style>
<body>
<ul style="position: relative;" class="d-flex justify-content-around align-items-center p-3">
    <button style="position:absolute; right:2rem;top:6px" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTourModal">
        + Add Tour
    </button>
</ul>
<div class="dashboard">
<table class="table table-striped">
    <tr>
        <th>ID</th>
        <th>Destination</th>
        <th>Title</th>
        <th>Description</th>
        <th>Type</th>
        <th>Price</th>
        <th>Duration</th>
        <th>Image</th>
        <th>Actions</th>
    </tr>
    <?php while ($tour = $tours->fetch_assoc()): ?>
    <tr>
        <td><?= $tour["tour_id"] ?></td>
        <td><?= htmlspecialchars($tour["destination"]) ?></td>
        <td><?= htmlspecialchars($tour["title"]) ?></td>
        <td><?= htmlspecialchars($tour["description"]) ?></td>
        <td><?= htmlspecialchars($tour["type"]) ?></td>
        <td>$<?= number_format($tour["price"], 2) ?></td>
        <td><?= htmlspecialchars($tour["duration"]) ?></td>
        <td>
            <?php if ($tour["image"]): ?>
                <img src="../Uploads/<?= htmlspecialchars($tour["image"]) ?>" width="50" alt="Tour Image">
            <?php else: ?>
                No Image
            <?php endif; ?>
        </td>
        <td>
            <a href="#" class="btn btn-success btn-sm edit-btn" 
               data-id="<?= $tour["tour_id"] ?>" 
               data-destination-id="<?= $tour["destination_id"] ?>"
               data-title="<?= htmlspecialchars($tour["title"]) ?>" 
               data-description="<?= htmlspecialchars($tour["description"]) ?>"
               data-type="<?= htmlspecialchars($tour["type"]) ?>"
               data-price="<?= $tour["price"] ?>"
               data-duration="<?= htmlspecialchars($tour["duration"]) ?>"
               data-image="<?= htmlspecialchars($tour["image"]) ?>"
               data-bs-toggle="modal" data-bs-target="#editModal">Edit</a>
            <a href="?delete=<?= $tour["tour_id"] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
</div>
<!-- Add Tour Modal -->
<div class="modal fade" id="addTourModal" tabindex="-1" aria-labelledby="addTourModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTourModalLabel">Add New Tour</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="destination_id" class="form-label">Destination</label>
                        <select name="destination_id" id="destination_id" class="form-control" required>
                            <option value="">Select a Destination</option>
                            <?php 
                            $destinations->data_seek(0);
                            while ($row = $destinations->fetch_assoc()): ?>
                                <option value="<?= $row["destination_id"] ?>"><?= htmlspecialchars($row["name"]) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tourTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="tourTitle" name="title" placeholder="Enter title of tour..." required>
                    </div>
                    <div class="mb-3">
                        <label for="tourDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="tourDescription" name="description" placeholder="Enter detail of tour..." rows="1" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="tourType" class="form-label">Type</label>
                        <input type="text" class="form-control" id="tourType" placeholder="hotel, sea, island, temple..." name="type" required>
                    </div>
                    <div class="mb-3">
                        <label for="tourPrice" class="form-label">Price</label>
                        <input type="number" class="form-control" id="tourPrice" name="price" min="0" step="0.01" placeholder="A person/day..." required>
                    </div>
                    <div class="mb-3">
                        <label for="tourDuration" class="form-label">Duration (e.g., "2 days")</label>
                        <input type="text" class="form-control" id="tourDuration" name="duration" required>
                    </div>
                    <div class="mb-3">
                        <label for="primary_image" class="form-label">Primary Image (Required)</label>
                        <input type="file" class="form-control" id="primary_image" name="primary_image" accept="image/*" required>
                    </div>
                    <div class="mb-3 image-upload-group">
                        <label class="form-label">Additional Images (Optional)</label>
                        <div id="image-inputs">
                            <div class="input-group mb-2">
                                <input type="file" class="form-control" name="images[]" accept="image/*">
                                <textarea class="form-control" name="image_descriptions[]" placeholder="Image description" rows="1"></textarea>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addImageInput()">Add Another Image</button>
                    </div>
                    <button type="submit" class="btn btn-success w-100" name="add_tour">Save Tour</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Edit Tour Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Tour</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tour_id" id="edit_tour_id">
                    <input type="hidden" name="current_primary_image" id="current_primary_image">
                    <label for="edit_destination_id">Destination:</label>
                    <select name="destination_id" id="edit_destination_id" class="form-control" required>
                        <?php 
                        $destinations->data_seek(0);
                        while ($row = $destinations->fetch_assoc()): ?>
                            <option value="<?= $row["destination_id"] ?>"><?= htmlspecialchars($row["name"]) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <label for="edit_title">Title:</label>
                    <input type="text" name="title" id="edit_title" class="form-control" required>
                    <label for="edit_description">Description:</label>
                    <textarea name="description" id="edit_description" class="form-control" rows="3" required></textarea>
                    <label for="edit_tourType">Type:</label>
                    <input type="text" name="type" id="edit_tourType" class="form-control" required>
                    <label for="edit_price">Price:</label>
                    <input type="number" name="price" id="edit_price" class="form-control" step="0.01" required>
                    <label for="edit_duration">Duration:</label>
                    <input type="text" name="duration" id="edit_duration" class="form-control" required>
                    <label>Current Primary Image:</label>
                    <img id="current_primary_image_display" src="" width="100" class="mb-2" alt="Current Primary Image">
                    <label for="primary_image">New Primary Image (Optional):</label>
                    <input type="file" name="primary_image" id="primary_image" class="form-control" accept="image/*">
                    <label>Current Additional Images:</label>
                    <div id="current-images" class="mb-3"></div>
                    <label>New Additional Images (Optional):</label>
                    <div id="edit-image-inputs" class="image-upload-group">
                        <div class="input-group mb-2">
                            <input type="file" class="form-control" name="images[]" accept="image/*">
                            <textarea class="form-control" name="image_descriptions[]" placeholder="Image description" rows="1"></textarea>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addEditImageInput()">Add Another Image</button>
                    <button type="submit" name="update_tour" class="btn btn-success w-100 mt-3">Update Tour</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".edit-btn").forEach(button => {
        button.addEventListener("click", function () {
            document.getElementById("edit_tour_id").value = this.getAttribute("data-id");
            document.getElementById("edit_destination_id").value = this.getAttribute("data-destination-id");
            document.getElementById("edit_title").value = this.getAttribute("data-title");
            document.getElementById("edit_description").value = this.getAttribute("data-description");
            document.getElementById("edit_tourType").value = this.getAttribute("data-type");
            document.getElementById("edit_price").value = this.getAttribute("data-price");
            document.getElementById("edit_duration").value = this.getAttribute("data-duration");
            const imageSrc = this.getAttribute("data-image");
            document.getElementById("current_primary_image").value = imageSrc;
            document.getElementById("current_primary_image_display").src = imageSrc ? "../Uploads/" + imageSrc : "";
            fetchImages(this.getAttribute("data-id"));
        });
    });
});
function addImageInput() {
    const container = document.getElementById("image-inputs");
    const div = document.createElement("div");
    div.className = "input-group mb-2";
    div.innerHTML = `
        <input type="file" class="form-control" name="images[]" accept="image/*">
        <textarea class="form-control" name="image_descriptions[]" placeholder="Image description" rows="1"></textarea>
    `;
    container.appendChild(div);
}
function addEditImageInput() {
    const container = document.getElementById("edit-image-inputs");
    const div = document.createElement("div");
    div.className = "input-group mb-2";
    div.innerHTML = `
        <input type="file" class="form-control" name="images[]" accept="image/*">
        <textarea class="form-control" name="image_descriptions[]" placeholder="Image description" rows="1"></textarea>
    `;
    container.appendChild(div);
}
function fetchImages(tour_id) {
    $.ajax({
        url: 'fetch_images.php',
        method: 'GET',
        data: { tour_id: tour_id },
        success: function (data) {
            const images = JSON.parse(data);
            const container = document.getElementById("current-images");
            container.innerHTML = '';
            images.forEach(image => {
                const div = document.createElement("div");
                div.className = "mb-2";
                div.innerHTML = `
                    <img src="../Uploads/${image.image_path}" width="100" alt="Tour Image">
                    <p>${image.description || 'No description'}</p>
                    <input type="checkbox" name="delete_images[]" value="${image.image_id}"> Delete
                `;
                container.appendChild(div);
            });
        }
    });
}
document.getElementById('tourDuration').value = 1;
</script>
</body>
</html>
<?php include "../admin/footer.php"; ?>