<?php
require_once('../vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once('../Globals.php');
require_once('./getTokenKey.php');
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
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while validating the token']);
}
