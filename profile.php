<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'config.php'; // DB connection

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB connection failed"]);
    exit;
}

// --- Fetch user profile (GET by email) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $email = $_GET['email'] ?? '';

    if (empty($email)) {
        echo json_encode(["status" => "error", "message" => "Missing email"]);
        exit;
    }

    $sql = "SELECT id, name, email, phone FROM users WHERE email='$email' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        echo json_encode(["status" => "success", "data" => $result->fetch_assoc()]);
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }
}

// --- Update profile (POST request) ---
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    $email = $input['email'] ?? '';
    $name  = $input['name'] ?? '';
    $phone = $input['phone'] ?? '';

    if (empty($email) || empty($name) || empty($phone)) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    $sql = "UPDATE users SET name='$name', phone='$phone' WHERE email='$email'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Profile updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Update failed"]);
    }
}

$conn->close();
?>
