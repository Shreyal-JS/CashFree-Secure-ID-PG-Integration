<?php
require 'db.php';

$verification_id = $_GET['verification_id'];

$stmt = $conn->prepare("DELETE FROM users WHERE verification_id = :verification_id");
$stmt->execute(['verification_id' => $verification_id]);

header("Location: index.php");
exit();
?>