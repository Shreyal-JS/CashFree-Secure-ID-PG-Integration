<?php
session_start();

require_once('../vendor/autoload.php');

$eventName = $_SESSION['user_data']['event_name'];

if (isset($_SESSION['user_data'])) {
    $userData = $_SESSION['user_data'];

    $client = new \GuzzleHttp\Client();

    try {
        $response = $client->request('GET', 'https://sandbox.cashfree.com/verification/document/aadhaar', [
            'query' => [
                'reference_id' => $userData['reference_id'],
                'verification_id' => $userData['verification_id']
            ],
            'headers' => [
                'accept' => 'application/json',
                'x-cf-signature' => $userData['x-cf-signature'],
                'x-client-id' => $userData['x-client-id'],
                'x-client-secret' => $userData['x-client-secret'],
            ],
        ]);

        $response->getBody();
        // $responseBody = $response->getBody();
        $responseData = json_decode($response->getBody(), true);

        if ($responseData && isset($responseData)) {
            $_SESSION['val_data'] = [
                'key_id' => [
                    'verification_id' => $responseData['verification_id'],
                    'reference_id' => $responseData['reference_id'],
                    'uid' => $responseData['uid'],
                    'event_name' => $eventName
                ],
                'user_data' => [
                    'name' => $responseData['name'],
                    'father' => $responseData['father'],
                    'gender' => $responseData['gender'],
                    'yob' => $responseData['yob'],
                    'state' => $responseData['state'],
                    'pincode' => $responseData['pincode'],
                ],
            ];
            header('Location: event-form.php');
            
            exit();
        } else{
            echo "Error: Unable to retrieve information.";
        }

    } catch (\GuzzleHttp\Exception\RequestException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Session data is missing. Please start from the beginning.";
}
