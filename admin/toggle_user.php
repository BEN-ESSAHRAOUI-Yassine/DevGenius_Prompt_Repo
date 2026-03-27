<?php
require '../auth/auth.php';
require '../auth/db.php';
require '../auth/role.php';

if(!canManageUsers()) die("Access denied");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id = $_POST['id'] ?? null;
    if(!$id || $id == 1 ){
        header("Location: users.php");
        exit;
    }
    if($id){
        $stmt = $pdo->prepare("
            UPDATE users 
            SET status = IF(status='Enabled','Disabled','Enabled')
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);
    }
}

header("Location: users.php");
exit;