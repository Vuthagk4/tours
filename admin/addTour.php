<?php

include "../includes/config.php";
include "../includes/admin_header.php";
// session_start();
// Check if the admin is logged in

//Consider Update
// if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
//     header("Location: ../login.php");
//     exit();
// }

// Fetch all destinations for dropdown
$destinations = $conn->query("SELECT * FROM destinations");

// Handle Tour Deletion
if (isset($_GET["delete"])) {
    $tour_id = intval($_GET["delete"]);
    $stmt = $conn->prepare("DELETE FROM tours WHERE tour_id = ?");
    $stmt->bind_param("i", $tour_id);
    if ($stmt->execute()) {
        echo "<script>alert('Tour deleted successfully!'); window.location.href='dashboard.php';</script>";
    }
}

// Handle Tour Addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_tour"])) {
    $tour_id = intval($_POST["tour_id"]);
    $destination_id = intval($_POST["destination_id"]);
    $title = htmlspecialchars(trim($_POST["title"]));
    $description = htmlspecialchars(trim($_POST["description"]));
    $price = floatval($_POST["price"]);
    $duration = htmlspecialchars(trim($_POST["duration"]));

    // Check if a new image is uploaded
    if (!empty($_FILES["image"]["name"])) {
        $image = $_FILES["image"]["name"];
        $image_tmp = $_FILES["image"]["tmp_name"];
        $image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];

        if (!in_array($image_ext, $allowed_extensions)) {
            die(
                "<script>alert('Invalid file type! Only JPG, JPEG, PNG, and GIF are allowed.');</script>"
            );
        }

        $new_image_name = uniqid("tour_", true) . "." . $image_ext;
        $upload_path = "../uploads/" . $new_image_name;
        move_uploaded_file($image_tmp, $upload_path);

        // Update with image
        $stmt = $conn->prepare(
            "UPDATE tours SET destination_id = ?, title = ?, description = ?, price = ?, duration = ?, image = ? WHERE tour_id = ?"
        );
        $stmt->bind_param(
            "issdssi",
            $destination_id,
            $title,
            $description,
            $price,
            $duration,
            $new_image_name,
            $tour_id
        );
    } else {
        // Update without image
        $stmt = $conn->prepare(
            "UPDATE tours SET destination_id = ?, title = ?, description = ?, price = ?, duration = ? WHERE tour_id = ?"
        );
        $stmt->bind_param(
            "issdsi",
            $destination_id,
            $title,
            $description,
            $price,
            $duration,
            $tour_id
        );
    }

    if ($stmt->execute()) {
        echo "<script>alert('Tour updated successfully!'); window.location.href='addTour.php';</script>";
    } else {
        echo "<script>alert('Error updating tour!');</script>";
    }
}

// Fetch all tours Join Table
$tours = $conn->query("SELECT tours.*, destinations.name AS destination FROM tours 
                        JOIN destinations ON tours.destination_id = destinations.destination_id");
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
    /* background-color: red; */
    min-height: 100vh; /* Ensures full height */
    /* padding: 20px; */
}

/* Sidebar */
.sidebar {
    width: 250px;
    height: 100vh;
    background:rgb(34, 49, 64);
    color: white;
    position: fixed;
    left: 0;
    top: 0;
    display: flex;
    flex-direction: column;
    padding-top: 20px;
}


/* Table inside the dashboard */
.table {
    width: calc(100% - 250px); /* Corrected Syntax */
    margin-left: 250px; /* Push it to the right of the sidebar */
    padding: 20px;
    background: white;
    position: relative; /* Prevent overlapping */
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

</style>
<body>

<ul style="position: relative;" class=" d-flex justify-content-around align-items-center p-3">
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
            <th>Price</th>
            <th>Duration</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php while ($tour = $tours->fetch_assoc()): ?>
        <tr>
            <td><?= $tour["tour_id"] ?></td>
            <td><?= $tour["destination"] ?></td>
            <td><?= $tour["title"] ?></td>
            <td><?= $tour["description"] ?></td>
            <td>$<?= $tour["price"] ?></td>
            <td><?= $tour["duration"] ?></td>
            <td><img src="../uploads/<?= $tour["image"] ?>" width="50"></td>
            <td>
            <a href="#" class="btn btn-success btn-sm edit-btn" 
    data-id="<?= $tour["tour_id"] ?>" 
    data-title="<?= $tour["title"] ?>" 
    data-description="<?= $tour["description"] ?>"
    data-price="<?= $tour["price"] ?>"
    data-duration="<?= $tour["duration"] ?>"
    data-image="<?= $tour["image"] ?>"
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
                <h5 class="modal-title" id="addTourModalLabel" style="position: relative;left:5rem">Add New Tour</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="destination_id" class="form-label">Destination</label>
                        <select name="destination_id" class="form-control" required>
                            <option value="">Select a Destination</option>
                            <?php while (
                                $row = $destinations->fetch_assoc()
                            ): ?>
                                <option value="<?= $row[
                                    "destination_id"
                                ] ?>"><?= $row["name"] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tourTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="tourTitle" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="tourDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="tourDescription" name="description" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="tourPrice" class="form-label">Price</label>
                        <input type="number" class="form-control" id="tourPrice" name="price" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label for="tourDuration" class="form-label">Duration</label>
                        <input type="text" class="form-control" id="tourDuration" name="duration" required>
                    </div>

                    <div class="mb-3">
                        <label for="tourImage" class="form-label">Image</label>
                        <input type="file" class="form-control" id="tourImage" name="image" accept="image/*" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Save Tour</button>
                    <!-- <button type="submit" name="add_tour" class="btn btn-success w-100">Save Tour</button> -->

                </form>
            </div>
        </div>
    </div>
</div>

<!-- Update Tour -->
 <!-- Edit Tour Modal -->
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

                    <label>Destination:</label>
                    <select name="destination_id" id="edit_destination_id" class="form-control" required>
                        <?php
                        $destinations->data_seek(0);
                        while ($row = $destinations->fetch_assoc()): ?>
                            <option value="<?= $row[
                                "destination_id"
                            ] ?>"><?= $row["name"] ?></option>
                        <?php endwhile;
                        ?>
                    </select>

                    <label>Title:</label>
                    <input type="text" name="title" id="edit_title" class="form-control" required>

                    <label>Description:</label>
                    <textarea name="description" id="edit_description" class="form-control" required></textarea>

                    <label>Price:</label>
                    <input type="number" name="price" id="edit_price" class="form-control" required>

                    <label>Duration:</label>
                    <input type="text" name="duration" id="edit_duration" class="form-control" required>

                    <label>Current Image:</label>
                    <img id="current_image" src="" width="100" class="mb-2">

                    <label>New Image (optional):</label>
                    <input type="file" name="image" class="form-control">

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
            document.getElementById("edit_title").value = this.getAttribute("data-title");
            document.getElementById("edit_description").value = this.getAttribute("data-description");
            document.getElementById("edit_price").value = this.getAttribute("data-price");
            document.getElementById("edit_duration").value = this.getAttribute("data-duration");
            document.getElementById("current_image").src = "../uploads/" + this.getAttribute("data-image");
        });
    });
});
</script>

</body>
</html>
<?php include "../admin/footer.php";
?>
