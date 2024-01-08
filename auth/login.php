<?php
header('Access-Control-Allow-Origin: http://localhost:3000'); 
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once('../dbconnection.php');
$data = json_decode(file_get_contents('php://input'), true);

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->bindParam(":email", $data['email']);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
if($result){
    if (password_verify($result['password'], $data['password'])) {
        echo json_encode(['success' => true, 'message' => 'User logined successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid password!']);
    }
}else{
    echo json_encode(['success' => false, 'message' => 'Invalid email!']);
}
