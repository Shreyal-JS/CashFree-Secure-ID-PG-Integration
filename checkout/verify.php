<?php
session_start();

require '../vendor/autoload.php';

if (!empty($_FILES['photo']['name']) && !empty($_POST['name'])) {
    $targetDir = "../../uploads/";

    // Generate photo name using the user's name and timestamp
    $userName = preg_replace("/[^a-zA-Z0-9]/", "_", $_POST['name']); // Sanitize username
    $photoName = strtolower($userName) . "_" . time() . "." . pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $targetFile = $targetDir . $photoName;

    // Move uploaded file
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
        $photo = $photoName; // Save this to DB
    } else {
        echo "Failed to upload photo.";
    }
} else {
    echo "Name or Photo is missing.";
}



$clientid = "TEST10092474d5af5cd5aef67e51058d47429001";
$clientSecret = "cfsk_ma_test_90392c77e5b386bc9b61d30a7d4e522b_dd73cb9d";

// Retrieve form data
$order_id = $_POST['verification_id']; // verification Id
$customer_id = $_POST['reference_id']; // Customer Id
$name = $_POST['name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$dob = $_POST['dob'];

$_SESSION['form_data'] = [
    'verification_id' => $order_id,
    'reference_id' => $customer_id,
    'afi' => $_POST['afi'] ?? '',
    'name' => $name,
    'gender' => $_POST['gender'] ?? '',
    'dob' => $dob, // keyword's bug 
    'age' => $_POST['age'] ?? '',
    'photo' => $_POST['photo'] ?? '',
    'photoName' => $photoName,
    'father' => $_POST['father'] ?? '',
    'phone' => $phone,
    'email' => $email,
    'event1' => $_POST['event1'] ?? '',
    'event2' => $_POST['event2'] ?? '',
    'state' => $_POST['state'] ?? '',
    'pincode' => $_POST['pincode'] ?? '',
    'district' => $_POST['district'] ?? '',
];


\Cashfree\Cashfree::$XClientId = "$clientid";
\Cashfree\Cashfree::$XClientSecret = "$clientSecret";
\Cashfree\Cashfree::$XEnvironment = Cashfree\Cashfree::$SANDBOX;

$cashfree = new \Cashfree\Cashfree();

$x_api_version = "2023-08-01";
$create_order_request = new \Cashfree\Model\CreateOrderRequest();
$create_order_request->setOrderAmount("200.00");
$create_order_request->setOrderCurrency("INR");
$create_order_request->setOrderId("$order_id");

$customer_details = new \Cashfree\Model\CustomerDetails();
$customer_details->setCustomerId("$customer_id");
$customer_details->setCustomerPhone("$phone");
$customer_details->setCustomerName("$name");
$customer_details->setCustomerEmail("$email");
$create_order_request->setCustomerDetails($customer_details);

$order_meta = new \Cashfree\Model\OrderMeta();
$order_meta->setReturnUrl("http://localhost/test/pg-integration/checkout/response.php?order_id={$order_id}");
$order_meta->setNotifyUrl("https://www.cashfree.com/devstudio/preview/pg/webhooks/29642900");
$create_order_request->setOrderMeta($order_meta);

try {
    $result = $cashfree->PGCreateOrder($x_api_version, $create_order_request);
    $paymentSessionId = $result[0]['payment_session_id'];
    header("Location: checkout.php?paymentSessionId=" . urlencode($paymentSessionId));
    exit();
} catch (Exception $e) {
    die('Exception when calling PGCreateOrder: ' . $e->getMessage());
}
?>