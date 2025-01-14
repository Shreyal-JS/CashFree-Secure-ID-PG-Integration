<?php
require_once('vendor/autoload.php');

class CashfreeSignature
{
    public static function getSignature($clientId)
    {
        $encodedData = $clientId . "." . time();
        $publicKey = "-----BEGIN PUBLIC KEY-----\n" .
                     "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA9luVsnGrS6+JyIykH3J7\n" .
                     "pyxieIv+ZkC0vu7aHf1mvEfPfwLj6oR+8x0JwU5NhnMKmcVZ2o57qzYT7Cm6GFzh\n" .
                     "pqZ6+3sDHocloXh4FCeztBrjQSMhHpI05RFYJ1yPj1Dxm23Eaed/UMcEqFlZqWBP\n" .
                     "+osn31r0GRYV5svNbshB/tqEcltb4G0hhHcgFqVjvMeLo8m5KWwXEK7HfUV9mOnN\n" .
                     "X3PfQDV/i4TDMPEUxK+gZge1j2Y2Sz229krLJwV9kjUWRnDf5v9y0YIHof8pbgbP\n" .
                     "6dsnOTbq1jSd2HqKj3Z4T5ctNTkcWGNUzRi7SMNaE4aNaX5L2X2O289DSUGhkq4U\n" .
                     "1wIDAQAB\n" .
                     "-----END PUBLIC KEY-----";
        /* $publicKey = "-----BEGIN PUBLIC KEY-----\n" .
            "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAntwCWleFeJRprVCSaCBu\n" .
            "5+oXcXmtmGq8nIKFrkD4mxd94tNk8gogL2YyCjBEllOIuMWOcKgL87WsW8Zti0oR\n" .
            "uL07KgknbNgaKXHHI7T0GjQ+N2nuSyGPEeQ85HXumCc70O5HRwxEYqOaXxfmFLXh\n" .
            "/vMnNiev8gnI9UXZP5XcLP5KWYcTkiGSa9wipUQGqznJM3MbT17+CBn+f6mhldCV\n" .
            "DMpLT+xfKcBPMhuN9LLOBFz1TE/bDrC+cktN4p/ldGj+oenctUvdzaqkR+W/HP9l\n" .
            "/h884iZeT/IctK7PSIMJwiTyr7YbmLgWn4nElKXqMp9H3LMtnkBSEbfssNPzItl/\n" .
            "XQIDAQAB\n" .
            "-----END PUBLIC KEY-----"; */

        return static::encrypt_RSA($encodedData, $publicKey);
    }

    private static function encrypt_RSA($plainData, $publicKey)
    {
        $publicKeyResource = openssl_pkey_get_public($publicKey);
        if (openssl_public_encrypt($plainData, $encrypted, $publicKeyResource, OPENSSL_PKCS1_OAEP_PADDING)) {
            return base64_encode($encrypted);
        }
        return null;
    }
}

function generateVerificationId($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-._';
    $id = '';
    for ($i = 0; $i < $length; $i++) {
        $id .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $id;
}

$verificationId = generateVerificationId();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['front_image'], $_FILES['back_image'])) {
    $clientId = 'CF10092474CSJIJ7JD133S7397FAEG'; // Test
    // $clientId = 'CF601003CSN5P4V5LTGS73CU27UG'; // Prod
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
                'x-client-secret' => 'cfsk_ma_test_06746ffb8d6398aa5943042f8766595e_970b9fcc', // Test
                // 'x-client-secret' => 'cfsk_ma_prod_a6e556719c0f44785b8a02313a904664_69bfc527', // Prod
            ],
        ]);

        // response
        $responseBody = $response->getBody();
        echo $response->getBody();

        $responseData = json_decode($responseBody, true);

        if ($responseData && isset($responseData['state'])) {
            $userState = $responseData['state'];
            if ($userState === 'Madhya Pradesh') {
                echo "You are eligible to participate!";
            } else {
                echo "You are not allowed to participate as you do not belong to Chhattisgarh state.";
            }
        } else {
            echo "Error: Unable to retrieve state information.";
        }
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request or missing images.";
}
