<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'config.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB connection failed"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

$sender_id   = intval($input['sender_id'] ?? 0);
$receiver_id = intval($input['receiver_id'] ?? 0);
$message     = trim($input['message'] ?? '');
$referral_id = intval($input['referral_id'] ?? 0);

if ($sender_id === 0 || $receiver_id === 0 || $message === '' || $referral_id === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO messages (sender_id, receiver_id, message, referral_id)
     VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("iisi", $sender_id, $receiver_id, $message, $referral_id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message_id" => $stmt->insert_id
    ]);
} else {
    echo json_encode(["status" => "error"]);
}

$stmt->close();
$conn->close();
