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
    echo json_encode(['success' => false, 'message' => 'An error occurred while validating the token']);
}
$userid = (int)$decoded->userid;

$sql = "SELECT postes.*, collabs.*, users.* 
        FROM postes 
        INNER JOIN collabs ON postes.id = collabs.id_post
        INNER JOIN users ON collabs.id_user_ask = users.id
        WHERE postes.id_user_request = :id_user_request AND postes.id_user_travail IS NULL";

$stm = $pdo->prepare($sql);
$stm->bindParam(":id_user_request", $userid);
$stm->execute();
$results = $stm->fetchAll(PDO::FETCH_ASSOC);

if ($results) {
    echo json_encode(['collabs' => $results]);
}
