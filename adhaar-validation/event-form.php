<?php
/** 
 # Customer details ->
 * customer_id => Unique id (maybe can use as transaction id)
 * customer_email => email
 * customer_phone => phone number
 * customer_name => fullname
 * 
 # Order meta ->
 *
 * return_url => formdetail.php , redirection to page of event form data (maybe)
 # Order tags ->
 * Will check later
 * 
 # Order details ->
 * order_id => Unique id (sending verification_id as order_id)
 * order_amount => 200.00
 * order_currency => INR
 * order_note => Event Paritication
 * 
 **/
?>

<?php
session_start();

// Check for user data availability
if (!isset($_SESSION['val_data'])) {
    echo "No user data available. Please go back to the verification step.";
    exit();
}

// Retrieve user data from the session
$keyId = $_SESSION['val_data']['key_id'];
$userData = $_SESSION['val_data']['user_data'];

$userData['yob'] = "14/05/2004"; // remove this line after testing

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Participation Form</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Event Participation Form</h2>
        <h4 class="text-center mb-4"><?php echo htmlspecialchars($keyId['event_name'])?></h4>
        <form action="../pg-integration/checkout/verify.php" method="post" enctype="multipart/form-data" class="p-4 border rounded bg-light">
            <!-- start of IDs --> <!-- Hidden fields -->
            <input type="hidden" name="verification_id" value="<?php echo $keyId['verification_id'] ?>"><!-- Also Order ID -->
            <input type="hidden" name="reference_id" value="<?php echo $keyId['reference_id'] ?>"><!-- Also Customer ID -->
            <input type="hidden" name="uid" value="<?php echo $keyId['uid'] ?>">

            <div class="form-group">
                <label for="afi">AFI ID</label>
                <input type="text" name="afi" id="afi" class="form-control" placeholder="XXXXXXXXXXX">
            </div>
            <!-- /End of IDs -->
            <!-- Start of personal details -->
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($userData['name']) ?>" autocomplete="off" readonly>
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <input type="text" name="gender" id="gender" class="form-control" value="<?php echo $userData['gender'] ?>" readonly>
            </div>

            <div class="form-group">
                <label for="dob">Date of Birth(DOB):</label>
                <input type="date" name="dob" id="dob" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label for="age">Age:</label>
                <input type="text" name="age" id="age" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label for="photo">Photo:</label>
                <input type="file" name="photo" id="photo" class="form-control" accept=".jpeg, .png, .jpg" onchange="validateFile(this)" title="passport size photo(Max 2mb)">
            </div>
            <div class="form-group">
                <label for="father">Father's Name:</label>
                <input type="text" name="father" id="father" value="<?php echo $userData['father'] ?>" class="form-control" autocomplete="off">
            </div>
            <!-- /End of personal details -->

            <!-- Start of contact details -->
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" name="phone" id="phone" class="form-control" placeholder="e.g. - 1234567890" autocomplete="off">
            </div>

            <div class="form-group">
                <label for="email">Email ID:</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="e.g - mail@example.com" autocomplete="off">
            </div>
            <!-- /End of contact details -->

            <!-- Start of event details -->
            <div class="form-group">
            <label for="event1">Event 1:</label>
            <select name="event1" id="event1" class="form-control">
                <option value="" selected disabled>Select Event</option>
                <option value="100m">100m</option>
                <option value="200m">200m</option>
                <option value="300m">300m</option>
                <option value="400m">400m</option>
                <option value="500m">500m</option>
            </select>
            </div>
            <div class="form-group">
            <label for="event2">Event 2:</label>
            <select name="event2" id="event2" class="form-control">
                <option value="" selected disabled>Select Event</option>
                <option value="100m">100m</option>
                <option value="200m">200m</option>
                <option value="300m">300m</option>
                <option value="400m">400m</option>
                <option value="500m">500m</option>
            </select>
            </div>
            <!-- /End of event details -->

            <!-- Start of location details -->
            <div class="form-group">
                <label for="state">State:</label>
                <input type="text" name="state" id="state" class="form-control" value="<?php echo $userData['state'] ?>" readonly>
            </div>
            <div class="form-group">
                <label for="pincode">Pincode:</label>
                <input type="text" name="pincode" id="pincode" class="form-control" value="<?php echo $userData['pincode'] ?>" readonly>
            </div>
            <div class="form-group">
                <label for="district">District:</label>
                <input type="text" name="district" id="district" class="form-control" value="" readonly>
            </div>
            <!-- /End of location details -->

            <input type="submit" value="Proceed to Payment" class="btn btn-primary btn-block">
        </form>
    </div>
    <script>
        let yobRes = <?php echo json_encode($userData['yob']) ?>;
        // console.log(yob);
    </script>
    <script src="main.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
