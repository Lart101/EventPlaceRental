<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_store";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Function to handle file upload
function uploadImage($file) {
    $target_dir = "uploads/";
    // Create uploads folder if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $target_file = $target_dir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }
    
    // Check file size
    if ($file["size"] > 5000000) {
        $uploadOk = 0;
    }
    
    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif") {
        $uploadOk = 0;
    }
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        return false;
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $target_file;
        } else {
            return false;
        }
    }
}

// Create operation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create"])) {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $location = $_POST["location"];
    $price_per_day = $_POST["price_per_day"];
    $image = uploadImage($_FILES["image"]);

    if ($image) {
        $sql = "INSERT INTO event_places (name, description, location, price_per_day, image)
                VALUES ('$name', '$description', '$location', '$price_per_day', '$image')";

        if ($conn->query($sql) === TRUE) {
            $message = "New place created successfully";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $message = "Error uploading image.";
    }
}

// Update operation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $description = $_POST["description"];
    $location = $_POST["location"];
    $price_per_day = $_POST["price_per_day"];
    
    // Check if a new image is uploaded
    if (!empty($_FILES["image"]["name"])) {
        $image = uploadImage($_FILES["image"]);
        if ($image) {
            $sql = "UPDATE event_places SET name='$name', description='$description', location='$location', price_per_day='$price_per_day', image='$image' WHERE id=$id";
        } else {
            $message = "Error uploading image.";
        }
    } else {
        $sql = "UPDATE event_places SET name='$name', description='$description', location='$location', price_per_day='$price_per_day' WHERE id=$id";
    }

    if ($conn->query($sql) === TRUE) {
        $message = "Place updated successfully";
    } else {
        $message = "Error updating place: " . $conn->error;
    }
}

// Delete operation
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM event_places WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        $message = "Place deleted successfully";
    } else {
        $message = "Error deleting place: " . $conn->error;
    }
}

// Fetch event places
$event_places = $conn->query("SELECT * FROM event_places");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <div class="container">
        <div class="form-left">
            <div class="title">Admin Panel</div>
        
            <?php if ($message != "") { ?>
                <div class='message'><?php echo $message; ?></div>
            <?php } ?>

            <div class="event-place-list">
                <div class="search-create-container row mb-3">
                    <div class="search-container col-md-8 d-flex">
                        <input type="text" id="searchInput" class="form-control mr-2" placeholder="Search for event places...">
                        <button type="button" id="searchButton" class="btn btn-primary btn-custom-large">Search</button>
                    </div>
                    <div class="col-md-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-success btn-custom-large" data-toggle="modal" data-target="#createEventPlaceModal">Create Place</button>
                    </div>
                </div>

                <table class="table" id="eventPlaceTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Location</th>
                            <th>Price Per Day</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($event_place = $event_places->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $event_place['id']; ?></td>
                                <td><?php echo $event_place['name']; ?></td>
                                <td><?php echo $event_place['description']; ?></td>
                                <td><?php echo $event_place['location']; ?></td>
                                <td><?php echo $event_place['price_per_day']; ?></td>
                                <td><img src="<?php echo $event_place['image']; ?>" alt="Image" style="width: 100px; height: 100px;"></td>
                                <td>
                                    <a href="#" class="edit-event-place" data-id="<?php echo $event_place['id']; ?>" data-name="<?php echo $event_place['name']; ?>" data-description="<?php echo $event_place['description']; ?>" data-location="<?php echo $event_place['location']; ?>" data-price_per_day="<?php echo $event_place['price_per_day']; ?>" data-image="<?php echo $event_place['image']; ?>" data-toggle="modal" data-target="#editEventPlaceModal">Edit</a>
                                    <a href="#" class="delete-event-place" data-id="<?php echo $event_place['id']; ?>">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Create Event Place Modal -->
            <div class="modal fade" id="createEventPlaceModal" tabindex="-1" role="dialog" aria-labelledby="createEventPlaceModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createEventPlaceModalLabel">Create New Event Place</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="createEventPlaceForm" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="create" value="1">
                                
                                <label for="name">Name:</label>
                                <input type="text" id="name" name="name" required>
                                
                                <label for="description">Description:</label>
                                <input type="text" id="description" name="description" required>
                                
                                <label for="location">Location:</label>
                                <input type="text" id="location" name="location" required>
                                
                                <label for="price_per_day">Price Per Day:</label>
                                <input type="number" id="price_per_day" name="price_per_day" required>
                                
                                <label for="image">Image:</label>
                                <input type="file" id="image" name="image" required>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Create Place</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Event Place Modal -->
            <div class="modal fade" id="editEventPlaceModal" tabindex="-1" role="dialog" aria-labelledby="editEventPlaceModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editEventPlaceModalLabel">Edit Event Place</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="editEventPlaceForm" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="update" value="1">
                                <input type="hidden" id="edit_event_place_id" name="id">

                                <label for="edit_name">Name:</label>
                                <input type="text" id="edit_name" name="name" required>

                                <label for="edit_description">Description:</label>
                                <input type="text" id="edit_description" name="description" required>

                                <label for="edit_location">Location:</label>
                                <input type="text" id="edit_location" name="location" required>

                                <label for="edit_price_per_day">Price Per Day:</label>
                                <input type="number" id="edit_price_per_day" name="price_per_day" required>

                                <label for="edit_image">Image:</label>
                                <input type="file" id="edit_image" name="image">

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#searchButton').click(function () {
                var searchText = $('#searchInput').val().toLowerCase();
                $('#eventPlaceTable tbody tr').each(function () {
                    var found = false;
                    $(this).each(function () {
                        if ($(this).text().toLowerCase().indexOf(searchText) !== -1) {
                            found = true;
                            return false;
                        }
                    });
                    found ? $(this).show() : $(this).hide();
                });
            });

            $('.delete-event-place').click(function () {
                var eventPlaceId = $(this).data('id');
                if (confirm('Are you sure you want to delete this place?')) {
                    window.location.href = 'admin_panel.php?delete=' + eventPlaceId;
                }
            });

            $('.edit-event-place').click(function () {
                var eventPlaceId = $(this).data('id');
                var name = $(this).data('name');
                var description = $(this).data('description');
                var location = $(this).data('location');
                var pricePerDay = $(this).data('price_per_day');
                var image = $(this).data('image');

                $('#edit_event_place_id').val(eventPlaceId);
                $('#edit_name').val(name);
                $('#edit_description').val(description);
                $('#edit_location').val(location);
                $('#edit_price_per_day').val(pricePerDay);
                $('#edit_image').val(image);
            });
        });
    </script>
</body>
</html>
