<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once 'config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

$user_id = $_GET['user_id'] ?? '';
$email = $_GET['email'] ?? ''; // This is the username

$response = [
    "tests" => [],
    "scenarios" => []
];

// 1. FETCH MARKS FROM 'scores' TABLE (Like History Page)
if (!empty($email)) {
    // We filter by user_name (which matches email in your app)
    $sql_scores = "SELECT * FROM scores WHERE user_name = '$email' ORDER BY id DESC";
    $result_scores = $conn->query($sql_scores);
    
    if ($result_scores) {
        while($row = $result_scores->fetch_assoc()) {
            $response['tests'][] = $row;
        }
    }

    // 2. FETCH SCENARIOS FROM 'responses' TABLE
    $sql_scenarios = "SELECT * FROM responses WHERE username = '$email' ORDER BY id DESC";
    $result_scenarios = $conn->query($sql_scenarios);
    
    if ($result_scenarios) {
        while($row = $result_scenarios->fetch_assoc()) {
            $response['scenarios'][] = $row;
        }
    }
}

echo json_encode($response);
$conn->close();
?>