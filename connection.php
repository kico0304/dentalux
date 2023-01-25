<?php

$database = "u605544755_dentalux";
$username = "u605544755_kico";
$password = "Sweden@2014";

try{
    $pdo = new PDO( 
        'mysql:host=localhost;dbname='.$database, 
        $username, 
        $password, 
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") 
    );
} catch (PDOException $e){
    exit('Database error');
}


?>