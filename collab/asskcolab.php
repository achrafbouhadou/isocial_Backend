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
$postId = (int)$_GET['postId']; // 16
$userid = (int)$decoded->userid; // 16

$stm = $pdo->prepare("SELECT * FROM collabs WHERE id_user_ask = :id_user_ask AND id_post = :id_post ");
$stm->bindParam(":id_user_ask", $userid); // Corrected binding
$stm->bindParam(":id_post", $postId); // Corrected binding

if ($stm->execute()) {
    $result = $stm->fetch();
    if ($result) {
        echo json_encode(['success' => false, 'message' => 'You have already asked for collab']);
        exit; // Exit after sending response
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error in selecting collabs: ' . $stm->errorInfo()[2]]);
    exit; // Exit on error
}

$stmt = $pdo->prepare("INSERT INTO collabs (id_user_ask, id_post) VALUES (:id_user_ask, :id_post)");

$stmt->bindParam(":id_user_ask", $userid); // Corrected binding
$stmt->bindParam(":id_post", $postId); // Corrected binding

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Collab added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error in adding collab: ' . $stmt->errorInfo()[2]]);
}
