<?php

require '../auth/auth.php';
require '../auth/db.php';
require '../auth/role.php';

if(!canManageUsers()){
die("Access denied");
}

$id=$_GET['id'];

$stmt=$pdo->prepare("DELETE FROM users WHERE id=:id");
$stmt->execute(['id'=>$id]);

header("Location: users.php");
exit;