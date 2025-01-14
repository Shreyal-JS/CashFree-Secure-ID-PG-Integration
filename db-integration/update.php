<?php
require 'db.php';

$verification_id = $_GET['verification_id'];

// Fetch user data first
$stmt = $conn->prepare("SELECT * FROM users WHERE verification_id = :verification_id");
$stmt->execute(['verification_id' => $verification_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo 'User not found!';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $verification_id = $_POST['verification_id'];
    $reference_id = $_POST['reference_id'];
    $afi = $_POST['afi'];
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $age = $_POST['age'];
    $father = $_POST['father'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $event1 = $_POST['event1'];
    $event2 = $_POST['event2'];
    $state = $_POST['state'];
    $pincode = $_POST['pincode'];
    $district = $_POST['district'];
    $photo = $user['photo']; // Default to existing photo

    // Handle File Upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../uploads/";
        $photoName = time() . "_" . basename($_FILES['photo']['name']);
        $targetFile = $targetDir . $photoName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photo = $photoName;  // Use the uploaded photo path
        } else {
            echo "Failed to upload photo.";
            exit();
        }
    } else {
        // Use the existing photo if no new upload
        $photo = $_POST['current_photo'] ?? $user['photo'];
    }

    // Update Query
    $stmt = $conn->prepare("
        UPDATE users 
        SET afi = :afi, name = :name, gender = :gender, dob = :dob, age = :age, 
            photo = :photo, father = :father, phone = :phone, email = :email, 
            event1 = :event1, event2 = :event2, state = :state, 
            pincode = :pincode, district = :district 
        WHERE verification_id = :verification_id
    ");

    $stmt->execute([
        'afi' => $afi,
        'name' => $name,
        'gender' => $gender,
        'dob' => $dob,
        'age' => $age,
        'photo' => $photo,
        'father' => $father,
        'phone' => $phone,
        'email' => $email,
        'event1' => $event1,
        'event2' => $event2,
        'state' => $state,
        'pincode' => $pincode,
        'district' => $district,
        'verification_id' => $verification_id
    ]);

    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Update User</title>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Update User</h1>
        <form method="post" enctype="multipart/form-data" class="border p-4 bg-white rounded shadow">
            <!-- Verification and Reference ID -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="verification_id" class="form_label">Verification ID:</label>
                    <input type="text" name="verification_id" value="<?= htmlspecialchars($user['verification_id']) ?>" class="form-control" readonly>
                </div>
                <div class="col-md-6">
                    <label for="reference_id" class="form_label">Reference ID:</label>
                    <input type="text" name="reference_id" value="<?= htmlspecialchars($user['reference_id']) ?>" class="form-control" readonly>
                </div>
                <div class="col-md-6">
                    <label for="reference_id" class="form_label">AFI ID:</label>
                    <input type="text" name="afi" value="<?= htmlspecialchars($user['afi']) ?>" class="form-control">
                </div>
            </div>

            <!-- Personal Details -->
            <div class="mb-3">
                <label for="name" class="form-label">Full Name:</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>">
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="gender" class="form-label">Gender:</label>
                    <input type="text" name="gender" id="gender" class="form-control" value="<?= htmlspecialchars($user['gender']) ?>">
                </div>
                <div class="col md-6">
                    <label for="dob" class="form-label">Date Of Birth (DOB):</label>
                    <input type="date" name="dob" id="dob" class="form-control" value="<?= htmlspecialchars($user['dob']) ?>">
                </div>
                <div class="col md-6">
                    <label for="age" class="form-label">Age:</label>
                    <input type="text" name="age" id="age" class="form-control" value="<?= htmlspecialchars($user['age']) ?>" readonly>
                </div>
            </div>
            <!-- Add Image Field -->
            <div class="row">
                <div class="col-md-6">
                    <label for="photo" class="form-label">Upload New Photo:</label>
                    <input type="file" name="photo" class="form-control">
                    <input type="hidden" name="current_photo" value="<?= htmlspecialchars($user['photo']) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Current Photo:</label>
                    <div>
                        <img src="../uploads/<?= htmlspecialchars($user['photo']) ?>" alt="Current Photo" class="img-thumbnail" width="150" style="height: 150px;">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="father" class="form-label">Father's Name:</label>
                <input type="text" name="father" id="father" class="form-control" value="<?= htmlspecialchars($user['father']) ?>">
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number:</label>
                <input type="phone" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email ID:</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>">
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="event1" class="form-label">Event 1:</label>
                    <select name="event1" id="event1" class="form-control">
                        <option value="100m">100m</option>
                        <option value="200m">200m</option>
                        <option value="300m">300m</option>
                        <option value="400m">400m</option>
                        <option value="500m">500m</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="event2" class="form-label">Event 2:</label>
                    <select name="event2" id="event2" class="form-control">
                        <option value="100m">100m</option>
                        <option value="200m">200m</option>
                        <option value="300m">300m</option>
                        <option value="400m">400m</option>
                        <option value="500m">500m</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="state" class="form-label">State:</label>
                    <input type="text" name="state" id="state" class="form-control" value="<?= htmlspecialchars($user['state']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="pincode" class="form-label">Pincode:</label>
                    <input type="text" name="pincode" id="pincode" class="form-control" value="<?= htmlspecialchars($user['pincode']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="district">District:</label>
                    <input type="text" name="district" id="district" class="form-control" value="<?= htmlspecialchars($user['district']) ?>">
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="index.php" class="btn btn-secondary">Back to Users</a>
            </div>
        </form>
    </div>
    <script>
        
        function setSelectedOption(selectId, selectedValue) {
            const selectElement = document.querySelector(selectId);
            const options = selectElement.querySelectorAll('option');

            options.forEach(option => {
                if (selectedValue === option.value) {
                    option.setAttribute('selected', "");
                }
            });
        }

        // Set selected options for event1 and event2
        setSelectedOption('#event1', <?php echo json_encode($user['event1']) ?>);
        setSelectedOption('#event2', <?php echo json_encode($user['event2']) ?>);
    </script>
    <script>
        // JavaScript function to validate file input
        function validateFile(input) {
            const maxSize = 2 * 1024 * 1024; // 2 MB
            const allowedExtensions = ["image/jpeg", "image/png", "image/jpg"];
            const file = input.files[0];
            if (file) {
                if (file.size > maxSize) {
                    alert("File size exceeds 2 MB. Please upload a smaller file.");
                    input.value = "";
                    return false;
                }
                if (!allowedExtensions.includes(file.type)) {
                    alert("Invalid file type. Only .jpg, .jpeg, and .png files are allowed.");
                    input.value = "";
                    return false;
                }
            }
            return true;
        }
    </script>
    <script src="main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>