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
    <link rel="stylesheet" href="../assets/css/style1.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@200;300;400;500;600&display=swap" rel="stylesheet">
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

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn-add-tour {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-add-tour:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
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

        .modal-content {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: #007bff;
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-body label {
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }

        .modal-body .form-control,
        .modal-body select,
        .modal-body textarea {
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 8px;
            font-size: 0.9rem;
            transition: border-color 0.3s ease;
        }

        .modal-body .form-control:focus,
        .modal-body select:focus,
        .modal-body textarea:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }

        .image-upload-group {
            margin-bottom: 20px;
        }

        .image-preview {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .image-preview img {
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .btn-add-image {
            background: #6c757d;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.85rem;
            transition: background 0.3s ease;
        }

        .btn-add-image:hover {
            background: #5a6268;
        }

        .btn-save {
            background: #28a745;
            color: white;
            padding: 10px;
            font-size: 1rem;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .btn-save:hover {
            background: #218838;
        }

        @media screen and (max-width: 768px) {
            .dashboard-content {
                margin-left: 60px;
            }

            .dashboard-header {
                flex-direction: column;
                gap: 10px;
            }

            .btn-add-tour {
                width: 100%;
                text-align: center;
            }

            .table-container {
                padding: 10px;
            }

            .table th,
            .table td {
                font-size: 0.8rem;
                padding: 8px;
            }

            .modal-dialog {
                margin: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-content" id="dashboard-content">
        <div class="dashboard-header">
            <h2 style="color: #333;">Manage Tours</h2>
            <button type="button" class="btn-add-tour" data-bs-toggle="modal" data-bs-target="#addTourModal">
                <i class="fa fa-plus me-2"></i>Add Tour
            </button>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
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
                </thead>
                <tbody>
                    <?php while ($tour = $tours->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $tour["tour_id"] ?></td>
                            <td><?= htmlspecialchars($tour["destination"]) ?></td>
                            <td><?= htmlspecialchars($tour["title"]) ?></td>
                            <td><?= htmlspecialchars($tour["description"]) ?></td>
                            <td><?= htmlspecialchars($tour["type"]) ?></td>
                            <td>$<?= number_format($tour["price"], 2) ?></td>
                            <td><?= htmlspecialchars($tour["duration"]) ?></td>
                            <td>
                                <?php if ($tour["image"]): ?>
                                    <img src="../Uploads/<?= htmlspecialchars($tour["image"]) ?>" width="50" height="50" alt="Tour Image">
                                <?php else: ?>
                                    <span class="text-muted">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="#" class="btn btn-success btn-sm btn-action edit-btn" data-id="<?= $tour["tour_id"] ?>"
                                    data-destination-id="<?= $tour["destination_id"] ?>"
                                    data-title="<?= htmlspecialchars($tour["title"]) ?>"
                                    data-description="<?= htmlspecialchars($tour["description"]) ?>"
                                    data-type="<?= htmlspecialchars($tour["type"]) ?>" 
                                    data-price="<?= $tour["price"] ?>"
                                    data-duration="<?= htmlspecialchars($tour["duration"]) ?>"
                                    data-image="<?= htmlspecialchars($tour["image"]) ?>" 
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal">
                                    <i class="fa fa-edit me-1"></i>Edit
                                </a>
                                <a href="?delete=<?= $tour["tour_id"] ?>" class="btn btn-danger btn-sm btn-action"
                                    onclick="return confirm('Are you sure?')">
                                    <i class="fa fa-trash me-1"></i>Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Add Tour Modal -->
        <div class="modal fade" id="addTourModal" tabindex="-1" aria-labelledby="addTourModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addTourModalLabel">Add New Tour</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <input type="text" class="form-control" id="tourTitle" name="title"
                                    placeholder="Enter title of tour..." required>
                            </div>
                            <div class="mb-3">
                                <label for="tourDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="tourDescription" name="description"
                                    placeholder="Enter detail of tour..." rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="tourType" class="form-label">Type</label>
                                <input type="text" class="form-control" id="tourType"
                                    placeholder="e.g., hotel, sea, island, temple..." name="type" required>
                            </div>
                            <div class="mb-3">
                                <label for="tourPrice" class="form-label">Price</label>
                                <input type="number" class="form-control" id="tourPrice" name="price" min="0"
                                    step="0.01" placeholder="Per person/day..." required>
                            </div>
                            <div class="mb-3">
                                <label for="tourDuration" class="form-label">Duration</label>
                                <input type="text" class="form-control" id="tourDuration" name="duration"
                                    placeholder="e.g., 2 days" required>
                            </div>
                            <div class="mb-3">
                                <label for="primary_image" class="form-label">Primary Image (Required)</label>
                                <input type="file" class="form-control" id="primary_image" name="primary_image"
                                    accept="image/*" required>
                            </div>
                            <div class="image-upload-group">
                                <label class="form-label">Additional Images (Optional)</label>
                                <div id="image-inputs">
                                    <div class="input-group mb-2">
                                        <input type="file" class="form-control" name="images[]" accept="image/*">
                                        <textarea class="form-control" name="image_descriptions[]"
                                            placeholder="Image description" rows="2"></textarea>
                                    </div>
                                </div>
                                <button type="button" class="btn-add-image" onclick="addImageInput()">
                                    <i class="fa fa-plus me-1"></i>Add Another Image
                                </button>
                            </div>
                            <button type="submit" class="btn-save w-100" name="add_tour">Save Tour</button>
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="tour_id" id="edit_tour_id">
                            <input type="hidden" name="current_primary_image" id="current_primary_image">
                            <div class="mb-3">
                                <label for="edit_destination_id">Destination</label>
                                <select name="destination_id" id="edit_destination_id" class="form-control" required>
                                    <?php
                                    $destinations->data_seek(0);
                                    while ($row = $destinations->fetch_assoc()): ?>
                                        <option value="<?= $row["destination_id"] ?>"><?= htmlspecialchars($row["name"]) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_title">Title</label>
                                <input type="text" name="title" id="edit_title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_description">Description</label>
                                <textarea name="description" id="edit_description" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="edit_tourType">Type</label>
                                <input type="text" name="type" id="edit_tourType" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_price">Price</label>
                            <input type="number" name="price" id="edit_price" class="form-control" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_duration">Duration</label>
                                <input type="text" name="duration" id="edit_duration" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Current Primary Image</label>
                                <div class="image-preview">
                                    <img id="current_primary_image_display" src="" width="100" height="100" alt="Current Primary Image">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="primary_image">New Primary Image (Optional)</label>
                                <input type="file" name="primary_image" id="primary_image" class="form-control" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label>Current Additional Images</label>
                                <div id="current-images" class="image-preview"></div>
                            </div>
                            <div class="image-upload-group">
                                <label>New Additional Images (Optional)</label>
                                <div id="edit-image-inputs">
                                    <div class="input-group mb-2">
                                        <input type="file" class="form-control" name="images[]" accept="image/*">
                                        <textarea class="form-control" name="image_descriptions[]" placeholder="Image description" rows="2"></textarea>
                                    </div>
                                </div>
                                <button type="button" class="btn-add-image" onclick="addEditImageInput()">
                                    <i class="fa fa-plus me-1"></i>Add Another Image
                                </button>
                            </div>
                            <button type="submit" name="update_tour" class="btn-save w-100">Update Tour</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include "../admin/footer.php"; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <textarea class="form-control" name="image_descriptions[]" placeholder="Image description" rows="2"></textarea>
            `;
            container.appendChild(div);
        }

        function addEditImageInput() {
            const container = document.getElementById("edit-image-inputs");
            const div = document.createElement("div");
            div.className = "input-group mb-2";
            div.innerHTML = `
                <input type="file" class="form-control" name="images[]" accept="image/*">
                <textarea class="form-control" name="image_descriptions[]" placeholder="Image description" rows="2"></textarea>
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
                            <img src="../Uploads/${image.image_path}" width="100" height="100" alt="Tour Image">
                            <p class="text-muted mt-1">${image.description || 'No description'}</p>
                            <label><input type="checkbox" name="delete_images[]" value="${image.image_id}"> Delete this image</label>
                        `;
                        container.appendChild(div);
                    });
                }
            });
        }
    </script>
</body>
</html>