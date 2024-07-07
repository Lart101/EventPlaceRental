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

function uploadImage($file, $is_multiple = false) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if ($is_multiple) {
        $file_paths = [];
        foreach ($file["name"] as $key => $name) {
            $target_file = $target_dir . basename($name);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $check = getimagesize($file["tmp_name"][$key]);

            if ($check !== false && $file["size"][$key] <= 5000000 && in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
                if (move_uploaded_file($file["tmp_name"][$key], $target_file)) {
                    $file_paths[] = $target_file;
                }
            }
        }
        return $file_paths;
    } else {
        $target_file = $target_dir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($file["tmp_name"]);

        if ($check !== false && $file["size"] <= 5000000 && in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                return $target_file;
            }
        }
        return false;
    }
}

// Create operation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create"])) {
    $package_name = $_POST["package_name"];
    $price = $_POST["price"];
    $duration = $_POST["duration"];
    $max_pax = $_POST["max_pax"];
    $inclusions = $_POST["inclusions"];

    $profile_image = uploadImage($_FILES["profile_image"]);
    $multiple_images = uploadImage($_FILES["multiple_images"], true);

    if ($profile_image !== false && !empty($multiple_images)) {
        $multiple_images = implode(',', $multiple_images);
        $sql = "INSERT INTO swimming_packages (package_name, price, duration, max_pax, inclusions, profile_image, multiple_images)
                VALUES ('$package_name', '$price', '$duration', '$max_pax', '$inclusions', '$profile_image', '$multiple_images')";

        if ($conn->query($sql) === TRUE) {
            $message = "New package created successfully";
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
    $package_name = $_POST["package_name"];
    $price = $_POST["price"];
    $duration = $_POST["duration"];
    $max_pax = $_POST["max_pax"];
    $inclusions = $_POST["inclusions"];

    $profile_image = !empty($_FILES["profile_image"]["name"]) ? uploadImage($_FILES["profile_image"]) : false;
    $multiple_images = !empty($_FILES["multiple_images"]["name"][0]) ? uploadImage($_FILES["multiple_images"], true) : false;

    if ($profile_image !== false && !empty($multiple_images)) {
        $multiple_images = implode(',', $multiple_images);
        $sql = "UPDATE swimming_packages SET package_name='$package_name', price='$price', duration='$duration', max_pax='$max_pax', inclusions='$inclusions', profile_image='$profile_image', multiple_images='$multiple_images' WHERE id=$id";
    } elseif ($profile_image !== false) {
        $sql = "UPDATE swimming_packages SET package_name='$package_name', price='$price', duration='$duration', max_pax='$max_pax', inclusions='$inclusions', profile_image='$profile_image' WHERE id=$id";
    } elseif (!empty($multiple_images)) {
        $multiple_images = implode(',', $multiple_images);
        $sql = "UPDATE swimming_packages SET package_name='$package_name', price='$price', duration='$duration', max_pax='$max_pax', inclusions='$inclusions', multiple_images='$multiple_images' WHERE id=$id";
    } else {
        $sql = "UPDATE swimming_packages SET package_name='$package_name', price='$price', duration='$duration', max_pax='$max_pax', inclusions='$inclusions' WHERE id=$id";
    }

    if ($conn->query($sql) === TRUE) {
        $message = "Package updated successfully";
    } else {
        $message = "Error updating package: " . $conn->error;
    }
}

// Delete operation
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM swimming_packages WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        $message = "Package deleted successfully";
    } else {
        $message = "Error deleting package: " . $conn->error;
    }
}

// Fetch swimming packages
$swimming_packages = $conn->query("SELECT * FROM swimming_packages");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Swimming Packages</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <div class="container">
        <div class="form-left">
            <div class="title">Admin Panel - Swimming Packages</div>
        
            <?php if ($message != "") { ?>
                <div class='message'><?php echo $message; ?></div>
            <?php } ?>

            <div class="package-list">
                <div class="search-create-container row mb-3">
                    <div class="search-container col-md-8 d-flex">
                        <input type="text" id="searchInput" class="form-control mr-2" placeholder="Search for packages...">
                        <button type="button" id="searchButton" class="btn btn-primary btn-custom-large">Search</button>
                    </div>
                    <div class="col-md-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-success btn-custom-large" data-toggle="modal" data-target="#createPackageModal">Create Package</button>
                    </div>
                </div>

                <table class="table" id="packageTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Package Name</th>
            <th>Price</th>
            <th>Duration</th>
            <th>Max Pax</th>
            <th>Inclusions</th>
            <th>Profile Image</th>
            <th>Multiple Images</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($package = $swimming_packages->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $package['id']; ?></td>
                <td><?php echo $package['package_name']; ?></td>
                <td><?php echo $package['price']; ?></td>
                <td><?php echo $package['duration']; ?></td>
                <td><?php echo $package['max_pax']; ?></td>
                <td><?php echo $package['inclusions']; ?></td>
                <td><img src="<?php echo $package['profile_image']; ?>" alt="Profile Image" style="width: 100px; height: 100px;"></td>
                <td>
                    <?php
                    $images = explode(',', $package['multiple_images']);
                    foreach ($images as $image) {
                        echo "<img src='$image' alt='Multiple Image' style='width: 100px; height: 100px; margin-right: 5px;'>";
                    }
                    ?>
                </td>
                <td>
                    <button class="btn btn-primary btn-sm edit-package-btn" data-toggle="modal" data-target="#editPackageModal" data-id="<?php echo $package['id']; ?>" data-package_name="<?php echo $package['package_name']; ?>" data-price="<?php echo $package['price']; ?>" data-duration="<?php echo $package['duration']; ?>" data-max_pax="<?php echo $package['max_pax']; ?>" data-inclusions="<?php echo $package['inclusions']; ?>">Edit</button>
                    <a href="?delete=<?php echo $package['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

            </div>

            <!-- Create Package Modal -->
            <div class="modal fade" id="createPackageModal" tabindex="-1" role="dialog" aria-labelledby="createPackageModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createPackageModalLabel">Create New Package</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="createPackageForm" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="create" value="1">
                                
                                <label for="package_name">Package Name:</label>
                                <input type="text" id="package_name" name="package_name" class="form-control" required>

                                <label for="profile_image">Profile Image:</label>
                                <input type="file" id="profile_image" name="profile_image" required>

                                <label for="multiple_images">Multiple Images:</label>
                                <input type="file" id="multiple_images" name="multiple_images[]" multiple required>

                                <label for="price">Price:</label>
                                <input type="number" id="price" name="price" class="form-control" required>
                                
                                <label for="duration">Duration:</label>
                                <input type="text" id="duration" name="duration" class="form-control" required>
                                
                                <label for="max_pax">Max Pax:</label>
                                <input type="number" id="max_pax" name="max_pax" class="form-control" required>
                                
                                <label for="inclusions">Inclusions:</label>
                                <textarea id="inclusions" name="inclusions" class="form-control" required></textarea>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Create Package</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Package Modal -->
            <div class="modal fade" id="editPackageModal" tabindex="-1" role="dialog" aria-labelledby="editPackageModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editPackageModalLabel">Edit Package</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="editPackageForm" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="update" value="1">
                                <input type="hidden" id="edit_package_id" name="id">

                                <label for="edit_package_name">Package Name:</label>
                                <input type="text" id="edit_package_name" name="package_name" class="form-control" required>

                                <label for="edit_profile_image">Profile Image:</label>
                                <input type="file" id="edit_profile_image" name="profile_image">

                                <label for="edit_multiple_images">Multiple Images:</label>
                                <input type="file" id="edit_multiple_images" name="multiple_images[]" multiple>

                                <label for="edit_price">Price:</label>
                                <input type="number" id="edit_price" name="price" class="form-control" required>

                                <label for="edit_duration">Duration:</label>
                                <input type="text" id="edit_duration" name="duration" class="form-control" required>

                                <label for="edit_max_pax">Max Pax:</label>
                                <input type="number" id="edit_max_pax" name="max_pax" class="form-control" required>

                                <label for="edit_inclusions">Inclusions:</label>
                                <textarea id="edit_inclusions" name="inclusions" class="form-control" required></textarea>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Update Package</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).on("click", ".edit-package-btn", function() {
            var id = $(this).data('id');
            var package_name = $(this).data('package_name');
            var price = $(this).data('price');
            var duration = $(this).data('duration');
            var max_pax = $(this).data('max_pax');
            var inclusions = $(this).data('inclusions');

            $("#edit_package_id").val(id);
            $("#edit_package_name").val(package_name);
            $("#edit_price").val(price);
            $("#edit_duration").val(duration);
            $("#edit_max_pax").val(max_pax);
            $("#edit_inclusions").val(inclusions);
        });
    </script>
</body>
</html>
