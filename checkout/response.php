<?php 
session_start();
require 'db.php';
require_once '../vendor/autoload.php';
require_once '../../vendor/autoload.php'; // phpmailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenvPG = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenvPG->load();

$clientIdPG = $_ENV['CLIENT_ID_PG']; // test
$clientSecretPG = $_ENV['CLIENT_SECRET_PG']; // test

$order_id = $_GET['order_id'] ?? $_POST['order_id'] ?? null;


if (!$order_id) {
    die('Order ID is missing. Cannot verify the payment.');
}

\Cashfree\Cashfree::$XClientId = $clientIdPG;
\Cashfree\Cashfree::$XClientSecret = $clientSecretPG;
\Cashfree\Cashfree::$XEnvironment = Cashfree\Cashfree::$SANDBOX;

$cashfree = new \Cashfree\Cashfree();
$x_api_version = "2023-08-01";

try {
    $response = $cashfree->PGOrderFetchPayments($x_api_version, $order_id);

    if (!is_array($response) || empty($response)) {
        echo json_encode(['error' => 'Invalid or empty response from API.']);
        exit;
    }

    $transactions = $response[0];
    $orderStatus = 'Failure'; 

    if (is_array($transactions)) {
        foreach ($transactions as $transaction) {
            if (isset($transaction['payment_status']) && $transaction['payment_status'] === 'SUCCESS') {
                $orderStatus = 'Success';
                break;
            } elseif (isset($transaction['payment_status']) && $transaction['payment_status'] === 'PENDING') {
                $orderStatus = 'Pending';
            }
        }
    }

    // Return the order status
    json_encode(['orderStatus' => $orderStatus]['orderStatus']);

} catch (Exception $e) {
    echo 'Exception when calling PGOrderFetchPayments: ', $e->getMessage(), PHP_EOL;
    echo json_encode(['error' => $e->getMessage()]);
}
/* 
if ($orderStatus === "Success" && isset($_SESSION['form_data'])) {
    
    if (checkDuplicateEntry($conn)) {
        $orderStatus = 'Duplicate';
        // echo 'Payment successful. Data already recorded!';
    } else {
        dbEntry($conn);
        // echo 'Successfully submitted data!';
    }
} else {
    // echo 'Failed! Payment was not successful, or form data is missing.';
    $orderStatus = 'Failure';
} */

function dbEntry($conn) {
    $form_data = $_SESSION['form_data'];
    $stmt = $conn->prepare("
        INSERT INTO users (verification_id, reference_id, afi, name, gender, dob, age, photo, father, phone, email, event1, event2, state, pincode, district) 
        VALUES (:verification_id, :reference_id, :afi, :name, :gender, :dob, :age, :photo, :father, :phone, :email, :event1, :event2, :state, :pincode, :district)
    ");
    $stmt->execute([
        'verification_id' => $form_data['verification_id'],
        'reference_id' => $form_data['reference_id'],
        'afi' => $form_data['afi'],
        'name' => $form_data['name'],
        'gender' => $form_data['gender'],
        'dob' => $form_data['dob'],
        'age' => $form_data['age'],
        'photo' => $form_data['photoName'],
        'father' => $form_data['father'],
        'phone' => $form_data['phone'],
        'email' => $form_data['email'],
        'event1' => $form_data['event1'],
        'event2' => $form_data['event2'],
        'state' => $form_data['state'],
        'pincode' => $form_data['pincode'],
        'district' => $form_data['district'],
    ]);
}
dbEntry($conn);
/* function checkDuplicateEntry($conn) {
    $form_data = $_SESSION['form_data'];
    $verification_id = $form_data['verification_id'];
    $reference_id = $form_data['reference_id'];

    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM users 
        WHERE verification_id = :verification_id OR reference_id = :reference_id
    ");
    $stmt->execute([
        'verification_id' => $verification_id,
        'reference_id' => $reference_id,
    ]);
    $count = $stmt->fetchColumn();
    return $count > 0;
} */

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Payment Status</title>
</head>

<body>
    <h1 class="event_name"><?php echo htmlspecialchars($eventData)?></h1>
    <div class="status-container">
        <div class="status-icon status-success" id="statusIcon"></div>
        <div class="status-title" id="statusTitle"></div>
        <div class="status-message" id="statusMessage"></div>
        <a href="/" class="btn back-btn" id="backBtn">Go Back</a>
        <a href="../../proof/confirmation.php?verification_id=<?= $order_id ?>" class="btn" id="dloadBtn">Download</a>
    </div>
    
    <!-- html2canvas for HTML to Image Conversion -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        const orderStatus = <?php echo json_encode(['orderStatus' => $orderStatus]['orderStatus']); ?>;
        // console.log(orderStatus);

        const statusIcon = document.getElementById('statusIcon');
        const statusTitle = document.getElementById('statusTitle');
        const statusMessage = document.getElementById('statusMessage');
        const backBtn = document.getElementById('backBtn');
        const dloadBtn = document.getElementById('dloadBtn');

        if (orderStatus === 'Success') {
            statusIcon.textContent = '✔';
            statusIcon.classList.add('status-success');
            statusTitle.textContent = 'Payment Successful!';
            statusMessage.textContent = 'You have successfully participated for the event!';
        } else if (orderStatus === 'Pending') {
            statusIcon.textContent = '⌛';
            statusIcon.classList.add('status-pending');
            statusTitle.textContent = 'Payment Pending';
            statusMessage.textContent = 'Please contact the helpdesk.';
        } else if (orderStatus === 'Duplicate') {
            statusIcon.textContent = 'ℹ️';
            statusIcon.classList.add('status-success');
            statusTitle.textContent = 'Payment Successful!';
            statusMessage.textContent = 'Your data is already recorded. No need to resubmit.';
        } else {
            statusIcon.textContent = '✖';
            statusIcon.classList.add('status-failure');
            statusTitle.textContent = 'Payment Failed';
            statusMessage.textContent = 'Unfortunately, your payment could not be processed.';
            dloadBtn.style.display = "none";
            backBtn.style.display = "inline-block";
        }

    </script>

</body>

</html>