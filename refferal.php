<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require_once 'config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed"]));
}

$postData = json_decode(file_get_contents("php://input"), true);

// ✅ FIX: Ensure counselor_id is captured
$counselor_id = isset($postData['counselor_id']) ? intval($postData['counselor_id']) : 0;
$name = isset($postData['name']) ? trim($postData['name']) : '';
$age = isset($postData['age']) ? intval($postData['age']) : 0;
$standard = isset($postData['standard']) ? trim($postData['standard']) : '';
$address = isset($postData['address']) ? trim($postData['address']) : '';
$reason = isset($postData['reason']) ? trim($postData['reason']) : '';
$behavior = isset($postData['behavior']) ? trim($postData['behavior']) : '';
$academic = isset($postData['academic']) ? trim($postData['academic']) : '';
$disciplinary = isset($postData['disciplinary']) ? trim($postData['disciplinary']) : '';
$special_need = isset($postData['special_need']) ? trim($postData['special_need']) : '';

if (empty($name) || empty($age)) {
    echo json_encode(["status" => "error", "message" => "Required fields missing"]);
    exit;
}

// Update Query to include counselor_id
$stmt = $conn->prepare("INSERT INTO referrals (name, age, standard, address, reason, behavior, academic, disciplinary, special_need, counselor_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
// s=string, i=integer
$stmt->bind_param("sisssssssi", $name, $age, $standard, $address, $reason, $behavior, $academic, $disciplinary, $special_need, $counselor_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Referral submitted"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>