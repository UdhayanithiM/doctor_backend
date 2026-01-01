<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
require_once 'config.php';
$conn = new mysqli($servername, $username, $password, $dbname);

// Check DB connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

// Get input data (support both form-data and raw JSON)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST;
    if (empty($input)) {
        $rawData = file_get_contents("php://input");
        $input = json_decode($rawData, true);
    }

    // Common fields
    $role     = $input['role'] ?? '';
    $name     = $input['name'] ?? '';
    $email    = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    $phone    = $input['phone'] ?? '';

    // Role-specific fields
    $age            = $input['age'] ?? null;
    $qualification  = $input['qualification'] ?? null;
    $school         = $input['school'] ?? null;
    $yearInSchool   = $input['year_in_school'] ?? null;

    $fatherOcc      = $input['father_occ'] ?? null;
    $motherOcc      = $input['mother_occ'] ?? null;
    $fatherPhone    = $input['father_phone'] ?? null;
    $motherPhone    = $input['mother_phone'] ?? null;

    $studentName    = $input['student_name'] ?? null;
    $standard       = $input['standard'] ?? null;
    $registerNumber = $input['register_number'] ?? null;

    $stmt = $conn->prepare("INSERT INTO users 
        (role, name, email, password, phone, age, qualification, school, year_in_school, 
        father_occ, mother_occ, father_phone, mother_phone, student_name, standard, register_number) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
        $conn->close();
        exit;
    }

    $stmt->bind_param(
        "ssssssssssssssss",
        $role, $name, $email, $password, $phone, $age, $qualification, $school, $yearInSchool,
        $fatherOcc, $motherOcc, $fatherPhone, $motherPhone, $studentName, $standard, $registerNumber
    );

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "$role Signed Up Successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Signup failed: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid Request"]);
}
?>