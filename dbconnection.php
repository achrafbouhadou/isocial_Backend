<?php 
    require_once __DIR__."\Globals.php";
    
    try{
        $pdo = new PDO($data, USER, PASSWORD);
        echo "Connected Seccessfully";
    }catch(PDOExeption $error){
        echo $error;
    }
?>