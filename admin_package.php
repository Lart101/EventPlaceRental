<?php
session_start();

require 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

require 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

function uploadImage($file, $is_multiple = false)
{
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


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create"])) {
    $package_name = $_POST["package_name"];
    $price = $_POST["price"];
    $duration = $_POST["duration"];
    $max_pax = $_POST["max_pax"];
    $inclusions = $_POST["inclusions"];

    $profile_image = uploadImage($_FILES["profile_image"]);
    $multiple_images = uploadImage($_FILES["multiple_images"], true);
    $package_type = $_POST["package_type"];


    if ($profile_image !== false && !empty($multiple_images)) {
        $multiple_images = implode(',', $multiple_images);
        $sql = "INSERT INTO swimming_packages (package_name, price, duration, max_pax, inclusions, profile_image, multiple_images, package_type)
        VALUES ('$package_name', '$price', '$duration', '$max_pax', '$inclusions', '$profile_image', '$multiple_images', '$package_type')";


        if ($conn->query($sql) === TRUE) {
            $message = "New package created successfully";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $message = "Error uploading image.";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $id = $_POST["id"];
    $package_name = $_POST["package_name"];
    $price = $_POST["price"];
    $duration = $_POST["duration"];
    $max_pax = $_POST["max_pax"];
    $inclusions = $_POST["inclusions"];
    $package_type = $_POST["package_type"]; // Make sure to retrieve package_type

    // Handle profile image upload if a new file is selected
    if (!empty($_FILES["profile_image"]["name"])) {
        $profile_image = uploadImage($_FILES["profile_image"]);
        if ($profile_image === false) {
            $message = "Error uploading profile image.";
        }
    }

    // Handle multiple images upload if new files are selected
    if (!empty($_FILES["multiple_images"]["name"][0])) {
        $multiple_images = uploadImage($_FILES["multiple_images"], true);
        if ($multiple_images === false) {
            $message = "Error uploading multiple images.";
        } else {
            $multiple_images = implode(',', $multiple_images);
        }
    }

    // Build SQL query based on what fields need to be updated
    $sql = "UPDATE swimming_packages SET package_name='$package_name', price='$price', duration='$duration', max_pax='$max_pax', inclusions='$inclusions'";
    
    // Append profile_image and multiple_images to SQL query if they are set
    if (isset($profile_image)) {
        $sql .= ", profile_image='$profile_image'";
    }
    if (isset($multiple_images)) {
        $sql .= ", multiple_images='$multiple_images'";
    }
    
    // Always update package_type in the query
    $sql .= ", package_type='$package_type' WHERE id=$id";

    // Execute the SQL query
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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="style2.css">
</head>
<style>
    .collage {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
    }

    .collage img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .collage img:hover {
        transform: scale(1.1);
    }

    .image-viewer {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        z-index: 999;
        text-align: center;
        overflow-y: auto;
    }

    .image-viewer img {
        max-width: 90%;
        max-height: 90%;
        margin: 20px auto;
        display: block;
    }

    .close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: #fff;
        font-size: 30px;
        font-weight: bold;
        cursor: pointer;
    }

    /* NavBar to Repa */

    .navbar {
        background-color: whitesmoke;

    }

    .navbar-nav .nav-link {
        color: black;
        font-size: 1.1rem;
    }

    .navbar-nav .nav-item {
        padding: 0 1rem;
    }

    .navbar .navbar-nav .nav-item {
        position: relative;
    }

    .navbar .navbar-nav .nav-item::after {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        content: '';
        background-color: black;
        width: 0%;
        height: 4px;
        transition: 500ms;
    }

    .navbar .navbar-nav .nav-item:hover:after {
        width: 100%;
    }

    .nav-item.logout button {
        color: #dc3545;
        background-color: transparent;
        border: none;
        cursor: pointer;
    }

    .nav-item.logout button:hover {
        color: #fff;
        background-color: #dc3545;
    }
</style>

<body>
<?php include 'admin_navbar.php'; ?>
    <script>


        function confirmLogout() {
            return confirm('Are you sure you want to logout?');
        }
    </script>


    <div class="container" style="max-width: 1200px;
    width: 100%; 
    margin: 10 auto; 
 
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);margin-top:5%">
        <div class="form-left">
            <div class="title">Admin Panel - Swimming Packages</div>

            <?php if ($message != "") { ?>
                <div class='message'><?php echo $message; ?></div>
            <?php } ?>

            <div class="package-list">
                <div class="search-create-container row mb-3">
                    <div class="search-container col-md-8 d-flex">
                        <input type="text" id="searchInput" class="form-control mr-2"
                            placeholder="Search for packages...">
                        <button type="button" id="searchButton" class="btn btn-primary btn-custom-large">Search</button>
                    </div>
                    <div class="col-md-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-success btn-custom-large" data-toggle="modal"
                            data-target="#createPackageModal">Create Package</button>
                    </div>
                </div>

                <table class="table" id="packageTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Package Name</th>
                            <th>Package Type</th>
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
                                <td><?php echo $package['package_type']; ?></td>
                                <td><?php echo $package['price']; ?></td>
                                <td><?php echo $package['duration']; ?></td>
                                <td><?php echo $package['max_pax']; ?></td>
                                <td><?php echo $package['inclusions']; ?></td>
                                <td>
                                    <img src="<?php echo $package['profile_image']; ?>" alt="Profile Image"
                                        style="width: 100px; height: 100px; cursor: pointer;"
                                        onclick="openImageViewer('<?php echo $package['multiple_images']; ?>')">
                                </td>
                                <td>
                                    <div class="collage">
                                        <?php
                                        $images = explode(',', $package['multiple_images']);
                                        $total_images = count($images);
                                        $max_display = 2;

                                        foreach (array_slice($images, 0, min($max_display, $total_images)) as $image) {
                                            echo "<img src='$image' alt='Multiple Image' style='width: 100px; height: 100px; margin-right: 5px; cursor: pointer;' onclick='openImageViewer(\"{$package['multiple_images']}\")'>";
                                        }

                                        if ($total_images > $max_display) {
                                            echo "<button class='btn btn-link p-0' onclick='openImageViewer(\"{$package['multiple_images']}\")'>Show More</button>";
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm edit-package-btn" data-toggle="modal"
                                        data-target="#editPackageModal" data-id="<?php echo $package['id']; ?>"
                                        data-package_name="<?php echo $package['package_name']; ?>"
                                        data-price="<?php echo $package['price']; ?>"
                                        data-duration="<?php echo $package['duration']; ?>"
                                        data-max_pax="<?php echo $package['max_pax']; ?>"
                                        data-inclusions="<?php echo $package['inclusions']; ?>">Edit</button>
                                    <a href="?delete=<?php echo $package['id']; ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this package?')"
                                        style="margin-top: 10px;">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>



            </div>

            <div class="modal fade" id="imageViewerModal" tabindex="-1" role="dialog"
                aria-labelledby="imageViewerModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="imageViewerModalLabel">Image Viewer</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="imageViewerBody">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="createPackageModal" tabindex="-1" role="dialog"
                aria-labelledby="createPackageModalLabel" aria-hidden="true">
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
                                <div id="proof_of_payment_error" class="invalid-feedback"></div>

                                <label for="multiple_images">Multiple Images:</label>
                                <input type="file" id="multiple_images" name="multiple_images[]" multiple required>
                                <div id="proof_of_payment_error" class="invalid-feedback"></div>

                                <label for="price">Price:</label>
                                <input type="number" id="price" name="price" class="form-control" required>

                                <label for="duration">Duration:</label>
                                <input type="text" id="duration" name="duration" class="form-control" required>

                                <label for="max_pax">Max Pax:</label>
                                <input type="number" id="max_pax" name="max_pax" class="form-control" required>

                                <label for="inclusions">Inclusions:</label>
                                <textarea id="inclusions" name="inclusions" class="form-control" required></textarea>


                                <label for="package_type">Package Type:</label>
                                <select id="package_type" name="package_type" class="form-control" required>
                                    <option value="Day">Day</option>
                                    <option value="Overnight">Overnight</option>
                                    <option value="Combo">Combo</option>
                                </select>


                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Create Package</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Select the file input element
                    var proofOfPaymentInput = document.getElementById('multiple_images', 'profile_image');

                    // Add an event listener to check the file type when a file is selected
                    proofOfPaymentInput.addEventListener('change', function () {
                        var file = this.files[0];

                        // Check if the selected file is an image
                        if (file.type.indexOf('image') === -1) {
                            // If not an image, show an error message
                            this.setCustomValidity('Please upload a valid image file.');
                            document.getElementById('proof_of_payment_error').textContent = 'Please upload a valid image file.';
                        } else {
                            // If it's an image, clear any previous error messages
                            this.setCustomValidity('');
                            document.getElementById('proof_of_payment_error').textContent = '';
                        }
                    });
                });
            </script>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Select the file input element
                    var proofOfPaymentInput = document.getElementById('profile_image');

                    // Add an event listener to check the file type when a file is selected
                    proofOfPaymentInput.addEventListener('change', function () {
                        var file = this.files[0];

                        // Check if the selected file is an image
                        if (file.type.indexOf('image') === -1) {
                            // If not an image, show an error message
                            this.setCustomValidity('Please upload a valid image file.');
                            document.getElementById('proof_of_payment_error').textContent = 'Please upload a valid image file.';
                        } else {
                            // If it's an image, clear any previous error messages
                            this.setCustomValidity('');
                            document.getElementById('proof_of_payment_error').textContent = '';
                        }
                    });
                });
            </script>


            <div class="modal fade" id="editPackageModal" tabindex="-1" role="dialog"
                aria-labelledby="editPackageModalLabel" aria-hidden="true">
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
                                <input type="text" id="edit_package_name" name="package_name" class="form-control"
                                    required>

                                <label for="edit_profile_image">Profile Image:</label>
                                <input type="file" id="edit_profile_image" name="profile_image">
                                <div id="proof_of_payment_error" class="invalid-feedback"></div>

                                <label for="edit_multiple_images">Multiple Images:</label>
                                <input type="file" id="edit_multiple_images" name="multiple_images[]" multiple>
                                <div id="proof_of_payment_error" class="invalid-feedback"></div>

                                <label for="edit_price">Price:</label>
                                <input type="number" id="edit_price" name="price" class="form-control" required>

                                <label for="edit_duration">Duration:</label>
                                <input type="text" id="edit_duration" name="duration" class="form-control" required>

                                <label for="edit_max_pax">Max Pax:</label>
                                <input type="number" id="edit_max_pax" name="max_pax" class="form-control" required>

                                <label for="edit_inclusions">Inclusions:</label>
                                <textarea id="edit_inclusions" name="inclusions" class="form-control"
                                    required></textarea>

                                <label for="edit_package_type">Package Type:</label>
                                <select id="edit_package_type" name="package_type" class="form-control" required>
                                    <option value="Day">Day</option>
                                    <option value="Overnight">Overnight</option>
                                    <option value="Combo">Combo</option>
                                </select>


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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Select the file input element
            var proofOfPaymentInput = document.getElementById('edit_multiple_images');

            // Add an event listener to check the file type when a file is selected
            proofOfPaymentInput.addEventListener('change', function () {
                var file = this.files[0];

                // Check if the selected file is an image
                if (file.type.indexOf('image') === -1) {
                    // If not an image, show an error message
                    this.setCustomValidity('Please upload a valid image file.');
                    document.getElementById('proof_of_payment_error').textContent = 'Please upload a valid image file.';
                } else {
                    // If it's an image, clear any previous error messages
                    this.setCustomValidity('');
                    document.getElementById('proof_of_payment_error').textContent = '';
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Select the file input element
            var proofOfPaymentInput = document.getElementById('edit_profile_image');

            // Add an event listener to check the file type when a file is selected
            proofOfPaymentInput.addEventListener('change', function () {
                var file = this.files[0];

                // Check if the selected file is an image
                if (file.type.indexOf('image') === -1) {
                    // If not an image, show an error message
                    this.setCustomValidity('Please upload a valid image file.');
                    document.getElementById('proof_of_payment_error').textContent = 'Please upload a valid image file.';
                } else {
                    // If it's an image, clear any previous error messages
                    this.setCustomValidity('');
                    document.getElementById('proof_of_payment_error').textContent = '';
                }
            });
        });
    </script>

    <script>
        $(document).on("click", ".edit-package-btn", function () {
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
    <script>
        function openImageViewer(multipleImages) {
            var images = multipleImages.split(',');
            var modalBody = document.getElementById('imageViewerBody');
            modalBody.innerHTML = '';

            images.forEach(function (image) {
                var img = document.createElement('img');
                img.src = image.trim();
                img.alt = 'Multiple Image';
                img.style.width = '100%';
                img.style.height = 'auto';
                modalBody.appendChild(img);
            });

            $('#imageViewerModal').modal('show');
        }
    </script>

</body>

</html>