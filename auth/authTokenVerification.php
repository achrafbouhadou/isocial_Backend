<?php
require_once('../vendor/autoload.php');
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
// Always set these headers for OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: http://localhost:3000');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 86400'); // Cache preflight request for 86400 seconds
    exit(0);
}

// Set headers for other requests
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Content-Type: application/json');

require_once('../Globals.php');
require_once('./getTokenKey.php');

$jwt = getJwtFromAuthorizationHeader();
echo getJwtFromAuthorizationHeader(); // eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyaWQiOjgsImlhdCI6MTcwNDc4NjY5MCwiZXhwIjoxNzA0ODAxMDkwfQ.ezIO9B4x-LGGXpq6soec2Xc7K_z3pYFk9ACY8YeM4jU
if (!$jwt) { 
    http_response_code(401); 
    echo json_encode(['success' => false, 'message' => 'No token provided']);
    exit;
}

try {
    $decoded = JWT::decode($jwt, new Key(JWT_SECRET_KEY , 'HS256'));
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