<?php

$host = "localhost";
$dbname = "DevGenius_db";
$user = "root";
$pasord = "";

try {

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pasord);

$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

}

catch(PDOException $e){

die("Database connection failed: " . $e->getMessage());

}