<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config.php';

// --- Connect DB ---
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB connection failed"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $data = json_decode(file_get_contents("php://input"), true);

    $email = isset($data['email']) ? trim($data['email']) : "";
    $password = isset($data['password']) ? trim($data['password']) : "";
    $role = isset($data['role']) ? trim($data['role']) : "";

    // Validate
    if (empty($email) || empty($password) || empty($role)) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    // --- SQL Query ---
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $dbPassword = $user['password'];

        // Check both hashed and plain text
        if (password_verify($password, $dbPassword) || $password === $dbPassword) {
            echo json_encode([
                "status" => "success",
                "message" => "Login successful",
                "role" => $user['role'],
                "user_id" => $user['id']
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid password"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}

$conn->close();
?>
