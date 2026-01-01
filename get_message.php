<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once 'config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Get parameters
$sender_id = $_GET['sender_id'] ?? '';
$receiver_id = $_GET['receiver_id'] ?? '';
$referral_id = $_GET['referral_id'] ?? '';

$messages = [];

if (!empty($referral_id)) {
    // SCENARIO 1: Chat specific to a Student Referral (Doctor <-> Counselor)
    // We fetch ALL messages attached to this referral_id
    $sql = "SELECT * FROM messages WHERE referral_id = '$referral_id' ORDER BY timestamp ASC";
} 
elseif (!empty($sender_id) && !empty($receiver_id)) {
    // SCENARIO 2: Direct Chat (Old Logic - Backup)
    $sql = "SELECT * FROM messages 
            WHERE (sender_id = '$sender_id' AND receiver_id = '$receiver_id') 
            OR (sender_id = '$receiver_id' AND receiver_id = '$sender_id') 
            ORDER BY timestamp ASC";
} else {
    // Invalid Request
    echo json_encode([]);
    exit;
}

$result = $conn->query($sql);

if ($result) {
    while($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}

echo json_encode($messages);
$conn->close();
?>