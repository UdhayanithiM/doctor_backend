<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once 'config.php';

// --- Connect DB ---
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB connection failed"]);
    exit();
}

// Get parameters from the URL
$test_type = isset($_GET['test_type']) ? $_GET['test_type'] : '';
$user_key = isset($_GET['user_key']) ? $_GET['user_key'] : '';

if (empty($test_type) || empty($user_key)) {
    echo json_encode(["status" => "error", "message" => "Test type and user key are required"]);
    exit();
}

$sql = "";
$param_type = "s"; // 's' for string, 'i' for integer

// --- This is the new logic ---
// We check a different table based on the 'test_type'
switch ($test_type) {
    case 'pretest':
        // Pre-Test (scores) uses the email (user_name)
        $sql = "SELECT COUNT(*) as count FROM scores WHERE user_name = ?";
        $param_type = "s";
        break;
        
    case 'posttest':
        // Post-Test (knowledge) uses the user_id
        $sql = "SELECT COUNT(*) as count FROM knowledge WHERE user_id = ?";
        $param_type = "i";
        break;
        
    case 'scenario':
        // Case Scenario (responses) uses the email (username)
        $sql = "SELECT COUNT(*) as count FROM responses WHERE username = ?";
        $param_type = "s";
        break;
        
    default:
        echo json_encode(["status" => "error", "message" => "Invalid test type"]);
        exit();
}

// --- SQL Query ---
$stmt = $conn->prepare($sql);
$stmt->bind_param($param_type, $user_key);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    // User has an entry, so they have completed this test
    echo json_encode(["status" => "completed"]);
} else {
    // No entry found, test not completed
    echo json_encode(["status" => "not_completed"]);
}

$stmt->close();
$conn->close();
?>