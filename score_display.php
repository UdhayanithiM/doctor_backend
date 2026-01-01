<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once 'config.php'; // include DB config (servername, username, password, dbname)

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed"
    ]);
    exit;
}

// Query to fetch scores
$sql = "SELECT id, user_name, score, total FROM scores ORDER BY id DESC";
$result = $conn->query($sql);

$scores = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $scores[] = $row;
    }
}

// Return JSON response
echo json_encode([
    "status" => "success",
    "data" => $scores
]);

$conn->close();
?>
