<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'config.php'; // Include your database config

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed"]);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['user_id']) || !isset($input['responses']) || !isset($input['total_score'])) {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit;
}

$user_id = intval($input['user_id']);
$total_score = intval($input['total_score']);
$responses = $input['responses']; // Array of objects [{question: "", answer: ""}, ...]

$all_inserted = true;

foreach ($responses as $resp) {
    $question = $conn->real_escape_string($resp['question']);
    $answer = $conn->real_escape_string($resp['answer']);

    $sql = "INSERT INTO knowledge (user_id, question_text, selected_answer, total_score)
            VALUES ($user_id, '$question', '$answer', $total_score)";

    if (!$conn->query($sql)) {
        $all_inserted = false;
    }
}

if ($all_inserted) {
    echo json_encode(["status" => "success", "message" => "Responses saved successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Some responses could not be saved"]);
}

$conn->close();
?>
