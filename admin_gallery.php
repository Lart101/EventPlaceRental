<?php
session_start();

// Check if admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Database connection (assuming config.php includes this)
require 'config.php';

$message = "";

// Handle Create or Update (handling both scenarios with one block)
if (isset($_POST['add']) || isset($_POST['update'])) {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Check if file is uploaded
    $imageUploaded = !empty($_FILES['image']['tmp_name']) && file_exists($_FILES['image']['tmp_name']);
    $image = $imageUploaded ? mysqli_real_escape_string($conn, file_get_contents($_FILES['image']['tmp_name'])) : null;

    if (isset($_POST['add'])) {
        $sql = "INSERT INTO gallery (title, description, image) VALUES ('$title', '$description', '$image')";
    } elseif (isset($_POST['update'])) {
        if ($imageUploaded) {
            $sql = "UPDATE gallery SET title='$title', description='$description', image='$image' WHERE id=$id";
        } else {
            $sql = "UPDATE gallery SET title='$title', description='$description' WHERE id=$id";
        }
    }

    if ($conn->query($sql) === TRUE) {
        if (isset($_POST['add'])) {
            $message = "New gallery item added successfully";
        } elseif (isset($_POST['update'])) {
            $message = "Gallery item updated successfully";
        }
        header("Location: admin_gallery.php");
        exit();
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Read gallery items
$galleryItems = $conn->query("SELECT * FROM gallery");

// Delete
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM gallery WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        $message = "Gallery item deleted successfully";
        header("Location: admin_gallery.php");
        exit();
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Function to display image from BLOB data
function displayImage($imageData, $title)
{
    $imageDataEncoded = base64_encode($imageData);
    return 'data:image/jpeg;base64,' . $imageDataEncoded;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style2.css">
</head>
<style>
    .btn {
        height: 80%;
        margin-bottom: 10px;
    }
</style>

<body>
    <?php include 'admin_navbar.php'; ?>

    <div class="container mt-5" style="width:800px;">
        <div class="row">
            <div class="col-md-8">
                <!-- Add New Gallery Item Modal -->
                <div class="modal fade" id="addGalleryItemModal" tabindex="-1" role="dialog"
                    aria-labelledby="addGalleryItemModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addGalleryItemModalLabel">Add New Gallery Item</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="admin_gallery.php" method="POST" enctype="multipart/form-data" id="add">
                                    <div class="form-group">
                                        <label for="title">Title:</label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description:</label>
                                        <textarea class="form-control" id="description" name="description"
                                            required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="galleryimage">Image (Max 1MB)</label>
                                        <input type="file" class="form-control-file" id="galleryimage"
                                            name="galleryimage" accept="image/*" required>
                                        <div id="galleryimage_error" class="invalid-feedback"></div>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="add">Add Gallery Item</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Update Gallery Item Modal -->
                <div class="modal fade" id="updateGalleryItemModal" tabindex="-1" role="dialog"
                    aria-labelledby="updateGalleryItemModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="updateGalleryItemModalLabel">Update Gallery Item</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="admin_gallery.php" method="POST" enctype="multipart/form-data"
                                    id="update">
                                    <input type="hidden" name="id" id="update_id">
                                    <div class="form-group">
                                        <label for="update_title">Title:</label>
                                        <input type="text" class="form-control" id="update_title" name="title" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="update_description">Description:</label>
                                        <textarea class="form-control" id="update_description" name="description"
                                            required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="update_image">Image (Max 1MB)</label>
                                        <input type="file" class="form-control-file" id="update_image" name="image"
                                            accept="image/*">
                                        <div id="galleryimageUpdate_error" class="invalid-feedbackUpdate"></div>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="update">Update Gallery
                                        Item</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        var form = document.getElementById("add"); // Get the form by ID

                        form.addEventListener("submit", function (event) {
                            var fileInput = document.getElementById("galleryimage");
                            if (fileInput.files.length > 0) {
                                var fileSize = fileInput.files[0].size; // Size in bytes

                                // Convert to megabytes (MB)
                                var maxSizeMB = 1;
                                var maxSizeBytes = maxSizeMB * 1024 * 1024;

                                if (fileSize > maxSizeBytes) {
                                    event.preventDefault(); // Prevent form submission
                                    var errorElement = document.getElementById("galleryimage_error");
                                    errorElement.textContent = "File size exceeds 1MB. Please choose a smaller file.";
                                    errorElement.classList.add("invalid-feedback");
                                    fileInput.classList.add("is-invalid");
                                }
                            }
                        });
                    });
                </script>

                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        var form = document.getElementById("update"); // Get the form by ID

                        form.addEventListener("submit", function (event) {
                            var fileInput = document.getElementById("update_image");
                            if (fileInput.files.length > 0) {
                                var fileSize = fileInput.files[0].size; // Size in bytes

                                // Convert to megabytes (MB)
                                var maxSizeMB = 1;
                                var maxSizeBytes = maxSizeMB * 1024 * 1024;

                                if (fileSize > maxSizeBytes) {
                                    event.preventDefault(); // Prevent form submission
                                    var errorElement = document.getElementById("galleryimageUpdate_error");
                                    errorElement.textContent = "File size exceeds 1MB. Please choose a smaller file.";
                                    errorElement.classList.add("invalid-feedback");
                                    fileInput.classList.add("is-invalid");
                                }
                            }
                        });
                    });
                </script>
                 <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        // Select the file input element
                        var proofOfPaymentInput = document.getElementById('update_image');

                        proofOfPaymentInput.addEventListener('change', function () {
                            var file = this.files[0];

                            
                            if (file.type.indexOf('image') === -1) {
                               
                                this.setCustomValidity('Please upload a valid image file.');
                                document.getElementById('galleryimageUpdate_error').textContent = 'Please upload a valid image file.';
                            } else {
                               
                                this.setCustomValidity('');
                                document.getElementById('galleryimageUpdate_error').textContent = '';
                            }
                        });
                    });
                </script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        // Select the file input element
                        var proofOfPaymentInput = document.getElementById('galleryimage');

                        proofOfPaymentInput.addEventListener('change', function () {
                            var file = this.files[0];

                            
                            if (file.type.indexOf('image') === -1) {
                               
                                this.setCustomValidity('Please upload a valid image file.');
                                document.getElementById('galleryimage_error').textContent = 'Please upload a valid image file.';
                            } else {
                               
                                this.setCustomValidity('');
                                document.getElementById('galleryimage_error').textContent = '';
                            }
                        });
                    });
                </script>


                <!-- Gallery Items Table -->
                <div class="row mt-5" style="width: 800px;">
                    <div class="col-md-12">
                        <h2>Gallery Items</h2>
                        <?php if ($message != "")
                            echo "<p>$message</p>"; ?>
                        <div class="search-create-container row mb-3">
                            <div class="search-container col-md-8 d-flex">
                                <input type="text" id="searchInput" class="form-control mr-2"
                                    placeholder="Search gallery items by title...">
                            </div>
                            <div class="col-md-4 d-flex justify-content-end">
                                <button type="button" class="btn btn-success" data-toggle="modal"
                                    data-target="#addGalleryItemModal">Add New Gallery Item</button>
                            </div>
                        </div>

                        <table class="table table-bordered mt-3" id="gallerytable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $galleryItems->fetch_assoc()): ?>
                                    <tr data-title="<?php echo strtolower($row['title']); ?>">
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                                        <td><img src="<?php echo displayImage($row['image'], $row['title']); ?>"
                                                alt="<?php echo htmlspecialchars($row['title']); ?>"
                                                style="max-width: 100px;"></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning editBtn"
                                                data-id="<?php echo $row['id']; ?>"
                                                data-title="<?php echo htmlspecialchars($row['title']); ?>"
                                                data-description="<?php echo htmlspecialchars($row['description']); ?>"
                                                data-toggle="modal" data-target="#updateGalleryItemModal">Update</button>
                                            <form action="admin_gallery.php" method="POST" style="display: inline;"
                                                class="deleteForm">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    name="delete">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const galleryTable = document.getElementById('gallerytable');
            const galleryRows = galleryTable.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            searchInput.addEventListener('keyup', function () {
                const searchTerm = searchInput.value.toLowerCase();
                Array.from(galleryRows).forEach(function (row) {
                    const title = row.getAttribute('data-title');
                    if (title.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            document.querySelectorAll('.editBtn').forEach(function (editButton) {
                editButton.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const title = this.getAttribute('data-title');
                    const description = this.getAttribute('data-description');
                    const image = this.getAttribute('data-image');

                    document.getElementById('update_id').value = id;
                    document.getElementById('update_title').value = title;
                    document.getElementById('update_description').value = description;
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const galleryTable = document.getElementById('gallerytable');
            const galleryRows = galleryTable.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            searchInput.addEventListener('keyup', function () {
                const searchTerm = searchInput.value.toLowerCase();
                Array.from(galleryRows).forEach(function (row) {
                    const title = row.getAttribute('data-title');
                    if (title.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });

        document.querySelectorAll('.deleteForm').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!confirm('Are you sure you want to delete this gallery item?')) {
                    event.preventDefault();
                }
            });
        });

        $(document).ready(function () {
            $('.editBtn').on('click', function () {
                var id = $(this).data('id');
                var title = $(this).data('title');
                var description = $(this).data('description');
                $('#update_id').val(id);
                $('#update_title').val(title);
                $('#update_description').val(description);
            });
        });

    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>