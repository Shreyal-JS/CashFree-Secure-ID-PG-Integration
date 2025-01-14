<?php
session_start();

require('../vendor/autoload.php');

$event_name = 'Event XYZ'; // confirmation.php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

class CashfreeSignature{
    public static function getSignature($clientId){
        $encodedData = $clientId . "." . time();
        $publicKey = $_ENV['PUBLIC_KEY'];
        return static::encrypt_RSA($encodedData, $publicKey);
    }

    private static function encrypt_RSA($plainData, $publicKey){
        $publicKeyResource = openssl_pkey_get_public($publicKey);

        if (openssl_public_encrypt($plainData, $encrypted, $publicKeyResource, OPENSSL_PKCS1_OAEP_PADDING)) {
            return base64_encode($encrypted);
        }
        return null;
    }
}

function generateVerificationId($length = 10){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-._';
    $id = '';
    for ($i = 0; $i < $length; $i++) {
        $id .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $id;
}

$verificationId = generateVerificationId();

// Error logging setup
ini_set('log_errors',1);
ini_set('error_log', '../logs/error.log');
ini_set('display_errors', 0);
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['front_image'], $_FILES['back_image'])) {
    // Cashfree credentials
    $clientId = $_ENV['CLIENT_ID'];
    $clientSecret = $_ENV['CLIENT_SECRET'];
    // x-cf-signature
    $xCfSignature = CashfreeSignature::getSignature($clientId);

    // back image
    if (isset($_FILES['back_image'])) {
        $backImagePath = $_FILES['back_image']['tmp_name'];
        $backImageName = $_FILES['back_image']['name'];
        $backFileType = mime_content_type($backImagePath);
        $backFileContent = fopen($backImagePath, 'r');
    }

    // front image
    if (isset($_FILES['front_image'])) {
        $frontImagePath = $_FILES['front_image']['tmp_name'];
        $frontImageName = $_FILES['front_image']['name'];
        $frontFileType = mime_content_type($frontImagePath);
        $frontFileContent = fopen($frontImagePath, 'r');
    }

    // Guzzle client
    $client = new \GuzzleHttp\Client();

    try {
        $response = $client->request('POST', 'https://sandbox.cashfree.com/verification/document/aadhaar', [
            'multipart' => [
                [
                    'name' => 'verification_id',
                    'contents' => $verificationId
                ],
                [
                    'name' => 'back_image',
                    'filename' => $backImageName,
                    'contents' => $backFileContent,
                    'headers' => [
                        'Content-Type' => $backFileType
                    ]
                ],
                [
                    'name' => 'front_image',
                    'filename' => $frontImageName,
                    'contents' => $frontFileContent,
                    'headers' => [
                        'Content-Type' => $frontFileType
                    ]
                ]
            ],
            'headers' => [
                'accept' => 'application/json',
                'x-api-version' => '2022-10-26',
                'x-cf-signature' => $xCfSignature,
                'x-client-id' => $clientId,
                'x-client-secret' => $clientSecret,
            ],
        ]);

        // response
        $responseBody = $response->getBody();
        $responseData = json_decode($responseBody, true);

        if ($responseData && isset($responseData['state'])) {
            $userState = $responseData['state'];
            if ($responseData['state'] === 'Madhya Pradesh') {
                // Store required user data in the session
                $_SESSION['user_data'] = [
                    'verification_id' => $verificationId,
                    'reference_id' => $responseData['reference_id'],
                    'x-cf-signature' => $xCfSignature,
                    'x-client-id' => $clientId,
                    'x-client-secret' => $clientSecret,
                    'event_name' => $event_name
                ];

                header("Location: response.php");
                exit();
            } else {
                throw new Exception('You are not allowed to participate as you do not belong to Chhattisgarh state.');
            }
        } else {
            throw new Exception('Invalid response from the API.');
        }
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        error_log("Request error: " . $e->getMessage());
        $errorMessage = 'Error: There was a problem with the server. Please try again later.';
    }
} else {
    $errorMessage = "Error: Invalid request or missing images.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Popup Example</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="errorMessage"></p>
                    <a href="/test/adhaar-validation/form.php" class="btn btn-secondary btn-block">Go back</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    var errorMessage = "<?php echo addslashes($errorMessage); ?>";
    if (errorMessage) {
        document.getElementById('errorMessage').innerText = errorMessage;
        var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
    }
</script>
</body>
</html>
