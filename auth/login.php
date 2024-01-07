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
