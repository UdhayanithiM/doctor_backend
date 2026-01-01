<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once 'config.php';

// Create DB connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed"
    ]);
    exit;
}

// Fetch all responses
$sql = "SELECT id, username, scenario, answer1, answer2, answer3, answer4 FROM responses";
$result = $conn->query($sql);

$responses = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // ✅ Calculate score (non-empty answers)
        $score = 0;
        $answerCount = 0;
        for ($i = 1; $i <= 4; $i++) {
            if (!empty($row["answer$i"])) {
                $score++;
                $answerCount++;
            }
        }

        $responses[] = [
            "id" => $row["id"],
            "username" => $row["username"],
            "scenario" => $row["scenario"],
            "answer1" => $row["answer1"],
            "answer2" => $row["answer2"],
            "answer3" => $row["answer3"],
            "answer4" => $row["answer4"],
            "responses" => $answerCount, // 🔹 Added count
            "score" => $score
        ];
    }
}

echo json_encode($responses);
$conn->close();
?>
