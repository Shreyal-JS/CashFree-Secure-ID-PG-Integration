<?php
session_start();
require '../db-integration/db.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$eventName = 'Event XYZ';

$verification_id = $_GET['verification_id'] ?? $_POST['verification_id'] ?? null;

if (!$verification_id) {
    die('Verification ID is missing.');
}

// Fetch user data from DB
$stmt = $conn->prepare("SELECT * FROM users WHERE verification_id = :verification_id");
$stmt->execute(['verification_id' => $verification_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// echo $email = $user['email'];

if (!$user) {
    die('No data found for this verification ID.');
}

// Path to photo (Adjust if necessary)
$photoPath = '../uploads/' . $user['photo'];

// Fallback Image   
if (file_exists($photoPath)) {
    $photoData = file_get_contents($photoPath);
    $photoBase64 = 'data:image/jpeg;base64,' . base64_encode($photoData);
} else {
    $photoBase64 = 'https://via.placeholder.com/150';
}



?>

<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Participant Certificate</title>

    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 700px;
            margin: 10px auto;
            border: 8px solid #2c3e50;
            border-radius: 15px;
            overflow: hidden;
            height: auto;
            /* Adjust height as needed */
        }

        .header {
            background: #3498db;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        .header h1,
        .header h2 {
            margin: 0;
        }

        .details {
            padding: 30px;
        }

        .section-title {
            font-size: 26px;
            margin-bottom: 10px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 5px;
            font-weight: bold;
        }

        .photo-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #3498db;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #3498db;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #ddd;
        }

        #download {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
        }

        #download:hover {
            background-color: #2980b9;
        }

        .note {
            text-align: center;
            font-size: 16px;
            color: #333;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <button id="download">Download</button>
    <p class="note">Download and take a Screenshot of the details.</p>
    <div class='container' id="capture-area">
        <div class='header'>
            <h1>Chhattisgarh Athletics Association</h1>
            <h2><?php echo htmlspecialchars($eventName) ?></h2>
        </div>
        <div class='details'>
            <div class='photo-container'>
                <img src='<?= $photoBase64 ?>' alt='User Photo' class='photo'>
            </div>
            <h1><?php echo htmlspecialchars($eventName)?></h1>
            <div class='section-title'>Participant Details</div>
            <table>
                <tr>
                    <th>AFI ID</th>
                    <td><?= $user['afi'] ?></td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td><?= $user['name'] ?></td>
                </tr>
                <tr>
                    <th>Gender</th>
                    <td><?= $user['gender'] ?></td>
                </tr>
                <tr>
                    <th>Date of Birth</th>
                    <td><?= $user['dob'] ?></td>
                </tr>
                <tr>
                    <th>Age</th>
                    <td><?= $user['age'] ?></td>
                </tr>
                <tr>
                    <th>Father's Name</th>
                    <td><?= $user['father'] ?></td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td><?= $user['phone'] ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= $user['email'] ?></td>
                </tr>
                <tr>
                    <th>Event 1</th>
                    <td><?= $user['event1'] ?></td>
                </tr>
                <tr>
                    <th>Event 2</th>
                    <td><?= $user['event2'] ?></td>
                </tr>
                <tr>
                    <th>State</th>
                    <td><?= $user['state'] ?></td>
                </tr>
                <tr>
                    <th>District</th>
                    <td><?= $user['district'] ?></td>
                </tr>
                <tr>
                    <th>Pincode</th>
                    <td><?= $user['pincode'] ?></td>
                </tr>
                <tr>
                    <th>Verification ID</th>
                    <td><?= $user['verification_id'] ?></td>
                </tr>
                <tr>
                    <th>Reference ID</th>
                    <td><?= $user['reference_id'] ?></td>
                </tr>
            </table>
        </div>
    </div>
    <!-- html2canvas for HTML to Image Conversion -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const captureArea = document.getElementById('capture-area');
            html2canvas(captureArea).then(canvas => {
                const link = document.createElement('a');
                link.download = 'participation_details_<?= $verification_id ?>.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                const imageData = canvas.toDataURL('image/png');
                const email = '<?= $user['email'] ?>'; 
                // Send the image data to PHP via AJAX
                fetch('sender.php', {
                        method: 'POST',
                        body: JSON.stringify({
                            image: imageData,
                            email: email
                        }),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Email sent successfully.');
                        } else {
                            alert('Error sending email:', data.error);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    </script>

</body>

</html>