<?php 
    const HOST = "localhost";
    const USER = "root";
    const PASSWORD = "root";
    const DATABASE = "isocialdb";
    $secretKey = bin2hex(openssl_random_pseudo_bytes(32));
    define('JWT_SECRET_KEY', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyaWQiOjgsImlhdCI6MTcwNDc4NjY5MCwiZXhwIjoxNzA0ODAxMDkwfQ.ezIO9B4x-LGGXpq6soec2Xc7K_z3pYFk9ACY8YeM4jU' );
    $host = HOST;
    $db = DATABASE;

    $data = "mysql:host=$host;dbname=$db";

?>