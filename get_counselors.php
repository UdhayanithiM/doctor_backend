<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once 'config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode([]));
}

// Fetch only users with role 'Counselor'
$sql = "SELECT id, name, email, phone, qualification, school, created_at FROM users WHERE role = 'Counselor'";
$result = $conn->query($sql);

$counselors = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $counselors[] = $row;
    }
}

echo json_encode($counselors);
$conn->close();
?>