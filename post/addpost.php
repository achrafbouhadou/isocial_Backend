<?php
require_once('../vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');
require_once('../dbconnection.php');
require_once('../Globals.php');
require_once('../auth/getTokenKey.php');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$jwt = getJwtFromAuthorizationHeader();
if (!$jwt) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No token provided']);
    exit;
}

try {
    $decoded = JWT::decode($jwt, new Key(JWT_SECRET_KEY, 'HS256'));

    echo json_encode(['success' => true, 'message' => 'Token is valid']);
} catch (Firebase\JWT\ExpiredException $e) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'Expired token']);
} catch (Firebase\JWT\SignatureInvalidException $e) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'Invalid token']);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    // Log the actual error message for server-side debugging
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while validating the token']);
}

$data = json_decode(file_get_contents('php://input'), true);

$errors = [];
// Check if all fields are present and not empty
$requiredFields = ['title', 'description', 'type', 'filed', 'end_date'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        $errors[$field] = $field . ' is required';
    }
}


// If there are any errors, send them back to the client
if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

var_dump($data);
$stmt = $pdo->prepare("INSERT INTO postes (id_field, id_user_request, title, description, type, end_date) VALUES (:id_field, :id_user_request, :title, :description, :type, :end_date)");

$idField = (int)$data['field'];
$userid = (int)$decoded->userid;
$stmt->bindParam(":id_field", $idField);
$stmt->bindParam(":id_user_request", $userid);
$stmt->bindParam(":title", $data['title']);
$stmt->bindParam(":description", $data['description']);
$stmt->bindParam(":type", $data['type']);
$stmt->bindParam(":end_date", $data['end_date']);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'poste added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error in adding postes: ' . $stmt->errorInfo()[2]]);
}
