<?php
// doc_referal.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'config.php'; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// We select ALL columns so we get gender, reason, doctor_suggestion, etc.
$sql = "SELECT * FROM referrals ORDER BY id DESC"; 
$result = $conn->query($sql);

$referrals = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $referrals[] = $row;
    }
}

echo json_encode($referrals);

$conn->close();
?>