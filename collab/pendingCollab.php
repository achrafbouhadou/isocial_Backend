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
$userid = (int)$decoded->userid;
$stmt = $pdo->prepare("SELECT p.*, c.id_user_ask, c.id_post
FROM postes p
JOIN collabs c ON p.id = c.id_post
WHERE 
    (c.id_user_ask = :current_user_id AND p.id_user_travail IS NULL) 
    OR
    (p.id_user_request = :current_user_id AND p.id_user_travail IS NOT NULL)
");
$stmt->bindParam('current_user_id', $userid);

if ($stmt->execute()) {
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['postes' => $results]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error in adding fetching poste data: ' . $stmt->errorInfo()[2]]);
}
