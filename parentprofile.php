<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// rest of your code

require_once 'config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Get id from POST JSON
$input = json_decode(file_get_contents("php://input"), true);
$id = isset($input['id']) ? intval($input['id']) : 0;

if ($id == 0) {
    echo json_encode(["status" => "error", "message" => "Missing id"]);
    exit;
}

// Fetch parent details
$sql = "SELECT name, father_occ, mother_occ, father_phone, mother_phone, email 
        FROM users WHERE id = $id";

$result = $conn->query($sql);

if ($result === false) {
    echo json_encode(["status" => "error", "message" => "SQL error: " . $conn->error]);
    exit;
}

if ($result->num_rows > 0) {
    $parent = $result->fetch_assoc();

    echo json_encode([
        "status" => "success",
        "parent_details" => [
            "name" => $parent["name"],
            "father_occ" => $parent["father_occ"],
            "mother_occ" => $parent["mother_occ"],
            "father_phone" => $parent["father_phone"],
            "mother_phone" => $parent["mother_phone"],
            "email" => $parent["email"]
        ]
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "No parent found"]);
}

$conn->close();
?>
