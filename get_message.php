<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

$referral_id = $_GET['referral_id'] ?? '';

if (empty($referral_id)) {
    echo json_encode(["error" => "No referral_id provided"]);
    exit;
}

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT id, sender_id, receiver_id, message, referral_id, timestamp FROM messages WHERE referral_id = ? ORDER BY timestamp ASC");

if (!$stmt) {
    echo json_encode(["error" => "Prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("s", $referral_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
$stmt->close();
$conn->close();
?>