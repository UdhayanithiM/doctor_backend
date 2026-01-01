<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once 'config.php';
$conn = new mysqli($servername, $username, $password, $dbname);

$input = json_decode(file_get_contents("php://input"), true);
$referral_id = intval($input['referral_id'] ?? 0);
$suggestion = trim($input['doctor_suggestion'] ?? '');

if ($referral_id === 0 || $suggestion === '') {
    echo json_encode(["status" => "error"]);
    exit;
}

$stmt = $conn->prepare(
    "UPDATE referrals SET doctor_suggestion = ? WHERE id = ?"
);
$stmt->bind_param("si", $suggestion, $referral_id);
$stmt->execute();

$stmt = $conn->prepare("SELECT * FROM referrals WHERE id = ?");
$stmt->bind_param("i", $referral_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode([
    "status" => "success",
    "referral" => $row
]);

$conn->close();
