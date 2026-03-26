<?php
require '../auth/auth.php';
require '../auth/db.php';
require '../auth/role.php';

if(!canManageUsers()) die("Access denied");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
$id = $_POST['id'] ?? null;
if(!$id) {
    header("Location: categories.php");
    exit;
}

$del = $pdo->prepare("DELETE FROM categories WHERE id=?");
$del->execute([$id]);
}
header("Location: categories.php");
exit();