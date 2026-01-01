<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "config.php";

// DB connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB connection failed"]);
    exit;
}

// Get raw JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['username'], $data['scenario'], $data['responses'])) {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit;
}

$username = $conn->real_escape_string($data['username']);  // Gmail/username
$scenario   = $conn->real_escape_string($data['scenario']);
$responses  = $data['responses'];

// Ensure we always have 4 responses (Q1 → Q4)
$answer1 = isset($responses[0]) ? $conn->real_escape_string($responses[0]) : "";
$answer2 = isset($responses[1]) ? $conn->real_escape_string($responses[1]) : "";
$answer3 = isset($responses[2]) ? $conn->real_escape_string($responses[2]) : "";
$answer4 = isset($responses[3]) ? $conn->real_escape_string($responses[3]) : "";

// Insert into table "responses"
$sql = "INSERT INTO responses (username, scenario, answer1, answer2, answer3, answer4, created_at) 
        VALUES ('$username', '$scenario', '$answer1', '$answer2', '$answer3', '$answer4', NOW())";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success", "message" => "Responses saved"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}

$conn->close();
?>
