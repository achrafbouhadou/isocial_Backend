<?php
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

require_once('../dbconnection.php');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$data = json_decode(file_get_contents('php://input'), true);

$errors = [];

// Check if all fields are present and not empty
$requiredFields = ['firstname', 'lastName', 'email', 'password', 'rePassword', 'birday', 'currentSchool', 'field', 'City'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        $errors[$field] = $field . ' is required';
    }
}

// Validate email format
var_dump(filter_var($data['email']));
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {

    $errors['email'] = 'Invalid email format';
}

// Check if passwords match
if ($data['password'] !== $data['rePassword']) {
    $errors['password'] = 'Passwords do not match';
}

// If there are any errors, send them back to the client
if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}


// test if the email is aleardy set in database 
$stm = $pdo->prepare("SELECT * From users WHERE email = :email");
$stm->bindParam(":email", $data['email']);
$stm->execute();
$result = $stm->fetch(PDO::FETCH_ASSOC);
if ($result) {
    // Email already exists in the database
    echo json_encode(['success' => false, 'message' => 'Email already exists']);
    exit;
}

$hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (id_filed, fr_name, ls_name, email, password, city, school) VALUES (:id_filed, :fr_name, :ls_name, :email, :password, :city, :school)");

$idField = (int)$data['field'];
$stmt->bindParam(":id_filed", $idField);
$stmt->bindParam(":fr_name", $data['firstname']);
$stmt->bindParam(":ls_name", $data['lastName']);
$stmt->bindParam(":email", $data['email']);
$stmt->bindParam(":password", $hashedPassword);
$stmt->bindParam(":city", $data['City']);
$stmt->bindParam(":school", $data['currentSchool']);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'User registered successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error in registration']);
}
