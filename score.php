<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Include your database configuration
require_once 'config.php';

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Get JSON input from Flutter
$input = json_decode(file_get_contents("php://input"), true);

// Validate input
$user_name = isset($input['user_name']) ? trim($input['user_name']) : '';
$score = isset($input['score']) ? (int)$input['score'] : 0;
$total = isset($input['total']) ? (int)$input['total'] : 0;

if (empty($user_name)) {
    echo json_encode(["status" => "error", "message" => "Username is required"]);
    exit;
}

// Insert score into database
$stmt = $conn->prepare("INSERT INTO scores (user_name, score, total) VALUES (?, ?, ?)");
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Prepare statement failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("sii", $user_name, $score, $total);

// Execute and respond
if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Score saved successfully",
        "user_name" => $user_name,
        "score" => $score,
        "total" => $total
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to save score: " . $stmt->error
    ]);
}

// Close connections
$stmt->close();
$conn->close();
?>
