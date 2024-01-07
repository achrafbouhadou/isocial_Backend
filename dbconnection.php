<?php 
    require_once __DIR__ . "/Globals.php";
    
    try {
        $pdo = new PDO($data, USER, PASSWORD);
     
    } catch (PDOException $error) {
        echo "Connection failed: " . $error->getMessage();
    }
?>
