<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once 'config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

$counselor_id = $_GET['counselor_id'] ?? 0;

if ($counselor_id == 0) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT * FROM referrals WHERE counselor_id = '$counselor_id' ORDER BY id DESC";
$result = $conn->query($sql);

$referrals = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $referrals[] = $row;
    }
}

echo json_encode($referrals);
$conn->close();
?>