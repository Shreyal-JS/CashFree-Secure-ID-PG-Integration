<!-- dynamic $email -->
<?php
session_start();;

require '../db-integration/db.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$verification_id = $_GET['verification_id'] ?? null;

$data = json_decode(file_get_contents('php://input'), true);
$imageData = $data['image'] ?? null;

if (!$data || empty($data['image']) || empty($data['email'])) {
    echo json_encode(['success' => false, 'error' => 'Missing data.']);
    exit;
}

$imageData = $data['image'];
$email = $data['email'];

// Decode the base64 image and save it
list($type, $imageData) = explode(';', $imageData);
list(, $imageData) = explode(',', $imageData);
$imageData = base64_decode($imageData);

$fileName = '../uploads/receipts/participation_details_' . $verification_id . '.png';
file_put_contents($fileName, $imageData);

// Send the email with the attachment
sendConfirmationEmail($email, $fileName);

function sendConfirmationEmail($email, $filePath) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  
        $mail->SMTPAuth = true;
        $mail->Username = 'damnshreyal@gmail.com';
        $mail->Password = 'iktq ivud fghx qhyw';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('damnshreyal@gmail.com', 'Event Team');
        $mail->addAddress($email);
        $mail->addAttachment($filePath);

        $mail->isHTML(true);
        $mail->Subject = 'Participation Details';
        $mail->Body    = '<p>Thank you for participating in the event. Please find your entry details below.</p>';
        $mail->send();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

die();
?>
